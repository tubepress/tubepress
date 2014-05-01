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
 * @covers tubepress_addons_wordpress_impl_ioc_WordPressIocContainerExtension<extended>
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
        $this->_expectSingletons();
        $this->_expectActions();
        $this->_expectFilters();
        $this->_expectWpSpecific();
        $this->_expectPluggables();
        $this->_expectListeners();
    }

    private function _expectSingletons()
    {
        $this->expectRegistration(

            'tubepress_addons_wordpress_impl_Callback',
            'tubepress_addons_wordpress_impl_Callback'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_));

        $this->expectRegistration(

            tubepress_api_translation_TranslatorInterface::_,
            'tubepress_addons_wordpress_impl_message_WordPressMessageService'
        )->andReturnDefinition();

        $this->expectRegistration(

            tubepress_api_options_PersistenceBackendInterface::_,
            'tubepress_addons_wordpress_impl_options_PersistenceBackend'
        )->andReturnDefinition();

        $this->expectRegistration(

            'tubepress_spi_options_ui_OptionsPageInterface',
            'tubepress_impl_options_ui_DefaultOptionsPage'

        )->withArgument(TUBEPRESS_ROOT . '/src/main/php/add-ons/wordpress/resources/templates/options_page.tpl.php')
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceInterface::_))
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER,
                array('tag' => 'tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface',
                    'method' => 'setOptionsPageParticipants'));
    }

    private function _expectPluggables()
    {
        $this->expectRegistration(

            'tubepress_addons_wordpress_impl_options_WordPressOptionProvider',
            'tubepress_addons_wordpress_impl_options_WordPressOptionProvider'
        )->withTag(tubepress_api_options_ProviderInterface::_);

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
            ->withArgument(false)
            ->withArgument(false)
            ->withTag('tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface');
    }

    private function _expectListeners()
    {
        $this->expectRegistration(

            'tubepress_addons_wordpress_impl_listeners_template_options_OptionsUiTemplateListener',
            'tubepress_addons_wordpress_impl_listeners_template_options_OptionsUiTemplateListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::OPTIONS_PAGE_TEMPLATE,
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

            tubepress_addons_wordpress_spi_WpFunctionsInterface::_,
            'tubepress_addons_wordpress_impl_WpFunctions'
        )->andReturnDefinition();

        $this->expectRegistration(

            'wordpress.optionsPage',
            'tubepress_addons_wordpress_impl_OptionsPage'
        );

        $this->expectRegistration(

            'wordpress.widget',
            'tubepress_addons_wordpress_impl_Widget'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_html_HtmlGeneratorInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_shortcode_ParserInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_));

        $this->expectRegistration(

            'wordpress.pluginActivator',
            'tubepress_addons_wordpress_impl_ActivationHook'
        );
    }

    private function _expectActions()
    {
        $this->expectRegistration(

            "wordpress.action.wp_head.WpHead",
            "tubepress_addons_wordpress_impl_actions_WpHead"

        )->withArgument(tubepress_api_html_HtmlGeneratorInterface::_)
         ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array(

                'event'    => "tubepress.wordpress.action.wp_head",
                'method'   => "action",
                'priority' => 10000
            ));

        $map = array(

            'admin_enqueue_scripts' => 'AdminEnqueueScripts',
            'admin_head'            => 'AdminHead',
            'admin_menu'            => 'AdminMenu',
            'init'                  => 'Init',
        );

        foreach ($map as $filter => $class) {

            $this->expectRegistration(

                "wordpress.action.$filter.$class",
                "tubepress_addons_wordpress_impl_actions_$class"

            )->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array(

                    'event'    => "tubepress.wordpress.action.$filter",
                    'method'   => "action",
                    'priority' => 10000
                ));
        }
    }

    private function _expectFilters()
    {
        $map = array(

            'plugin_row_meta'     => 'RowMeta',
            'plugin_action_links' => 'RowMeta',
        );

        $this->expectRegistration(

            "wordpress.filter.the_content.Content",
            "tubepress_addons_wordpress_impl_filters_Content"

        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_html_HtmlGeneratorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_shortcode_ParserInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
        ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array(

                'event'    => "tubepress.wordpress.filter.the_content",
                'method'   => 'filter',
                'priority' => 10000
            ));

        foreach ($map as $filter => $class) {

            $this->expectRegistration(

                "wordpress.filter.$filter.$class",
                "tubepress_addons_wordpress_impl_filters_$class"

            )->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array(

                'event'    => "tubepress.wordpress.filter.$filter",
                'method'   => 'filter',
                'priority' => 10000
            ));
        }

        $this->expectRegistration(

            'wordpress.action.admin_notices',
            'tubepress_addons_wordpress_impl_actions_AdminNotices'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_url_CurrentUrlServiceInterface::_))
         ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array(

                'event'    => "tubepress.wordpress.action.admin_notices",
                'method'   => "action",
                'priority' => 10000
            ));

        $this->expectRegistration(

            'wordpress.action.widgets_init',
            'tubepress_addons_wordpress_impl_actions_WidgetsInit'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array(

                'event'    => "tubepress.wordpress.action.widgets_init",
                'method'   => "action",
                'priority' => 10000
            ));
    }
}