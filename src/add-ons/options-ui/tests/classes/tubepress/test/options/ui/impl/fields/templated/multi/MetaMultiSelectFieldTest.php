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
 * @covers tubepress_options_ui_impl_fields_templated_multi_MetaMultiSelectField<extended>
 */
class tubepress_test_app_impl_options_ui_fields_templated_multi_MetaMultiSelectFieldTest extends tubepress_test_app_impl_options_ui_fields_templated_multi_AbstractMultiSelectFieldTest
{
    /**
     * @var Mockery\MockInterface
     */
    private $_mockOptionProvider;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockVideoProvider1;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockVideoProvider2;

    public function testIsPro()
    {
        $this->assertFalse($this->getSut()->isProOnly());
    }

    protected function onAfterTemplateBasedFieldSetup()
    {
        $this->_mockOptionProvider = $this->mock(tubepress_api_options_ReferenceInterface::_);
        $this->_mockVideoProvider1 = $this->mock(tubepress_spi_media_MediaProviderInterface::__);
        $this->_mockVideoProvider2 = $this->mock(tubepress_spi_media_MediaProviderInterface::__);
    }

    protected function getAdditionalExpectedTemplateVariables()
    {
        $this->_setupForMetaOptionNames();

        foreach (array('foo', 'bar', 'buzz') as $metaOptionName) {

            $this->getMockPersistence()->shouldReceive('fetch')->once()->with($metaOptionName)->andReturn($metaOptionName !== 'bar');

            $this->_mockOptionProvider->shouldReceive('getUntranslatedLabel')->once()->with($metaOptionName)->andReturn(strtoupper($metaOptionName));
        }

        $this->_mockVideoProvider1->shouldReceive('getDisplayName')->atLeast(1)->andReturn('Provider 1');
        $this->_mockVideoProvider2->shouldReceive('getDisplayName')->atLeast(1)->andReturn('Provider 2');

        return array(
            'currentlySelectedValues' => array('foo', 'buzz'),
            'ungroupedChoices'        => array(),
            'groupedChoices'          => array(
                'Provider 1 / Provider 2' => array('foo' => 'FOO'),
                'Provider 2'              => array('bar' => 'BAR'),
                'Provider 1'              => array('buzz' => 'BUZZ'),
            ),
        );
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
     * @return tubepress_options_ui_impl_fields_AbstractField
     */
    protected function getSut()
    {
        $sut = new tubepress_options_ui_impl_fields_templated_multi_MetaMultiSelectField(

            $this->getMockPersistence(),
            $this->getMockHttpRequestParams(),
            $this->getMockTemplating(),
            $this->_mockOptionProvider,
            array($this->_mockVideoProvider1, $this->_mockVideoProvider2)
        );

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
        $this->_mockVideoProvider1->shouldReceive('getMapOfMetaOptionNamesToAttributeDisplayNames')->atLeast(1)->andReturn(array(

            'foo'  => 'bla',
            'buzz' => 'boo',
        ));
        $this->_mockVideoProvider2->shouldReceive('getMapOfMetaOptionNamesToAttributeDisplayNames')->atLeast(1)->andReturn(array(

            'foo' => 'acb',
            'bar' => 'xyz',
        ));
    }
}
