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
 *
 */
abstract class tubepress_test_app_impl_options_ui_fields_templated_multi_AbstractMultiSelectFieldTest extends tubepress_test_app_impl_options_ui_fields_templated_AbstractTemplatedFieldTest
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
     * @return array
     */
    protected final function getExpectedTemplateVariables()
    {
        $dis = array(
            'id' => $this->getSut()->getId(),
            'selectText' => 'select ...',
        );
        $fromChild = $this->getAdditionalExpectedTemplateVariables();

        return array_merge($dis, $fromChild);
    }

    /**
     * @return string
     */
    protected function getExpectedTemplateName()
    {
        return 'options-ui/fields/multiselect';
    }

    protected abstract function getAdditionalExpectedTemplateVariables();

    protected abstract function setupExpectationsForFailedStorageWhenMixed($errorMessage);

    protected abstract function setupExpectationsForGoodStorageWhenMixed();

    protected abstract function setupExpectationsForFailedStorageWhenAllMissing($errorMessage);

    protected abstract function setupExpectationsForGoodStorageWhenAllMissing();
}
