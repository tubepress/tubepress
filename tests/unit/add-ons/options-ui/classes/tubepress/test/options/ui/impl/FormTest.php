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
 * @covers tubepress_options_ui_impl_Form<extended>
 */
class tubepress_test_app_impl_options_ui_FormTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_options_ui_impl_Form
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface[]
     */
    private $_mockFieldProviders;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockPersistence;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplating;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockStringUtils;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockCssAndJsHelper;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockRequestParams;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockSingleSourceField;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMultiSourceField;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFieldProvider;

    public function onSetup()
    {
        $this->_mockPersistence         = $this->mock('tubepress_options_impl_Persistence');
        $this->_mockTemplating          = $this->mock(tubepress_api_template_TemplatingInterface::_);
        $this->_mockStringUtils         = $this->mock(tubepress_api_util_StringUtilsInterface::_);
        $this->_mockCssAndJsHelper      = $this->mock('tubepress_html_impl_CssAndJsGenerationHelper');
        $this->_mockRequestParams       = $this->mock(tubepress_api_http_RequestParametersInterface::_);
        $this->_mockLogger              = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockSingleSourceField   = $this->mock('tubepress_api_options_ui_FieldInterface');
        $this->_mockMultiSourceField    = $this->mock('tubepress_api_options_ui_MultiSourceFieldInterface');
        $this->_mockFieldProvider       = $this->mock('tubepress_spi_options_ui_FieldProviderInterface');

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_mockFieldProvider->shouldReceive('getFields')->atLeast(1)->andReturn(array(
            $this->_mockSingleSourceField,
            $this->_mockMultiSourceField
        ));

        $this->_mockSingleSourceField->shouldReceive('getId')->atLeast(1)->andReturn('single-field-id');

        $this->_sut = new tubepress_options_ui_impl_Form(

            $this->_mockLogger,
            $this->_mockTemplating,
            $this->_mockPersistence,
            $this->_mockStringUtils,
            $this->_mockCssAndJsHelper,
            $this->_mockRequestParams
        );

        $this->_sut->setFieldProviders(array($this->_mockFieldProvider));
    }

    public function testSubmitNoErrors()
    {
        $this->_mockRequestParams->shouldReceive('getAllParams')->once()->andReturn(array(

            'tubepress-multisource-999-something' => 'ms-value-1',
            'tubepress-multisource-888-something' => 'ms-value-2',
            'foo' => 'bar',
        ));

        $this->_mockPersistence->shouldReceive('fetchAll')->once()->andReturn(array(
            'stored' => 'option',
        ));

        $mockNewPersistence1 = $this->mock(tubepress_api_options_PersistenceInterface::_);
        $mockNewPersistence2 = $this->mock(tubepress_api_options_PersistenceInterface::_);
        $mockMultiClone1     = $this->mock('tubepress_api_options_ui_MultiSourceFieldInterface');
        $mockMultiClone2     = $this->mock('tubepress_api_options_ui_MultiSourceFieldInterface');

        $this->_mockPersistence->shouldReceive('getCloneWithCustomBackend')->twice()
            ->with(ehough_mockery_Mockery::type('tubepress_options_ui_impl_MultiSourcePersistenceBackend'))
            ->andReturn($mockNewPersistence1, $mockNewPersistence2);

        $this->_mockMultiSourceField->shouldReceive('cloneForMultiSource')->once()->with(

            ehough_mockery_Mockery::on(array($this, '__verifyPrefix')),
            $mockNewPersistence1

        )->andReturn($mockMultiClone1);

        $this->_mockMultiSourceField->shouldReceive('cloneForMultiSource')->once()->with(

            ehough_mockery_Mockery::on(array($this, '__verifyPrefix')),
            $mockNewPersistence2

        )->andReturn($mockMultiClone2);

        $mockMultiClone1->shouldReceive('getId')->atLeast(1)->andReturn('multisource-field-id-1');
        $mockMultiClone2->shouldReceive('getId')->atLeast(1)->andReturn('multisource-field-id-2');

        $this->_mockSingleSourceField->shouldReceive('onSubmit')->once()->andReturnNull();
        $mockMultiClone1->shouldReceive('onSubmit')->once()->andReturnNull();
        $mockMultiClone2->shouldReceive('onSubmit')->once()->andReturnNull();

        $mockNewPersistence1->shouldReceive('flushSaveQueue')->once()->andReturnNull();
        $mockNewPersistence2->shouldReceive('flushSaveQueue')->once()->andReturnNull();

        $this->_mockPersistence->shouldReceive('queueForSave')->once()->with(tubepress_api_options_Names::SOURCES, '[[],[]]');
        $this->_mockPersistence->shouldReceive('flushSaveQueue')->once()->andReturnNull();

        $result = $this->_sut->onSubmit();

        $this->assertEquals(array(), $result);
    }

    public function testGetUrlsJS()
    {
        $this->_mockCssAndJsHelper->shouldReceive('getUrlsJS')->once()->andReturn('expected');

        $actualCss = $this->_sut->getUrlsJS();

        $this->assertEquals('expected', $actualCss);
    }

    public function testGetUrlsCSS()
    {
        $this->_mockCssAndJsHelper->shouldReceive('getUrlsCSS')->once()->andReturn('expected');

        $actualCss = $this->_sut->getUrlsCSS();

        $this->assertEquals('expected', $actualCss);
    }

    public function testGetJS()
    {
        $this->_mockCssAndJsHelper->shouldReceive('getJS')->once()->andReturn('expected');

        $actualCss = $this->_sut->getJS();

        $this->assertEquals('expected', $actualCss);
    }

    public function testGetCSS()
    {
        $this->_mockCssAndJsHelper->shouldReceive('getCSS')->once()->andReturn('expected');

        $actualCss = $this->_sut->getCSS();

        $this->assertEquals('expected', $actualCss);
    }

    public function testGetHtmlModernSource()
    {
        $sources = array(
            array(
                'foo' => 'bar',
            ),
            array(
                'fuzz' => 'bot',
            )
        );

        $this->_mockPersistence->shouldReceive('fetchAll')->once()->andReturn(array(
            tubepress_api_options_Names::SOURCES => json_encode($sources),
        ));

        $mockNewPersistence1 = $this->mock(tubepress_api_options_PersistenceInterface::_);
        $mockNewPersistence2 = $this->mock(tubepress_api_options_PersistenceInterface::_);
        $mockMultiClone1     = $this->mock('tubepress_api_options_ui_MultiSourceFieldInterface');
        $mockMultiClone2     = $this->mock('tubepress_api_options_ui_MultiSourceFieldInterface');

        $this->_mockPersistence->shouldReceive('getCloneWithCustomBackend')->twice()
            ->with(ehough_mockery_Mockery::type('tubepress_options_ui_impl_MultiSourcePersistenceBackend'))
            ->andReturn($mockNewPersistence1, $mockNewPersistence2);

        $this->_mockMultiSourceField->shouldReceive('cloneForMultiSource')->once()->with(

            ehough_mockery_Mockery::on(array($this, '__verifyPrefix')),
            $mockNewPersistence1

        )->andReturn($mockMultiClone1);

        $this->_mockMultiSourceField->shouldReceive('cloneForMultiSource')->once()->with(

            ehough_mockery_Mockery::on(array($this, '__verifyPrefix')),
            $mockNewPersistence2

        )->andReturn($mockMultiClone2);

        $mockMultiClone1->shouldReceive('getId')->atLeast(1)->andReturn('multisource-field-id-1');
        $mockMultiClone2->shouldReceive('getId')->atLeast(1)->andReturn('multisource-field-id-2');

        $this->_mockTemplating->shouldReceive('renderTemplate')->once()->with('options-ui/form', array(

            'errors'        => array('some-error' => 'message'),
            'fields'        => array(
                'single-field-id'        => $this->_mockSingleSourceField,
                'multisource-field-id-1' => $mockMultiClone1,
                'multisource-field-id-2' => $mockMultiClone2,
            ),
            'justSubmitted' => false,
        ))->andReturn('final result');

        $actual = $this->_sut->getHTML(array('some-error' => 'message'));

        $this->assertEquals('final result', $actual);
    }

    public function testGetHtmlLegacySource()
    {
        $this->_mockPersistence->shouldReceive('fetchAll')->once()->andReturn(array('foo' => 'bar'));

        $mockNewPersistence = $this->mock(tubepress_api_options_PersistenceInterface::_);
        $mockMultiClone     = $this->mock('tubepress_api_options_ui_MultiSourceFieldInterface');

        $this->_mockPersistence->shouldReceive('getCloneWithCustomBackend')->once()
            ->with(ehough_mockery_Mockery::type('tubepress_options_ui_impl_MultiSourcePersistenceBackend'))
            ->andReturn($mockNewPersistence);

        $this->_mockMultiSourceField->shouldReceive('cloneForMultiSource')->once()->with(

            ehough_mockery_Mockery::on(array($this, '__verifyPrefix')),
            $mockNewPersistence

        )->andReturn($mockMultiClone);

        $mockMultiClone->shouldReceive('getId')->atLeast(1)->andReturn('multisource-field-id');

        $this->_mockTemplating->shouldReceive('renderTemplate')->once()->with('options-ui/form', array(

            'errors'        => array('some-error' => 'message'),
            'fields'        => array(
                'single-field-id'      => $this->_mockSingleSourceField,
                'multisource-field-id' => $mockMultiClone
            ),
            'justSubmitted' => false,
        ))->andReturn('final result');

        $actual = $this->_sut->getHTML(array('some-error' => 'message'));

        $this->assertEquals('final result', $actual);
    }

    public function __verifyPrefix($candidate)
    {
        return is_string($candidate) && preg_match_all('/^tubepress-multisource-[0-9]+-$/', $candidate, $matches) === 1;
    }
}