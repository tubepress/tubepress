<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_impl_addon_AbstractManifestValidityTest extends TubePressUnitTest
{
    protected function getAddonFromManifest($pathToManifest)
    {
        $mockFinderFactory = $this->createMockSingletonService('ehough_finder_FinderFactoryInterface');
        $mockFinder        = $this->createMockSingletonService('ehough_finder_FinderInterface');

        $mockFinder->shouldReceive('files')->once()->andReturn($mockFinder);
        $mockFinder->shouldReceive('in')->once()->with(dirname($pathToManifest))->andReturn($mockFinder);
        $mockFinder->shouldReceive('name')->once()->with('*.json')->andReturn($mockFinder);
        $mockFinder->shouldReceive('depth')->once()->with('< 2')->andReturn(array(new SplFileInfo($pathToManifest)));

        $mockFinderFactory->shouldReceive('createFinder')->once()->andReturn($mockFinder);

        $discoverer = new tubepress_impl_addon_FilesystemAddonDiscoverer();

        $addons = $discoverer->findAddonsInDirectory(dirname($pathToManifest));

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

            $this->assertTrue(is_readable($path) && is_file($path));

            if (!class_exists($className) && !interface_exists($className)) {

                require $path;

                $this->assertTrue(class_exists($className) || interface_exists($className));
            }
        }

        foreach ($expectedClassMap as $className => $abbreviatedPrefix) {

            $this->assertTrue(isset($actualClassMap[$className]));

            $this->assertTrue(tubepress_impl_util_StringUtils::endsWith($actualClassMap[$className], $abbreviatedPrefix),
                $actualClassMap[$className] . ' does not end with ' . $abbreviatedPrefix);
        }
    }
}