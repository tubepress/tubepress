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
 * @covers tubepress_embedded_common_ioc_EmbeddedCommonExtension
 */
class tubepress_test_embedded_common_ioc_EmbeddedCommonExtensionTest extends tubepress_api_test_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_embedded_common_ioc_EmbeddedCommonExtension
     */
    protected function buildSut()
    {
        return  new tubepress_embedded_common_ioc_EmbeddedCommonExtension();
    }

    protected function prepareForLoad()
    {
        $this->_registerListeners();
        $this->_registerOptions();
        $this->_registerOptionsUi();
    }

    private function _registerOptions()
    {
        $this->expectRegistration(
            'tubepress_api_options_Reference__embedded_common',
            'tubepress_api_options_Reference'
        )->withTag(tubepress_api_options_ReferenceInterface::_)
            ->withArgument(array(

                tubepress_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                    tubepress_api_options_Names::EMBEDDED_AUTOPLAY        => false,
                    tubepress_api_options_Names::EMBEDDED_HEIGHT          => 390,
                    tubepress_api_options_Names::EMBEDDED_LAZYPLAY        => true,
                    tubepress_api_options_Names::EMBEDDED_LOOP            => false,
                    tubepress_api_options_Names::EMBEDDED_PLAYER_IMPL     => tubepress_api_options_AcceptableValues::EMBEDDED_IMPL_PROVIDER_BASED,
                    tubepress_api_options_Names::EMBEDDED_SHOW_INFO       => false,
                    tubepress_api_options_Names::EMBEDDED_WIDTH           => 640,
                    tubepress_api_options_Names::RESPONSIVE_EMBEDS        => true,
                ),

                tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                    tubepress_api_options_Names::EMBEDDED_AUTOPLAY        => 'Auto-play all videos',                               //>(translatable)<
                    tubepress_api_options_Names::EMBEDDED_HEIGHT          => 'Max height (px)',                                    //>(translatable)<
                    tubepress_api_options_Names::EMBEDDED_LAZYPLAY        => '"Lazy" play videos',                                 //>(translatable)<
                    tubepress_api_options_Names::EMBEDDED_LOOP            => 'Loop',                                               //>(translatable)<
                    tubepress_api_options_Names::EMBEDDED_PLAYER_IMPL     => 'Implementation',                                     //>(translatable)<
                    tubepress_api_options_Names::EMBEDDED_SHOW_INFO       => 'Show title and rating before video starts',          //>(translatable)<
                    tubepress_api_options_Names::EMBEDDED_WIDTH           => 'Max width (px)',                                     //>(translatable)<
                    tubepress_api_options_Names::RESPONSIVE_EMBEDS        => 'Responsive embeds',    //>(translatable)<
                ),

                tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(
                    tubepress_api_options_Names::EMBEDDED_HEIGHT          => sprintf('Default is %s.', 390), //>(translatable)<
                    tubepress_api_options_Names::EMBEDDED_LAZYPLAY        => 'Auto-play each video after thumbnail click.', //>(translatable)<
                    tubepress_api_options_Names::EMBEDDED_LOOP            => 'Continue playing the video until the user stops it.', //>(translatable)<
                    tubepress_api_options_Names::EMBEDDED_PLAYER_IMPL     => 'The brand of the embedded player. Default is the provider\'s player (YouTube, Vimeo, etc).', //>(translatable)<
                    tubepress_api_options_Names::EMBEDDED_WIDTH           => sprintf('Default is %s.', 640), //>(translatable)<
                    tubepress_api_options_Names::RESPONSIVE_EMBEDS        => 'Auto-resize media players to best fit the viewer\'s screen.', //>(translatable)<
                ),
            ))->withArgument(array(

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

    private function _registerListeners()
    {
        $this->expectRegistration(
            'tubepress_embedded_common_impl_listeners_EmbeddedListener',
            'tubepress_embedded_common_impl_listeners_EmbeddedListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
                'tag'    => 'tubepress_spi_embedded_EmbeddedProviderInterface',
                'method' => 'setEmbeddedProviders'))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::GALLERY_INIT_JS,
                'priority' => 98000,
                'method'   => 'onGalleryInitJs'))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_api_options_Names::EMBEDDED_PLAYER_IMPL,
                'priority' => 100000,
                'method'   => 'onAcceptableValues'))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::TEMPLATE_SELECT . '.single/embedded',
                'priority' => 100000,
                'method'   => 'onEmbeddedTemplateSelect'))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::TEMPLATE_PRE_RENDER . '.single/main',
                'priority' => 100000,
                'method'   => 'onSingleItemTemplatePreRender'))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::TEMPLATE_PRE_RENDER . '.gallery/player/ajax',
                'priority' => 100000,
                'method'   => 'onPlayerTemplatePreRender'))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::TEMPLATE_PRE_RENDER . '.gallery/player/static',
                'priority' => 100000,
                'method'   => 'onPlayerTemplatePreRender'));
    }

    private function _registerOptionsUi()
    {
        $fieldReferences = array();
        $fieldMap = array(
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
        $serviceId = 'embedded_category_' . tubepress_api_options_ui_CategoryNames::EMBEDDED;
        $this->expectRegistration(
            $serviceId,
            'tubepress_options_ui_impl_BaseElement'
        )->withArgument(tubepress_api_options_ui_CategoryNames::EMBEDDED)
            ->withArgument('Player');                                                           //>(translatable)<

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

        $this->expectRegistration(
            'tubepress_api_options_ui_BaseFieldProvider__embedded_common',
            'tubepress_api_options_ui_BaseFieldProvider'
        )->withArgument('field-provider-embedded-common')
            ->withArgument('Embedded')
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
            tubepress_api_template_TemplatingInterface::_     => tubepress_api_template_TemplatingInterface::_,
            tubepress_api_options_ui_FieldBuilderInterface::_ => $fieldBuilder,
            tubepress_api_options_ReferenceInterface::_       => tubepress_api_options_ReferenceInterface::_,
            tubepress_api_translation_TranslatorInterface::_  => tubepress_api_translation_TranslatorInterface::_,
        );
    }
}
