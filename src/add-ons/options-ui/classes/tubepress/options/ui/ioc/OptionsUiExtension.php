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
class tubepress_options_ui_ioc_OptionsUiExtension implements tubepress_platform_api_ioc_ContainerExtensionInterface
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
        $this->_registerOptionsUiSingletons($containerBuilder);
        $this->_registerOptionsUiFieldProvider($containerBuilder);
    }

    private function _registerOptionsUiSingletons(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            tubepress_app_api_options_ui_FieldBuilderInterface::_,
            'tubepress_options_ui_impl_FieldBuilder'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_PersistenceInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_RequestParametersInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_template_TemplatingInterface::_ . '.admin'))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ReferenceInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_LangUtilsInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_AcceptableValuesInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_contrib_RegistryInterface::_ . '.' . tubepress_app_api_theme_ThemeInterface::_))
            ->addTag(tubepress_lib_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_app_api_media_MediaProviderInterface::__,
                'method' => 'setMediaProviders'
            ));

        $containerBuilder->register(
            'tubepress_html_impl_CssAndJsGenerationHelper.admin',
            'tubepress_html_impl_CssAndJsGenerationHelper'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_contrib_RegistryInterface::_ . '.' . tubepress_app_api_theme_ThemeInterface::_ . '.admin'))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_template_TemplatingInterface::_ . '.admin'))
         ->addArgument(new tubepress_platform_api_ioc_Reference('tubepress_theme_impl_CurrentThemeService.admin'))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_environment_EnvironmentInterface::_))
         ->addArgument(tubepress_app_api_event_Events::HTML_STYLESHEETS_ADMIN)
         ->addArgument(tubepress_app_api_event_Events::HTML_SCRIPTS_ADMIN)
         ->addArgument('options-ui/cssjs/styles')
         ->addArgument('options-ui/cssjs/scripts');

        $containerBuilder->register(
            tubepress_app_api_options_ui_FormInterface::_,
            'tubepress_options_ui_impl_Form'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_template_TemplatingInterface::_ . '.admin'))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_PersistenceInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_StringUtilsInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference('tubepress_html_impl_CssAndJsGenerationHelper.admin'))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_RequestParametersInterface::_))
            ->addTag(tubepress_lib_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
                'tag'    => 'tubepress_app_api_options_ui_FieldProviderInterface',
                'method' => 'setFieldProviders',
            ));
    }

    private function _registerOptionsUiFieldProvider(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $fieldReferences = array();
        $fieldMap = array(
            'boolean' => array(
                tubepress_app_api_options_Names::EMBEDDED_LAZYPLAY,
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_ON,
                tubepress_app_api_options_Names::EMBEDDED_SHOW_INFO,
                tubepress_app_api_options_Names::EMBEDDED_AUTOPLAY,
                tubepress_app_api_options_Names::EMBEDDED_LOOP,
                tubepress_app_api_options_Names::GALLERY_AJAX_PAGINATION,
                tubepress_app_api_options_Names::GALLERY_FLUID_THUMBS,
                tubepress_app_api_options_Names::GALLERY_PAGINATE_ABOVE,
                tubepress_app_api_options_Names::GALLERY_PAGINATE_BELOW,
                tubepress_app_api_options_Names::GALLERY_HQ_THUMBS,
                tubepress_app_api_options_Names::GALLERY_RANDOM_THUMBS,
                tubepress_app_api_options_Names::GALLERY_AUTONEXT,
                tubepress_app_api_options_Names::HTML_HTTPS,
                tubepress_app_api_options_Names::DEBUG_ON,
                tubepress_app_api_options_Names::META_RELATIVE_DATES,
                tubepress_app_api_options_Names::RESPONSIVE_EMBEDS,
            ),
            'dropdown' => array(
                tubepress_app_api_options_Names::PLAYER_LOCATION,
                tubepress_app_api_options_Names::EMBEDDED_PLAYER_IMPL,
                tubepress_app_api_options_Names::HTTP_METHOD,
                tubepress_app_api_options_Names::FEED_PER_PAGE_SORT,
            ),
            'text' => array(
                tubepress_app_api_options_Names::EMBEDDED_HEIGHT,
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_DURATION,
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_OFFSET,
                tubepress_app_api_options_Names::EMBEDDED_WIDTH,
                tubepress_app_api_options_Names::GALLERY_THUMB_HEIGHT,
                tubepress_app_api_options_Names::GALLERY_THUMB_WIDTH,
                tubepress_app_api_options_Names::META_DATEFORMAT,
                tubepress_app_api_options_Names::META_DESC_LIMIT,
                tubepress_app_api_options_Names::FEED_RESULT_COUNT_CAP,
                tubepress_app_api_options_Names::FEED_ITEM_ID_BLACKLIST,
                tubepress_app_api_options_Names::FEED_RESULTS_PER_PAGE,
                tubepress_app_api_options_Names::SEARCH_ONLY_USER,
            ),
            'fieldProviderFilter' => array(
                tubepress_options_ui_impl_fields_templated_multi_FieldProviderFilterField::FIELD_ID
            ),
            'metaMultiSelect' => array(
                'does not matter'
            ),
            'orderBy' => array(
                tubepress_app_api_options_Names::FEED_ORDER_BY
            ),
            'gallerySource' => array(
                tubepress_app_api_options_Names::GALLERY_SOURCE,
            ),
            'multiSourceText' => array(
                tubepress_app_api_options_Names::FEED_RESULTS_PER_PAGE,
                tubepress_app_api_options_Names::SEARCH_ONLY_USER
            ),
        );

        foreach ($fieldMap as $type => $ids) {
            foreach ($ids as $id) {

                $serviceId = 'core_field_' . $id;

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

        $categoryReferences = array();
        $categories = array(
            array(tubepress_app_api_options_ui_CategoryNames::GALLERY_SOURCE, 'Which videos?'), //>(translatable)<
            array(tubepress_app_api_options_ui_CategoryNames::THUMBNAILS,     'Thumbnails'),    //>(translatable)<
            array(tubepress_app_api_options_ui_CategoryNames::EMBEDDED,       'Player'),        //>(translatable)<
            array(tubepress_app_api_options_ui_CategoryNames::META,           'Meta'),          //>(translatable)<
            array(tubepress_app_api_options_ui_CategoryNames::FEED,           'Feed'),          //>(translatable)<
            array(tubepress_app_api_options_ui_CategoryNames::ADVANCED,       'Advanced'),      //>(translatable)<
        );
        foreach ($categories as $categoryIdAndLabel) {

            $serviceId = 'core_category_' . $categoryIdAndLabel[0];
            $containerBuilder->register(
                $serviceId,
                'tubepress_options_ui_impl_BaseElement'
            )->addArgument($categoryIdAndLabel[0])
             ->addArgument($categoryIdAndLabel[1]);

            $categoryReferences[] = new tubepress_platform_api_ioc_Reference($serviceId);
        }

        $fieldMap = array(
            tubepress_app_api_options_ui_CategoryNames::GALLERY_SOURCE => array(
                tubepress_app_api_options_Names::GALLERY_SOURCE,
            ),
            tubepress_app_api_options_ui_CategoryNames::EMBEDDED => array(
                tubepress_app_api_options_Names::PLAYER_LOCATION,
                tubepress_app_api_options_Names::EMBEDDED_PLAYER_IMPL,
                tubepress_app_api_options_Names::EMBEDDED_HEIGHT,
                tubepress_app_api_options_Names::EMBEDDED_WIDTH,
                tubepress_app_api_options_Names::RESPONSIVE_EMBEDS,
                tubepress_app_api_options_Names::EMBEDDED_LAZYPLAY,
                tubepress_app_api_options_Names::EMBEDDED_SHOW_INFO,
                tubepress_app_api_options_Names::EMBEDDED_AUTOPLAY,
                tubepress_app_api_options_Names::EMBEDDED_LOOP,
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_ON,
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_DURATION,
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_OFFSET,
                tubepress_app_api_options_Names::GALLERY_AUTONEXT
            ),
            tubepress_app_api_options_ui_CategoryNames::THUMBNAILS => array(
                tubepress_app_api_options_Names::GALLERY_THUMB_HEIGHT,
                tubepress_app_api_options_Names::GALLERY_THUMB_WIDTH,
                tubepress_app_api_options_Names::GALLERY_AJAX_PAGINATION,
                tubepress_app_api_options_Names::GALLERY_FLUID_THUMBS,
                tubepress_app_api_options_Names::GALLERY_PAGINATE_ABOVE,
                tubepress_app_api_options_Names::GALLERY_PAGINATE_BELOW,
                tubepress_app_api_options_Names::GALLERY_HQ_THUMBS,
                tubepress_app_api_options_Names::GALLERY_RANDOM_THUMBS
            ),
            tubepress_app_api_options_ui_CategoryNames::ADVANCED => array(
                tubepress_app_api_options_Names::HTML_HTTPS,
                tubepress_app_api_options_Names::HTTP_METHOD,
                tubepress_app_api_options_Names::DEBUG_ON
            ),
            tubepress_app_api_options_ui_CategoryNames::META => array(
                tubepress_options_ui_impl_fields_templated_multi_MetaMultiSelectField::FIELD_ID,
                tubepress_app_api_options_Names::META_DATEFORMAT,
                tubepress_app_api_options_Names::META_RELATIVE_DATES,
                tubepress_app_api_options_Names::META_DESC_LIMIT,
            ),
            tubepress_app_api_options_ui_CategoryNames::FEED => array(
                tubepress_app_api_options_Names::FEED_RESULTS_PER_PAGE,
                tubepress_app_api_options_Names::FEED_ORDER_BY,
                tubepress_app_api_options_Names::FEED_PER_PAGE_SORT,
                tubepress_app_api_options_Names::SEARCH_ONLY_USER,
                tubepress_app_api_options_Names::FEED_RESULT_COUNT_CAP,
                tubepress_app_api_options_Names::FEED_ITEM_ID_BLACKLIST,
            ),
        );

        $containerBuilder->register(
            'tubepress_options_ui_impl_FieldProvider',
            'tubepress_options_ui_impl_FieldProvider'
        )->addArgument($categoryReferences)
         ->addArgument($fieldReferences)
         ->addArgument($fieldMap)
            ->addTag('tubepress_app_api_options_ui_FieldProviderInterface');
    }
}