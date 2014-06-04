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
 *
 */
abstract class tubepress_test_core_options_ui_impl_fields_AbstractMultiSelectFieldTest extends tubepress_test_core_options_ui_impl_fields_AbstractTemplateBasedOptionsPageFieldTest
{
    public function testOnSubmitMixedWithStorageProblem()
    {
        $this->getMockHttpRequestParams()->shouldReceive('hasParam')->once()->with($this->getSut()->getId())->andReturn(true);

        $this->setupExpectationsForFailedStorageWhenMixed('some error message');

        $this->assertEquals('some error message', $this->getSut()->onSubmit());
    }

    public function testOnSubmitMixedNoStorageProblem()
    {
        $this->getMockHttpRequestParams()->shouldReceive('hasParam')->once()->with($this->getSut()->getId())->andReturn(true);

        $this->setupExpectationsForGoodStorageWhenMixed();

        $this->assertNull($this->getSut()->onSubmit());
    }

    public function testOnSubmitAllMissingWithStorageProblem()
    {
        $this->getMockHttpRequestParams()->shouldReceive('hasParam')->once()->with($this->getSut()->getId())->andReturn(false);

        $this->setupExpectationsForFailedStorageWhenAllMissing('some error');

        $this->assertEquals('some error', $this->getSut()->onSubmit());
    }

    public function testOnSubmitAllMissingNoStorageProblem()
    {
        $this->getMockHttpRequestParams()->shouldReceive('hasParam')->once()->with($this->getSut()->getId())->andReturn(false);

        $this->setupExpectationsForGoodStorageWhenAllMissing();

        $this->assertNull($this->getSut()->onSubmit());
    }

    /**
     * @return void
     */
    protected final function prepareForGetWidgetHtml(ehough_mockery_mockery_MockInterface $mockTemplate)
    {
        $mockTemplate->shouldReceive('setVariable')->once()->with('id', $this->getSut()->getId());
        $mockTemplate->shouldReceive('setVariable')->once()->with('selectText', 'select ...');

        $this->doPrepareForGetWidgetHtml($mockTemplate);
    }

    /**
     * @return string
     */
    protected function getExpectedTemplatePath()
    {
        return TUBEPRESS_ROOT . '/src/main/add-ons/options-ui/resources/field-templates/multiselect.tpl.php';
    }

    protected abstract function doPrepareForGetWidgetHtml(ehough_mockery_mockery_MockInterface $mockTemplate);

    protected abstract function setupExpectationsForFailedStorageWhenMixed($errorMessage);

    protected abstract function setupExpectationsForGoodStorageWhenMixed();

    protected abstract function setupExpectationsForFailedStorageWhenAllMissing($errorMessage);

    protected abstract function setupExpectationsForGoodStorageWhenAllMissing();
}
