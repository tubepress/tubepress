<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 *
 */
class tubepress_html_ioc_HtmlExtension implements tubepress_platform_api_ioc_ContainerExtensionInterface
{
    /**
     * Called during construction of the TubePress service container. If an add-on intends to add
     * services to the container, it should do so here. The incoming `tubepress_platform_api_ioc_ContainerBuilderInterface`
     * will be completely empty, and after this method is executed will be merged into the primary service container.
     *
     * @param tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder An empty `tubepress_platform_api_ioc_ContainerBuilderInterface` instance.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function load(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_registerListeners($containerBuilder);
        $this->_registerPathProvider($containerBuilder);
        $this->_registerServices($containerBuilder);
        $this->_registerOptions($containerBuilder);
        $this->_registerOptionsUi($containerBuilder);
    }

    private function _registerServices(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_html_impl_CssAndJsGenerationHelper',
            'tubepress_html_impl_CssAndJsGenerationHelper'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_contrib_RegistryInterface::_ . '.' . tubepress_app_api_theme_ThemeInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_template_TemplatingInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference('tubepress_theme_impl_CurrentThemeService'))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_environment_EnvironmentInterface::_))
         ->addArgument(tubepress_app_api_event_Events::HTML_STYLESHEETS)
         ->addArgument(tubepress_app_api_event_Events::HTML_SCRIPTS)
         ->addArgument('cssjs/styles')
         ->addArgument('cssjs/scripts');

        $containerBuilder->register(
            tubepress_app_api_html_HtmlGeneratorInterface::_,
            'tubepress_html_impl_HtmlGenerator'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_template_TemplatingInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference('tubepress_html_impl_CssAndJsGenerationHelper'))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_environment_EnvironmentInterface::_))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
             'event'    => tubepress_app_api_event_Events::HTML_SCRIPTS,
             'priority' => 100000,
             'method'   => 'onScripts'));
    }

    private function _registerListeners(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_html_impl_listeners_ExceptionLogger',
            'tubepress_html_impl_listeners_ExceptionLogger'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    =>   tubepress_app_api_event_Events::HTML_EXCEPTION_CAUGHT,
            'priority' => 100000,
            'method'   => 'onException'
        ));

        $containerBuilder->register(
            'tubepress_html_impl_listeners_CssJsPostListener',
            'tubepress_html_impl_listeners_CssJsPostListener'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_RequestParametersInterface::_))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::TEMPLATE_POST_RENDER . '.cssjs/styles',
            'priority' => 100000,
            'method'   => 'onPostStylesTemplateRender'))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::TEMPLATE_POST_RENDER . '.cssjs/scripts',
            'priority' => 100000,
            'method'   => 'onPostScriptsTemplateRender'));

        $containerBuilder->register(
            'tubepress_html_impl_listeners_BaseUrlSetter',
            'tubepress_html_impl_listeners_BaseUrlSetter'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_environment_EnvironmentInterface::_))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::HTML_GLOBAL_JS_CONFIG,
            'priority' => 100000,
            'method'   => 'onGlobalJsConfig'
        ));
    }

    private function _registerPathProvider(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_api_template_BasePathProvider__html',
            'tubepress_api_template_BasePathProvider'
        )->addArgument(array(
            TUBEPRESS_ROOT . '/src/add-ons/html/templates',
        ))->addTag('tubepress_lib_api_template_PathProviderInterface');
    }

    private function _registerOptions(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_app_api_options_Reference__html',
            'tubepress_app_api_options_Reference'
        )->addTag(tubepress_app_api_options_ReferenceInterface::_)
            ->addArgument(array(

                tubepress_app_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(

                    tubepress_app_api_options_Names::HTML_GALLERY_ID => null,
                    tubepress_app_api_options_Names::HTML_HTTPS      => false,
                    tubepress_app_api_options_Names::HTML_OUTPUT     => null,
                    tubepress_app_api_options_Names::HTTP_METHOD     => 'GET',
                ),

                tubepress_app_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(

                    tubepress_app_api_options_Names::HTML_HTTPS  => 'Enable HTTPS',       //>(translatable)<
                    tubepress_app_api_options_Names::HTTP_METHOD => 'HTTP method',        //>(translatable)<
                ),

                tubepress_app_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(

                    tubepress_app_api_options_Names::HTML_HTTPS                  => 'Serve thumbnails and embedded video player over a secure connection.',  //>(translatable)<
                    tubepress_app_api_options_Names::HTTP_METHOD                 => 'Defines the HTTP method used in most TubePress Ajax operations',  //>(translatable)<
                ),
            ))->addArgument(array(

                tubepress_app_api_options_Reference::PROPERTY_NO_PERSIST => array(
                    tubepress_app_api_options_Names::HTML_GALLERY_ID,
                    tubepress_app_api_options_Names::HTML_OUTPUT,
                ),

                tubepress_app_api_options_Reference::PROPERTY_PRO_ONLY => array(
                    tubepress_app_api_options_Names::HTML_HTTPS,
                ),
            ));

        $toValidate = array(
            tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_ONE_OR_MORE_WORDCHARS => array(
                tubepress_app_api_options_Names::HTML_GALLERY_ID,
            ),
        );

        foreach ($toValidate as $type => $optionNames) {
            foreach ($optionNames as $optionName) {
                $containerBuilder->register(
                    'regex_validator.' . $optionName,
                    'tubepress_app_api_listeners_options_RegexValidatingListener'
                )->addArgument($type)
                 ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ReferenceInterface::_))
                 ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_translation_TranslatorInterface::_))
                 ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                    'event'    => tubepress_app_api_event_Events::OPTION_SET . ".$optionName",
                    'priority' => 100000,
                    'method'   => 'onOption',
                ));
            }
        }

        $fixedValuesMap = array(
            tubepress_app_api_options_Names::HTTP_METHOD => array(
                'GET'  => 'GET',
                'POST' => 'POST'
            ),
        );
        foreach ($fixedValuesMap as $optionName => $valuesMap) {
            $containerBuilder->register(
                'fixed_values.' . $optionName,
                'tubepress_app_api_listeners_options_FixedValuesListener'
            )->addArgument($valuesMap)
             ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'priority' => 100000,
                'event'    => tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . ".$optionName",
                'method'   => 'onAcceptableValues'
            ));
        }
    }

    private function _registerOptionsUi(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $fieldReferences = array();
        $fieldMap = array(
            'boolean' => array(
                tubepress_app_api_options_Names::HTML_HTTPS,
            ),
            'dropdown' => array(
                tubepress_app_api_options_Names::HTTP_METHOD,
            ),
        );

        foreach ($fieldMap as $type => $ids) {
            foreach ($ids as $id) {

                $serviceId = 'html_field_' . $id;

                $containerBuilder->register(
                    $serviceId,
                    'tubepress_app_api_options_ui_FieldInterface'
                )->setFactoryService(tubepress_app_api_options_ui_FieldBuilderInterface::_)
                 ->setFactoryMethod('newInstance')
                 ->addArgument($id)
                 ->addArgument($type);

                $fieldReferences[] = new tubepress_platform_api_ioc_Reference($serviceId);
            }
        }

        $fieldMap = array(
            tubepress_app_api_options_ui_CategoryNames::ADVANCED => array(
                tubepress_app_api_options_Names::HTML_HTTPS,
                tubepress_app_api_options_Names::HTTP_METHOD,
            ),
        );

        $containerBuilder->register(
            'tubepress_api_options_ui_BaseFieldProvider__html',
            'tubepress_api_options_ui_BaseFieldProvider'
        )->addArgument('field-provider-html')
         ->addArgument('HTML')
         ->addArgument(false)
         ->addArgument(false)
         ->addArgument(array())
         ->addArgument($fieldReferences)
         ->addArgument($fieldMap)
         ->addTag('tubepress_app_api_options_ui_FieldProviderInterface');
    }
}