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
 * @covers tubepress_core_options_ui_impl_fields_MetaMultiSelectField<extended>
 */
class tubepress_test_core_options_ui_impl_fields_MetaMultiSelectFieldTest extends tubepress_test_core_options_ui_impl_fields_AbstractMultiSelectFieldTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionProvider;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockVideoProvider1;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockVideoProvider2;

    public function testIsPro()
    {
        $this->assertFalse($this->getSut()->isProOnly());
    }

    protected function onAfterTemplateBasedFieldSetup()
    {
        $this->_mockOptionProvider = $this->mock(tubepress_core_options_api_ReferenceInterface::_);
        $this->_mockVideoProvider1  = $this->mock(tubepress_core_media_provider_api_MediaProviderInterface::_);
        $this->_mockVideoProvider2  = $this->mock(tubepress_core_media_provider_api_MediaProviderInterface::_);
    }

    protected function doPrepareForGetWidgetHtml(ehough_mockery_mockery_MockInterface $mockTemplate)
    {
        $this->_setupForMetaOptionNames();

        foreach (array('foo', 'bar', 'buzz') as $metaOptionName) {

            $this->getMockPersistence()->shouldReceive('fetch')->once()->with($metaOptionName)->andReturn($metaOptionName !== 'bar');

            $this->getMockTranslator()->shouldReceive('_')->once()->with(strtoupper($metaOptionName))->andReturn('<<' . $metaOptionName . '>>');
            $this->_mockOptionProvider->shouldReceive('getUntranslatedLabel')->once()->with($metaOptionName)->andReturn(strtoupper($metaOptionName));
        }

        $this->_mockVideoProvider1->shouldReceive('getDisplayName')->atLeast(1)->andReturn('Provider 1');
        $this->_mockVideoProvider2->shouldReceive('getDisplayName')->atLeast(1)->andReturn('Provider 2');

        $mockTemplate->shouldReceive('setVariable')->once()->with('currentlySelectedValues', array('foo', 'buzz'));
        $mockTemplate->shouldReceive('setVariable')->once()->with('ungroupedChoices', array());
        $mockTemplate->shouldReceive('setVariable')->once()->with('groupedChoices', array(
            'Provider 1 / Provider 2' => array('foo' => '<<foo>>'),
            'Provider 2' => array('bar' => '<<bar>>'),
            'Provider 1' => array('buzz' => '<<buzz>>'),
        ));

    }

    protected function setupExpectationsForFailedStorageWhenAllMissing($errorMessage)
    {
        $this->_setupForMetaOptionNames();

        $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with('foo', false)->andReturn($errorMessage);
    }

    protected function setupExpectationsForGoodStorageWhenAllMissing()
    {
        $this->_setupForMetaOptionNames();

        foreach (array('foo', 'bar', 'buzz') as $odName) {

            $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with($odName, false)->andReturn(null);
        }
    }

    /**
     * @return tubepress_core_options_ui_impl_fields_AbstractOptionsPageField
     */
    protected function buildSut()
    {
        $sut = new tubepress_core_options_ui_impl_fields_MetaMultiSelectField(

            $this->getMockTranslator(),
            $this->getMockPersistence(),
            $this->getMockHttpRequestParams(),
            $this->getMockEventDispatcher(),
            $this->getMockTemplateFactory(),
            $this->_mockOptionProvider
        );

        $sut->setVideoProviders(array($this->_mockVideoProvider1, $this->_mockVideoProvider2));

        return $sut;
    }

    protected function getExpectedUntranslatedFieldLabel()
    {
        return 'Show each video\'s...';
    }

    protected function getExpectedUntranslatedFieldDescription()
    {
        return '';
    }

    protected function setupExpectationsForFailedStorageWhenMixed($errorMessage)
    {
        $this->_setupForMetaOptionNames();

        $this->getMockHttpRequestParams()->shouldReceive('getParamValue')->once()->with($this->getOptionsPageItemId())->andReturn(array('foo', 'buzz'));

        $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with('foo', true)->andReturn($errorMessage);
    }

    protected function setupExpectationsForGoodStorageWhenMixed()
    {
        $this->_setupForMetaOptionNames();

        $this->getMockHttpRequestParams()->shouldReceive('getParamValue')->once()->with($this->getOptionsPageItemId())->andReturn(array('foo', 'buzz'));

        foreach (array('foo', 'buzz', 'bar') as $odName) {

            $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with($odName, in_array($odName, array('foo', 'buzz')))->andReturn(null);
        }
    }

    /**
     * @return string
     */
    protected function getOptionsPageItemId()
    {
        return 'meta-dropdown';
    }

    private function _setupForMetaOptionNames()
    {
        $this->_mockVideoProvider1->shouldReceive('getMetaOptionNames')->atLeast(1)->andReturn(array(

            'foo',
            'buzz',
        ));
        $this->_mockVideoProvider2->shouldReceive('getMetaOptionNames')->atLeast(1)->andReturn(array(

            'foo',
            'bar',
        ));
    }
}
