<?php
/*
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_gallery_ioc_GalleryExtension
 */
class tubepress_test_gallery_ioc_GalleryExtensionTest extends tubepress_api_test_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_gallery_ioc_GalleryExtension
     */
    protected function buildSut()
    {
        return  new tubepress_gallery_ioc_GalleryExtension();
    }

    protected function prepareForLoad()
    {
        $this->_registerListeners();
        $this->_registerTemplatePathProvider();
        $this->_registerOptions();
        $this->_registerOptionsUi();
    }

    private function _registerTemplatePathProvider()
    {
        $this->expectRegistration(
            'tubepress_api_template_BasePathProvider__gallery',
            'tubepress_api_template_BasePathProvider'
        )->withArgument(array(
            TUBEPRESS_ROOT . '/src/add-ons/gallery/templates',
        ))->withTag('tubepress_spi_template_PathProviderInterface');
    }

    private function _registerListeners()
    {
        $this->expectRegistration(
            'tubepress_gallery_impl_listeners_PaginationListener',
            'tubepress_gallery_impl_listeners_PaginationListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_theme_impl_CurrentThemeService'))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::TEMPLATE_PRE_RENDER . '.gallery/main',
                'priority' => 96000,
                'method'   => 'onGalleryTemplatePreRender',
            ));

        $this->expectRegistration(
            'tubepress_gallery_impl_listeners_GalleryListener',
            'tubepress_gallery_impl_listeners_GalleryListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_media_CollectorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::TEMPLATE_PRE_RENDER . '.gallery/main',
                'priority' => 100000,
                'method'   => 'onGalleryTemplatePreRender', ))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::TEMPLATE_POST_RENDER . '.gallery/main',
                'priority' => 100000,
                'method'   => 'onPostGalleryTemplateRender', ))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::GALLERY_INIT_JS,
                'priority' => 100000,
                'method'   => 'onGalleryInitJs', ))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::HTML_GENERATION,
                'priority' => 92000,
                'method'   => 'onHtmlGeneration', ));
    }

    private function _registerOptions()
    {
        $this->expectRegistration(
            'tubepress_api_options_Reference__gallery',
            'tubepress_api_options_Reference'
        )->withTag(tubepress_api_options_ReferenceInterface::_)
            ->withArgument(array(

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
                    tubepress_api_options_Names::GALLERY_AJAX_PAGINATION => sprintf('<a href="%s" target="_blank">Ajax</a>-enabled pagination', "http://wikipedia.org/wiki/Ajax_(programming)"),
                    tubepress_api_options_Names::GALLERY_AUTONEXT        => 'Play videos sequentially without user intervention',
                    tubepress_api_options_Names::GALLERY_FLUID_THUMBS    => 'Use "fluid" thumbnails',
                    tubepress_api_options_Names::GALLERY_HQ_THUMBS       => 'Use high-quality thumbnails',
                    tubepress_api_options_Names::GALLERY_PAGINATE_ABOVE  => 'Show pagination above thumbnails',
                    tubepress_api_options_Names::GALLERY_PAGINATE_BELOW  => 'Show pagination below thumbnails',
                    tubepress_api_options_Names::GALLERY_RANDOM_THUMBS   => 'Randomize thumbnail images',
                    tubepress_api_options_Names::GALLERY_THUMB_HEIGHT    => 'Height (px) of thumbs',
                    tubepress_api_options_Names::GALLERY_THUMB_WIDTH     => 'Width (px) of thumbs',
                ),

                tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(

                    tubepress_api_options_Names::GALLERY_AJAX_PAGINATION => sprintf('<a href="%s" target="_blank">Ajax</a>-enabled pagination', "http://wikipedia.org/wiki/Ajax_(programming)"),
                    tubepress_api_options_Names::GALLERY_AUTONEXT        => 'When a video finishes, this will start playing the next video in the gallery.',
                    tubepress_api_options_Names::GALLERY_FLUID_THUMBS    => 'Dynamically set thumbnail spacing based on the width of their container.',
                    tubepress_api_options_Names::GALLERY_HQ_THUMBS       => 'Note: this option cannot be used with the "randomize thumbnails" feature.',
                    tubepress_api_options_Names::GALLERY_PAGINATE_ABOVE  => 'Only applies to galleries that span multiple pages.',
                    tubepress_api_options_Names::GALLERY_PAGINATE_BELOW  => 'Only applies to galleries that span multiple pages.',
                    tubepress_api_options_Names::GALLERY_RANDOM_THUMBS   => 'Most videos come with several thumbnails. By selecting this option, each time someone views your gallery they will see the same videos with each video\'s thumbnail randomized. Note: this option cannot be used with the "high quality thumbnails" feature.',
                    tubepress_api_options_Names::GALLERY_THUMB_HEIGHT    => sprintf('Default is %s.', 90),
                    tubepress_api_options_Names::GALLERY_THUMB_WIDTH     => sprintf('Default is %s.', 120),

                ),
            ))->withArgument(array(

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
                $this->expectRegistration(
                    'regex_validator.' . $optionName,
                    'tubepress_api_options_listeners_RegexValidatingListener'
                )->withArgument($type)
                    ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
                    ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
                    ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                        'event'    => tubepress_api_event_Events::OPTION_SET . ".$optionName",
                        'priority' => 100000,
                        'method'   => 'onOption',
                    ));
            }
        }
    }

    private function _registerOptionsUi()
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

        $categoryReferences = array();
        $categories         = array(
            array(tubepress_api_options_ui_CategoryNames::GALLERY_SOURCE, 'Which videos?'),
            array(tubepress_api_options_ui_CategoryNames::THUMBNAILS,     'Thumbnails'),
        );
        foreach ($categories as $categoryIdAndLabel) {

            $serviceId = 'gallery_category_' . $categoryIdAndLabel[0];
            $this->expectRegistration(
                $serviceId,
                'tubepress_options_ui_impl_BaseElement'
            )->withArgument($categoryIdAndLabel[0])
                ->withArgument($categoryIdAndLabel[1]);

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

        $this->expectRegistration(
            'tubepress_api_options_ui_BaseFieldProvider__gallery',
            'tubepress_api_options_ui_BaseFieldProvider'
        )->withArgument('field-provider-gallery')
            ->withArgument('Gallery')
            ->withArgument(false)
            ->withArgument(false)
            ->withArgument($categoryReferences)
            ->withArgument($fieldReferences)
            ->withArgument($fieldMap)
            ->withTag('tubepress_spi_options_ui_FieldProviderInterface');
    }

    protected function getExpectedExternalServicesMap()
    {
        $fieldBuilder = $this->mock(tubepress_api_options_ui_FieldBuilderInterface::_);
        $mockField    = $this->mock('tubepress_api_options_ui_FieldInterface');
        $fieldBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockField);

        return array(
            tubepress_api_options_ContextInterface::_         => tubepress_api_options_ContextInterface::_,
            tubepress_api_url_UrlFactoryInterface::_          => tubepress_api_url_UrlFactoryInterface::_,
            tubepress_api_http_RequestParametersInterface::_  => tubepress_api_http_RequestParametersInterface::_,
            tubepress_api_options_ReferenceInterface::_       => tubepress_api_options_ReferenceInterface::_,
            tubepress_api_event_EventDispatcherInterface::_   => tubepress_api_event_EventDispatcherInterface::_,
            tubepress_api_template_TemplatingInterface::_     => tubepress_api_template_TemplatingInterface::_,
            'tubepress_theme_impl_CurrentThemeService'        => 'tubepress_theme_impl_CurrentThemeService',
            tubepress_api_translation_TranslatorInterface::_  => tubepress_api_translation_TranslatorInterface::_,
            tubepress_api_log_LoggerInterface::_              => tubepress_api_log_LoggerInterface::_,
            tubepress_api_media_CollectorInterface::_         => tubepress_api_media_CollectorInterface::_,
            tubepress_api_options_ui_FieldBuilderInterface::_ => $fieldBuilder,
        );
    }
}
