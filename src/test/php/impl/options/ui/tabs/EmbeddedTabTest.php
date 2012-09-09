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
class tubepress_impl_options_ui_tabs_EmbeddedTabTest extends tubepress_impl_options_ui_tabs_AbstractTabTest
{
	protected function _getFieldArray()
	{
	    return array(

    	    tubepress_api_const_options_names_Embedded::PLAYER_LOCATION  => tubepress_impl_options_ui_fields_DropdownField::_,
    	    tubepress_api_const_options_names_Embedded::PLAYER_IMPL      => tubepress_impl_options_ui_fields_DropdownField::_,
    	    tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT  => tubepress_impl_options_ui_fields_TextField::__,
    	    tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH   => tubepress_impl_options_ui_fields_TextField::__,
    	    tubepress_api_const_options_names_Embedded::LAZYPLAY         => tubepress_impl_options_ui_fields_BooleanField::__,
    	    tubepress_api_const_options_names_Embedded::PLAYER_COLOR     => tubepress_impl_options_ui_fields_ColorField::__,
    	    tubepress_api_const_options_names_Embedded::PLAYER_HIGHLIGHT => tubepress_impl_options_ui_fields_ColorField::__,
    	    tubepress_api_const_options_names_Embedded::SHOW_INFO        => tubepress_impl_options_ui_fields_BooleanField::__,
    	    tubepress_api_const_options_names_Embedded::FULLSCREEN       => tubepress_impl_options_ui_fields_BooleanField::__,
    	    tubepress_api_const_options_names_Embedded::HIGH_QUALITY     => tubepress_impl_options_ui_fields_BooleanField::__,
		    tubepress_api_const_options_names_Embedded::AUTONEXT         => tubepress_impl_options_ui_fields_BooleanField::__,
    	    tubepress_api_const_options_names_Embedded::AUTOPLAY         => tubepress_impl_options_ui_fields_BooleanField::__,
    	    tubepress_api_const_options_names_Embedded::LOOP             => tubepress_impl_options_ui_fields_BooleanField::__,
    	    tubepress_api_const_options_names_Embedded::SHOW_RELATED     => tubepress_impl_options_ui_fields_BooleanField::__,
	        tubepress_api_const_options_names_Embedded::AUTOHIDE         => tubepress_impl_options_ui_fields_BooleanField::__,
	        tubepress_api_const_options_names_Embedded::MODEST_BRANDING  => tubepress_impl_options_ui_fields_BooleanField::__,
	        tubepress_api_const_options_names_Embedded::ENABLE_JS_API    => tubepress_impl_options_ui_fields_BooleanField::__,

        );
	}

	protected function _getRawTitle()
	{
	    return 'Player';
	}

	protected function _buildSut(

        tubepress_spi_message_MessageService          $messageService,
        ehough_contemplate_api_TemplateBuilder        $templateBuilder,
        tubepress_spi_environment_EnvironmentDetector $environmentDetector,
        tubepress_spi_options_ui_FieldBuilder         $fieldBuilder
    )
	{
	    return new tubepress_impl_options_ui_tabs_EmbeddedTab(

            $messageService,
            $templateBuilder,
            $environmentDetector,
            $fieldBuilder
        );
	}
}