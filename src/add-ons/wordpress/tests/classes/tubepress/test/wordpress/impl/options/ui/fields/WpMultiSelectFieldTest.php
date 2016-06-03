<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_wordpress_impl_options_ui_fields_WpMultiSelectField<extended>
 */
class tubepress_test_wordpress_impl_options_ui_fields_WpMultiSelectFieldTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_options_ui_fields_WpMultiSelectField
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockWpFunctionWrapper;

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
    private $_mockResourceRepo;

    public function onSetup()
    {
        $this->_mockWpFunctionWrapper = $this->mock(tubepress_wordpress_impl_wp_WpFunctions::_);
        $this->_mockRequestParams     = $this->mock(tubepress_api_http_RequestParametersInterface::_);
        $this->_mockPersistence       = $this->mock(tubepress_api_options_PersistenceInterface::_);
        $this->_mockTemplating        = $this->mock(tubepress_api_template_TemplatingInterface::_);
        $this->_mockResourceRepo      = $this->mock('tubepress_wordpress_impl_wp_ResourceRepository');

        $this->_sut = new tubepress_wordpress_impl_options_ui_fields_WpMultiSelectField(
            'id',
            'display name',
            'description',
            $this->_mockPersistence,
            $this->_mockRequestParams,
            $this->_mockTemplating,
            $this->_mockWpFunctionWrapper,
            $this->_mockResourceRepo
        );
    }

    public function testSubmit()
    {
        $this->_mockRequestParams->shouldReceive('hasParam')->once()->with('id')->andReturn(true);
        $this->_mockRequestParams->shouldReceive('getParamValue')->once()->with('id')->andReturn(array(
            'foo', 'bar',
        ));
        $this->_mockPersistence->shouldReceive('queueForSave')->once()->with('id', 'foo,bar')->andReturnNull();

        $this->_sut->onSubmit();
    }

    public function testSubmitAllMissing()
    {
        $this->_mockRequestParams->shouldReceive('hasParam')->once()->with('id')->andReturn(false);
        $this->_mockPersistence->shouldReceive('queueForSave')->once()->with('id', null)->andReturnNull();

        $this->_sut->onSubmit();
    }

    public function testGetHTML()
    {
        $tag1 = new stdClass();
        $tag2 = new stdClass();

        $tag1->slug = 'foo';
        $tag2->slug = 'goo';

        $tag1->name = 'Yo foo';
        $tag2->name = 'Yo goo';

        $this->_mockPersistence->shouldReceive('fetch')->once()->with('id')->andReturn('foo,bar');
        $this->_mockResourceRepo->shouldReceive('getAllTags')->once()->andReturn(array($tag2, $tag1));
        $this->_mockTemplating->shouldReceive('renderTemplate')->once()->with(
            'options-ui/fields/multiselect',
            array(
                'id'                      => 'id',
                'currentlySelectedValues' => array('foo', 'bar'),
                'ungroupedChoices'        => array(
                    'foo' => 'Yo foo',
                    'goo' => 'Yo goo',
                ),
                'groupedChoices' => array(),
                'selectText'     => 'select ...',
            )
        )->andReturn('hi');

        $actual = $this->_sut->getWidgetHTML();

        $this->assertEquals('hi', $actual);
    }

    public function testBasics()
    {
        $this->assertTrue($this->_sut->isProOnly());
        $this->assertEquals('id', $this->_sut->getId());
        $this->assertEquals('display name', $this->_sut->getUntranslatedDisplayName());
        $this->assertEquals('description', $this->_sut->getUntranslatedDescription());
    }
}
