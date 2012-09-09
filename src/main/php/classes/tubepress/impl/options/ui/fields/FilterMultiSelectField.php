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

/**
 * Displays a multi-select drop-down input for video meta.
 */
class tubepress_impl_options_ui_fields_FilterMultiSelectField extends tubepress_impl_options_ui_fields_AbstractMultiSelectField
{
    const __ = 'tubepress_impl_options_ui_fields_FilterMultiSelectField';
    
    public function __construct(

        tubepress_spi_message_MessageService            $messageService,
        tubepress_spi_http_HttpRequestParameterService  $hrps,
        tubepress_spi_environment_EnvironmentDetector   $environmentDetector,
        ehough_contemplate_api_TemplateBuilder          $templateBuilder,
        tubepress_spi_options_StorageManager            $storageManager,
        tubepress_spi_options_OptionDescriptorReference $optionsDescriptorReference)
    {
        parent::__construct(

            $messageService,
            $hrps,
            $environmentDetector,
            $templateBuilder,
            $storageManager,
            array(

                $optionsDescriptorReference->findOneByName(tubepress_api_const_options_names_WordPress::SHOW_VIMEO_OPTIONS),
                $optionsDescriptorReference->findOneByName(tubepress_api_const_options_names_WordPress::SHOW_YOUTUBE_OPTIONS),

        ), 'filterdropdown');
    }

    /**
     * Get the untranslated title of this field.
     *
     * @return string The untranslated title of this field.
     */
    protected final function getRawTitle()
    {
        return 'Only show options applicable to...';    //>(translatable)<
    }

    /**
     * Get the untranslated description of this field.
     *
     * @return string The untranslated description of this field.
     */
    protected final function getRawDescription()
    {
        return '';
    }
}