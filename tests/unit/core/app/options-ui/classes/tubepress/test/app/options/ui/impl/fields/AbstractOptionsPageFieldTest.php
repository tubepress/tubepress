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
 * @covers tubepress_app_options_ui_impl_fields_AbstractOptionsPageField
 */
abstract class tubepress_test_app_options_ui_impl_fields_AbstractOptionsPageFieldTest extends tubepress_test_app_options_ui_impl_AbstractOptionsPageItemTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockPersistence;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParams;

    public final function onOptionsPageItemSetup()
    {
        $this->_mockPersistence       = $this->mock(tubepress_app_options_api_PersistenceInterface::_);
        $this->_mockHttpRequestParams = $this->mock(tubepress_app_http_api_RequestParametersInterface::_);

        $this->onAfterOptionsPageFieldSetup();
    }

    /**
     * @return ehough_mockery_mockery_MockInterface
     */
    protected function getMockPersistence()
    {
        return $this->_mockPersistence;
    }

    protected function getMockHttpRequestParams()
    {
        return $this->_mockHttpRequestParams;
    }

    protected function onAfterOptionsPageFieldSetup()
    {
        //override point
    }
}
