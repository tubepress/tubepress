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
        $mockUrlFactory = $this->createMockSingletonService(tubepress_spi_url_UrlFactoryInterface::_);
        $mockUrlFactory->shouldReceive('fromString')->andReturnUsing(function ($incoming) {

            $factory = new tubepress_addons_puzzle_impl_url_UrlFactory();
            return $factory->fromString($incoming);
        });

        $discoverer = new tubepress_impl_addon_AddonFinder(
            new ehough_finder_FinderFactory(),
            new tubepress_impl_environment_SimpleEnvironmentDetector()
        );

        $addons = $discoverer->_findContributablesInDirectory(dirname($pathToManifest));

        $this->assertTrue(count($addons) === 1, 'Expected 1 addon but got ' . count($addons));

        $this->assertTrue($addons[0] instanceof tubepress_spi_addon_AddonInterface);

        return $addons[0];
    }

    public function testClassMapIntegrity()
    {
        $map      = \Symfony\Component\ClassLoader\ClassMapGenerator::createMap(dirname($this->getPathToManifest()));
        $missing  = array();
        $manifest = $this->_decodeManifest();
        $toIgnore = $this->getClassNamesToIgnore();

        foreach ($map as $className => $path) {

            if (in_array($className, $toIgnore)) {

                continue;
            }

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

    protected function getClassNamesToIgnore()
    {
        //override point
        return array();
    }
}