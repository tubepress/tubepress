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

class tubepress_embedded_common_ioc_EmbeddedCommonExtension implements tubepress_spi_ioc_ContainerExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_registerListeners($containerBuilder);
        $this->_registerOptions($containerBuilder);
        $this->_registerOptionsUi($containerBuilder);
    }

    private function _registerOptions(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_api_options_Reference__embedded_common',
            'tubepress_api_options_Reference'
        )->addTag(tubepress_api_options_ReferenceInterface::_)
         ->addArgument(array(

            tubepress_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                tubepress_api_options_Names::EMBEDDED_AUTOPLAY    => false,
                tubepress_api_options_Names::EMBEDDED_HEIGHT      => 390,
                tubepress_api_options_Names::EMBEDDED_LAZYPLAY    => true,
                tubepress_api_options_Names::EMBEDDED_LOOP        => false,
                tubepress_api_options_Names::EMBEDDED_PLAYER_IMPL => tubepress_api_options_AcceptableValues::EMBEDDED_IMPL_PROVIDER_BASED,
                tubepress_api_options_Names::EMBEDDED_SHOW_INFO   => false,
                tubepress_api_options_Names::EMBEDDED_WIDTH       => 640,
                tubepress_api_options_Names::RESPONSIVE_EMBEDS    => true,
            ),

            tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                tubepress_api_options_Names::EMBEDDED_AUTOPLAY    => 'Auto-play all videos',                               //>(translatable)<
                tubepress_api_options_Names::EMBEDDED_HEIGHT      => 'Max height (px)',                                    //>(translatable)<
                tubepress_api_options_Names::EMBEDDED_LAZYPLAY    => '"Lazy" play videos',                                 //>(translatable)<
                tubepress_api_options_Names::EMBEDDED_LOOP        => 'Loop',                                               //>(translatable)<
                tubepress_api_options_Names::EMBEDDED_PLAYER_IMPL => 'Implementation',                                     //>(translatable)<
                tubepress_api_options_Names::EMBEDDED_SHOW_INFO   => 'Show title and rating before video starts',          //>(translatable)<
                tubepress_api_options_Names::EMBEDDED_WIDTH       => 'Max width (px)',                                     //>(translatable)<
                tubepress_api_options_Names::RESPONSIVE_EMBEDS    => 'Responsive embeds',                                  //>(translatable)<
            ),

            tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(
                tubepress_api_options_Names::EMBEDDED_HEIGHT      => sprintf('Default is %s.', 390), //>(translatable)<
                tubepress_api_options_Names::EMBEDDED_LAZYPLAY    => 'Auto-play each video after thumbnail click.', //>(translatable)<
                tubepress_api_options_Names::EMBEDDED_LOOP        => 'Continue playing the video until the user stops it.', //>(translatable)<
                tubepress_api_options_Names::EMBEDDED_PLAYER_IMPL => 'The brand of the embedded player. Default is the provider\'s player (YouTube, Vimeo, etc).', //>(translatable)<
                tubepress_api_options_Names::EMBEDDED_WIDTH       => sprintf('Default is %s.', 640), //>(translatable)<
                tubepress_api_options_Names::RESPONSIVE_EMBEDS    => 'Auto-resize media players to best fit the viewer\'s screen.', //>(translatable)<
            ),
        ))->addArgument(array(

            tubepress_api_options_Reference::PROPERTY_PRO_ONLY => array(
                tubepress_api_options_Names::RESPONSIVE_EMBEDS,
            ),
        ));

        $toValidate = array(
            tubepress_api_options_listeners_RegexValidatingListener::TYPE_INTEGER_POSITIVE => array(
                tubepress_api_options_Names::EMBEDDED_HEIGHT,
                tubepress_api_options_Names::EMBEDDED_WIDTH,
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

    private function _registerListeners(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_embedded_common_impl_listeners_EmbeddedListener',
            'tubepress_embedded_common_impl_listeners_EmbeddedListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_))
         ->addTag(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
            'tag'    => 'tubepress_spi_embedded_EmbeddedProviderInterface',
            'method' => 'setEmbeddedProviders', ))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::GALLERY_INIT_JS,
            'priority' => 98000,
            'method'   => 'onGalleryInitJs', ))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_api_options_Names::EMBEDDED_PLAYER_IMPL,
            'priority' => 100000,
            'method'   => 'onAcceptableValues', ))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::TEMPLATE_SELECT . '.single/embedded',
            'priority' => 100000,
            'method'   => 'onEmbeddedTemplateSelect', ))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::TEMPLATE_PRE_RENDER . '.single/main',
            'priority' => 100000,
            'method'   => 'onSingleItemTemplatePreRender', ))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::TEMPLATE_PRE_RENDER . '.gallery/player/ajax',
            'priority' => 100000,
            'method'   => 'onPlayerTemplatePreRender', ))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::TEMPLATE_PRE_RENDER . '.gallery/player/static',
            'priority' => 100000,
            'method'   => 'onPlayerTemplatePreRender', ));
    }

    private function _registerOptionsUi(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $fieldReferences = array();
        $fieldMap        = array(
            'boolean' => array(
                tubepress_api_options_Names::EMBEDDED_LAZYPLAY,
                tubepress_api_options_Names::EMBEDDED_SHOW_INFO,
                tubepress_api_options_Names::EMBEDDED_AUTOPLAY,
                tubepress_api_options_Names::EMBEDDED_LOOP,
                tubepress_api_options_Names::RESPONSIVE_EMBEDS,
            ),
            'dropdown' => array(
                tubepress_api_options_Names::EMBEDDED_PLAYER_IMPL,
            ),
            'text' => array(
                tubepress_api_options_Names::EMBEDDED_HEIGHT,
                tubepress_api_options_Names::EMBEDDED_WIDTH,
            ),
        );

        foreach ($fieldMap as $type => $ids) {
            foreach ($ids as $id) {

                $serviceId = 'embedded_field_' . $id;

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
        $serviceId          = 'embedded_category_' . tubepress_api_options_ui_CategoryNames::EMBEDDED;
        $containerBuilder->register(
            $serviceId,
            'tubepress_options_ui_impl_BaseElement'
        )->addArgument(tubepress_api_options_ui_CategoryNames::EMBEDDED)
         ->addArgument('Player');                                                           //>(translatable)<

        $categoryReferences[] = new tubepress_api_ioc_Reference($serviceId);

        $fieldMap = array(
            tubepress_api_options_ui_CategoryNames::EMBEDDED => array(
                tubepress_api_options_Names::EMBEDDED_PLAYER_IMPL,
                tubepress_api_options_Names::EMBEDDED_HEIGHT,
                tubepress_api_options_Names::EMBEDDED_WIDTH,
                tubepress_api_options_Names::RESPONSIVE_EMBEDS,
                tubepress_api_options_Names::EMBEDDED_LAZYPLAY,
                tubepress_api_options_Names::EMBEDDED_SHOW_INFO,
                tubepress_api_options_Names::EMBEDDED_AUTOPLAY,
                tubepress_api_options_Names::EMBEDDED_LOOP,
            ),
        );

        $containerBuilder->register(
            'tubepress_api_options_ui_BaseFieldProvider__embedded_common',
            'tubepress_api_options_ui_BaseFieldProvider'
        )->addArgument('field-provider-embedded-common')
         ->addArgument('Embedded')
         ->addArgument(false)
         ->addArgument(false)
         ->addArgument($categoryReferences)
         ->addArgument($fieldReferences)
         ->addArgument($fieldMap)
         ->addTag('tubepress_spi_options_ui_FieldProviderInterface');
    }
}
