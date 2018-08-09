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
 * @covers tubepress_options_ui_impl_fields_AbstractOptionsPageField
 */
abstract class tubepress_test_options_ui_impl_fields_AbstractFieldTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var Mockery\MockInterface
     */
    private $_mockPersistence;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockHttpRequestParams;

    public final function onSetup()
    {
        $this->_mockPersistence       = $this->mock(tubepress_api_options_PersistenceInterface::_);
        $this->_mockHttpRequestParams = $this->mock(tubepress_api_http_RequestParametersInterface::_);

        $this->onAfterAbstractFieldSetup();
    }

    /**
     * @return Mockery\MockInterface
     */
    protected function getMockPersistence()
    {
        return $this->_mockPersistence;
    }

    protected function getMockHttpRequestParams()
    {
        return $this->_mockHttpRequestParams;
    }

    protected function onAfterAbstractFieldSetup()
    {
        //override point
    }
}
