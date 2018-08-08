<?php
/*
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_wordpress_impl_listeners_html_WpHtmlListener
 */
class tubepress_test_wordpress_impl_listeners_html_WpHtmlListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_listeners_html_WpHtmlListener
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_wordpress_impl_listeners_html_WpHtmlListener();
    }

    public function testOnTemplatePreRender()
    {
        $mockEvent = $this->mock('tubepress_api_event_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn(array());
        $mockEvent->shouldReceive('setSubject')->once()->with(array(
            'urls' => array(),
        ));

        $this->_sut->onScriptsStylesTemplatePreRender($mockEvent);

        $this->assertTrue(true);
    }
}
