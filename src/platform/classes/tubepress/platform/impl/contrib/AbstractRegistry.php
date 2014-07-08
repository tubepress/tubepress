<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Discovers add-ons and themes for TubePress.
 */
abstract class tubepress_platform_impl_contrib_AbstractRegistry implements tubepress_platform_api_contrib_RegistryInterface
{
    /**
     * @var tubepress_platform_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_shouldLog;

    /**
     * @var tubepress_platform_api_boot_BootSettingsInterface
     */
    private $_bootSettings;

    /**
     * @var ehough_finder_FinderFactoryInterface
     */
    private $_finderFactory;

    public function __construct(tubepress_platform_api_log_LoggerInterface        $logger,
                                tubepress_platform_api_boot_BootSettingsInterface $bootSettings,
                                ehough_finder_FinderFactoryInterface     $finderFactory)
    {
        $this->_logger        = $logger;
        $this->_shouldLog     = $logger->isEnabled();
        $this->_bootSettings  = $bootSettings;
        $this->_finderFactory = $finderFactory;
    }

    protected function findContributables($sysPath, $userPath)
    {
        $systemContributables = $this->_findSystemContributables($sysPath);
        $userContributables   = $this->_findUserContributables($userPath);
        $allContributables    = array_merge($systemContributables, $userContributables);

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Found %d instances of %s (%d system and %d user) on the filesystem',
                count($allContributables), $this->getContributableClassName(), count($systemContributables), count($userContributables)));
        }

        return $allContributables;
    }

    /**
     * @return ehough_finder_FinderFactoryInterface
     */
    protected function getFinderFactory()
    {
        return $this->_finderFactory;
    }

    /**
     * @return tubepress_platform_api_boot_BootSettingsInterface
     */
    protected function getBootSettings()
    {
        return $this->_bootSettings;
    }

    /**
     * @return tubepress_platform_api_log_LoggerInterface
     */
    protected function getLogger()
    {
        return $this->_logger;
    }

    /**
     * @return bool
     */
    protected function shouldLog()
    {
        return $this->_shouldLog;
    }


    /**
     * @return array A map of optional attributes.
     */
    protected abstract function getOptionalAttributesMap();

    /**
     * @return string The class name that this discoverer instantiates.
     */
    protected abstract function getContributableClassName();

    protected abstract function getManifestName();

    protected abstract function getAdditionalRequiredConstructorArgs(array $manifestContents, $absPath);

    protected function getCleanedAttributeValue(/** @noinspection PhpUnusedParameterInspection */
        $attributeName, $candidateValue, $manifestFileAbsPath, array $manifestContents)
    {
        //override point
        return $candidateValue;
    }

    protected function filter(array &$contributables)
    {
        //override point
        return $contributables;
    }

    private function _findSystemContributables($sysPath)
    {
        $coreContributables = $this->_findContributablesInDirectory(TUBEPRESS_ROOT . $sysPath);

        return $coreContributables;
    }

    private function _findUserContributables($userPath)
    {
        $userContentDir         = $this->_bootSettings->getUserContentDirectory();
        $userContributablesDir  = $userContentDir . $userPath;
        $userContributables     = $this->_findContributablesInDirectory($userContributablesDir);

        return $userContributables;
    }

    public function _findContributablesInDirectory($directory)
    {
        if (! is_dir($directory)) {

            return array();
        }

        $finder   = $this->_finderFactory->createFinder();
        $toReturn = array();

        if ($this->_shouldLog) {

            $this->_logger->debug('Searching for manifests in ' . $directory);
        }

        $finder = $finder->followLinks()->files()->in($directory)->name($this->getManifestName())->depth('< 3');

        /**
         * @var $infoFile SplFileInfo
         */
        foreach ($finder as $infoFile) {

            $decoded = $this->_tryToDecodeManifest($infoFile);

            if ($decoded === null) {

                continue;
            }

            $absPath = $infoFile->getPathname();

            if ($this->_manifestContainsRequiredAttributes($decoded)) {

                if ($this->_shouldLog) {

                    $this->_logger->debug(sprintf('%s has all the required attributes', $absPath));
                }

                $toReturn[$absPath] = $decoded;

            } else {

                if ($this->_shouldLog) {

                    $this->_logger->error(sprintf('%s does not have all the required attributes', $absPath));
                }
            }
        }

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Found %d parseable manifest(s) from %s' , count($toReturn), $directory));
        }

        return $this->_buildFromManifests($toReturn);
    }

    private function _buildFromManifests(array $manifests)
    {
        $toReturn = array();

        foreach ($manifests as $absPath => $manifest) {

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Now trying to build %s from manifest at %s', $this->getContributableClassName(),
                    $absPath));
            }

            try {

                $toReturn[] = $this->_build($manifest, $absPath);

                if ($this->_shouldLog) {

                    $this->_logger->debug(sprintf('Successfully built new %s from manifest at %s', $this->getContributableClassName(),
                        $absPath));
                }

            } catch (Exception $e) {

                if ($this->_shouldLog) {

                    $this->_logger->error(sprintf('Caught exception when trying to build %s from manifest at %s: %s', $this->getContributableClassName(),
                        $absPath, $e->getMessage()));
                }
            }
        }

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Successfully constructed %d instance(s) of %s out of the %d found. Now filtering.',
                count($toReturn),
                $this->getContributableClassName(),
                count($manifests)));
        }

        $this->filter($toReturn);

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('After filtering, we now have %d instance(s) of %s.',
                count($toReturn),
                $this->getContributableClassName()));
        }

        return $toReturn;
    }

    private function _build(array $manifestContents, $absPath)
    {
        $ref  = new ReflectionClass($this->getContributableClassName());
        $args = array(

            $manifestContents[tubepress_platform_impl_contrib_ContributableBase::ATTRIBUTE_NAME],
            $manifestContents[tubepress_platform_impl_contrib_ContributableBase::ATTRIBUTE_VERSION],
            $manifestContents[tubepress_platform_impl_contrib_ContributableBase::ATTRIBUTE_TITLE],
            $manifestContents[tubepress_platform_impl_contrib_ContributableBase::ATTRIBUTE_AUTHOR],
            $manifestContents[tubepress_platform_impl_contrib_ContributableBase::ATTRIBUTE_LICENSES]
        );

        $additionalConstructorArgs = $this->getAdditionalRequiredConstructorArgs($manifestContents, $absPath);

        if ($additionalConstructorArgs) {

            $args = array_merge($args, $additionalConstructorArgs);
        }

        $instance = $ref->newInstanceArgs($args);

        $this->_setOptionalAttributes($instance, $manifestContents, $absPath);

        return $instance;
    }

    private function _setOptionalAttributes(tubepress_platform_api_contrib_ContributableInterface $contributable,
                                            array $manifestContentsAsArray, $manifestFileAbsPath)
    {
        $optionalAttributeMap = array(

            tubepress_platform_impl_contrib_ContributableBase::ATTRIBUTE_DESCRIPTION => 'Description',
            tubepress_platform_impl_contrib_ContributableBase::ATTRIBUTE_KEYWORDS    => 'Keywords',
            tubepress_platform_impl_contrib_ContributableBase::CATEGORY_URLS                 => array(

                tubepress_platform_impl_contrib_ContributableBase::ATTRIBUTE_URL_HOMEPAGE      => 'HomepageUrl',
                tubepress_platform_impl_contrib_ContributableBase::ATTRIBUTE_URL_DOCUMENTATION => 'DocumentationUrl',
                tubepress_platform_impl_contrib_ContributableBase::ATTRIBUTE_URL_DEMO          => 'DemoUrl',
                tubepress_platform_impl_contrib_ContributableBase::ATTRIBUTE_URL_DOWNLOAD      => 'DownloadUrl',
                tubepress_platform_impl_contrib_ContributableBase::ATTRIBUTE_URL_BUGS          => 'BugTrackerUrl',
            ),
            tubepress_platform_impl_contrib_ContributableBase::ATTRIBUTE_SCREENSHOTS => 'Screenshots',
        );
        $optionalAttributeMap = array_merge($optionalAttributeMap, $this->getOptionalAttributesMap());

        $this->_setOptionalAttributesFromMap($contributable, $manifestContentsAsArray, $manifestFileAbsPath, $optionalAttributeMap);
    }

    private function _tryToDecodeManifest( $infoFile)
    {
        /**
         * @var $infoFile SplFileInfo
         */
        $manifestFilePath = $infoFile->getPathname();

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Attempting to decode manifest at %s', $manifestFilePath));
        }

        $decodedManifest  = @json_decode(file_get_contents($manifestFilePath), true);
        $manifestParsed   = $decodedManifest !== null && $decodedManifest !== false && !empty($decodedManifest);

        if ($this->_shouldLog) {

            if ($manifestParsed) {

                $this->_logger->debug(sprintf('Decoded manifest at %s? %s', $manifestFilePath, $manifestParsed ? 'yes' : 'no'));

            } else {

                $this->_logger->error(sprintf('Decoded manifest at %s? %s', $manifestFilePath, $manifestParsed ? 'yes' : 'no'));
            }
        }

        if ($manifestParsed) {

            return $decodedManifest;
        }

        return null;
    }

    private function _setOptionalAttributesFromMap(tubepress_platform_api_contrib_ContributableInterface $contributable, array $manifestContentsAsArray,
                                                   $manifestFileAbsPath, array $attributeNameToSetterNameMap)
    {
        foreach ($attributeNameToSetterNameMap as $optionalAttributeName => $setterSuffix) {

            /**
             * Dig into array if we need to.
             */
            if (is_array($setterSuffix)) {

                if (isset($manifestContentsAsArray[$optionalAttributeName])) {

                    $this->_setOptionalAttributesFromMap($contributable, $manifestContentsAsArray[$optionalAttributeName], $manifestFileAbsPath, $setterSuffix);
                }

                continue;
            }

            if (isset($manifestContentsAsArray[$optionalAttributeName])) {

                $method = 'set' . $setterSuffix;
                $value  = $this->getCleanedAttributeValue(

                    $optionalAttributeName,
                    $manifestContentsAsArray[$optionalAttributeName],
                    $manifestFileAbsPath,
                    $manifestContentsAsArray);

                $contributable->$method($value);
            }
        }
    }

    private function _manifestContainsRequiredAttributes(array $manifestContentsAsArray)
    {
        $requiredAttributeNames = array(

            tubepress_platform_impl_contrib_ContributableBase::ATTRIBUTE_NAME,
            tubepress_platform_impl_contrib_ContributableBase::ATTRIBUTE_VERSION,
            tubepress_platform_impl_contrib_ContributableBase::ATTRIBUTE_TITLE,
            tubepress_platform_impl_contrib_ContributableBase::ATTRIBUTE_AUTHOR,
            tubepress_platform_impl_contrib_ContributableBase::ATTRIBUTE_LICENSES
        );

        foreach ($requiredAttributeNames as $requiredAttributeName) {

            if (!isset($manifestContentsAsArray[$requiredAttributeName])) {

                if ($this->_shouldLog) {

                    $this->_logger->error(sprintf('Manifest is missing %s attribute', $requiredAttributeName));
                }

                return false;
            }
        }

        return true;
    }
}
