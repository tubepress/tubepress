<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_gallery_ioc_GalleryExtension implements tubepress_spi_ioc_ContainerExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_registerListeners($containerBuilder);
        $this->_registerTemplatePathProvider($containerBuilder);
        $this->_registerOptions($containerBuilder);
        $this->_registerOptionsUi($containerBuilder);
    }

    private function _registerTemplatePathProvider(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_api_template_BasePathProvider__gallery',
            'tubepress_api_template_BasePathProvider'
        )->addArgument(array(
            TUBEPRESS_ROOT . '/src/add-ons/gallery/templates',
        ))->addTag('tubepress_spi_template_PathProviderInterface');
    }

    private function _registerListeners(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_gallery_impl_listeners_PaginationListener',
            'tubepress_gallery_impl_listeners_PaginationListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('tubepress_theme_impl_CurrentThemeService'))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::TEMPLATE_PRE_RENDER . '.gallery/main',
            'priority' => 96000,
            'method'   => 'onGalleryTemplatePreRender',
        ));

        $containerBuilder->register(
            'tubepress_gallery_impl_listeners_GalleryListener',
            'tubepress_gallery_impl_listeners_GalleryListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_media_CollectorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::TEMPLATE_PRE_RENDER . '.gallery/main',
            'priority' => 100000,
            'method'   => 'onGalleryTemplatePreRender', ))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::TEMPLATE_POST_RENDER . '.gallery/main',
            'priority' => 100000,
            'method'   => 'onPostGalleryTemplateRender', ))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::GALLERY_INIT_JS,
            'priority' => 100000,
            'method'   => 'onGalleryInitJs', ))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::HTML_GENERATION,
            'priority' => 92000,
            'method'   => 'onHtmlGeneration', ));
    }

    private function _registerOptions(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_api_options_Reference__gallery',
            'tubepress_api_options_Reference'
        )->addTag(tubepress_api_options_ReferenceInterface::_)
            ->addArgument(array(

                tubepress_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(

                    tubepress_api_options_Names::GALLERY_AJAX_PAGINATION => false,
                    tubepress_api_options_Names::GALLERY_AUTONEXT        => true,
                    tubepress_api_options_Names::GALLERY_FLUID_THUMBS    => true,
                    tubepress_api_options_Names::GALLERY_HQ_THUMBS       => false,
                    tubepress_api_options_Names::GALLERY_PAGINATE_ABOVE  => true,
                    tubepress_api_options_Names::GALLERY_PAGINATE_BELOW  => true,
                    tubepress_api_options_Names::GALLERY_RANDOM_THUMBS   => true,
                    tubepress_api_options_Names::GALLERY_SOURCE          => 'user',
                    tubepress_api_options_Names::GALLERY_THUMB_HEIGHT    => 90,
                    tubepress_api_options_Names::GALLERY_THUMB_WIDTH     => 120,
                    tubepress_api_options_Names::SOURCES                 => null,

                ),

                tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                    tubepress_api_options_Names::GALLERY_AJAX_PAGINATION => sprintf('<a href="%s" target="_blank">Ajax</a>-enabled pagination', "http://wikipedia.org/wiki/Ajax_(programming)"),  //>(translatable)<
                    tubepress_api_options_Names::GALLERY_AUTONEXT        => 'Play videos sequentially without user intervention', //>(translatable)<
                    tubepress_api_options_Names::GALLERY_FLUID_THUMBS    => 'Use "fluid" thumbnails',             //>(translatable)<
                    tubepress_api_options_Names::GALLERY_HQ_THUMBS       => 'Use high-quality thumbnails',        //>(translatable)<
                    tubepress_api_options_Names::GALLERY_PAGINATE_ABOVE  => 'Show pagination above thumbnails',   //>(translatable)<
                    tubepress_api_options_Names::GALLERY_PAGINATE_BELOW  => 'Show pagination below thumbnails',   //>(translatable)<
                    tubepress_api_options_Names::GALLERY_RANDOM_THUMBS   => 'Randomize thumbnail images',         //>(translatable)<
                    tubepress_api_options_Names::GALLERY_THUMB_HEIGHT    => 'Height (px) of thumbs',              //>(translatable)<
                    tubepress_api_options_Names::GALLERY_THUMB_WIDTH     => 'Width (px) of thumbs',               //>(translatable)<
                ),

                tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(

                    tubepress_api_options_Names::GALLERY_AJAX_PAGINATION => sprintf('<a href="%s" target="_blank">Ajax</a>-enabled pagination', "http://wikipedia.org/wiki/Ajax_(programming)"),  //>(translatable)<
                    tubepress_api_options_Names::GALLERY_AUTONEXT        => 'When a video finishes, this will start playing the next video in the gallery.',  //>(translatable)<
                    tubepress_api_options_Names::GALLERY_FLUID_THUMBS    => 'Dynamically set thumbnail spacing based on the width of their container.', //>(translatable)<
                    tubepress_api_options_Names::GALLERY_HQ_THUMBS       => 'Note: this option cannot be used with the "randomize thumbnails" feature.', //>(translatable)<
                    tubepress_api_options_Names::GALLERY_PAGINATE_ABOVE  => 'Only applies to galleries that span multiple pages.', //>(translatable)<
                    tubepress_api_options_Names::GALLERY_PAGINATE_BELOW  => 'Only applies to galleries that span multiple pages.', //>(translatable)<
                    tubepress_api_options_Names::GALLERY_RANDOM_THUMBS   => 'Most videos come with several thumbnails. By selecting this option, each time someone views your gallery they will see the same videos with each video\'s thumbnail randomized. Note: this option cannot be used with the "high quality thumbnails" feature.', //>(translatable)<
                    tubepress_api_options_Names::GALLERY_THUMB_HEIGHT    => sprintf('Default is %s.', 90),   //>(translatable)<
                    tubepress_api_options_Names::GALLERY_THUMB_WIDTH     => sprintf('Default is %s.', 120),  //>(translatable)<

                ),
            ))->addArgument(array(

                tubepress_api_options_Reference::PROPERTY_PRO_ONLY => array(
                    tubepress_api_options_Names::GALLERY_AJAX_PAGINATION,
                    tubepress_api_options_Names::GALLERY_AUTONEXT,
                    tubepress_api_options_Names::GALLERY_HQ_THUMBS,
                    tubepress_api_options_Names::SOURCES,
                ),
            ));

        $toValidate = array(
            tubepress_api_options_listeners_RegexValidatingListener::TYPE_INTEGER_POSITIVE => array(
                tubepress_api_options_Names::GALLERY_THUMB_HEIGHT,
                tubepress_api_options_Names::GALLERY_THUMB_WIDTH,
            ),
        );

        foreach ($toValidate as $type => $optionNames) {
            foreach ($optionNames as $optionName) {
                $containerBuilder->register(
                    'regex_validator.' . $optionName,
                    'tubepress_api_options_listeners_RegexValidatingListener'
                )->addArgument($type)
                 ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
                 ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
                 ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                    'event'    => tubepress_api_event_Events::OPTION_SET . ".$optionName",
                    'priority' => 100000,
                    'method'   => 'onOption',
                ));
            }
        }
    }

    private function _registerOptionsUi(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $fieldReferences = array();
        $fieldMap        = array(
            'boolean' => array(
                tubepress_api_options_Names::GALLERY_AJAX_PAGINATION,
                tubepress_api_options_Names::GALLERY_FLUID_THUMBS,
                tubepress_api_options_Names::GALLERY_PAGINATE_ABOVE,
                tubepress_api_options_Names::GALLERY_PAGINATE_BELOW,
                tubepress_api_options_Names::GALLERY_HQ_THUMBS,
                tubepress_api_options_Names::GALLERY_RANDOM_THUMBS,
                tubepress_api_options_Names::GALLERY_AUTONEXT,
            ),
            'text' => array(
                tubepress_api_options_Names::GALLERY_THUMB_HEIGHT,
                tubepress_api_options_Names::GALLERY_THUMB_WIDTH,
            ),
            'gallerySource' => array(
                tubepress_api_options_Names::GALLERY_SOURCE,
            ),
        );

        foreach ($fieldMap as $type => $ids) {
            foreach ($ids as $id) {

                $serviceId = 'gallery_field_' . $id;

                $containerBuilder->register(
                    $serviceId,
                    'tubepress_api_options_ui_FieldInterface'
                )->setFactoryService(tubepress_api_options_ui_FieldBuilderInterface::_)
                 ->setFactoryMethod('newInstance')
                 ->addArgument($id)
                 ->addArgument($type);

                $fieldReferences[] = new tubepress_api_ioc_Reference($serviceId);
            }
        }

        $categoryReferences = array();
        $categories         = array(
            array(tubepress_api_options_ui_CategoryNames::GALLERY_SOURCE, 'Which videos?'), //>(translatable)<
            array(tubepress_api_options_ui_CategoryNames::THUMBNAILS,     'Thumbnails'),    //>(translatable)<
        );
        foreach ($categories as $categoryIdAndLabel) {

            $serviceId = 'gallery_category_' . $categoryIdAndLabel[0];
            $containerBuilder->register(
                $serviceId,
                'tubepress_options_ui_impl_BaseElement'
            )->addArgument($categoryIdAndLabel[0])
                ->addArgument($categoryIdAndLabel[1]);

            $categoryReferences[] = new tubepress_api_ioc_Reference($serviceId);
        }

        $fieldMap = array(
            tubepress_api_options_ui_CategoryNames::GALLERY_SOURCE => array(
                tubepress_api_options_Names::GALLERY_SOURCE,
            ),
            tubepress_api_options_ui_CategoryNames::EMBEDDED => array(
                tubepress_api_options_Names::GALLERY_AUTONEXT,
            ),
            tubepress_api_options_ui_CategoryNames::THUMBNAILS => array(
                tubepress_api_options_Names::GALLERY_THUMB_HEIGHT,
                tubepress_api_options_Names::GALLERY_THUMB_WIDTH,
                tubepress_api_options_Names::GALLERY_AJAX_PAGINATION,
                tubepress_api_options_Names::GALLERY_FLUID_THUMBS,
                tubepress_api_options_Names::GALLERY_PAGINATE_ABOVE,
                tubepress_api_options_Names::GALLERY_PAGINATE_BELOW,
                tubepress_api_options_Names::GALLERY_HQ_THUMBS,
                tubepress_api_options_Names::GALLERY_RANDOM_THUMBS,
            ),
        );

        $containerBuilder->register(
            'tubepress_api_options_ui_BaseFieldProvider__gallery',
            'tubepress_api_options_ui_BaseFieldProvider'
        )->addArgument('field-provider-gallery')
         ->addArgument('Gallery')
         ->addArgument(false)
         ->addArgument(false)
         ->addArgument($categoryReferences)
         ->addArgument($fieldReferences)
         ->addArgument($fieldMap)
         ->addTag('tubepress_spi_options_ui_FieldProviderInterface');
    }
}
