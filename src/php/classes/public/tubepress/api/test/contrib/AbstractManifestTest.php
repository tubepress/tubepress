<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
abstract class tubepress_api_test_contrib_AbstractManifestTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @param $pathToManifest
     *
     * @return tubepress_api_contrib_AddonInterface
     */
    protected function getAddonFromManifest($pathToManifest)
    {
        $mockUrlFactory = $this->mock(tubepress_api_url_UrlFactoryInterface::_);
        $mockUrlFactory->shouldReceive('fromString')->andReturnUsing(function ($incoming) {

            $factory = new tubepress_url_impl_puzzle_UrlFactory($_SERVER);
            return $factory->fromString($incoming);
        });

        $logger        = new tubepress_internal_logger_BootLogger(false);
        $urlFactory    = new tubepress_url_impl_puzzle_UrlFactory();
        $bootSettings  = new tubepress_internal_boot_BootSettings($logger, $urlFactory);
        $langUtils     = new tubepress_util_impl_LangUtils();
        $stringUtils   = new tubepress_util_impl_StringUtils();
        $finderFactory = new tubepress_internal_finder_FinderFactory();

        $manifestFinder = new tubepress_internal_boot_helper_uncached_contrib_ManifestFinder(

            dirname($pathToManifest), 'whatevs', 'manifest.json', $logger, $bootSettings, $finderFactory
        );
        $addonFactory = new tubepress_internal_boot_helper_uncached_contrib_AddonFactory(
            $logger, $urlFactory, $langUtils, $stringUtils, $bootSettings
        );
        $addonManifests = $manifestFinder->find();

        $this->assertTrue(count($addonManifests) === 1, 'Expected 1 add-on but got ' . count($addonManifests));

        $addons = array();

        foreach ($addonManifests as $manifestPath => $contents) {

            $addons[] = $addonFactory->fromManifestData($manifestPath, $contents);
        }

        $this->assertTrue($addons[0] instanceof tubepress_api_contrib_AddonInterface);

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
        $map          = \Symfony\Component\ClassLoader\ClassMapGenerator::createMap(dirname($this->getPathToManifest()));
        $missing      = array();
        $manifest     = $this->_decodeManifest();
        $toIgnore     = $this->getClassNamesToIgnore();
        $manifestPath = $this->getPathToManifest();
        $manifestDir  = dirname($manifestPath);

        foreach ($map as $className => $path) {

            if (strpos($path, "$manifestDir/tests") === 0) {

                unset($map[$className]);
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

            $expected = '';
            foreach ($missing as $className) {

                $expected .= "\"$className\" : \"classes/" . str_replace('_', '/', $className) . ".php\",\n";
            }

            $message = "The following classes are missing from the manifest's classmap: \n\n" . $expected;
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

    protected function assertLicense(tubepress_api_contrib_AddonInterface $addon, array $expected)
    {
        $actual = $addon->getLicense();

        $map = $actual;
        $this->assertInstanceOf('tubepress_api_collection_MapInterface', $map);

        if ($map->containsKey('urls')) {

            $actualUrls = $map->get('urls');

            for ($x = 0; $x < count($actualUrls); $x++) {

                $actualUrls[$x] = (string) $actualUrls[$x];
            }

            $map->put('urls', $actualUrls);
        }

        foreach ($expected as $key => $value) {

            $this->assertTrue($map->containsKey($key));
            $this->assertEquals($value, $map->get($key));
        }
    }

    protected function assertAuthors(tubepress_api_contrib_AddonInterface $addon, array $expected)
    {
        $actual = $addon->getAuthors();

        if (count($actual) !== count($expected)) {

            $this->fail('Wrong count of authors');
            return;
        }

        for ($x = 0; $x < count($actual); $x++) {

            $map = $actual[$x];
            $this->assertInstanceOf('tubepress_api_collection_MapInterface', $map);

            $expected = $expected[$x];

            foreach ($expected as $key => $value) {

                $this->assertTrue($map->containsKey($key));
                $this->assertEquals($value, $map->get($key));
            }
        }
    }
}