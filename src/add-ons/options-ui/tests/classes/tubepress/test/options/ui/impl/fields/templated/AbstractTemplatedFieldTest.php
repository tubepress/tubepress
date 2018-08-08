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
 * @covers tubepress_options_ui_impl_fields_AbstractTemplateBasedOptionsPageField
 */
abstract class tubepress_test_options_ui_impl_fields_templated_AbstractTemplatedFieldTest extends tubepress_test_options_ui_impl_fields_AbstractFieldTest
{
    /**
     * @var Mockery\MockInterface
     */
    private $_mockTemplating;

    protected function onAfterAbstractFieldSetup()
    {
        $this->_mockTemplating = $this->mock(tubepress_api_template_TemplatingInterface::_);

        $this->onAfterTemplateBasedFieldSetup();
    }

    public function testGetWidgetHtmlWithTemplate()
    {
        $expectedVars = $this->getExpectedTemplateVariables();

        $this->_mockTemplating->shouldReceive('renderTemplate')->once()->with(
            $this->getExpectedTemplateName(), $expectedVars
        )->andReturn('foo');

        $html = $this->getSut()->getWidgetHTML();

        $this->assertEquals('foo', $html);
    }

    /**
     * @return string
     */
    protected abstract function getExpectedTemplateName();

    /**
     * @return array
     */
    protected abstract function getExpectedTemplateVariables();

    protected function onAfterTemplateBasedFieldSetup()
    {
        //override point
    }

    protected function getMockTemplating()
    {
        return $this->_mockTemplating;
    }

    /**
     * @return tubepress_options_ui_impl_fields_templated_AbstractTemplatedField
     */
    protected abstract function getSut();
}
