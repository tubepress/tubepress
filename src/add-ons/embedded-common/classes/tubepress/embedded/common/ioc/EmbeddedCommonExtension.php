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
class tubepress_embedded_common_ioc_EmbeddedCommonExtension implements tubepress_platform_api_ioc_ContainerExtensionInterface
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
        $this->_registerOptions($containerBuilder);
        $this->_registerOptionsUi($containerBuilder);
    }

    private function _registerOptions(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_app_api_options_Reference__embedded_common',
            'tubepress_app_api_options_Reference'
        )->addTag(tubepress_app_api_options_ReferenceInterface::_)
         ->addArgument(array(

            tubepress_app_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                tubepress_app_api_options_Names::EMBEDDED_AUTOPLAY        => false,
                tubepress_app_api_options_Names::EMBEDDED_HEIGHT          => 390,
                tubepress_app_api_options_Names::EMBEDDED_LAZYPLAY        => true,
                tubepress_app_api_options_Names::EMBEDDED_LOOP            => false,
                tubepress_app_api_options_Names::EMBEDDED_PLAYER_IMPL     => tubepress_app_api_options_AcceptableValues::EMBEDDED_IMPL_PROVIDER_BASED,
                tubepress_app_api_options_Names::EMBEDDED_SHOW_INFO       => false,
                tubepress_app_api_options_Names::EMBEDDED_WIDTH           => 640,
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_ON       => true,
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_DURATION => 0,
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_OFFSET   => 0,
                tubepress_app_api_options_Names::RESPONSIVE_EMBEDS        => true,
            ),

            tubepress_app_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                tubepress_app_api_options_Names::EMBEDDED_AUTOPLAY        => 'Auto-play all videos',                               //>(translatable)<
                tubepress_app_api_options_Names::EMBEDDED_HEIGHT          => 'Max height (px)',                                    //>(translatable)<
                tubepress_app_api_options_Names::EMBEDDED_LAZYPLAY        => '"Lazy" play videos',                                 //>(translatable)<
                tubepress_app_api_options_Names::EMBEDDED_LOOP            => 'Loop',                                               //>(translatable)<
                tubepress_app_api_options_Names::EMBEDDED_PLAYER_IMPL     => 'Implementation',                                     //>(translatable)<
                tubepress_app_api_options_Names::EMBEDDED_SHOW_INFO       => 'Show title and rating before video starts',          //>(translatable)<
                tubepress_app_api_options_Names::EMBEDDED_WIDTH           => 'Max width (px)',                                     //>(translatable)<
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_ON       => 'Scroll page to embedded player after thumbnail click',
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_DURATION => 'Scroll duration (ms)',
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_OFFSET   => 'Scroll offset (px)',
                tubepress_app_api_options_Names::RESPONSIVE_EMBEDS        => 'Responsive embeds',    //>(translatable)<
            ),

            tubepress_app_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(
                tubepress_app_api_options_Names::EMBEDDED_HEIGHT          => sprintf('Default is %s.', 390), //>(translatable)<
                tubepress_app_api_options_Names::EMBEDDED_LAZYPLAY        => 'Auto-play each video after thumbnail click.', //>(translatable)<
                tubepress_app_api_options_Names::EMBEDDED_LOOP            => 'Continue playing the video until the user stops it.', //>(translatable)<
                tubepress_app_api_options_Names::EMBEDDED_PLAYER_IMPL     => 'The brand of the embedded player. Default is the provider\'s player (YouTube, Vimeo, etc).', //>(translatable)<
                tubepress_app_api_options_Names::EMBEDDED_WIDTH           => sprintf('Default is %s.', 640), //>(translatable)<
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_ON       => 'Only applies when the video player is already embedded on the page; i.e. does not apply to modal or popup players.',
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_DURATION => 'Set to 0 for "instant" scroll.',
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_OFFSET   => 'Set to 0 to scroll to the top of the embedded player. Negative or positive values here will scroll to above or below the player, respectively.',
                tubepress_app_api_options_Names::RESPONSIVE_EMBEDS        => 'Auto-resize media players to best fit the viewer\'s screen.', //>(translatable)<
            ),
        ))->addArgument(array(

            tubepress_app_api_options_Reference::PROPERTY_PRO_ONLY => array(
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_ON,
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_OFFSET,
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_DURATION,
                tubepress_app_api_options_Names::RESPONSIVE_EMBEDS,
            ),
        ));

        $toValidate = array(
            tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_INTEGER_POSITIVE => array(
                tubepress_app_api_options_Names::EMBEDDED_HEIGHT,
                tubepress_app_api_options_Names::EMBEDDED_WIDTH,
            ),
            tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_INTEGER_NONNEGATIVE => array(
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_DURATION,
            ),
            tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_INTEGER => array(
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_OFFSET,
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
    }

    private function _registerListeners(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_embedded_common_impl_listeners_EmbeddedListener',
            'tubepress_embedded_common_impl_listeners_EmbeddedListener'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_template_TemplatingInterface::_))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
            'tag'    => 'tubepress_app_api_embedded_EmbeddedProviderInterface',
            'method' => 'setEmbeddedProviders'))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::GALLERY_INIT_JS,
            'priority' => 98000,
            'method'   => 'onGalleryInitJs'))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_app_api_options_Names::EMBEDDED_PLAYER_IMPL,
            'priority' => 100000,
            'method'   => 'onAcceptableValues'))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::TEMPLATE_SELECT . '.single/embedded',
            'priority' => 100000,
            'method'   => 'onEmbeddedTemplateSelect'))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::TEMPLATE_PRE_RENDER . '.single/main',
            'priority' => 100000,
            'method'   => 'onSingleItemTemplatePreRender'))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::TEMPLATE_PRE_RENDER . '.gallery/player/ajax',
            'priority' => 100000,
            'method'   => 'onPlayerTemplatePreRender'))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::TEMPLATE_PRE_RENDER . '.gallery/player/static',
            'priority' => 100000,
            'method'   => 'onPlayerTemplatePreRender'));
    }

    private function _registerOptionsUi(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $fieldReferences = array();
        $fieldMap = array(
            'boolean' => array(
                tubepress_app_api_options_Names::EMBEDDED_LAZYPLAY,
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_ON,
                tubepress_app_api_options_Names::EMBEDDED_SHOW_INFO,
                tubepress_app_api_options_Names::EMBEDDED_AUTOPLAY,
                tubepress_app_api_options_Names::EMBEDDED_LOOP,
                tubepress_app_api_options_Names::RESPONSIVE_EMBEDS,
            ),
            'dropdown' => array(
                tubepress_app_api_options_Names::EMBEDDED_PLAYER_IMPL,
            ),
            'text' => array(
                tubepress_app_api_options_Names::EMBEDDED_HEIGHT,
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_DURATION,
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_OFFSET,
                tubepress_app_api_options_Names::EMBEDDED_WIDTH,
            ),
        );

        foreach ($fieldMap as $type => $ids) {
            foreach ($ids as $id) {

                $serviceId = 'embedded_field_' . $id;

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
        $serviceId = 'embedded_category_' . tubepress_app_api_options_ui_CategoryNames::EMBEDDED;
        $containerBuilder->register(
            $serviceId,
            'tubepress_options_ui_impl_BaseElement'
        )->addArgument(tubepress_app_api_options_ui_CategoryNames::EMBEDDED)
         ->addArgument('Player');                                                           //>(translatable)<

        $categoryReferences[] = new tubepress_platform_api_ioc_Reference($serviceId);


        $fieldMap = array(
            tubepress_app_api_options_ui_CategoryNames::EMBEDDED => array(
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
         ->addTag('tubepress_app_api_options_ui_FieldProviderInterface');
    }
}