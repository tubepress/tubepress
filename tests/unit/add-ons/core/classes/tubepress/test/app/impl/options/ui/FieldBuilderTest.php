<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_app_impl_options_ui_FieldBuilder<extended>
 */
class tubepress_test_app_impl_options_ui_FieldBuilderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_impl_options_ui_FieldBuilder
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockPersistence;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockRequestParams;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplating;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionsReference;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLangUtils;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockAcceptableValues;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockThemeRegistry;

    public function onSetup()
    {
        $this->_mockPersistence      = $this->mock(tubepress_app_api_options_PersistenceInterface::_);
        $this->_mockRequestParams    = $this->mock(tubepress_lib_api_http_RequestParametersInterface::_);
        $this->_mockTemplating       = $this->mock(tubepress_lib_api_template_TemplatingInterface::_);
        $this->_mockOptionsReference = $this->mock(tubepress_app_api_options_ReferenceInterface::_);
        $this->_mockLangUtils        = $this->mock(tubepress_platform_api_util_LangUtilsInterface::_);
        $this->_mockContext          = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $this->_mockAcceptableValues = $this->mock(tubepress_app_api_options_AcceptableValuesInterface::_);
        $this->_mockThemeRegistry    = $this->mock(tubepress_platform_api_contrib_RegistryInterface::_);

        $this->_sut = new tubepress_app_impl_options_ui_FieldBuilder(
            $this->_mockPersistence,
            $this->_mockRequestParams,
            $this->_mockTemplating,
            $this->_mockOptionsReference,
            $this->_mockLangUtils,
            $this->_mockContext,
            $this->_mockAcceptableValues,
            $this->_mockThemeRegistry
        );
    }

    public function testUnknownType()
    {
        $this->setExpectedException('InvalidArgumentException', 'Unknown field type');
        $this->_sut->newInstance('t', 'other');
    }

    public function testGallerySource()
    {
        $field = $this->_sut->newInstance('t', 'gallerySourceRadio', array('additionalField' => $this->mock('tubepress_app_api_options_ui_FieldInterface')));

        $this->assertInstanceOf('tubepress_app_impl_options_ui_fields_templated_GallerySourceRadioField', $field);
    }

    public function testTheme()
    {
        $this->setupProvidedField('theme');
        $field = $this->_sut->newInstance('theme', 'theme');

        $this->assertInstanceOf('tubepress_app_impl_options_ui_fields_templated_single_ThemeField', $field);
    }

    public function testFieldProviderFilter()
    {
        $this->_mockOptionsReference->shouldReceive('getUntranslatedLabel')->once()->with('disabledFieldProviderNames')->andReturn('disabledFieldProviderNames label');
        $this->_mockOptionsReference->shouldReceive('getUntranslatedDescription')->once()->with('disabledFieldProviderNames')->andReturn('disabledFieldProviderNames desc');

        $field = $this->_sut->newInstance('disabledFieldProviderNames', 'fieldProviderFilter');

        $this->assertInstanceOf('tubepress_app_impl_options_ui_fields_templated_multi_FieldProviderFilterField', $field);
    }

    public function testOrderBy()
    {
        $this->setupProvidedField('orderBy');
        $field = $this->_sut->newInstance('orderBy', 'orderBy');

        $this->assertInstanceOf('tubepress_app_impl_options_ui_fields_templated_single_DropdownField', $field);
    }

    public function testMetaMultiSelect()
    {
        $field = $this->_sut->newInstance('s', 'metaMultiSelect');

        $this->assertInstanceOf('tubepress_app_impl_options_ui_fields_templated_multi_MetaMultiSelectField', $field);
    }

    /**
     * @dataProvider getDataSimpleTypes
     */
    public function testSimpleTypes($type, $class)
    {
        $this->setupProvidedField('t');
        $field = $this->_sut->newInstance('t', $type);

        $this->assertInstanceOf($class, $field);
    }

    public function getDataSimpleTypes()
    {
        return array(

            array('dropdown', 'tubepress_app_impl_options_ui_fields_templated_single_DropdownField'),
            array('boolean', 'tubepress_app_impl_options_ui_fields_templated_single_SingleOptionField'),
            array('hidden', 'tubepress_app_impl_options_ui_fields_templated_single_SingleOptionField'),
        );
    }

    /**
     * @dataProvider getDataSpectrum
     */
    public function testSpectrum($options)
    {
        $this->setupProvidedField('t');
        $field = $this->_sut->newInstance('t', 'spectrum', $options);

        $this->assertInstanceOf('tubepress_app_impl_options_ui_fields_templated_single_SpectrumColorField', $field);
    }

    public function getDataSpectrum()
    {
        return array(

            array(array()),
            array(array('preferredFormat' => 'rgb')),
            array(array('preferredFormat' => 'something')),
            array(array('preferredFormat' => 'name')),
            array(array('preferredFormat' => 'hex')),
            array(array('preferredFormat' => 'hex', 'showAlpha' => true, 'showInput' => false, 'showSelectionPalette' => true)),
        );
    }

    public function testTextField()
    {
        $this->setupProvidedField('t');
        $field = $this->_sut->newInstance('t', 'text', array('size' => 50));

        $this->assertInstanceOf('tubepress_app_impl_options_ui_fields_templated_single_TextField', $field);
    }

    private function setupProvidedField($id)
    {
        $this->_mockOptionsReference->shouldReceive('optionExists')->once()->with($id)->andReturn(true);
        $this->_mockOptionsReference->shouldReceive('getUntranslatedLabel')->once()->with($id)->andReturn($id . ' label');
        $this->_mockOptionsReference->shouldReceive('getUntranslatedDescription')->once()->with($id)->andReturn($id . ' desc');
    }
}