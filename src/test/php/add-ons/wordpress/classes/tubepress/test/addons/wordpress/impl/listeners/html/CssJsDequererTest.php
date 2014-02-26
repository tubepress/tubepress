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
 * @covers tubepress_addons_wordpress_impl_listeners_html_CssJsDequerer
 */
class tubepress_test_addons_wordpress_impl_listeners_html_CssJsDequererTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_wordpress_impl_listeners_html_CssJsDequerer
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_addons_wordpress_impl_listeners_html_CssJsDequerer();
    }

    public function testCss()
    {
        $styles = array(

            'foo'                     => 'bar',
            'tubepress'               => 'something',
            'tubepress-theme-style-5' => 'xyz'
        );

        $mockEvent = new tubepress_spi_event_EventBase($styles);

        $this->_sut->onCss($mockEvent);

        $this->assertEquals(array('foo' => 'bar'), $mockEvent->getSubject());
    }

    public function testJs()
    {
        $scripts = array(

            'foo'                      => 'bar',
            'tubepress'                => 'something',
            'tubepress-theme-script-3' => 'abc',
        );

        $mockEvent = new tubepress_spi_event_EventBase($scripts);

        $this->_sut->onJs($mockEvent);

        $this->assertEquals(array('foo' => 'bar'), $mockEvent->getSubject());
    }
}