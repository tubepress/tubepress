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

/**
 * @covers tubepress_addons_jwplayer_impl_ioc_JwPlayerIocContainerExtension<extended>
 */
class tubepress_test_addons_jwplayer_impl_ioc_JwPlayerIocContainerExtensionTest extends tubepress_test_impl_ioc_AbstractIocContainerExtensionTest
{
    protected function buildSut()
    {
        return new tubepress_addons_jwplayer_impl_ioc_JwPlayerIocContainerExtension();
    }

    protected function prepareForLoad()
    {
        $this->_pluggables();

        $this->_listeners();
    }

    private function _pluggables()
    {
        $this->expectRegistration(

            'tubepress_addons_jwplayer_impl_embedded_JwPlayerPluggableEmbeddedPlayerService',
            'tubepress_addons_jwplayer_impl_embedded_JwPlayerPluggableEmbeddedPlayerService'

        )->withTag(tubepress_spi_embedded_PluggableEmbeddedPlayerService::_);

        $this->expectRegistration(

            'tubepress_addons_jwplayer_impl_options_JwPlayerOptionsProvider',
            'tubepress_addons_jwplayer_impl_options_JwPlayerOptionsProvider'
        )->withTag(tubepress_spi_options_PluggableOptionDescriptorProvider::_);

        $fieldIndex = 0;
        $fieldMap = array(

            'tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_BACK'   => 'tubepress_impl_options_ui_fields_SpectrumColorField',
            'tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_FRONT'  => 'tubepress_impl_options_ui_fields_SpectrumColorField',
            'tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT'  => 'tubepress_impl_options_ui_fields_SpectrumColorField',
            'tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN' => 'tubepress_impl_options_ui_fields_SpectrumColorField',
        );

        foreach ($fieldMap as $name => $class) {

            $this->expectRegistration('jwplayer-field-' . $fieldIndex++, $class)->withArgument($name);
        }

        $fieldReferences = array();

        for ($x = 0 ; $x < $fieldIndex; $x++) {

            $fieldReferences[] = new tubepress_impl_ioc_Reference('jwplayer-field-' . $x);
        }

        $map = array(

            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_PLAYER => array(

                tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_BACK,
                tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_FRONT,
                tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT,
                tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN)
        );

        $this->expectRegistration(

            'jw-player-options-page-participant',
            'tubepress_impl_options_ui_BaseOptionsPageParticipant'
        )->withArgument('jwplayer-participant')
            ->withArgument('JW Player')
            ->withArgument(array())
            ->withArgument($fieldReferences)
            ->withArgument($map)
            ->withTag('tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface');
    }

    private function _listeners()
    {
        $this->expectRegistration(

            'tubepress_addons_jwplayer_impl_listeners_template_JwPlayerTemplateVars',
            'tubepress_addons_jwplayer_impl_listeners_template_JwPlayerTemplateVars'
        )->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER,
                array('event' => tubepress_api_const_event_EventNames::TEMPLATE_EMBEDDED, 'method' => 'onEmbeddedTemplate', 'priority' => 10000));

        $this->expectRegistration(

            'jw-color-sanitizer',
            'tubepress_impl_listeners_options_ColorSanitizingListener'
        )->withArgument(array(
                tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_BACK,
                tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_FRONT,
                tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT,
                tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN,
            ))
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER,
                array('event' => tubepress_api_const_event_EventNames::OPTIONS_NVP_PREVALIDATIONSET, 'method' => 'onPreValidationOptionSet', 'priority' => 9500));
    }

}