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
 * @covers tubepress_options_ui_impl_FieldBuilder<extended>
 */
class tubepress_test_app_impl_options_ui_FieldBuilderTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_options_ui_impl_FieldBuilder
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockPersistence;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockRequestParams;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockTemplating;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockOptionsReference;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockLangUtils;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockAcceptableValues;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockThemeRegistry;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockPersistenceHelper;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockRedirectionEndpointCalculator;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockTranslator;

    public function onSetup()
    {
        $this->_mockPersistence                   = $this->mock(tubepress_api_options_PersistenceInterface::_);
        $this->_mockRequestParams                 = $this->mock(tubepress_api_http_RequestParametersInterface::_);
        $this->_mockTemplating                    = $this->mock(tubepress_api_template_TemplatingInterface::_);
        $this->_mockOptionsReference              = $this->mock(tubepress_api_options_ReferenceInterface::_);
        $this->_mockLangUtils                     = $this->mock(tubepress_api_util_LangUtilsInterface::_);
        $this->_mockAcceptableValues              = $this->mock(tubepress_api_options_AcceptableValuesInterface::_);
        $this->_mockThemeRegistry                 = $this->mock(tubepress_api_contrib_RegistryInterface::_);
        $this->_mockPersistenceHelper             = $this->mock('tubepress_http_oauth2_impl_util_PersistenceHelper');
        $this->_mockRedirectionEndpointCalculator = $this->mock(tubepress_api_http_oauth2_Oauth2EnvironmentInterface::_);
        $this->_mockTranslator                    = $this->mock(tubepress_api_translation_TranslatorInterface::_);

        $this->_sut = new tubepress_options_ui_impl_FieldBuilder(
            $this->_mockPersistence,
            $this->_mockRequestParams,
            $this->_mockTemplating,
            $this->_mockOptionsReference,
            $this->_mockLangUtils,
            $this->_mockAcceptableValues,
            $this->_mockThemeRegistry,
            $this->_mockPersistenceHelper,
            $this->_mockRedirectionEndpointCalculator,
            $this->_mockTranslator
        );
    }

    public function testOauth2TokenMgmt()
    {
        $mockOauth2Provider = $this->mock(tubepress_spi_http_oauth2_Oauth2ProviderInterface::_);

        $mockOauth2Provider->shouldReceive('getName')->once()->andReturn('vimeoV3');

        $actual = $this->_sut->newInstance('foobar', 'oauth2TokenManagement', array(
            'provider' => $mockOauth2Provider
        ));

        $this->assertInstanceOf('tubepress_http_oauth2_impl_options_ui_TokenManagementField', $actual);
    }

    public function testUnknownType()
    {
        $this->setExpectedException('InvalidArgumentException', 'Unknown field type');
        $this->_sut->newInstance('t', 'other');
    }

    public function testGallerySource()
    {
        $field = $this->_sut->newInstance('t', 'gallerySourceRadio', array('additionalField' => $this->mock('tubepress_api_options_ui_FieldInterface')));

        $this->assertInstanceOf('tubepress_options_ui_impl_fields_templated_GallerySourceRadioField', $field);
    }

    public function testTheme()
    {
        $this->setupProvidedField('theme');
        $field = $this->_sut->newInstance('theme', 'theme');

        $this->assertInstanceOf('tubepress_options_ui_impl_fields_templated_single_ThemeField', $field);
    }

    public function testFieldProviderFilter()
    {
        $this->_mockOptionsReference->shouldReceive('getUntranslatedLabel')->once()->with('disabledFieldProviderNames')->andReturn('disabledFieldProviderNames label');
        $this->_mockOptionsReference->shouldReceive('getUntranslatedDescription')->once()->with('disabledFieldProviderNames')->andReturn('disabledFieldProviderNames desc');

        $field = $this->_sut->newInstance('disabledFieldProviderNames', 'fieldProviderFilter');

        $this->assertInstanceOf('tubepress_options_ui_impl_fields_templated_multi_FieldProviderFilterField', $field);
    }

    public function testOrderBy()
    {
        $this->setupProvidedField('orderBy');
        $field = $this->_sut->newInstance('orderBy', 'orderBy');

        $this->assertInstanceOf('tubepress_options_ui_impl_fields_templated_single_DropdownField', $field);
    }

    public function testMetaMultiSelect()
    {
        $field = $this->_sut->newInstance('s', 'metaMultiSelect');

        $this->assertInstanceOf('tubepress_options_ui_impl_fields_templated_multi_MetaMultiSelectField', $field);
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

            array('dropdown', 'tubepress_options_ui_impl_fields_templated_single_DropdownField'),
            array('boolean', 'tubepress_options_ui_impl_fields_templated_single_SingleOptionField'),
            array('hidden', 'tubepress_options_ui_impl_fields_templated_single_SingleOptionField'),
        );
    }

    /**
     * @dataProvider getDataSpectrum
     */
    public function testSpectrum($options)
    {
        $this->setupProvidedField('t');
        $field = $this->_sut->newInstance('t', 'spectrum', $options);

        $this->assertInstanceOf('tubepress_options_ui_impl_fields_templated_single_SpectrumColorField', $field);
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

        $this->assertInstanceOf('tubepress_options_ui_impl_fields_templated_single_TextField', $field);
    }

    private function setupProvidedField($id)
    {
        $this->_mockOptionsReference->shouldReceive('optionExists')->once()->with($id)->andReturn(true);
        $this->_mockOptionsReference->shouldReceive('getUntranslatedLabel')->once()->with($id)->andReturn($id . ' label');
        $this->_mockOptionsReference->shouldReceive('getUntranslatedDescription')->once()->with($id)->andReturn($id . ' desc');
    }
}