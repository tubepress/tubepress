<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class tubepress_impl_options_ui_fields_ThemeFieldTest extends tubepress_impl_options_ui_fields_DropdownFieldTest
{
    protected function _buildSut(tubepress_spi_message_MessageService            $messageService,
                                 tubepress_spi_options_OptionDescriptorReference $optionDescriptorReference,
                                 tubepress_spi_options_StorageManager            $storageManager,
                                 tubepress_spi_options_OptionValidator           $optionValidator,
                                 tubepress_spi_http_HttpRequestParameterService  $hrps,
                                 tubepress_spi_environment_EnvironmentDetector   $environmentDetector,
                                 ehough_contemplate_api_TemplateBuilder          $templateBuilder,
                                 $name)
    {
        return new tubepress_impl_options_ui_fields_ThemeField(

            $messageService,
            $optionDescriptorReference,
            $storageManager,
            $optionValidator,
            $hrps,
            $environmentDetector,
            $templateBuilder,
            $name);
    }

    protected function _performAdditionGetDescriptionSetup()
    {
        $this->getMockEnvironmentDetector()->shouldReceive('getTubePressBaseInstallationPath')->once()->andReturn('<<basepath>>');
        $this->getMockEnvironmentDetector()->shouldReceive('getUserContentDirectory')->once()->andReturn('<<user content dir>>');

    }
}

