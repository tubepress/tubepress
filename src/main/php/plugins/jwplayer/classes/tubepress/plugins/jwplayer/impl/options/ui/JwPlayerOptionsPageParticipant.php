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
 * Hooks JW Player into TubePress.
 */
class tubepress_plugins_jwplayer_impl_options_ui_JwPlayerOptionsPageParticipant implements tubepress_spi_options_ui_PluggableOptionsPageParticipant
{
    /**
     * @return string The name that will be displayed in the options page filter (top right).
     */
    public final function getFriendlyName()
    {
        return 'JW Player';     //>(translatable)<
    }

    /**
     * @return string All lowercase alphanumerics.
     */
    public final function getName()
    {
        return 'jwplayer';
    }

    /**
     * @param string $tabName The name of the tab being built.
     *
     * @return array An array of fields that should be shown on the given tab. May be empty, never null.
     */
    public final function getFieldsForTab($tabName)
    {
        if ($tabName !== tubepress_impl_options_ui_tabs_EmbeddedTab::TAB_NAME) {

            return array();
        }

        $fieldBuilder = tubepress_impl_patterns_sl_ServiceLocator::getOptionsUiFieldBuilder();

        return array(

            $fieldBuilder->build(

                tubepress_plugins_jwplayer_api_const_options_names_Embedded::COLOR_BACK,
                tubepress_impl_options_ui_fields_ColorField::FIELD_CLASS_NAME
            ),

            $fieldBuilder->build(

                tubepress_plugins_jwplayer_api_const_options_names_Embedded::COLOR_FRONT,
                tubepress_impl_options_ui_fields_ColorField::FIELD_CLASS_NAME
            ),

            $fieldBuilder->build(

                tubepress_plugins_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT,
                tubepress_impl_options_ui_fields_ColorField::FIELD_CLASS_NAME
            ),

            $fieldBuilder->build(

                tubepress_plugins_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN,
                tubepress_impl_options_ui_fields_ColorField::FIELD_CLASS_NAME
            )
        );
    }
}
