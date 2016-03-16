<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @runTestsInSeparateProcess
 * @covers tubepress_wordpress_ioc_WordPressExtension<extended>
 */
class tubepress_test_wordpress_ioc_WordPressExtensionTest extends tubepress_api_test_ioc_AbstractContainerExtensionTest
{

    /**
     * @return tubepress_spi_ioc_ContainerExtensionInterface
     */
    protected function buildSut()
    {
        return new tubepress_wordpress_ioc_WordPressExtension();
    }

    protected function prepareForLoad()
    {
        define('ABSPATH', 'foo');

        $this->_registerListeners();
        $this->_registerOptions();
        $this->_registerOptionsUiCore();
        $this->_registerOptionsUiAutoPost();
        $this->_registerSingletons();
        $this->_registerTemplatePathProvider();
        $this->_registerWpServices();
        $this->_registerVendorServices();
        $this->_registerHttpOauth2Services();
    }

    private function _registerTemplatePathProvider()
    {
        $this->expectRegistration(
            'tubepress_api_template_BasePathProvider__wordpress',
            'tubepress_api_template_BasePathProvider'
        )->withArgument(array(TUBEPRESS_ROOT . '/src/add-ons/wordpress/resources/templates'))
            ->withTag('tubepress_spi_template_PathProviderInterface.admin');
    }

    private function _registerOptions()
    {
        $this->expectRegistration(
            'tubepress_api_options_Reference__wordpress',
            'tubepress_api_options_Reference'
        )->withArgument(array(
            tubepress_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                tubepress_wordpress_api_Constants::OPTION_WIDGET_TITLE              => 'TubePress',
                tubepress_wordpress_api_Constants::OPTION_WIDGET_SHORTCODE          => '[tubepress thumbHeight=\'105\' thumbWidth=\'135\']',
                tubepress_api_options_Names::SHORTCODE_KEYWORD                      => 'tubepress',
                tubepress_api_options_Names::TUBEPRESS_API_KEY                      => null,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_ENABLE           => false,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_AUTHOR           => null,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_DATE_SOURCE      => tubepress_wordpress_api_Constants::AUTOPOST_DATA_SOURCE_UPLOAD,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_TITLE_FORMAT     => '{{ title }}',
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_POST_STATUS      => 'publish',
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_TYPE             => 'post',
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_ALLOW_COMMENTS   => true,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_ALLOW_PING       => true,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_PASSWORD         => null,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_TAGS             => null,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_CATEGORIES       => null,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_PAGE_TEMPLATE    => null,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_META_MAP         => '{}',
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_CONTENT_TEMPLATE => '[tubepress video="{{ id }}" title="false" length="true" description="true" descriptionLimit="0"]'
            ),
            tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                tubepress_api_options_Names::SHORTCODE_KEYWORD                      => 'Shortcode keyword',        //>(translatable)<,
                tubepress_api_options_Names::TUBEPRESS_API_KEY                      => 'tubepress.com API Key',    //>(translatable)<,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_ENABLE           => 'Enable auto post',         //>(translatable)<,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_AUTHOR           => 'Author',                   //>(translatable)<,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_DATE_SOURCE      => 'Date source',              //>(translatable)<,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_TITLE_FORMAT     => 'Title template',           //>(translatable)<,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_POST_STATUS      => 'Status',                   //>(translatable)<,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_TYPE             => 'Type',                     //>(translatable)<,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_ALLOW_COMMENTS   => 'Allow comments',           //>(translatable)<,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_ALLOW_PING       => 'Allow pings',              //>(translatable)<,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_PASSWORD         => 'Password',                 //>(translatable)<,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_TAGS             => 'Tags',                     //>(translatable)<,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_CATEGORIES       => 'Categories',               //>(translatable)<,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_PAGE_TEMPLATE    => 'Page template',            //>(translatable)<,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_META_MAP         => 'Metadata template',        //>(translatable)<,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_CONTENT_TEMPLATE => 'Content template',         //>(translatable)<,
            ),

            tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(
                tubepress_api_options_Names::SHORTCODE_KEYWORD => 'The word you insert (in plaintext, between square brackets) into your posts/pages to display a gallery.',         //>(translatable)<,
                tubepress_api_options_Names::TUBEPRESS_API_KEY => sprintf('Enable automatic plugin updates by supplying your <a href="%s" target="_blank">TubePress API key</a>.',   //>(translatable)<,
                    'https://dashboard.tubepress.com/profile'
                ),
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_ENABLE          => sprintf('Create and link a WordPress post or page for every video displayed by TubePress. See the <a href="%s" target="_blank">documentation</a> for details.',           //>(translatable)<,
                    'http://support.tubepress.com/customer/portal/articles/2360408-auto-post'),
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_DATE_SOURCE     => 'TubePress will assign a publish date for each post. You may choose to use either the video upload date or the date that TubePress creates the post.',                     //>(translatable)<,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_TITLE_FORMAT    => sprintf('A <a href="%s" target="_blank">Twig</a> fragment used to create post titles. <a href="%s" target="_blank">Here</a> is a list of available variables.',      //>(translatable)<,
                    'http://twig.sensiolabs.org/', 'http://support.tubepress.com/customer/portal/articles/2360408-auto-post#variables'),
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_TYPE            => sprintf('Each new post should be of this <a href="%s" target="_blank">type</a>.',                                     //>(translatable)<,
                    'https://codex.wordpress.org/Post_Types'),
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_POST_STATUS     => sprintf('TubePress will only display videos when their corresponding <a href="%s" target="_blank">post status</a> is public. Selecting a non-public status here (e.g. <code>Pending</code>) will allow you to review posts before they are published.', //>(translatable)<,
                    'https://codex.wordpress.org/Post_Status'),
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_PASSWORD        => 'Set a password to restrict viewing of each new post. Leave this field blank to disable.',                            //>(translatable)<,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_TAGS            => 'Assign these WordPress tags to each new post.',                                                                      //>(translatable)<,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_CATEGORIES      => 'Assign these WordPress categories to each new post.',                                                                //>(translatable)<,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_PAGE_TEMPLATE   => sprintf('Each new page should use this <a href="%s" target="_blank">template</a> from your WordPress theme.',                         //>(translatable)<,
                    'https://codex.wordpress.org/Templates'),
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_META_MAP        => sprintf('For advanced users. A <a href="%s" target="_blank">Twig</a> fragment, which must compile to a simple JSON object with scalar keys and values, that defines post metadata to be inserted. <a href="%s" target="_blank">Here</a> is a list of available variables.',     //>(translatable)<,
                    'http://twig.sensiolabs.org/', 'http://support.tubepress.com/customer/portal/articles/2360408-auto-post#variables'),
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_CONTENT_TEMPLATE => sprintf('A <a href="%s" target="_blank">Twig</a> fragment used to create post contents. <a href="%s" target="_blank">Here</a> is a list of available variables.',  //>(translatable)<,
                    'http://twig.sensiolabs.org/', 'http://support.tubepress.com/customer/portal/articles/2360408-auto-post#variables'),
            ),
        ))
            ->withArgument(array(
                tubepress_api_options_Reference::PROPERTY_PRO_ONLY => array(
                    tubepress_api_options_Names::TUBEPRESS_API_KEY,
                    tubepress_wordpress_api_Constants::OPTION_AUTOPOST_ENABLE,
                    tubepress_wordpress_api_Constants::OPTION_AUTOPOST_AUTHOR,
                    tubepress_wordpress_api_Constants::OPTION_AUTOPOST_DATE_SOURCE,
                    tubepress_wordpress_api_Constants::OPTION_AUTOPOST_TITLE_FORMAT,
                    tubepress_wordpress_api_Constants::OPTION_AUTOPOST_POST_STATUS,
                    tubepress_wordpress_api_Constants::OPTION_AUTOPOST_TYPE,
                    tubepress_wordpress_api_Constants::OPTION_AUTOPOST_ALLOW_COMMENTS,
                    tubepress_wordpress_api_Constants::OPTION_AUTOPOST_ALLOW_PING,
                    tubepress_wordpress_api_Constants::OPTION_AUTOPOST_PASSWORD,
                    tubepress_wordpress_api_Constants::OPTION_AUTOPOST_TAGS,
                    tubepress_wordpress_api_Constants::OPTION_AUTOPOST_CATEGORIES,
                    tubepress_wordpress_api_Constants::OPTION_AUTOPOST_PAGE_TEMPLATE,
                    tubepress_wordpress_api_Constants::OPTION_AUTOPOST_META_MAP,
                    tubepress_wordpress_api_Constants::OPTION_AUTOPOST_CONTENT_TEMPLATE,
                )
            ))->withTag(tubepress_api_options_ReferenceInterface::_);

        $toValidate = array(
            tubepress_api_options_listeners_RegexValidatingListener::TYPE_ONE_OR_MORE_WORDCHARS => array(
                tubepress_api_options_Names::SHORTCODE_KEYWORD,
            ),
            tubepress_api_options_listeners_RegexValidatingListener::TYPE_ZERO_OR_MORE_WORDCHARS => array(
                tubepress_api_options_Names::TUBEPRESS_API_KEY,
            )
        );

        foreach ($toValidate as $type => $optionNames) {
            foreach ($optionNames as $optionName) {
                $this->expectRegistration('regex_validator.' . $optionName, 'tubepress_api_options_listeners_RegexValidatingListener')->withArgument($type)->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array('event' => tubepress_api_event_Events::OPTION_SET . ".$optionName", 'priority' => 100000, 'method' => 'onOption',));
            }
        }
    }

    private function _registerOptionsUiCore()
    {
        $fieldIndex = 0;
        $this->expectRegistration(
            'wordpress_field_' . $fieldIndex++,
            'tubepress_wordpress_impl_options_ui_fields_WpNonceField'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_));

        $this->expectRegistration(
            'wordpress_field_' . $fieldIndex++,
            'tubepress_api_options_ui_FieldInterface'
        )->withFactoryService(tubepress_api_options_ui_FieldBuilderInterface::_)
            ->withFactoryMethod('newInstance')
            ->withArgument(tubepress_api_options_Names::SHORTCODE_KEYWORD)
            ->withArgument('text');

        $this->expectRegistration(
            'wordpress_field_' . $fieldIndex++,
            'tubepress_api_options_ui_FieldInterface'
        )->withFactoryService(tubepress_api_options_ui_FieldBuilderInterface::_)
            ->withFactoryMethod('newInstance')
            ->withArgument(tubepress_api_options_Names::TUBEPRESS_API_KEY)
            ->withArgument('text');

        $fieldReferences = array();
        for ($x = 0; $x < $fieldIndex; $x++) {
            $fieldReferences[] = new tubepress_api_ioc_Reference('wordpress_field_' . $x);
        }

        $fieldMap = array(
            'wordpress' => array(
                tubepress_api_options_Names::SHORTCODE_KEYWORD,
            ),
            tubepress_api_options_ui_CategoryNames::ADVANCED => array(
                tubepress_api_options_Names::TUBEPRESS_API_KEY,
            ),
        );

        $categoryReferences = array();
        $categories = array(
            array('wordpress', 'WordPress'),
        );
        foreach ($categories as $categoryIdAndLabel) {

            $serviceId = 'wordpress_category_' . $categoryIdAndLabel[0];
            $this->expectRegistration(
                $serviceId,
                'tubepress_options_ui_impl_BaseElement'
            )->withArgument($categoryIdAndLabel[0])
                ->withArgument($categoryIdAndLabel[1]);

            $categoryReferences[] = new tubepress_api_ioc_Reference($serviceId);
        }

        $this->expectRegistration(
            'tubepress_api_options_ui_BaseFieldProvider__wordpress',
            'tubepress_api_options_ui_BaseFieldProvider'
        )->withArgument('field-provider-wordpress')
            ->withArgument('WordPress')                     //>(translatable)<
            ->withArgument(false)
            ->withArgument(false)
            ->withArgument($categoryReferences)
            ->withArgument($fieldReferences)
            ->withArgument($fieldMap)
            ->withTag('tubepress_spi_options_ui_FieldProviderInterface');
    }

    private function _registerOptionsUiAutoPost()
    {
        $fieldReferences = array();
        $fieldMap = array(
            'boolean' => array(
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_ENABLE,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_ALLOW_COMMENTS,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_ALLOW_PING,
            ),
            'dropdown' => array(
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_AUTHOR,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_POST_STATUS,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_TYPE,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_PAGE_TEMPLATE,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_DATE_SOURCE,
            ),
            'text' => array(
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_TITLE_FORMAT,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_PASSWORD,
            ),
            'textarea' => array(
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_META_MAP,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_CONTENT_TEMPLATE,
            )
        );

        foreach ($fieldMap as $type => $ids) {

            foreach ($ids as $id) {

                $serviceId = 'wp_autopost_field_' . $id;

                $this->expectRegistration(
                    $serviceId,
                    'tubepress_api_options_ui_FieldInterface'
                )->withFactoryService(tubepress_api_options_ui_FieldBuilderInterface::_)
                    ->withFactoryMethod('newInstance')
                    ->withArgument($id)
                    ->withArgument($type);

                $fieldReferences[] = new tubepress_api_ioc_Reference($serviceId);
            }
        }

        $this->expectRegistration(
            'tubepress_wordpress_impl_options_ui_fields_WpMultiSelectField__tags',
            'tubepress_wordpress_impl_options_ui_fields_WpMultiSelectField'
        )->withArgument(tubepress_wordpress_api_Constants::OPTION_AUTOPOST_TAGS)
            ->withArgument('Tags')                                 //>(translatable)<,
            ->withArgument('Assign these tags to each new post.')  //>(translatable)<,
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_ . '.admin'))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_AcceptableValuesInterface::_));
        $fieldReferences[] = new tubepress_api_ioc_Reference('tubepress_wordpress_impl_options_ui_fields_WpMultiSelectField__tags');

        $this->expectRegistration(
            'tubepress_wordpress_impl_options_ui_fields_WpMultiSelectField__categories',
            'tubepress_wordpress_impl_options_ui_fields_WpMultiSelectField'
        )->withArgument(tubepress_wordpress_api_Constants::OPTION_AUTOPOST_CATEGORIES)
            ->withArgument('Categories')                                 //>(translatable)<,
            ->withArgument('Assign these categories to each new post.')  //>(translatable)<,
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_ . '.admin'))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_AcceptableValuesInterface::_));
        $fieldReferences[] = new tubepress_api_ioc_Reference('tubepress_wordpress_impl_options_ui_fields_WpMultiSelectField__categories');

        $fieldMap = array(
            'wordpress' => array(
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_ENABLE,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_TYPE,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_POST_STATUS,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_AUTHOR,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_PAGE_TEMPLATE,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_TITLE_FORMAT,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_CONTENT_TEMPLATE,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_TAGS,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_CATEGORIES,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_DATE_SOURCE,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_ALLOW_COMMENTS,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_ALLOW_PING,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_PASSWORD,
                tubepress_wordpress_api_Constants::OPTION_AUTOPOST_META_MAP,
            ),
        );

        $this->expectRegistration(
            'tubepress_api_options_ui_BaseFieldProvider__wp_autopost',
            'tubepress_api_options_ui_BaseFieldProvider'
        )->withArgument('field-provider-wp_autopost')
            ->withArgument('Auto Post')                     //>(translatable)<
            ->withArgument(false)
            ->withArgument(true)
            ->withArgument(array())
            ->withArgument($fieldReferences)
            ->withArgument($fieldMap)
            ->withTag('tubepress_spi_options_ui_FieldProviderInterface');
    }

    private function _registerListeners()
    {
        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_html_WpHtmlListener',
            'tubepress_wordpress_impl_listeners_html_WpHtmlListener'
        )->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::TEMPLATE_PRE_RENDER . ".cssjs/scripts",
            'method'   => 'onScriptsStylesTemplatePreRender',
            'priority' => 100000,
        ))->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::TEMPLATE_PRE_RENDER . ".cssjs/styles",
            'method'   => 'onScriptsStylesTemplatePreRender',
            'priority' => 100000,
        ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_options_ui_OptionsPageListener',
            'tubepress_wordpress_impl_listeners_options_ui_OptionsPageListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ui_FormInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_wordpress_api_Constants::EVENT_OPTIONS_PAGE_INVOKED,
                'method'   => 'run',
                'priority' => 100000
            ))->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::TEMPLATE_SELECT . '.options-ui/form',
                'method'   => 'onTemplateSelect',
                'priority' => 100000,
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_options_AdminThemeListener',
            'tubepress_wordpress_impl_listeners_options_AdminThemeListener'
        )->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::OPTION_DEFAULT_VALUE . '.' . tubepress_api_options_Names::THEME_ADMIN,
            'method'   => 'onDefaultValue',
            'priority' => 100000,
        ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_wp_ActivationListener',
            'tubepress_wordpress_impl_listeners_wp_ActivationListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_boot_BootSettingsInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('filesystem'))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_wordpress_api_Constants::EVENT_PLUGIN_ACTIVATION,
                'method'   => 'onPluginActivation',
                'priority' => 100000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_wp_ShortcodeListener',
            'tubepress_wordpress_impl_listeners_wp_ShortcodeListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_html_HtmlGeneratorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event' => tubepress_wordpress_api_Constants::EVENT_SHORTCODE_FOUND,
                'method' => 'onShortcode',
                'priority' => 100000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_wpaction_AdminHeadAndScriptsListener',
            'tubepress_wordpress_impl_listeners_wpaction_AdminHeadAndScriptsListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ui_FormInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.admin_enqueue_scripts',
                'method'   => 'onAction_admin_enqueue_scripts',
                'priority' => 100000))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.admin_head',
                'method'   => 'onAction_admin_head',
                'priority' => 100000))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.admin_print_scripts-settings_page_tubepress',
                'method'   => 'onAction_admin_print_scripts',
                'priority' => 100000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_wpaction_AjaxListener',
            'tubepress_wordpress_impl_listeners_wpaction_AjaxListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_http_AjaxInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.wp_ajax_nopriv_tubepress',
                'method'   => 'onAction_ajax',
                'priority' => 100000
            ))->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.wp_ajax_tubepress',
                'method'   => 'onAction_ajax',
                'priority' => 100000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_wpaction_HeadListener',
            'tubepress_wordpress_impl_listeners_wpaction_HeadListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_html_HtmlGeneratorInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.wp_head',
                'method'   => 'onAction_wp_head',
                'priority' => 100000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_wpaction_MenuAndPageListener',
            'tubepress_wordpress_impl_listeners_wpaction_MenuAndPageListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_http_oauth2_impl_popup_AuthorizationInitiator'))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_http_oauth2_impl_popup_RedirectionCallback'))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.admin_menu',
                'method'   => 'onAction_admin_menu',
                'priority' => 100000))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.load-admin_page_tubepress_oauth2_start',
                'method'   => 'onAction_load_admin_page_tubepress_oauth2_start',
                'priority' => 100000))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.load-admin_page_tubepress_oauth2',
                'method'   => 'onAction_load_admin_page_tubepress_oauth2',
                'priority' => 100000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_wpaction_ThemeCssJsListener',
            'tubepress_wordpress_impl_listeners_wpaction_ThemeCssJsListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_html_HtmlGeneratorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.init',
                'method'   => 'onAction_init',
                'priority' => 100000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_wpaction_UpdateMessageListener',
            'tubepress_wordpress_impl_listeners_wpaction_UpdateMessageListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_ . '.admin'))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => sprintf('tubepress.wordpress.action.in_plugin_update_message-%s/tubepress.php', basename(TUBEPRESS_ROOT)),
                'method'   => 'onAction_in_plugin_update_message',
                'priority' => 100000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_wpaction_WidgetInitListener',
            'tubepress_wordpress_impl_listeners_wpaction_WidgetInitListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.action.widgets_init',
                'method'   => 'onAction_widgets_init',
                'priority' => 100000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_wpfilter_PhotonListener',
            'tubepress_wordpress_impl_listeners_wpfilter_PhotonListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
            ->withArgument(array(
                'ytimg.com',
                'vimeocdn.com',
                'dmcdn.net',
            ))->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.filter.jetpack_photon_skip_for_url',
                'method'   => 'onFilter_jetpack_photon_skip_for_url',
                'priority' => 100000,
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_wpfilter_PucListener',
            'tubepress_wordpress_impl_listeners_wpfilter_PucListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.filter.puc_request_info_query_args-tubepress',
                'method'   => 'onFilter_PucRequestInfoQueryArgsTubePress',
                'priority' => 100000))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.filter.puc_request_info_result-tubepress',
                'method'   => 'onFilter_PucRequestInfoResultTubePress',
                'priority' => 100000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_wpfilter_RowMetaListener',
            'tubepress_wordpress_impl_listeners_wpfilter_RowMetaListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => 'tubepress.wordpress.filter.plugin_row_meta',
                'method'   => 'onFilter_row_meta',
                'priority' => 100000
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_listeners_options_AcceptableValuesListener',
            'tubepress_wordpress_impl_listeners_options_AcceptableValuesListener'
        )->withArgument(new tubepress_api_ioc_Reference('tubepress_wordpress_impl_wp_ResourceRepository'))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_wordpress_api_Constants::OPTION_AUTOPOST_AUTHOR,
                'method'   => 'onWpUser',
                'priority' => 100000,
            ))->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_wordpress_api_Constants::OPTION_AUTOPOST_POST_STATUS,
                'method'   => 'onWpPostStatus',
                'priority' => 100000
            ))->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_wordpress_api_Constants::OPTION_AUTOPOST_TYPE,
                'method'   => 'onWpPostType',
                'priority' => 100000
            ))->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_wordpress_api_Constants::OPTION_AUTOPOST_PAGE_TEMPLATE,
                'method'   => 'onWpPostTemplate',
                'priority' => 100000
            ))->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_wordpress_api_Constants::OPTION_AUTOPOST_TAGS,
                'method'   => 'onWpPostTags',
                'priority' => 100000
            ))->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_wordpress_api_Constants::OPTION_AUTOPOST_CATEGORIES,
                'method'   => 'onWpPostCategories',
                'priority' => 100000
            ));

        $this->expectRegistration(
            'tubepress_api_options_listeners_FixedValuesListener.' . tubepress_wordpress_api_Constants::OPTION_AUTOPOST_DATE_SOURCE,
            'tubepress_api_options_listeners_FixedValuesListener'
        )->withArgument(array(
            tubepress_wordpress_api_Constants::AUTOPOST_DATA_SOURCE_UPLOAD    => 'video upload date',
            tubepress_wordpress_api_Constants::AUTOPOST_DATA_SOURCE_DISCOVERY => 'post creation date',
        ))->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_wordpress_api_Constants::OPTION_AUTOPOST_DATE_SOURCE,
            'method'   => 'onAcceptableValues',
            'priority' => 100000
        ));
    }

    private function _registerSingletons()
    {
        $this->expectRegistration(
            tubepress_api_translation_TranslatorInterface::_,
            'tubepress_wordpress_impl_translation_WpTranslator'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_));

        $this->expectRegistration(
            tubepress_spi_options_PersistenceBackendInterface::_,
            'tubepress_wordpress_impl_options_WpPersistence'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_));

        $this->expectRegistration(
            'tubepress_wordpress_impl_EntryPoint',
            'tubepress_wordpress_impl_EntryPoint'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
            ->withArgument(array(
                'admin_enqueue_scripts',
                'admin_head',
                'admin_menu',
                'admin_print_scripts-settings_page_tubepress',
                'init',
                'in_plugin_update_message-' . basename(TUBEPRESS_ROOT) . '/tubepress.php',
                'load-admin_page_tubepress_oauth2',
                'load-admin_page_tubepress_oauth2_start',
                'widgets_init',
                'wp_ajax_nopriv_tubepress',
                'wp_ajax_tubepress',
                'wp_head',
            ))->withArgument(array(
                array('plugin_row_meta',      10, 2),
                array('upgrader_pre_install', 10, 2),
                array('puc_request_info_query_args-tubepress'),
                array('puc_request_info_result-tubepress'),
                array('jetpack_photon_skip_for_url', 10, 3),
            ));

        $this->expectRegistration(
            'tubepress_wordpress_impl_wp_ResourceRepository',
            'tubepress_wordpress_impl_wp_ResourceRepository'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_));
    }

    private function _registerWpServices()
    {
        $this->expectRegistration(
            'tubepress_wordpress_impl_wp_Widget',
            'tubepress_wordpress_impl_wp_Widget'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_html_HtmlGeneratorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_shortcode_ParserInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_ . '.admin'))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_wordpress_api_Constants::EVENT_WIDGET_PUBLIC_HTML,
                'method'   => 'printWidgetHtml',
                'priority' => 100000))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_wordpress_api_Constants::EVENT_WIDGET_PRINT_CONTROLS,
                'method'   => 'printControlHtml',
                'priority' => 100000
            ));

        $this->expectRegistration(
            tubepress_wordpress_impl_wp_WpFunctions::_,
            'tubepress_wordpress_impl_wp_WpFunctions'
        );
    }

    private function _registerVendorServices()
    {
        $this->expectRegistration(
            'filesystem',
            'Symfony\Component\Filesystem\Filesystem'
        );
    }

    private function _registerHttpOauth2Services()
    {
        $this->expectRegistration(
            tubepress_api_http_oauth2_Oauth2EnvironmentInterface::_,
            'tubepress_wordpress_impl_http_oauth2_Oauth2Environment'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
            ->withTag(tubepress_api_http_oauth2_Oauth2EnvironmentInterface::_);
    }

    protected function getExpectedExternalServicesMap()
    {
        $mockFieldBuilder = $this->mock(tubepress_api_options_ui_FieldBuilderInterface::_);
        $mockField        = $this->mock('tubepress_api_options_ui_FieldInterface');
        $mockFieldBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockField);

        $mockLogger = $this->mock(tubepress_api_log_LoggerInterface::_);
        $mockLogger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);

        return array(

            tubepress_api_options_ui_FormInterface::_                 => tubepress_api_options_ui_FormInterface::_,
            tubepress_api_http_RequestParametersInterface::_          => tubepress_api_http_RequestParametersInterface::_,
            tubepress_api_url_UrlFactoryInterface::_                  => tubepress_api_url_UrlFactoryInterface::_,
            tubepress_api_util_StringUtilsInterface::_                => tubepress_api_util_StringUtilsInterface::_,
            tubepress_api_options_ContextInterface::_                 => tubepress_api_options_ContextInterface::_,
            tubepress_api_html_HtmlGeneratorInterface::_              => tubepress_api_html_HtmlGeneratorInterface::_,
            tubepress_api_shortcode_ParserInterface::_                => tubepress_api_shortcode_ParserInterface::_,
            tubepress_api_event_EventDispatcherInterface::_           => tubepress_api_event_EventDispatcherInterface::_,
            tubepress_api_template_TemplatingInterface::_             => tubepress_api_template_TemplatingInterface::_,
            tubepress_api_template_TemplatingInterface::_ . '.admin'  => tubepress_api_template_TemplatingInterface::_,
            tubepress_api_html_HtmlGeneratorInterface::_              => tubepress_api_html_HtmlGeneratorInterface::_,
            tubepress_api_environment_EnvironmentInterface::_         => tubepress_api_environment_EnvironmentInterface::_,
            tubepress_api_boot_BootSettingsInterface::_               => tubepress_api_boot_BootSettingsInterface::_,
            tubepress_api_options_ui_FieldBuilderInterface::_         => $mockFieldBuilder,
            tubepress_api_http_AjaxInterface::_                       => tubepress_api_http_AjaxInterface::_,
            tubepress_api_options_ReferenceInterface::_               => tubepress_api_options_ReferenceInterface::_,
            tubepress_api_options_PersistenceInterface::_             => tubepress_api_options_PersistenceInterface::_,
            'tubepress_http_oauth2_impl_popup_AuthorizationInitiator' => 'tubepress_http_oauth2_impl_popup_AuthorizationInitiator',
            'tubepress_http_oauth2_impl_popup_RedirectionCallback'    => 'tubepress_http_oauth2_impl_popup_RedirectionCallback',
            tubepress_api_log_LoggerInterface::_                      => $mockLogger,
            tubepress_api_options_AcceptableValuesInterface::_        => tubepress_api_options_AcceptableValuesInterface::_,
        );
    }
}