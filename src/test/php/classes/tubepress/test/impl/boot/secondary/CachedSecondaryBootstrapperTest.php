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
 * @covers tubepress_impl_boot_secondary_CachedSecondaryBootstrapper<extended>
 */
class tubepress_test_impl_boot_secondary_CachedSecondaryBootstrapperTest extends tubepress_test_impl_boot_secondary_AbstractSecondaryBootstrapperTest
{

    public function buildSut()
    {
        return new tubepress_impl_boot_secondary_CachedSecondaryBootstrapper(

            true
        );
    }

    public function testGetContainer()
    {
        file_put_contents($this->getMockContainerPath(), $this->getDumpedEmptyIconicContainerBuilder());

        $this->getMockSettingsFileReader()->shouldReceive('getCachedContainerStoragePath')->once()->andReturn($this->getMockContainerPath());

        $container = $this->getContainer();

        $this->assertInstanceOf('TubePressServiceContainer', $container);
    }

    protected function getDumpedEmptyIconicContainerBuilder()
    {
        return <<<XYZ
<?php

/**
 * TubePressServiceContainer
 *
 * This class has been auto-generated
 * by the Symfony Dependency Injection Component.
 */
class TubePressServiceContainer extends ehough_iconic_Container
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }
}

XYZ;

    }
}