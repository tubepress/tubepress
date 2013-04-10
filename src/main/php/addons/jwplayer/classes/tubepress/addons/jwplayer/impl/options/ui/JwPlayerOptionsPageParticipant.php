<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
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
