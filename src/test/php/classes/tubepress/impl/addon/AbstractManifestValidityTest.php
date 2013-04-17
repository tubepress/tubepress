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
}