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
 * @covers tubepress_player_ioc_PlayerExtension
 */
class tubepress_test_player_ioc_PlayerExtensionTest extends tubepress_api_test_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_player_ioc_PlayerExtension
     */
    protected function buildSut()
    {
        return  new tubepress_player_ioc_PlayerExtension();
    }

    protected function prepareForLoad()
    {
        $this->_registerListeners();
        $this->_registerDefaultPlayers();
        $this->_registerTemplatePaths();
        $this->_registerOptions();
        $this->_registerOptionsUi();
    }

    private function _registerListeners()
    {
        $this->expectRegistration(
            'tubepress_player_impl_listeners_PlayerAjaxListener',
            'tubepress_player_impl_listeners_PlayerAjaxListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_media_CollectorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_http_ResponseCodeInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::HTTP_AJAX . '.playerHtml',
                'priority' => 100000,
                'method'   => 'onAjax',
            ));

        $this->expectRegistration(
            'tubepress_player_impl_listeners_PlayerListener',
            'tubepress_player_impl_listeners_PlayerListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
                'tag'    => 'tubepress_spi_player_PlayerLocationInterface',
                'method' => 'setPlayerLocations', ))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::MEDIA_PAGE_NEW,
                'priority' => 92000,
                'method'   => 'onNewMediaPage', ))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_api_options_Names::PLAYER_LOCATION,
                'priority' => 100000,
                'method'   => 'onAcceptableValues', ))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::TEMPLATE_SELECT . '.gallery/player/static',
                'priority' => 100000,
                'method'   => 'onStaticPlayerTemplateSelection', ))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::TEMPLATE_SELECT . '.gallery/player/ajax',
                'priority' => 100000,
                'method'   => 'onAjaxPlayerTemplateSelection', ))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::TEMPLATE_PRE_RENDER . '.gallery/main',
                'priority' => 94000,
                'method'   => 'onGalleryTemplatePreRender', ))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::GALLERY_INIT_JS,
                'priority' => 96000,
                'method'   => 'onGalleryInitJs', ));

        $this->expectRegistration(
            'tubepress_player_impl_listeners_SoloPlayerListener',
            'tubepress_player_impl_listeners_SoloPlayerListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::HTML_GENERATION,
                'priority' => 98000,
                'method'   => 'onHtmlGeneration',
            ));
    }

    private function _registerDefaultPlayers()
    {
        $this->expectRegistration(
            'tubepress_player_impl_JsPlayerLocation__jqmodal',
            'tubepress_player_impl_JsPlayerLocation'
        )->withArgument(tubepress_api_options_AcceptableValues::PLAYER_LOC_JQMODAL)
            ->withArgument('with jqModal')
            ->withArgument('gallery/players/jqmodal/static')
            ->withArgument('gallery/players/jqmodal/ajax')
            ->withTag('tubepress_spi_player_PlayerLocationInterface');

        $this->expectRegistration(
            'tubepress_player_impl_JsPlayerLocation__normal',
            'tubepress_player_impl_JsPlayerLocation'
        )->withArgument(tubepress_api_options_AcceptableValues::PLAYER_LOC_NORMAL)
            ->withArgument('normally (at the top of your gallery)')
            ->withArgument('gallery/players/normal/static')
            ->withArgument('gallery/players/normal/ajax')
            ->withTag('tubepress_spi_player_PlayerLocationInterface');

        $this->expectRegistration(
            'tubepress_player_impl_JsPlayerLocation__popup',
            'tubepress_player_impl_JsPlayerLocation'
        )->withArgument(tubepress_api_options_AcceptableValues::PLAYER_LOC_POPUP)
            ->withArgument('in a popup window')
            ->withArgument('gallery/players/popup/static')
            ->withArgument('gallery/players/popup/ajax')
            ->withTag('tubepress_spi_player_PlayerLocationInterface');

        $this->expectRegistration(
            'tubepress_player_impl_JsPlayerLocation__shadowbox',
            'tubepress_player_impl_JsPlayerLocation'
        )->withArgument(tubepress_api_options_AcceptableValues::PLAYER_LOC_SHADOWBOX)
            ->withArgument('with Shadowbox')
            ->withArgument('gallery/players/shadowbox/static')
            ->withArgument('gallery/players/shadowbox/ajax')
            ->withTag('tubepress_spi_player_PlayerLocationInterface');

        $this->expectRegistration(
            'tubepress_player_impl_SoloOrStaticPlayerLocation__solo',
            'tubepress_player_impl_SoloOrStaticPlayerLocation'
        )->withArgument(tubepress_api_options_AcceptableValues::PLAYER_LOC_SOLO)
            ->withArgument('in a new window on its own')
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
            ->withTag('tubepress_spi_player_PlayerLocationInterface');

        $this->expectRegistration(
            'tubepress_player_impl_SoloOrStaticPlayerLocation__static',
            'tubepress_player_impl_SoloOrStaticPlayerLocation'
        )->withArgument(tubepress_api_options_AcceptableValues::PLAYER_LOC_STATIC)
            ->withArgument('statically (page refreshes on each thumbnail click)')
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
            ->withArgument('gallery/players/static/static')
            ->withTag('tubepress_spi_player_PlayerLocationInterface');
    }

    private function _registerTemplatePaths()
    {
        $this->expectRegistration(
            'tubepress_api_template_BasePathProvider__player',
            'tubepress_api_template_BasePathProvider'
        )->withArgument(array(
            TUBEPRESS_ROOT . '/src/add-ons/player/templates',
        ))->withTag('tubepress_spi_template_PathProviderInterface');
    }

    private function _registerOptions()
    {
        $this->expectRegistration(
            'tubepress_api_options_Reference__player',
            'tubepress_api_options_Reference'
        )->withTag(tubepress_api_options_ReferenceInterface::_)
            ->withArgument(array(

                tubepress_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(

                    tubepress_api_options_Names::PLAYER_LOCATION          => 'normal',
                    tubepress_api_options_Names::EMBEDDED_SCROLL_ON       => true,
                    tubepress_api_options_Names::EMBEDDED_SCROLL_DURATION => 0,
                    tubepress_api_options_Names::EMBEDDED_SCROLL_OFFSET   => 0,
                ),

                tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                    tubepress_api_options_Names::PLAYER_LOCATION          => 'Play each video',
                    tubepress_api_options_Names::EMBEDDED_SCROLL_ON       => 'Scroll page to embedded player after thumbnail click',
                    tubepress_api_options_Names::EMBEDDED_SCROLL_DURATION => 'Scroll duration (ms)',
                    tubepress_api_options_Names::EMBEDDED_SCROLL_OFFSET   => 'Scroll offset (px)',
                ),

                tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(
                    tubepress_api_options_Names::EMBEDDED_SCROLL_ON       => 'Only applies when the video player is already embedded on the page; i.e. does not apply to modal or popup players.',
                    tubepress_api_options_Names::EMBEDDED_SCROLL_DURATION => 'Set to 0 for "instant" scroll.',
                    tubepress_api_options_Names::EMBEDDED_SCROLL_OFFSET   => 'Set to 0 to scroll to the top of the embedded player. Negative or positive values here will scroll to above or below the player, respectively.',

                ),
            ))->withArgument(array(

                tubepress_api_options_Reference::PROPERTY_PRO_ONLY => array(
                    tubepress_api_options_Names::EMBEDDED_SCROLL_ON,
                    tubepress_api_options_Names::EMBEDDED_SCROLL_OFFSET,
                    tubepress_api_options_Names::EMBEDDED_SCROLL_DURATION,
                ),
            ));

        $toValidate = array(
            tubepress_api_options_listeners_RegexValidatingListener::TYPE_INTEGER_NONNEGATIVE => array(
                tubepress_api_options_Names::EMBEDDED_SCROLL_DURATION,
            ),
            tubepress_api_options_listeners_RegexValidatingListener::TYPE_INTEGER => array(
                tubepress_api_options_Names::EMBEDDED_SCROLL_OFFSET,
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
                tubepress_api_options_Names::EMBEDDED_SCROLL_ON,
            ),
            'dropdown' => array(
                tubepress_api_options_Names::PLAYER_LOCATION,
            ),
            'text' => array(
                tubepress_api_options_Names::EMBEDDED_SCROLL_DURATION,
                tubepress_api_options_Names::EMBEDDED_SCROLL_OFFSET,
            ),
        );

        foreach ($fieldMap as $type => $ids) {
            foreach ($ids as $id) {

                $serviceId = 'player_field_' . $id;

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
            tubepress_api_options_ui_CategoryNames::EMBEDDED => array(
                tubepress_api_options_Names::PLAYER_LOCATION,
                tubepress_api_options_Names::EMBEDDED_SCROLL_ON,
                tubepress_api_options_Names::EMBEDDED_SCROLL_DURATION,
                tubepress_api_options_Names::EMBEDDED_SCROLL_OFFSET,
            ),
        );

        $this->expectRegistration(
            'tubepress_api_options_ui_BaseFieldProvider__player',
            'tubepress_api_options_ui_BaseFieldProvider'
        )->withArgument('field-provider-player')
            ->withArgument('Player')
            ->withArgument(false)
            ->withArgument(false)
            ->withArgument(array())
            ->withArgument($fieldReferences)
            ->withArgument($fieldMap)
            ->withTag('tubepress_spi_options_ui_FieldProviderInterface');
    }

    protected function getExpectedExternalServicesMap()
    {
        $mockCurrentUrl = $this->mock(tubepress_api_url_UrlInterface::_);
        $mockCurrentUrl->shouldReceive('removeSchemeAndAuthority');

        $mockUrlFactory = $this->mock(tubepress_api_url_UrlFactoryInterface::_);
        $mockUrlFactory->shouldReceive('fromCurrent')->atLeast(1)->andReturn($mockCurrentUrl);

        $fieldBuilder = $this->mock(tubepress_api_options_ui_FieldBuilderInterface::_);
        $mockField    = $this->mock('tubepress_api_options_ui_FieldInterface');
        $fieldBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockField);

        return array(
            tubepress_api_log_LoggerInterface::_              => tubepress_api_log_LoggerInterface::_,
            tubepress_api_options_ContextInterface::_         => tubepress_api_options_ContextInterface::_,
            tubepress_api_media_CollectorInterface::_         => tubepress_api_media_CollectorInterface::_,
            tubepress_api_http_RequestParametersInterface::_  => tubepress_api_http_RequestParametersInterface::_,
            tubepress_api_http_ResponseCodeInterface::_       => tubepress_api_http_ResponseCodeInterface::_,
            tubepress_api_template_TemplatingInterface::_     => tubepress_api_template_TemplatingInterface::_,
            tubepress_api_url_UrlFactoryInterface::_          => $mockUrlFactory,
            tubepress_api_options_ui_FieldBuilderInterface::_ => $fieldBuilder,
            tubepress_api_options_ReferenceInterface::_       => tubepress_api_options_ReferenceInterface::_,
            tubepress_api_translation_TranslatorInterface::_  => tubepress_api_translation_TranslatorInterface::_,
        );
    }
}
