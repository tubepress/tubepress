<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_addons_core_impl_listeners_html_JsConfigTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_listeners_html_JsConfig
     */
    private $_sut;

    /**
     * @var tubepress_api_video_VideoGalleryPage
     */
    private $_providerResult;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    public function onSetup()
    {
        $this->_sut                  = new tubepress_addons_core_impl_listeners_html_JsConfig();
        $this->_providerResult       = new tubepress_api_video_VideoGalleryPage();
        $this->_mockEventDispatcher  = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);
    }

    public function testAlterHtml()
    {
        $fakeArgs = array('yo' => 'mamma', 'is' => '"so fat"', 'x' => array('foo' => 500, 'html' => '<>\'"'));

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::CSS_JS_GLOBAL_JS_CONFIG, ehough_mockery_Mockery::on(function ($arg) use ($fakeArgs) {

            $good = $arg instanceof tubepress_api_event_EventInterface && $arg->getSubject() === array();

            $arg->setSubject($fakeArgs);

            return $good;
        }));

        $event = new tubepress_spi_event_EventBase('hello');

        $event->setArguments(array(

            'page' => 1,
            'providerName' => 'something',
            'videoGalleryPage' => $this->_providerResult
        ));

        $this->_sut->onInlineJs($event);

        $this->assertEquals($this->expectedAjax(), $event->getSubject());
    }

    public function expectedAjax()
    {
        return <<<EOT
<script type="text/javascript">var TubePressJsConfig = {"yo":"mamma","is":"\"so fat\"","x":{"foo":500,"html":"<>'\""}};</script>hello
EOT;
    }
}