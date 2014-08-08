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
 * @covers tubepress_app_impl_options_ui_Form<extended>
 */
class tubepress_test_app_impl_options_ui_FormTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_impl_options_ui_Form
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface[]
     */
    private $_mockFieldProviders;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironment;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockPersistence;

    /**
     * @var ehough_mockery_mockery_MockInterface[]
     */
    private $_mockFields;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplating;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockStringUtils;

    public function onSetup()
    {
        $this->_mockEnvironment = $this->mock(tubepress_app_api_environment_EnvironmentInterface::_);
        $this->_mockPersistence = $this->mock(tubepress_app_api_options_PersistenceInterface::_);
        $this->_mockTemplating  = $this->mock(tubepress_lib_api_template_TemplatingInterface::_);
        $this->_mockStringUtils = $this->mock(tubepress_platform_api_util_StringUtilsInterface::_);

        $mockFieldA        = $this->mock('tubepress_app_api_options_ui_FieldInterface');
        $mockFieldB        = $this->mock('tubepress_app_api_options_ui_FieldInterface');
        $this->_mockFields = array($mockFieldA, $mockFieldB);

        $mockFieldProvider  = $this->mock('tubepress_app_api_options_ui_FieldProviderInterface');
        $mockFieldProvider->shouldReceive('getFields')->once()->andReturn($this->_mockFields);
        $this->_mockFieldProviders = array($mockFieldProvider);

        $this->_sut = new tubepress_app_impl_options_ui_Form(

            $this->_mockTemplating,
            $this->_mockEnvironment,
            $this->_mockPersistence,
            $this->_mockStringUtils
        );

        $this->_sut->setFieldProviders($this->_mockFieldProviders);
    }

    public function testSubmitWithErrors()
    {
        $index = 0;

        foreach ($this->_mockFields as $mockField) {

            $mockField->shouldReceive('getId')->twice()->andReturn('field' . $index++);
            $mockField->shouldReceive('onSubmit')->once()->andReturn('yikes');
        }

        $this->_mockPersistence->shouldReceive('flushSaveQueue')->once()->andReturn(null);

        $result = $this->_sut->onSubmit();

        $this->assertEquals(array('field0' => 'yikes', 'field1' => 'yikes'), $result);
    }

    public function testSubmitNoErrors()
    {
        $index = 0;

        foreach ($this->_mockFields as $mockField) {

            $mockField->shouldReceive('getId')->once()->andReturn('field' . $index++);
            $mockField->shouldReceive('onSubmit')->once()->andReturn(null);
        }

        $this->_mockPersistence->shouldReceive('flushSaveQueue')->once()->andReturn(null);

        $result = $this->_sut->onSubmit();

        $this->assertEquals(array(), $result);
    }

    public function testGetHTML()
    {
        $mockCategory   = $this->mock('tubepress_app_api_options_ui_ElementInterface');
        $mockCategories = array($mockCategory);
        $mockMap        = array('categoryid' => array('field0', 'field1'));

        $mockCategory->shouldReceive('getId')->once()->andReturn('categoryid');

        $this->_mockFieldProviders[0]->shouldReceive('getCategories')->once()->andReturn($mockCategories);
        $this->_mockFieldProviders[0]->shouldReceive('getCategoryIdsToFieldIdsMap')->once()->andReturn($mockMap);
        $this->_mockFieldProviders[0]->shouldReceive('getId')->twice()->andReturn('providerId');


        $this->_mockTemplating->shouldReceive('renderTemplate')->once()->with('options-ui/form', ehough_mockery_Mockery::on(function ($actual) use ($mockCategories) {

            $ctx = array(
                'categories'                        => $mockCategories,
                'categoryIdToProviderIdToFieldsMap' => array('categoryid' => array('providerId' => array('field0', 'field1'))),
                'errors'                            => array('some error'),
                //'fields'                            => $this->_mockFields,
                'isPro'                             => true,
                'justSubmitted'                     => false,
                //'fieldProviders'                    => $this->_mockFieldProviders,
                'tubePressBaseUrl'                  => 'syz',
            );

            foreach ($ctx as $key => $value) {

                if ($ctx[$key] != $actual[$key]) {

                    return false;
                }
            }

            return true;

        }))->andReturn('x');

        $this->_mockEnvironment->shouldReceive('isPro')->once()->andReturn(true);
        $mockBaseUrl = $this->mock('tubepress_platform_api_url_UrlInterface');
        $mockBaseUrl->shouldReceive('toString')->once()->andReturn('syz');
        $this->_mockEnvironment->shouldReceive('getBaseUrl')->once()->andReturn($mockBaseUrl);

        $index = 0;

        foreach ($this->_mockFields as $mockField) {

            $mockField->shouldReceive('getId')->once()->andReturn('field' . $index++);
        }

        $this->assertEquals('x', $this->_sut->getHTML(array('some error')));
    }

    public function __callbackVerifyFields($fields)
    {
        return is_array($fields);
    }
}