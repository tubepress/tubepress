<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_test_addons_core_impl_options_ui_fields_ThemeFieldTest extends tubepress_test_impl_options_ui_fields_DropdownFieldTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_environmentDetector;

    protected function buildSut()
    {
        return new tubepress_addons_core_impl_options_ui_fields_ThemeField();
    }

    /**
     * @return string
     */
    protected function getOptionName()
    {
        return tubepress_api_const_options_names_Thumbs::THEME;
    }

    protected function performAdditionalSetup()
    {
        $this->_environmentDetector = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
    }

    protected function prepareForGetDescription()
    {
        $this->_environmentDetector->shouldReceive('getUserContentDirectory')->once()->andReturn('xyz');
    }

    protected function getExpectedFieldId()
    {
        return 'theme';
    }

    protected function getExpectedUntranslatedFieldLabel()
    {
        return 'the label';
    }

    protected function getExpectedUntranslatedFieldDescription()
    {
        return 'the description';
    }
}
