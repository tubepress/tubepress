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
 * @covers tubepress_addons_core_impl_options_ui_fields_MetaMultiSelectField<extended>
 */
class tubepress_test_addons_core_impl_options_ui_fields_MetaMultiSelectFieldTest extends tubepress_test_impl_options_ui_fields_AbstractMultiSelectFieldTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionProvider;

    /**
     * @var ehough_mockery_mockery_MockInterface[]
     */
    private $_mockVideoProviders;

    public function testIsPro()
    {
        $this->assertFalse($this->getSut()->isProOnly());
    }

    protected function doMoreSetup()
    {
        $this->_mockOptionProvider = $this->createMockSingletonService(tubepress_spi_options_OptionProvider::_);
    }

    protected function _getCoreOdNames()
    {
        return array(

            tubepress_api_const_options_names_Meta::AUTHOR,
            tubepress_api_const_options_names_Meta::CATEGORY,
            tubepress_api_const_options_names_Meta::UPLOADED,
            tubepress_api_const_options_names_Meta::DESCRIPTION,
            tubepress_api_const_options_names_Meta::ID,
            tubepress_api_const_options_names_Meta::KEYWORDS,
            tubepress_api_const_options_names_Meta::LENGTH,
            tubepress_api_const_options_names_Meta::TITLE,
            tubepress_api_const_options_names_Meta::URL,
            tubepress_api_const_options_names_Meta::VIEWS,
        );
    }

    private function _buildMockVideoProviders()
    {
        $this->_mockVideoProviders = array();

        $mockProvider1 = ehough_mockery_Mockery::mock('tubepress_spi_provider_PluggableVideoProviderService');
        $mockProvider1->shouldReceive('getAdditionalMetaNames')->once()->andReturn(array('a', 'b', 'c'));
        $mockProvider1->shouldReceive('getFriendlyName')->once()->andReturn('Mock 1');

        $mockProvider2 = ehough_mockery_Mockery::mock('tubepress_spi_provider_PluggableVideoProviderService');
        $mockProvider2->shouldReceive('getAdditionalMetaNames')->once()->andReturn(array('x', 'y', 'z'));
        $mockProvider2->shouldReceive('getFriendlyName')->once()->andReturn('Mock 2');

        $this->_mockVideoProviders[] = $mockProvider1;
        $this->_mockVideoProviders[] = $mockProvider2;
    }

    protected function doPrepareForGetWidgetHtml(ehough_mockery_mockery_MockInterface $mockTemplate)
    {
        $this->_setupOds();

        $all      = array_merge($this->_getCoreOdNames(), array('a', 'b', 'c', 'x', 'y', 'z'));
        $selected = array_merge($this->_getCoreOdNames(), array('a', 'b', 'c',      'y', 'z'));

        foreach ($all as $odName) {

            $this->getMockStorageManager()->shouldReceive('fetch')->once()->with($odName)->andReturn($odName !== 'x');

            $this->getMockMessageService()->shouldReceive('_')->once()->with(strtoupper($odName))->andReturn('<<' . $odName . '>>');
        }

        $coreOds = array(

            tubepress_api_const_options_names_Meta::AUTHOR => '<<author>>',
            tubepress_api_const_options_names_Meta::CATEGORY => '<<category>>',
            tubepress_api_const_options_names_Meta::UPLOADED => '<<uploaded>>',
            tubepress_api_const_options_names_Meta::DESCRIPTION => '<<description>>',
            tubepress_api_const_options_names_Meta::ID => '<<id>>',
            tubepress_api_const_options_names_Meta::KEYWORDS => '<<tags>>',
            tubepress_api_const_options_names_Meta::LENGTH => '<<length>>',
            tubepress_api_const_options_names_Meta::TITLE => '<<title>>',
            tubepress_api_const_options_names_Meta::URL => '<<url>>',
            tubepress_api_const_options_names_Meta::VIEWS => '<<views>>',
        );

        asort($coreOds);

        $groupedChoicesArray = array(

            'Mock 1' => array('a' => '<<a>>', 'b' => '<<b>>', 'c' => '<<c>>'),
            'Mock 2' => array('x' => '<<x>>', 'y' => '<<y>>', 'z' => '<<z>>')
        );

        $mockTemplate->shouldReceive('setVariable')->once()->with('currentlySelectedValues', $selected);
        $mockTemplate->shouldReceive('setVariable')->once()->with('ungroupedChoices', $coreOds);
        $mockTemplate->shouldReceive('setVariable')->once()->with('groupedChoices', $groupedChoicesArray);

        foreach (array_keys($coreOds) as $metaOptionName) {

            $this->_mockOptionProvider->shouldReceive('getLabel')->with($metaOptionName)->once()->andReturn(strtoupper($metaOptionName));
        }

        foreach ($groupedChoicesArray as $g) {

            foreach ($g as $value => $label) {

                $this->_mockOptionProvider->shouldReceive('getLabel')->with($value)->once()->andReturn(strtoupper($value));
            }
        }
    }

    protected function setupExpectationsForFailedStorageWhenAllMissing($errorMessage)
    {
        $this->_setupOds();

        $this->getMockStorageManager()->shouldReceive('queueForSave')->once()->with(tubepress_api_const_options_names_Meta::AUTHOR, false)->andReturn($errorMessage);
    }

    protected function setupExpectationsForGoodStorageWhenAllMissing()
    {
        $this->_setupOds();

        $all = array_merge($this->_getCoreOdNames(), array('a', 'b', 'c', 'x', 'y', 'z'));

        foreach ($all as $odName) {

            $this->getMockStorageManager()->shouldReceive('queueForSave')->once()->with($odName, false)->andReturn(null);
        }
    }

    /**
     * @return tubepress_impl_options_ui_fields_AbstractOptionsPageField
     */
    protected function buildSut()
    {
        $sut = new tubepress_addons_core_impl_options_ui_fields_MetaMultiSelectField();

        return $sut;
    }

    protected function getExpectedFieldId()
    {
        return 'meta-dropdown';
    }

    protected function getExpectedUntranslatedFieldLabel()
    {
        return 'Show each video\'s...';
    }

    protected function getExpectedUntranslatedFieldDescription()
    {
        return '';
    }

    private function _setupOds()
    {
        $this->_buildMockVideoProviders();

        $this->getSut()->setVideoProviders($this->_mockVideoProviders);
    }

    protected function setupExpectationsForFailedStorageWhenMixed($errorMessage)
    {
        $this->_setupOds();

        $this->getMockHttpRequestParameterService()->shouldReceive('getParamValue')->once()->with($this->getExpectedFieldId())->andReturn(array('a', 'b'));

        $this->getMockStorageManager()->shouldReceive('queueForSave')->once()->with('author', false)->andReturn($errorMessage);
    }

    protected function setupExpectationsForGoodStorageWhenMixed()
    {
        $this->_setupOds();

        $this->getMockHttpRequestParameterService()->shouldReceive('getParamValue')->once()->with($this->getExpectedFieldId())->andReturn(array('a', 'b'));

        $all = array_merge($this->_getCoreOdNames(), array('a', 'b', 'c', 'x', 'y', 'z'));

        foreach ($all as $odName) {

            $this->getMockStorageManager()->shouldReceive('queueForSave')->once()->with($odName, in_array($odName, array('a', 'b')))->andReturn(null);
        }
    }
}
