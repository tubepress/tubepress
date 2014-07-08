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
 * @covers tubepress_app_feature_gallery_ioc_GalleryExtension
 */
class tubepress_test_app_feature_gallery_ioc_GalleryExtensionTest extends tubepress_test_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_platform_api_ioc_ContainerExtensionInterface
     */
    protected function buildSut()
    {
        return new tubepress_app_feature_gallery_ioc_GalleryExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            'tubepress_app_feature_gallery_impl_listeners_html_AsyncGalleryInitJsListener',
            'tubepress_app_feature_gallery_impl_listeners_html_AsyncGalleryInitJsListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ReferenceInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_feature_gallery_api_Constants::EVENT_HTML_THUMBNAIL_GALLERY,
                'method'   => 'onGalleryHtml',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_app_feature_gallery_impl_listeners_html_GenerationListener',
            'tubepress_app_feature_gallery_impl_listeners_html_GenerationListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_http_api_RequestParametersInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_media_provider_api_CollectorInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_template_api_TemplateFactoryInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_html_api_Constants::EVENT_PRIMARY_HTML,
                'method'   => 'onHtmlGeneration',
                'priority' => 4000
            ));

        $this->expectRegistration(
            'tubepress_app_feature_gallery_impl_listeners_html_NoRobotsListener',
            'tubepress_app_feature_gallery_impl_listeners_html_NoRobotsListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_http_api_RequestParametersInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_html_api_Constants::EVENT_STYLESHEETS_PRE,
                'method'   => 'onBeforeCssHtml',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_app_feature_gallery_impl_listeners_js_JsOptionsListener',
            'tubepress_app_feature_gallery_impl_listeners_js_JsOptionsListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_feature_gallery_api_Constants::EVENT_GALLERY_INIT_JS,
                'method'   => 'onGalleryInitJs',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_app_feature_gallery_impl_listeners_template_CoreGalleryTemplateListener',
            'tubepress_app_feature_gallery_impl_listeners_template_CoreGalleryTemplateListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_translation_api_TranslatorInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_feature_gallery_api_Constants::EVENT_TEMPLATE_THUMBNAIL_GALLERY,
                'method'   => 'onGalleryTemplate',
                'priority' => 10400
            ));

        $this->expectRegistration(
            'tubepress_app_feature_gallery_impl_listeners_template_PaginationTemplateListener',
            'tubepress_app_feature_gallery_impl_listeners_template_PaginationTemplateListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_url_api_UrlFactoryInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_translation_api_TranslatorInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_http_api_RequestParametersInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_template_api_TemplateFactoryInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_util_api_UrlUtilsInterface ::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_theme_api_ThemeLibraryInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_feature_gallery_api_Constants::EVENT_TEMPLATE_THUMBNAIL_GALLERY,
                'method'   => 'onGalleryTemplate',
                'priority' => 10200
            ));

        $this->expectParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_gallery', array(

            'defaultValues' => array(
                tubepress_app_feature_gallery_api_Constants::OPTION_AJAX_PAGINATION => false,
                tubepress_app_feature_gallery_api_Constants::OPTION_AUTONEXT        => true,
                tubepress_app_feature_gallery_api_Constants::OPTION_FLUID_THUMBS    => true,
                tubepress_app_feature_gallery_api_Constants::OPTION_HQ_THUMBS       => false,
                tubepress_app_feature_gallery_api_Constants::OPTION_PAGINATE_ABOVE  => true,
                tubepress_app_feature_gallery_api_Constants::OPTION_PAGINATE_BELOW  => true,
                tubepress_app_feature_gallery_api_Constants::OPTION_RANDOM_THUMBS   => true,
                tubepress_app_feature_gallery_api_Constants::OPTION_SEQUENCE        => null,
                tubepress_app_feature_gallery_api_Constants::OPTION_THUMB_HEIGHT    => 90,
                tubepress_app_feature_gallery_api_Constants::OPTION_THUMB_WIDTH     => 120,
            ),

            'labels' => array(
                tubepress_app_feature_gallery_api_Constants::OPTION_AJAX_PAGINATION  => sprintf('<a href="%s" target="_blank">Ajax</a>-enabled pagination', "http://wikipedia.org/wiki/Ajax_(programming)"),  //>(translatable)<
                tubepress_app_feature_gallery_api_Constants::OPTION_AUTONEXT         => 'Play videos sequentially without user intervention', //>(translatable)<
                tubepress_app_feature_gallery_api_Constants::OPTION_FLUID_THUMBS     => 'Use "fluid" thumbnails',             //>(translatable)<
                tubepress_app_feature_gallery_api_Constants::OPTION_HQ_THUMBS        => 'Use high-quality thumbnails',        //>(translatable)<
                tubepress_app_feature_gallery_api_Constants::OPTION_PAGINATE_ABOVE   => 'Show pagination above thumbnails',   //>(translatable)<
                tubepress_app_feature_gallery_api_Constants::OPTION_PAGINATE_BELOW   => 'Show pagination below thumbnails',   //>(translatable)<
                tubepress_app_feature_gallery_api_Constants::OPTION_RANDOM_THUMBS    => 'Randomize thumbnail images',         //>(translatable)<
                tubepress_app_feature_gallery_api_Constants::OPTION_THUMB_HEIGHT     => 'Height (px) of thumbs',              //>(translatable)<
                tubepress_app_feature_gallery_api_Constants::OPTION_THUMB_WIDTH      => 'Width (px) of thumbs',               //>(translatable)<
            ),

            'descriptions' => array(
                tubepress_app_feature_gallery_api_Constants::OPTION_AJAX_PAGINATION  => sprintf('<a href="%s" target="_blank">Ajax</a>-enabled pagination', "http://wikipedia.org/wiki/Ajax_(programming)"),  //>(translatable)<
                tubepress_app_feature_gallery_api_Constants::OPTION_AUTONEXT         => 'When a video finishes, this will start playing the next video in the gallery.',  //>(translatable)<
                tubepress_app_feature_gallery_api_Constants::OPTION_FLUID_THUMBS     => 'Dynamically set thumbnail spacing based on the width of their container.', //>(translatable)<
                tubepress_app_feature_gallery_api_Constants::OPTION_HQ_THUMBS        => 'Note: this option cannot be used with the "randomize thumbnails" feature.', //>(translatable)<
                tubepress_app_feature_gallery_api_Constants::OPTION_PAGINATE_ABOVE   => 'Only applies to galleries that span multiple pages.', //>(translatable)<
                tubepress_app_feature_gallery_api_Constants::OPTION_PAGINATE_BELOW   => 'Only applies to galleries that span multiple pages.', //>(translatable)<
                tubepress_app_feature_gallery_api_Constants::OPTION_RANDOM_THUMBS    => 'Most videos come with several thumbnails. By selecting this option, each time someone views your gallery they will see the same videos with each video\'s thumbnail randomized. Note: this option cannot be used with the "high quality thumbnails" feature.', //>(translatable)<
                tubepress_app_feature_gallery_api_Constants::OPTION_THUMB_HEIGHT     => sprintf('Default is %s.', 90),   //>(translatable)<
                tubepress_app_feature_gallery_api_Constants::OPTION_THUMB_WIDTH      => sprintf('Default is %s.', 120),  //>(translatable)<
            ),

            'doNotPersistNames' => array(
                tubepress_app_feature_gallery_api_Constants::OPTION_SEQUENCE,
            ),

            'proOptionNames' => array(
                tubepress_app_feature_gallery_api_Constants::OPTION_AJAX_PAGINATION,
                tubepress_app_feature_gallery_api_Constants::OPTION_AUTONEXT,
                tubepress_app_feature_gallery_api_Constants::OPTION_HQ_THUMBS,
            )
        ));

        $this->expectParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_VALIDATION . '_gallery', array(

            'priority' => 30000,
            'map'      => array(
                'positiveInteger' => array(
                    tubepress_app_feature_gallery_api_Constants::OPTION_THUMB_HEIGHT,
                    tubepress_app_feature_gallery_api_Constants::OPTION_THUMB_WIDTH,
                )
            )
        ));

        $fieldIndex = 0;
        $fieldMap = array(
            'text' => array(
                tubepress_app_feature_gallery_api_Constants::OPTION_THUMB_HEIGHT,
                tubepress_app_feature_gallery_api_Constants::OPTION_THUMB_WIDTH,
            ),
            'boolean' => array(
                tubepress_app_feature_gallery_api_Constants::OPTION_AJAX_PAGINATION,
                tubepress_app_feature_gallery_api_Constants::OPTION_FLUID_THUMBS,
                tubepress_app_feature_gallery_api_Constants::OPTION_PAGINATE_ABOVE,
                tubepress_app_feature_gallery_api_Constants::OPTION_PAGINATE_BELOW,
                tubepress_app_feature_gallery_api_Constants::OPTION_HQ_THUMBS,
                tubepress_app_feature_gallery_api_Constants::OPTION_RANDOM_THUMBS,
                tubepress_app_feature_gallery_api_Constants::OPTION_AUTONEXT
            )
        );
        foreach ($fieldMap as $type => $fieldIds) {
            foreach ($fieldIds as $id) {
                $this->expectRegistration(
                    'html_gallery_field_' . $fieldIndex++,
                    'tubepress_app_options_ui_api_FieldInterface'
                )->withFactoryService(tubepress_app_options_ui_api_FieldBuilderInterface::_)
                    ->withFactoryMethod('newInstance')
                    ->withArgument($id)
                    ->withArgument($type);
            }
        }
        $fieldReferences = array();
        for ($x = 0; $x < $fieldIndex; $x++) {
            $fieldReferences[] = new tubepress_platform_api_ioc_Reference('html_gallery_field_' . $x);
        }

        $this->expectRegistration(
            'thumbnails_category',
            'tubepress_app_options_ui_api_ElementInterface'
        )->withFactoryService(tubepress_app_options_ui_api_ElementBuilderInterface::_)
            ->withFactoryMethod('newInstance')
            ->withArgument(tubepress_app_feature_gallery_api_Constants::OPTIONS_UI_CATEGORY_THUMBNAILS)
            ->withArgument('Thumbnails');

        $fieldMap = array(
            tubepress_app_feature_gallery_api_Constants::OPTIONS_UI_CATEGORY_THUMBNAILS => array(
                tubepress_app_feature_gallery_api_Constants::OPTION_THUMB_HEIGHT,
                tubepress_app_feature_gallery_api_Constants::OPTION_THUMB_WIDTH,
                tubepress_app_feature_gallery_api_Constants::OPTION_AJAX_PAGINATION,
                tubepress_app_feature_gallery_api_Constants::OPTION_FLUID_THUMBS,
                tubepress_app_feature_gallery_api_Constants::OPTION_PAGINATE_ABOVE,
                tubepress_app_feature_gallery_api_Constants::OPTION_PAGINATE_BELOW,
                tubepress_app_feature_gallery_api_Constants::OPTION_HQ_THUMBS,
                tubepress_app_feature_gallery_api_Constants::OPTION_RANDOM_THUMBS
            ),
            tubepress_app_embedded_api_Constants::OPTIONS_UI_CATEGORY_EMBEDDED => array(
                tubepress_app_feature_gallery_api_Constants::OPTION_AUTONEXT
            )
        );

        $this->expectRegistration(
            'tubepress_app_feature_gallery_impl_options_ui_FieldProvider',
            'tubepress_app_feature_gallery_impl_options_ui_FieldProvider'
        )->withArgument(array(new tubepress_platform_api_ioc_Reference('thumbnails_category')))
            ->withArgument($fieldReferences)
            ->withArgument($fieldMap)
            ->withTag('tubepress_app_options_ui_api_FieldProviderInterface');
    }

    protected function getExpectedExternalServicesMap()
    {
        $mockField = $this->mock('tubepress_app_options_ui_api_FieldInterface');
        $fieldBuilder = $this->mock(tubepress_app_options_ui_api_FieldBuilderInterface::_);
        $fieldBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockField);

        $mockCategory = $this->mock('tubepress_app_options_ui_api_ElementInterface');
        $elementBuilder = $this->mock(tubepress_app_options_ui_api_ElementBuilderInterface::_);
        $elementBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockCategory);

        return array(

            tubepress_platform_api_log_LoggerInterface::_ => tubepress_platform_api_log_LoggerInterface::_,
            tubepress_app_options_api_ContextInterface::_ => tubepress_app_options_api_ContextInterface::_,
            tubepress_lib_event_api_EventDispatcherInterface::_ => tubepress_lib_event_api_EventDispatcherInterface::_,
            tubepress_app_options_api_ReferenceInterface::_ => tubepress_app_options_api_ReferenceInterface::_,
            tubepress_app_environment_api_EnvironmentInterface::_ => tubepress_app_environment_api_EnvironmentInterface::_,
            tubepress_app_media_provider_api_CollectorInterface::_ => tubepress_app_media_provider_api_CollectorInterface::_,
            tubepress_app_http_api_RequestParametersInterface::_ => tubepress_app_http_api_RequestParametersInterface::_,
            tubepress_lib_template_api_TemplateFactoryInterface::_ => tubepress_lib_template_api_TemplateFactoryInterface::_,
            tubepress_lib_url_api_UrlFactoryInterface::_ => tubepress_lib_url_api_UrlFactoryInterface::_,
            tubepress_lib_translation_api_TranslatorInterface::_ => tubepress_lib_translation_api_TranslatorInterface::_,
            tubepress_lib_util_api_UrlUtilsInterface::_ => tubepress_lib_util_api_UrlUtilsInterface::_,
            tubepress_app_theme_api_ThemeLibraryInterface::_ => tubepress_app_theme_api_ThemeLibraryInterface::_,
            tubepress_app_player_api_PlayerHtmlInterface::_ => tubepress_app_player_api_PlayerHtmlInterface::_,
            tubepress_app_options_ui_api_FieldBuilderInterface::_ => $fieldBuilder,
            tubepress_app_options_ui_api_ElementBuilderInterface::_ => $elementBuilder
        );
    }
}