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
 * @covers tubepress_wordpress_impl_actions_AdminHead
 */
class tubepress_test_wordpress_impl_actions_AdminHeadTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_actions_AdminHead
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_wordpress_impl_actions_AdminHead();
    }

    public function testExecute()
    {
        $mockEvent = $this->mock('tubepress_core_api_event_EventInterface');

        ob_start();
        $this->_sut->action($mockEvent);
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('<meta name="viewport" content="width=device-width, initial-scale=1.0"><meta http-equiv="X-UA-Compatible" content="IE=edge">', $result);
    }
}