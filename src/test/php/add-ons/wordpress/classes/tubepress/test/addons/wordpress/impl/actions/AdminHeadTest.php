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
 * @covers tubepress_addons_wordpress_impl_actions_AdminHead
 */
class tubepress_test_addons_wordpress_impl_actions_AdminHeadTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_wordpress_impl_actions_AdminHead
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockWordPressFunctionWrapper;

    public function onSetup()
    {
        $this->_sut = new tubepress_addons_wordpress_impl_actions_AdminHead();

        $this->_mockWordPressFunctionWrapper = $this->createMockSingletonService(tubepress_addons_wordpress_spi_WpFunctionsInterface::_);
    }

    public function testExecute()
    {
        ob_start();
        $this->_sut->execute(array());
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('<meta name="viewport" content="width=device-width, initial-scale=1.0"><meta http-equiv="X-UA-Compatible" content="IE=edge">', $result);
    }
}
