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
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMetaNameService;

    public function testIsPro()
    {
        $this->assertFalse($this->getSut()->isProOnly());
    }

    protected function doMoreSetup()
    {
        $this->_mockOptionProvider  = $this->createMockSingletonService(tubepress_api_options_ProviderInterface::_);
        $this->_mockMetaNameService = $this->createMockSingletonService(tubepress_addons_core_impl_options_MetaOptionNameService::_);
    }

    protected function doPrepareForGetWidgetHtml(ehough_mockery_mockery_MockInterface $mockTemplate)
    {
        $all      = array('a', 'b', 'c', 'x', 'y', 'z', 'hello');
        $selected = array('a', 'b', 'c',      'y', 'z', 'hello');

        $this->_mockMetaNameService->shouldReceive('getAllMetaOptionNames')->once()->andReturn($all);
        $this->_mockMetaNameService->shouldReceive('getCoreMetaOptionNames')->once()->andReturn(array('hello'));
        $this->_mockMetaNameService->shouldReceive('getMapOfFriendlyProviderNameToMetaOptionNames')->once()->andReturn(array(

            'Mock 1' => array('a', 'b', 'c'),
            'Mock 2' => array('x', 'y', 'z')
         ));

        foreach ($all as $metaOptionName) {

            $this->getMockStorageManager()->shouldReceive('fetch')->once()->with($metaOptionName)->andReturn($metaOptionName !== 'x');

            $this->getMockMessageService()->shouldReceive('_')->once()->with(strtoupper($metaOptionName))->andReturn('<<' . $metaOptionName . '>>');
        }

        $groupedChoicesArray = array(

            'Mock 1' => array('a' => '<<a>>', 'b' => '<<b>>', 'c' => '<<c>>'),
            'Mock 2' => array('x' => '<<x>>', 'y' => '<<y>>', 'z' => '<<z>>')
        );

        $mockTemplate->shouldReceive('setVariable')->once()->with('currentlySelectedValues', $selected);
        $mockTemplate->shouldReceive('setVariable')->once()->with('ungroupedChoices', array('hello' => '<<hello>>'));
        $mockTemplate->shouldReceive('setVariable')->once()->with('groupedChoices', $groupedChoicesArray);

        $this->_mockOptionProvider->shouldReceive('getLabel')->with('hello')->once()->andReturn('HELLO');


        foreach ($groupedChoicesArray as $g) {

            foreach ($g as $value => $label) {

                $this->_mockOptionProvider->shouldReceive('getLabel')->with($value)->once()->andReturn(strtoupper($value));
            }
        }
    }

    protected function setupExpectationsForFailedStorageWhenAllMissing($errorMessage)
    {
        $this->_mockMetaNameService->shouldReceive('getAllMetaOptionNames')->once()->andReturn(array('abc'));

        $this->getMockStorageManager()->shouldReceive('queueForSave')->once()->with('abc', false)->andReturn($errorMessage);
    }

    protected function setupExpectationsForGoodStorageWhenAllMissing()
    {
        $all = array('a', 'b', 'c', 'x', 'y', 'z');
        $this->_mockMetaNameService->shouldReceive('getAllMetaOptionNames')->once()->andReturn($all);


        foreach ($all as $odName) {

            $this->getMockStorageManager()->shouldReceive('queueForSave')->once()->with($odName, false)->andReturn(null);
        }
    }

    /**
     * @return tubepress_impl_options_ui_fields_AbstractOptionsPageField
     */
    protected function buildSut()
    {
        $sut = new tubepress_addons_core_impl_options_ui_fields_MetaMultiSelectField(
            $this->getMockStorageManager(),
            $this->getMockMessageService(),
            $this->getMockEventDispatcher(),
            $this->_mockOptionProvider
        );

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

    protected function setupExpectationsForFailedStorageWhenMixed($errorMessage)
    {
        $this->_mockMetaNameService->shouldReceive('getAllMetaOptionNames')->once()->andReturn(array('abc'));

        $this->getMockHttpRequestParameterService()->shouldReceive('getParamValue')->once()->with($this->getExpectedFieldId())->andReturn(array('a', 'b'));

        $this->getMockStorageManager()->shouldReceive('queueForSave')->once()->with('abc', false)->andReturn($errorMessage);
    }

    protected function setupExpectationsForGoodStorageWhenMixed()
    {
        $this->getMockHttpRequestParameterService()->shouldReceive('getParamValue')->once()->with($this->getExpectedFieldId())->andReturn(array('a', 'b'));

        $all = array('a', 'b', 'c', 'x', 'y', 'z');
        $this->_mockMetaNameService->shouldReceive('getAllMetaOptionNames')->once()->andReturn($all);

        foreach ($all as $odName) {

            $this->getMockStorageManager()->shouldReceive('queueForSave')->once()->with($odName, in_array($odName, array('a', 'b')))->andReturn(null);
        }
    }
}
