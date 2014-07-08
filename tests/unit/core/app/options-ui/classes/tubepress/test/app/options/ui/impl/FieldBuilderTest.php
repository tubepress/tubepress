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
 * @covers tubepress_app_options_ui_impl_FieldBuilder<extended>
 */
class tubepress_test_app_options_ui_impl_FieldBuilderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_options_ui_impl_FieldBuilder
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTranslator;

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
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplateFactory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionProvider;

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
    private $_mockThemeLibrary;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockThemeRegistry;

    public function onSetup()
    {
        $this->_mockTranslator = $this->mock(tubepress_lib_translation_api_TranslatorInterface::_);
        $this->_mockPersistence = $this->mock(tubepress_app_options_api_PersistenceInterface::_);
        $this->_mockRequestParams = $this->mock(tubepress_app_http_api_RequestParametersInterface::_);
        $this->_mockEventDispatcher = $this->mock(tubepress_lib_event_api_EventDispatcherInterface::_);
        $this->_mockTemplateFactory = $this->mock(tubepress_lib_template_api_TemplateFactoryInterface::_);
        $this->_mockOptionProvider = $this->mock(tubepress_app_options_api_ReferenceInterface::_);
        $this->_mockLangUtils = $this->mock(tubepress_platform_api_util_LangUtilsInterface::_);
        $this->_mockContext = $this->mock(tubepress_app_options_api_ContextInterface::_);
        $this->_mockAcceptableValues = $this->mock(tubepress_app_options_api_AcceptableValuesInterface::_);
        $this->_mockThemeLibrary = $this->mock(tubepress_app_theme_api_ThemeLibraryInterface::_);
        $this->_mockThemeRegistry = $this->mock(tubepress_platform_api_contrib_RegistryInterface::_);

        $this->_sut = new tubepress_app_options_ui_impl_FieldBuilder(
            $this->_mockTranslator,
            $this->_mockPersistence,
            $this->_mockRequestParams,
            $this->_mockEventDispatcher,
            $this->_mockTemplateFactory,
            $this->_mockOptionProvider,
            $this->_mockLangUtils,
            $this->_mockContext,
            $this->_mockAcceptableValues,
            $this->_mockThemeLibrary
        );

        $this->_sut->setThemeRegistry($this->_mockThemeRegistry);
    }

    public function testUnknownType()
    {
        $this->setExpectedException('InvalidArgumentException', 'Unknown field type');
        $this->_sut->newInstance('t', 'other');
    }

    public function testGallerySource()
    {
        $field = $this->_sut->newInstance('t', 'gallerySourceRadio', array('additionalField' => $this->mock('tubepress_app_options_ui_api_FieldInterface')));

        $this->assertInstanceOf('tubepress_app_options_ui_impl_fields_GallerySourceRadioField', $field);
    }

    public function testTheme()
    {
        $this->setupProvidedField('theme');
        $field = $this->_sut->newInstance('theme', 'theme');

        $this->assertInstanceOf('tubepress_app_options_ui_impl_fields_provided_ThemeField', $field);
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

            array('bool',     'tubepress_app_options_ui_impl_fields_provided_BooleanField'),
            array('boolean',  'tubepress_app_options_ui_impl_fields_provided_BooleanField'),
            array('dropdown', 'tubepress_app_options_ui_impl_fields_provided_DropdownField'),
            array('hidden',   'tubepress_app_options_ui_impl_fields_provided_HiddenField'),
        );
    }

    /**
     * @dataProvider getDataSpectrum
     */
    public function testSpectrum($options)
    {
        $this->setupProvidedField('t');
        $field = $this->_sut->newInstance('t', 'spectrum', $options);

        $this->assertInstanceOf('tubepress_app_options_ui_impl_fields_provided_SpectrumColorField', $field);
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

        $this->assertInstanceOf('tubepress_app_options_ui_impl_fields_provided_TextField', $field);
    }

    private function setupProvidedField($id)
    {
        $this->_mockOptionProvider->shouldReceive('optionExists')->once()->with($id)->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('getUntranslatedLabel')->once()->with($id)->andReturn($id . ' label');
        $this->_mockOptionProvider->shouldReceive('getUntranslatedDescription')->once()->with($id)->andReturn($id . ' desc');
    }
}