<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_options_ui_impl_fields_templated_GallerySourceRadioField<extended>
 */
class tubepress_test_app_impl_options_ui_fields_templated_GallerySourceRadioFieldTest extends tubepress_test_options_ui_impl_fields_templated_AbstractTemplatedFieldTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockAdditionalField;

    public function testCloneForMultiSource()
    {
        $mockPersistence = $this->mock(tubepress_api_options_PersistenceInterface::_);

        $actual = $this->getSut()->cloneForMultiSource('foobar', $mockPersistence);

        $this->assertInstanceOf('tubepress_options_ui_impl_fields_templated_GallerySourceRadioField', $actual);

        $this->assertNotSame($this->getSut(), $actual);
    }

    public function testGetName()
    {
        $this->_mockAdditionalField->shouldReceive('getUntranslatedDisplayName')->once()->andReturn('hi');

        $result = $this->getSut()->getUntranslatedDisplayName();

        $this->assertEquals('hi', $result);
    }

    public function testGetDescription()
    {
        $this->_mockAdditionalField->shouldReceive('getUntranslatedDescription')->once()->andReturn('hi');

        $result = $this->getSut()->getUntranslatedDescription();

        $this->assertEquals('hi', $result);
    }

    public function testIsPro()
    {
        $this->assertFalse($this->getSut()->isProOnly());
    }

    public function testSubmit()
    {
        $this->_mockAdditionalField->shouldReceive('onSubmit')->once()->andReturn('hello');

        $result = $this->getSut()->onSubmit();

        $this->assertEquals('hello', $result);
    }

    /**
     * @return string
     */
    protected function getOptionsPageItemId()
    {
        return 'foo';
    }

    protected function onAfterTemplateBasedFieldSetup()
    {
        $this->_mockContext = $this->mock(tubepress_api_options_ContextInterface::_);
        $this->_mockAdditionalField = $this->mock('tubepress_api_options_ui_FieldInterface');
    }

    protected function getSut()
    {
        return new tubepress_options_ui_impl_fields_templated_GallerySourceRadioField(

            'foo',
            $this->getMockPersistence(),
            $this->getMockHttpRequestParams(),
            $this->getMockTemplating(),
            $this->_mockAdditionalField
        );
    }

    /**
     * @return string
     */
    protected function getExpectedTemplateName()
    {
        return 'options-ui/fields/gallery-source-radio';
    }

    /**
     * @return array
     */
    protected function getExpectedTemplateVariables()
    {
        $this->getMockPersistence()->shouldReceive('fetch')->once()->with(tubepress_api_options_Names::GALLERY_SOURCE)->andReturn('somethin');
        $this->_mockAdditionalField->shouldReceive('getWidgetHTML')->once()->andReturn('boo');

        return array(

            'id'                        => 'foo',
            'modeName'                  => 'foo',
            'currentMode'               => 'somethin',
            'additionalFieldWidgetHtml' => 'boo',
            'prefix'                    => '',
        );
    }
}