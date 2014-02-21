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
 * @covers tubepress_impl_options_ui_fields_DropdownField<extended>
 */
class tubepress_test_impl_options_ui_fields_DropdownFieldTest extends tubepress_test_impl_options_ui_fields_AbstractProvidedOptionBasedFieldTest
{
    protected function buildSut()
    {
        return new tubepress_impl_options_ui_fields_DropdownField('name');
    }

    /**
     * @return string
     */
    protected function getExpectedTemplatePath()
    {
        return TUBEPRESS_ROOT . '/src/main/resources/options-gui/field-templates/dropdown.tpl.php';
    }

    /**
     * @return void
     */
    protected function doAdditionalPrepForGetWidgetHtml(ehough_mockery_mockery_MockInterface $template)
    {
        $this->getMockOptionProvider()->shouldReceive('getDiscreteAcceptableValues')->once()->with($this->getOptionName())->andReturn(array(

            'foo' => 'bar', 'smack' => 'rock'
        ));

        $this->getMockMessageService()->shouldReceive('_')->once()->with('bar')->andReturn('abc');
        $this->getMockMessageService()->shouldReceive('_')->once()->with('rock')->andReturn('xyz');

        $template->shouldReceive('setVariable')->once()->with('choices',
            array('foo' => 'abc', 'smack' => 'xyz'));
    }

}
