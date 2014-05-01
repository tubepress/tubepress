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
 * Provides BC for legacy add-ons.
 */
class tubepress_impl_bc_LegacyExtensionConverter implements tubepress_spi_bc_LegacyExtensionConverterInterface
{
    private static $_knownLegacyAddonNamesToMaxVersions = array(

        'tubepress-flexible-thumbnail-rows' => '1.0.0',
        'tubepress-quickplay-addon'         => '1.0.0',
        'vimeo-all-access'                  => '1.0.0',
        'youtube-black-bars-remover'        => '1.0.2',
    );

    /**
     * @param boolean                            $shouldLog          Should we log?
     * @param ehough_epilog_Logger               $logger             Logger.
     * @param int                                $index              Index of add-on.
     * @param int                                $count              Total add-on count.
     * @param tubepress_spi_addon_AddonInterface $addon              The add-on itself.
     * @param string                             $extensionClassName The extension class name.
     *
     * @return boolean True if successfully loaded, false otherwise.
     */
    public function evaluateLegacyExtensionClass($shouldLog, ehough_epilog_Logger $logger, $index, $count, tubepress_spi_addon_AddonInterface $addon, $extensionClassName)
    {
        if (!$this->_canCallEval()) {

            if ($shouldLog) {

                $logger->warn(sprintf('(Add-on %d of %d: %s) eval() is not permitted. %s will not be loaded. Please upgrade this add-on.',
                    $index, $count, $addon->getName(), $extensionClassName));
            }

            return false;
        }

        $contents = $this->_getClassFileContents($addon, $extensionClassName);

        if ($contents === null) {

            if ($shouldLog) {

                $logger->warn(sprintf('(Add-on %d of %d: %s) Could not read extension file. %s will not be loaded. Please upgrade this add-on.',
                    $index, $count, $addon->getName(), $extensionClassName));
            }

            return false;
        }

        $contents = str_replace('tubepress_api_ioc_ContainerInterface', 'tubepress_api_ioc_ContainerBuilderInterface', $contents);
        $contents = str_replace('<?php', '', $contents);

        if ($shouldLog) {

            $logger->debug(sprintf('(Add-on %d of %d: %s) Modified class contents of %s. Now attempting to eval().',
                $index, $count, $addon->getName(), $extensionClassName));
        }

        $result = @eval($contents);

        if ($result !== null) {

            if ($shouldLog) {

                $logger->warn(sprintf('(Add-on %d of %d: %s) eval() of %s failed. Please upgrade this add-on.',
                    $index, $count, $addon->getName(), $extensionClassName));
            }

            return false;
        }

        if (!class_exists($extensionClassName)) {

            if ($shouldLog) {

                $logger->warn(sprintf('(Add-on %d of %d: %s) eval() succeeded, but something is still wrong. Please upgrade this add-on.',
                    $index, $count, $addon->getName(), $extensionClassName));
            }

            return false;
        }

        if ($shouldLog) {

            $logger->debug(sprintf('(Add-on %d of %d: %s) Legacy workaround seems to have worked. Now attempting normal registration.',
                $index, $count, $addon->getName(), $extensionClassName));
        }

        return true;
    }

    /**
     * @param tubepress_spi_addon_AddonInterface $addon The add-on.
     *
     * @return boolean True if this is a pre 3.2.x add-op, false otherwise.
     */
    public function isLegacyAddon(tubepress_spi_addon_AddonInterface $addon)
    {
        $addonName = $addon->getName();

        if (!array_key_exists($addonName, self::$_knownLegacyAddonNamesToMaxVersions)) {

            return false;
        }

        $maxVersionAsString = self::$_knownLegacyAddonNamesToMaxVersions[$addonName];
        $maxVersion         = tubepress_api_version_Version::parse($maxVersionAsString);
        $addonVersion       = $addon->getVersion();

        return $addonVersion->compareTo($maxVersion) <= 0;
    }

    private function _getClassFileContents(tubepress_spi_addon_AddonInterface $addon, $extensionClassName)
    {
        $classMap = $addon->getClassMap();

        if (!isset($classMap[$extensionClassName])) {

            return null;
        }

        $classLocation = $classMap[$extensionClassName];

        if (!is_file($classLocation) || !is_readable($classLocation)) {

            return null;
        }

        $contents = file_get_contents($classLocation);

        if ($contents === false) {

            return null;
        }

        return $contents;
    }

    private function _canCallEval()
    {
        $result = @eval('return 33;');

        return $result === 33;
    }
}
