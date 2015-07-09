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
class tubepress_app_template_ioc_TemplateExtension implements tubepress_platform_api_ioc_ContainerExtensionInterface
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
        $this->_registerOptions($containerBuilder);
        $this->_registerOptionsUiFieldProvider($containerBuilder);
        $this->_registerTemplatingService($containerBuilder);
    }

    private function _registerOptions(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_app_api_options_Reference__template',
            'tubepress_app_api_options_Reference'
        )->addTag(tubepress_app_api_options_ReferenceInterface::_)
         ->addArgument(array(

            tubepress_app_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                tubepress_app_api_options_Names::TEMPLATE_CACHE_AUTORELOAD           => false,
                tubepress_app_api_options_Names::TEMPLATE_CACHE_DIR                  => null,
                tubepress_app_api_options_Names::TEMPLATE_CACHE_ENABLED              => true,
            ),

            tubepress_app_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                tubepress_app_api_options_Names::TEMPLATE_CACHE_AUTORELOAD => 'Monitor templates for changes',    //>(translatable)<
                tubepress_app_api_options_Names::TEMPLATE_CACHE_DIR        => 'Template cache directory',         //>(translatable)<
                tubepress_app_api_options_Names::TEMPLATE_CACHE_ENABLED    => 'Enable template cache',            //>(translatable)<

            ),

            tubepress_app_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(
                tubepress_app_api_options_Names::TEMPLATE_CACHE_AUTORELOAD => 'Automatically recompile templates when they are changed. Turning on the monitor is very useful if you are developing custom templates, but doing so also incurs a performance penalty. If you are unsure, leave this disabled.',    //>(translatable)<
                tubepress_app_api_options_Names::TEMPLATE_CACHE_DIR        => 'Leave blank to attempt to use your system\'s temp directory. Otherwise enter the absolute path of a writeable directory where TubePress can store cached templates.',         //>(translatable)<
                tubepress_app_api_options_Names::TEMPLATE_CACHE_ENABLED    => 'Compile and cache Twig templates to pure PHP for maximum performance. Most users should leave this enabled.',            //>(translatable)<
            ),
        ));
    }

    private function _registerOptionsUiFieldProvider(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $fieldReferences = array();
        $fieldMap = array(
            'boolean' => array(
                tubepress_app_api_options_Names::TEMPLATE_CACHE_AUTORELOAD,
                tubepress_app_api_options_Names::TEMPLATE_CACHE_ENABLED,
            ),
            'text' => array(
                tubepress_app_api_options_Names::TEMPLATE_CACHE_DIR,
            ),
        );
        foreach ($fieldMap as $type => $ids) {
            foreach ($ids as $id) {

                $serviceId = 'template_field_' . $id;

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
            tubepress_app_api_options_ui_CategoryNames::CACHE => array(
                tubepress_app_api_options_Names::TEMPLATE_CACHE_ENABLED,
                tubepress_app_api_options_Names::TEMPLATE_CACHE_DIR,
                tubepress_app_api_options_Names::TEMPLATE_CACHE_AUTORELOAD,
            ),

        );

        $containerBuilder->register(
            'tubepress_app_template_impl_options_ui_FieldProvider',
            'tubepress_app_template_impl_options_ui_FieldProvider'
        )->addArgument($fieldReferences)
         ->addArgument($fieldMap)
         ->addTag('tubepress_app_api_options_ui_FieldProviderInterface');
    }

    private function _registerTemplatingService(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $parallelServices = array(
            ''       => 'public',
            '.admin' => 'admin'
        );

        foreach ($parallelServices as $serviceSuffix => $templatePath) {

            /**
             * Theme template locators.
             */
            $containerBuilder->register(
                'tubepress_app_template_impl_ThemeTemplateLocator' . $serviceSuffix,
                'tubepress_app_template_impl_ThemeTemplateLocator'
            )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
             ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
             ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_contrib_RegistryInterface::_ . '.' . tubepress_app_api_theme_ThemeInterface::_ . $serviceSuffix))
             ->addArgument(new tubepress_platform_api_ioc_Reference('tubepress_app_impl_theme_CurrentThemeService' . $serviceSuffix));

            /**
             * Twig loaders.
             */
            $containerBuilder->register(
                'tubepress_app_template_impl_twig_ThemeLoader' . $serviceSuffix,
                'tubepress_app_template_impl_twig_ThemeLoader'
            )->addArgument(new tubepress_platform_api_ioc_Reference('tubepress_app_template_impl_ThemeTemplateLocator' . $serviceSuffix));

            $containerBuilder->register(
                'Twig_Loader_Filesystem' . $serviceSuffix,
                'tubepress_app_template_impl_twig_FsLoader'
            )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
             ->addArgument(array(
                TUBEPRESS_ROOT . '/src/add-ons/core/templates/' . $templatePath,
            ));

            $twigLoaderReferences = array(
                new tubepress_platform_api_ioc_Reference('tubepress_app_template_impl_twig_ThemeLoader' . $serviceSuffix),
                new tubepress_platform_api_ioc_Reference('Twig_Loader_Filesystem' . $serviceSuffix)
            );
            $containerBuilder->register(
                'Twig_LoaderInterface' . $serviceSuffix,
                'Twig_Loader_Chain'
            )->addArgument($twigLoaderReferences);

            /**
             * Twig environment builder.
             */
            $containerBuilder->register(
                'tubepress_app_template_impl_twig_EnvironmentBuilder' . $serviceSuffix,
                'tubepress_app_template_impl_twig_EnvironmentBuilder'
            )->addArgument(new tubepress_platform_api_ioc_Reference('Twig_LoaderInterface' . $serviceSuffix))
             ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_boot_BootSettingsInterface::_))
             ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
             ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_translation_TranslatorInterface::_));

            /**
             * Twig environment.
             */
            $containerBuilder->register(
                'Twig_Environment' . $serviceSuffix,
                'Twig_Environment'
            )->setFactoryService('tubepress_app_template_impl_twig_EnvironmentBuilder' . $serviceSuffix)
             ->setFactoryMethod('buildTwigEnvironment');

            /**
             * Twig engine
             */
            $containerBuilder->register(
                'tubepress_app_template_impl_twig_Engine' . $serviceSuffix,
                'tubepress_app_template_impl_twig_Engine'
            )->addArgument(new tubepress_platform_api_ioc_Reference('Twig_Environment' . $serviceSuffix));
        }

        /**
         * Register PHP engine support
         */
        $containerBuilder->register(
            'tubepress_app_template_impl_php_Support',
            'tubepress_app_template_impl_php_Support'
        )->addArgument(new tubepress_platform_api_ioc_Reference('tubepress_app_template_impl_ThemeTemplateLocator'));

        /**
         * Register the PHP templating engine
         */
        $containerBuilder->register(
            'tubepress_app_template_impl_php_PhpEngine',
            'tubepress_app_template_impl_php_PhpEngine'
        )->addArgument(new tubepress_platform_api_ioc_Reference('tubepress_app_template_impl_php_Support'))
         ->addArgument(new tubepress_platform_api_ioc_Reference('tubepress_app_template_impl_php_Support'));

        /**
         * Public templating engine
         */
        $engineReferences = array(
            new tubepress_platform_api_ioc_Reference('tubepress_app_template_impl_php_PhpEngine'),
            new tubepress_platform_api_ioc_Reference('tubepress_app_template_impl_twig_Engine')
        );
        $containerBuilder->register(
            'tubepress_app_template_impl_DelegatingEngine',
            'tubepress_app_template_impl_DelegatingEngine'
        )->addArgument($engineReferences)
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_));

        /**
         * Final templating services
         */
        $containerBuilder->register(
            tubepress_lib_api_template_TemplatingInterface::_,
            'tubepress_app_template_impl_TemplatingService'
        )->addArgument(new tubepress_platform_api_ioc_Reference('tubepress_app_template_impl_DelegatingEngine'))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_));
        $containerBuilder->register(
            tubepress_lib_api_template_TemplatingInterface::_ . '.admin',
            'tubepress_app_template_impl_TemplatingService'
        )->addArgument(new tubepress_platform_api_ioc_Reference('tubepress_app_template_impl_twig_Engine.admin'))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_));
    }
}
