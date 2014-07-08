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
 * @covers tubepress_app_options_ui_impl_ElementBuilder<extended>
 */
class tubepress_test_app_options_ui_impl_ElementBuilderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_options_ui_impl_ElementBuilder
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTranslator;

    public function onSetup()
    {
        $this->_mockTranslator = $this->mock(tubepress_lib_translation_api_TranslatorInterface::_);

        $this->_sut = new tubepress_app_options_ui_impl_ElementBuilder($this->_mockTranslator);
    }

    public function testBasics()
    {
        $result = $this->_sut->newInstance('id', 'hello');

        $this->assertInstanceOf('tubepress_app_options_ui_api_ElementInterface', $result);
    }
}