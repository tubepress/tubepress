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
 * Discovers add-ons for TubePress.
 */
class tubepress_core_theme_impl_ThemeRegistry extends tubepress_impl_contrib_AbstractRegistry
{
    private static $_LEGACY_THEME_NAME_PREFIX = 'unknown/legacy-';

    /**
     * @var tubepress_core_contrib_api_ContributableValidatorInterface
     */
    private $_validator;

    public function __construct(tubepress_api_log_LoggerInterface                          $logger,
                                tubepress_api_boot_BootSettingsInterface          $bootSettings,
                                ehough_finder_FinderFactoryInterface                       $finderFactory,
                                tubepress_core_contrib_api_ContributableValidatorInterface $validator)
    {
        parent::__construct($logger, $bootSettings, $finderFactory);

        $this->_validator = $validator;
    }

    /**
     * @return tubepress_api_contrib_ContributableInterface[] May be empty, never null.
     *
     * @api
     * @since 4.0.0
     */
    public function getAll()
    {
        $modernThemes = $this->findContributables('/src/core/themes/web', '/themes');
        $legacyThemes = $this->_findLegacyThemes($modernThemes);
        $toReturn     = array_merge($modernThemes, $legacyThemes);

        if ($this->shouldLog()) {

            $this->getLogger()->debug(sprintf('Found %d theme(s) (%d modern and %d legacy)',

                count($toReturn), count($modernThemes), count($legacyThemes)
            ));
        }

        return $toReturn;
    }

    /**
     * @return array A map of optional attributes.
     */
    protected function getOptionalAttributesMap()
    {
        return array(

            tubepress_core_theme_impl_ThemeBase::ATTRIBUTE_PARENT   => 'ParentThemeName',
            tubepress_core_theme_impl_ThemeBase::CATEGORY_RESOURCES => array(

                tubepress_core_theme_impl_ThemeBase::ATTRIBUTE_SCRIPTS => 'Scripts',
                tubepress_core_theme_impl_ThemeBase::ATTRIBUTE_STYLES  => 'Styles',
            ),
        );
    }

    /**
     * @return string The class name that this discoverer instantiates.
     */
    protected function getContributableClassName()
    {
        return 'tubepress_core_theme_impl_ThemeBase';
    }

    protected function getManifestName()
    {
        return 'theme.json';
    }

    protected function getAdditionalRequiredConstructorArgs(array $manifestContents, $absPath)
    {
        $pathElements = array(
            TUBEPRESS_ROOT,
            'src', 'core', 'themes', 'web'
        );

        $needle   = implode(DIRECTORY_SEPARATOR, $pathElements);
        $isSystem = strpos($absPath, $needle) !== false;

        return array($isSystem, dirname($absPath));
    }

    protected function filter(array &$contributables)
    {
        $contributables = array_filter($contributables, array($this, '__callbackRemoveStarterTheme'));
        $contributables = array_filter($contributables, array($this, '__removeInvalidThemes'));
    }

    public function __removeInvalidThemes($element)
    {
        if (!($element instanceof tubepress_core_theme_api_ThemeInterface)) {

            return false;
        }

        if (!$this->_validator->isValid($element)) {

            if ($this->shouldLog()) {

                $this->getLogger()->error(sprintf('Ignoring invalid theme: %s', $this->_validator->getProblemMessage($element)));
            }

            return false;
        }

        return true;
    }

    public function __callbackRemoveStarterTheme($element)
    {
        if (!($element instanceof tubepress_core_theme_api_ThemeInterface)) {

            return true;
        }

        return $element->getName() !== 'changeme/themename';
    }

    private function _findLegacyThemes(array $modernThemes)
    {
        $userThemeDir = $this->getBootSettings()->getUserContentDirectory() . '/themes';

        if (!is_dir($userThemeDir)) {

            if ($this->shouldLog()) {

                $this->getLogger()->debug(sprintf('User theme directory at %s does not exist.',
                    $userThemeDir
                ));
            }

            return array();
        }

        $finder       = $this->getFinderFactory()->createFinder()->directories()->in($userThemeDir)->depth('< 1');
        $themesToKeep = array();

        if ($this->shouldLog()) {

            $this->getLogger()->debug(sprintf('Looking for legacy themes in %s. %d candidates.',
                $userThemeDir, count($finder)
            ));
        }

        /**
         * @var $candidateLegacyThemeDir SplFileInfo
         */
        foreach ($finder as $candidateLegacyThemeDir) {

            $keepTheme = true;

            if (basename($candidateLegacyThemeDir->getPathname()) === 'starter') {

                /**
                 * Ignore the starter theme.
                 */
                continue;
            }

            /**
             * @var $modernTheme tubepress_core_theme_api_ThemeInterface
             */
            foreach ($modernThemes as $modernTheme) {

                if (strpos($candidateLegacyThemeDir->getPathname(), $modernTheme->getRootFilesystemPath()) !== false) {

                    $keepTheme = false;
                    break;
                }
            }

            if ($keepTheme) {

                if ($this->shouldLog()) {

                    $this->getLogger()->debug(sprintf('Considering %s as a legacy theme.', $candidateLegacyThemeDir->getPathname()));
                }

                $themesToKeep[] = $candidateLegacyThemeDir->getPathname();

            } else {

                if ($this->shouldLog()) {

                    $this->getLogger()->debug(sprintf('Determined that %s is not a legacy theme.', $candidateLegacyThemeDir));
                }
            }
        }

        $toReturn = array();

        foreach ($themesToKeep as $legacyThemeDirectory) {

            $theme = new tubepress_core_theme_impl_ThemeBase(

                self::$_LEGACY_THEME_NAME_PREFIX . basename($legacyThemeDirectory),
                '1.0.0',
                ucwords(basename($legacyThemeDirectory)) . ' (legacy)',
                array('name' => 'unknown'),
                array(array('type' => 'MIT', 'url' => 'http://opensource.org/licenses/MIT')),
                false,
                $legacyThemeDirectory
            );

            $theme->setParentThemeName('tubepress/legacy-default');

            $toReturn[] = $theme;
        }

        return $toReturn;
    }
}