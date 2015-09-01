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
 * @covers tubepress_template_ioc_TemplateExtension<extended>
 */
class tubepress_test_template_ioc_TemplateExtensionTest extends tubepress_api_test_ioc_AbstractContainerExtensionTest
{
    protected function buildSut()
    {
        return new tubepress_template_ioc_TemplateExtension();
    }

    protected function prepareForLoad()
    {
        $this->_expectOptions();
        $this->_expectOptionsUi();
        $this->_expectTemplateService();
    }

    private function _expectOptions()
    {
        $this->expectRegistration(
            'tubepress_api_options_Reference__template',
            'tubepress_api_options_Reference'
        )->withTag(tubepress_api_options_ReferenceInterface::_)
            ->withArgument(array(

                tubepress_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                    tubepress_api_options_Names::TEMPLATE_CACHE_AUTORELOAD           => false,
                    tubepress_api_options_Names::TEMPLATE_CACHE_DIR                  => null,
                    tubepress_api_options_Names::TEMPLATE_CACHE_ENABLED              => true,
                ),

                tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                    tubepress_api_options_Names::TEMPLATE_CACHE_AUTORELOAD => 'Monitor templates for changes',    //>(translatable)<
                    tubepress_api_options_Names::TEMPLATE_CACHE_DIR        => 'Template cache directory',         //>(translatable)<
                    tubepress_api_options_Names::TEMPLATE_CACHE_ENABLED    => 'Enable template cache',            //>(translatable)<

                ),

                tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(
                    tubepress_api_options_Names::TEMPLATE_CACHE_AUTORELOAD => 'Automatically recompile templates when they are changed. Turning on the monitor is very useful if you are developing custom templates, but doing so also incurs a performance penalty. If you are unsure, leave this disabled.',    //>(translatable)<
                    tubepress_api_options_Names::TEMPLATE_CACHE_DIR        => 'Leave blank to attempt to use your system\'s temp directory. Otherwise enter the absolute path of a writable directory where TubePress can store cached templates.',         //>(translatable)<
                    tubepress_api_options_Names::TEMPLATE_CACHE_ENABLED    => 'Compile and cache Twig templates to pure PHP for maximum performance. Most users should leave this enabled.',            //>(translatable)<
                ),
            ));
    }

    private function _expectOptionsUi()
    {
        $fieldReferences = array();
        $fieldMap = array(
            'boolean' => array(
                tubepress_api_options_Names::TEMPLATE_CACHE_AUTORELOAD,
                tubepress_api_options_Names::TEMPLATE_CACHE_ENABLED,
            ),
            'text' => array(
                tubepress_api_options_Names::TEMPLATE_CACHE_DIR,
            ),
        );
        foreach ($fieldMap as $type => $ids) {
            foreach ($ids as $id) {

                $serviceId = 'template_field_' . $id;

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

        $fieldMap = array(
            tubepress_api_options_ui_CategoryNames::CACHE => array(
                tubepress_api_options_Names::TEMPLATE_CACHE_ENABLED,
                tubepress_api_options_Names::TEMPLATE_CACHE_DIR,
                tubepress_api_options_Names::TEMPLATE_CACHE_AUTORELOAD,
            ),

        );

        $this->expectRegistration(
            'tubepress_api_options_ui_BaseFieldProvider__template',
            'tubepress_api_options_ui_BaseFieldProvider'
        )->withArgument('field-provider-template')
            ->withArgument('HTML Templates')
            ->withArgument(false)
            ->withArgument(true)
            ->withArgument(array())
            ->withArgument($fieldReferences)
            ->withArgument($fieldMap)
            ->withTag('tubepress_spi_options_ui_FieldProviderInterface');
    }

    private function _expectTemplateService()
    {
        $parallelServices = array(
            '',
            '.admin'
        );

        foreach ($parallelServices as $serviceSuffix) {

            /**
             * Theme template locators.
             */
            $this->expectRegistration(
                'tubepress_template_impl_ThemeTemplateLocator' . $serviceSuffix,
                'tubepress_template_impl_ThemeTemplateLocator'
            )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
                ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
                ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_contrib_RegistryInterface::_ . '.' . tubepress_api_theme_ThemeInterface::_ . $serviceSuffix))
                ->withArgument(new tubepress_api_ioc_Reference('tubepress_theme_impl_CurrentThemeService' . $serviceSuffix));

            /**
             * Twig loaders.
             */
            $this->expectRegistration(
                'tubepress_template_impl_twig_ThemeLoader' . $serviceSuffix,
                'tubepress_template_impl_twig_ThemeLoader'
            )->withArgument(new tubepress_api_ioc_Reference('tubepress_template_impl_ThemeTemplateLocator' . $serviceSuffix));

            $this->expectRegistration(
                'Twig_Loader_Filesystem' . $serviceSuffix,
                'tubepress_template_impl_twig_FsLoader'
            )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
                ->withArgument(array());

            $twigLoaderReferences = array(
                new tubepress_api_ioc_Reference('tubepress_template_impl_twig_ThemeLoader' . $serviceSuffix),
                new tubepress_api_ioc_Reference('Twig_Loader_Filesystem' . $serviceSuffix)
            );
            $this->expectRegistration(
                'Twig_LoaderInterface' . $serviceSuffix,
                'Twig_Loader_Chain'
            )->withArgument($twigLoaderReferences);

            /**
             * Twig environment builder.
             */
            $this->expectRegistration(
                'tubepress_template_impl_twig_EnvironmentBuilder' . $serviceSuffix,
                'tubepress_template_impl_twig_EnvironmentBuilder'
            )->withArgument(new tubepress_api_ioc_Reference('Twig_LoaderInterface' . $serviceSuffix))
                ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_boot_BootSettingsInterface::_))
                ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
                ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_));

            /**
             * Twig environment.
             */
            $this->expectRegistration(
                'Twig_Environment' . $serviceSuffix,
                'Twig_Environment'
            )->withFactoryService('tubepress_template_impl_twig_EnvironmentBuilder' . $serviceSuffix)
                ->withFactoryMethod('buildTwigEnvironment');

            /**
             * Twig engine
             */
            $this->expectRegistration(
                'tubepress_template_impl_twig_Engine' . $serviceSuffix,
                'tubepress_template_impl_twig_Engine'
            )->withArgument(new tubepress_api_ioc_Reference('Twig_Environment' . $serviceSuffix));
        }

        /**
         * Register PHP engine support
         */
        $this->expectRegistration(
            'tubepress_template_impl_php_Support',
            'tubepress_template_impl_php_Support'
        )->withArgument(new tubepress_api_ioc_Reference('tubepress_template_impl_ThemeTemplateLocator'));

        /**
         * Register the PHP templating engine
         */
        $this->expectRegistration(
            'tubepress_template_impl_php_PhpEngine',
            'tubepress_template_impl_php_PhpEngine'
        )->withArgument(new tubepress_api_ioc_Reference('tubepress_template_impl_php_Support'))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_template_impl_php_Support'));

        /**
         * Public templating engine
         */
        $engineReferences = array(
            new tubepress_api_ioc_Reference('tubepress_template_impl_php_PhpEngine'),
            new tubepress_api_ioc_Reference('tubepress_template_impl_twig_Engine')
        );
        $this->expectRegistration(
            'tubepress_template_impl_DelegatingEngine',
            'tubepress_template_impl_DelegatingEngine'
        )->withArgument($engineReferences)
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_));

        /**
         * Final templating services
         */
        $this->expectRegistration(
            tubepress_api_template_TemplatingInterface::_,
            'tubepress_template_impl_TemplatingService'
        )->withArgument(new tubepress_api_ioc_Reference('tubepress_template_impl_DelegatingEngine'))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_));
        $this->expectRegistration(
            tubepress_api_template_TemplatingInterface::_ . '.admin',
            'tubepress_template_impl_TemplatingService'
        )->withArgument(new tubepress_api_ioc_Reference('tubepress_template_impl_twig_Engine.admin'))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_));
    }

    protected function getExpectedExternalServicesMap()
    {
        $fieldBuilder = $this->mock(tubepress_api_options_ui_FieldBuilderInterface::_);

        $text = array(
            tubepress_api_options_Names::TEMPLATE_CACHE_DIR
        );
        $bool = array(
            tubepress_api_options_Names::TEMPLATE_CACHE_ENABLED,
            tubepress_api_options_Names::TEMPLATE_CACHE_AUTORELOAD
        );

        foreach ($text as $color) {

            $mockSpectrumField = $this->mock('tubepress_api_options_ui_FieldInterface');
            $fieldBuilder->shouldReceive('newInstance')->once()->with($color, 'text')->andReturn($mockSpectrumField);
        }
        foreach ($bool as $color) {

            $mockSpectrumField = $this->mock('tubepress_api_options_ui_FieldInterface');
            $fieldBuilder->shouldReceive('newInstance')->once()->with($color, 'boolean')->andReturn($mockSpectrumField);
        }

        $logger = $this->mock(tubepress_api_log_LoggerInterface::_);
        $logger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);

        $context = $this->mock(tubepress_api_options_ContextInterface::_);
        $context->shouldReceive('get')->twice()->with(tubepress_api_options_Names::TEMPLATE_CACHE_DIR)->andReturnNull();
        $context->shouldReceive('get')->twice()->with(tubepress_api_options_Names::TEMPLATE_CACHE_ENABLED)->andReturn(true);
        $context->shouldReceive('get')->twice()->with(tubepress_api_options_Names::TEMPLATE_CACHE_AUTORELOAD)->andReturn(true);

        $bootSettings = $this->mock(tubepress_api_boot_BootSettingsInterface::_);
        $bootSettings->shouldReceive('getPathToSystemCacheDirectory')->twice()->andReturn(sys_get_temp_dir());

        return array(

            tubepress_api_log_LoggerInterface::_             => $logger,
            tubepress_api_options_ContextInterface::_        => $context,
            tubepress_api_contrib_RegistryInterface::_ . '.' . tubepress_api_theme_ThemeInterface::_ => tubepress_api_contrib_RegistryInterface::_,
            tubepress_api_contrib_RegistryInterface::_ . '.' . tubepress_api_theme_ThemeInterface::_ . '.admin' => tubepress_api_contrib_RegistryInterface::_,
            'tubepress_theme_impl_CurrentThemeService'       => 'tubepress_theme_impl_CurrentThemeService',
            'tubepress_theme_impl_CurrentThemeService.admin' => 'tubepress_theme_impl_CurrentThemeService',
            tubepress_api_boot_BootSettingsInterface::_      => $bootSettings,
            tubepress_api_translation_TranslatorInterface::_ => tubepress_api_translation_TranslatorInterface::_,
            tubepress_api_event_EventDispatcherInterface::_  => tubepress_api_event_EventDispatcherInterface::_,
            tubepress_api_options_ui_FieldBuilderInterface::_ => $fieldBuilder,
        );
    }
}