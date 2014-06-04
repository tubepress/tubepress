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
abstract class tubepress_test_core_ioc_AbstractManifestValidityTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @param $pathToManifest
     *
     * @return tubepress_api_addon_AddonInterface
     */
    protected function getAddonFromManifest($pathToManifest)
    {
        $mockUrlFactory = $this->mock(tubepress_core_url_api_UrlFactoryInterface::_);
        $mockUrlFactory->shouldReceive('fromString')->andReturnUsing(function ($incoming) {

            $factory = new tubepress_core_url_impl_puzzle_UrlFactory($_SERVER);
            return $factory->fromString($incoming);
        });

        $logger = new tubepress_impl_log_BootLogger(false);

        $discoverer = new tubepress_impl_addon_Registry(
            $logger,
            new tubepress_impl_boot_BootSettings($logger),
            new ehough_finder_FinderFactory()
        );

        $addons = $discoverer->_findContributablesInDirectory(dirname($pathToManifest));

        $this->assertTrue(count($addons) === 1, 'Expected 1 addon but got ' . count($addons));

        $this->assertTrue($addons[0] instanceof tubepress_api_addon_AddonInterface);

        return $addons[0];
    }

    public function testCompilerPassesExist()
    {
        $addon = $this->getAddonFromManifest($this->getPathToManifest());

        $compilerPasses = $addon->getMapOfCompilerPassClassNamesToPriorities();

        $this->assertTrue(is_array($compilerPasses));

        foreach ($compilerPasses as $pass => $priority) {

            $this->assertTrue(class_exists($pass), "$pass is not a valid container compiler pass");
            $this->assertTrue(is_numeric($priority), "$pass must have a numeric priority");
        }
    }

    public function testIocContainerExtensionsExist()
    {
        $addon = $this->getAddonFromManifest($this->getPathToManifest());

        $extensions = $addon->getExtensionClassNames();

        $this->assertTrue(is_array($extensions));

        foreach ($extensions as $extension) {

            $this->assertTrue(class_exists($extension), "$extension is not a valid container extension");
        }
    }

    public function testClassMapIntegrity()
    {
        $map      = \Symfony\Component\ClassLoader\ClassMapGenerator::createMap(dirname($this->getPathToManifest()));
        $missing  = array();
        $manifest = $this->_decodeManifest();
        $toIgnore = $this->getClassNamesToIgnore();

        if (isset($manifest['inversion-of-control'])) {

            $toIgnore = array_merge($toIgnore, $manifest['inversion-of-control']['container-extensions']);

            if (isset($manifest['inversion-of-control']['compiler-passes'])) {

                $toIgnore = array_merge($toIgnore, array_keys($manifest['inversion-of-control']['compiler-passes']));
            }
        }

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

                $this->fail(dirname(realpath($this->getPathToManifest())) . DIRECTORY_SEPARATOR . $path . ' does not exist');
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