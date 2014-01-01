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
class tubepress_test_addons_wordpress_impl_ioc_WordPressIocContainerExtensionTest extends tubepress_test_impl_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_api_ioc_ContainerExtensionInterface
     */
    protected function buildSut()
    {
        return new tubepress_addons_wordpress_impl_ioc_WordPressIocContainerExtension();
    }

    protected function prepareForLoad()
    {
        $envDetector = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
        $envDetector->shouldReceive('isWordPress')->once()->andReturn(true);

        $this->_expectSingletons();
        $this->_expectPluggables();
        $this->_expectWpSpecific();
        $this->_expectListeners();
    }

    private function _expectSingletons()
    {
        $this->expectRegistration(

            tubepress_spi_message_MessageService::_,
            'tubepress_addons_wordpress_impl_message_WordPressMessageService'
        )->andReturnDefinition();

        $this->expectRegistration(

            tubepress_spi_options_StorageManager::_,
            'tubepress_addons_wordpress_impl_options_WordPressStorageManager'
        )->andReturnDefinition();

        $this->expectRegistration(

            'tubepress_spi_options_ui_OptionsPageInterface',
            'tubepress_impl_options_ui_DefaultOptionsPage'

        )->withArgument(TUBEPRESS_ROOT . '/src/main/php/add-ons/wordpress/resources/templates/options_page.tpl.php')
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER,
                array('tag' => 'tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface',
                    'method' => 'setOptionsPageParticipants'));
    }

    private function _expectPluggables()
    {
        $this->expectRegistration(

            'tubepress_addons_wordpress_impl_options_WordPressOptionsProvider',
            'tubepress_addons_wordpress_impl_options_WordPressOptionsProvider'
        )->withTag(tubepress_spi_options_PluggableOptionDescriptorProvider::_);

        $this->_expectOptionsPageParticipant();
    }

    private function _expectOptionsPageParticipant()
    {
        $fieldIndex = 0;
        $this->expectRegistration('wordpress_options_field_' . $fieldIndex++, 'tubepress_addons_wordpress_impl_options_ui_fields_WpNonceField');
        $this->expectRegistration('wordpress_options_field_' . $fieldIndex++, 'tubepress_impl_options_ui_fields_TextField')
            ->withArgument(tubepress_api_const_options_names_Advanced::KEYWORD);

        $fieldReferences = array();

        for ($x = 0 ; $x < $fieldIndex; $x++) {

            $fieldReferences[] = new tubepress_impl_ioc_Reference('wordpress_options_field_' . $x);
        }

        $map = array(

            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_ADVANCED => array(

                tubepress_api_const_options_names_Advanced::KEYWORD
            )
        );

        $this->expectRegistration(

            'wordpress_options_page_participant',
            'tubepress_impl_options_ui_BaseOptionsPageParticipant'

        )->withArgument('wordpress_participant')
            ->withArgument('WordPress')
            ->withArgument(array())
            ->withArgument($fieldReferences)
            ->withArgument($map)
            ->withTag('tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface');
    }

    private function _expectListeners()
    {
        $this->expectRegistration(

            'tubepress_addons_wordpress_impl_listeners_boot_WordPressApiIntegrator',
            'tubepress_addons_wordpress_impl_listeners_boot_WordPressApiIntegrator'
        )->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER,
                array('event' => tubepress_api_const_event_EventNames::BOOT_COMPLETE, 'method' => 'onBoot', 'priority' => 10000));;

        $this->expectRegistration(

            'tubepress_addons_wordpress_impl_listeners_template_options_OptionsUiTemplateListener',
            'tubepress_addons_wordpress_impl_listeners_template_options_OptionsUiTemplateListener'
        )->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::OPTIONS_PAGE_TEMPLATE,
                'method' => 'onOptionsUiTemplate', 'priority' => 10000));

        $this->expectRegistration(

            'tubepress_addons_wordpress_impl_listeners_html_CssJsDequerer',
            'tubepress_addons_wordpress_impl_listeners_html_CssJsDequerer'
        )->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::CSS_JS_STYLESHEETS,
                'method' => 'onCss', 'priority' => 10000))
         ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::CSS_JS_SCRIPTS,
                'method' => 'onJs', 'priority' => 10000));
    }

    private function _expectWpSpecific()
    {

        $this->expectRegistration(

            tubepress_addons_wordpress_spi_ContentFilter::_,
            'tubepress_addons_wordpress_impl_DefaultContentFilter'
        )->andReturnDefinition();

        $this->expectRegistration(

            tubepress_addons_wordpress_spi_FrontEndCssAndJsInjector::_,
            'tubepress_addons_wordpress_impl_DefaultFrontEndCssAndJsInjector'
        )->andReturnDefinition();

        $this->expectRegistration(

            tubepress_addons_wordpress_spi_WidgetHandler::_,
            'tubepress_addons_wordpress_impl_DefaultWidgetHandler'
        )->andReturnDefinition();

        $this->expectRegistration(

            tubepress_addons_wordpress_spi_WpAdminHandler::_,
            'tubepress_addons_wordpress_impl_DefaultWpAdminHandler'
        )->andReturnDefinition();

        $this->expectRegistration(

            tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_,
            'tubepress_addons_wordpress_impl_DefaultWordPressFunctionWrapper'
        )->andReturnDefinition();


    }
}