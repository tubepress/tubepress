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
abstract class tubepress_test_impl_addon_AbstractManifestValidityTest extends tubepress_test_TubePressUnitTest
{
    protected function getAddonFromManifest($pathToManifest)
    {
        $discoverer = new tubepress_impl_boot_secondary_DefaultAddonDiscoverer(
            new ehough_finder_FinderFactory(),
            new tubepress_impl_environment_SimpleEnvironmentDetector()
        );

        $addons = $discoverer->_findContributablesInDirectory(dirname($pathToManifest));

        $this->assertTrue(count($addons) === 1, 'Expected 1 addon but got ' . count($addons));

        $this->assertTrue($addons[0] instanceof tubepress_spi_addon_Addon);

        return $addons[0];
    }

    protected function validateClassMap($expectedClassMap, $actualClassMap)
    {
        $this->assertTrue(is_array($actualClassMap));

        $this->assertTrue(tubepress_impl_util_LangUtils::isAssociativeArray($actualClassMap));

        $this->assertTrue(count($expectedClassMap) === count($actualClassMap), 'Expected and actual class map sizes differ');

        foreach ($actualClassMap as $className => $path) {

            $this->assertTrue(is_readable($path) && is_file($path), "$path is not a readable file. Fix it!");

            if (!class_exists($className) && !interface_exists($className)) {

                require $path;

                $this->assertTrue(class_exists($className) || interface_exists($className));
            }
        }

        foreach ($expectedClassMap as $className => $abbreviatedPrefix) {

            $this->assertTrue(isset($actualClassMap[$className]), "$className is missing from the classmap");

            $this->assertTrue(tubepress_impl_util_StringUtils::endsWith($actualClassMap[$className], $abbreviatedPrefix),
                $actualClassMap[$className] . ' does not end with ' . $abbreviatedPrefix);
        }
    }

    public function testClassMapIntegrity()
    {
        $map      = \Symfony\Component\ClassLoader\ClassMapGenerator::createMap(dirname($this->getPathToManifest()));
        $missing  = array();
        $manifest = $this->_decodeManifest();

        foreach ($map as $className => $path) {

            if (!array_key_exists($className, $manifest['autoload']['classmap'])) {

                $missing[] = $className;
            }
        }

        if (!empty($missing)) {

            $missing = array_unique($missing);
            sort($missing);

            $message = "The following classes are missing from the manifest's classmap: \n\n" . implode("\n", $missing);
            $this->fail($message);
            return;
        }

        $extra = array_diff(array_keys($manifest['autoload']['classmap']), array_keys($map));

        if (!empty($extra)) {

            $message = "The following extra classes are in the manifest's classmap: \n\n" . implode("\n", $extra);
            $this->fail($message);
            return;
        }

        foreach ($manifest['autoload']['classmap'] as $className => $path) {

            if (!is_file(dirname($this->getPathToManifest()) . DIRECTORY_SEPARATOR . $path)) {

                $this->fail(dirname($this->getPathToManifest()) . DIRECTORY_SEPARATOR . $path . ' does not exist');
                return;
            }
        }

        $this->assertTrue(true);
    }

    private function _decodeManifest()
    {
        return json_decode(file_get_contents($this->getPathToManifest()), true);
    }

    protected abstract function getPathToManifest();
}