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

class tubepress_player_ioc_PlayerExtension implements tubepress_spi_ioc_ContainerExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_registerListeners($containerBuilder);
        $this->_registerDefaultPlayers($containerBuilder);
        $this->_registerTemplatePaths($containerBuilder);
        $this->_registerOptions($containerBuilder);
        $this->_registerOptionsUi($containerBuilder);
    }

    private function _registerListeners(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_player_impl_listeners_PlayerAjaxListener',
            'tubepress_player_impl_listeners_PlayerAjaxListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_media_CollectorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_http_ResponseCodeInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
             'event'    => tubepress_api_event_Events::HTTP_AJAX . '.playerHtml',
             'priority' => 100000,
             'method'   => 'onAjax',
        ));

        $containerBuilder->register(
            'tubepress_player_impl_listeners_PlayerListener',
            'tubepress_player_impl_listeners_PlayerListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_))
         ->addTag(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
            'tag'    => 'tubepress_spi_player_PlayerLocationInterface',
            'method' => 'setPlayerLocations', ))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::MEDIA_PAGE_NEW,
            'priority' => 92000,
            'method'   => 'onNewMediaPage', ))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_api_options_Names::PLAYER_LOCATION,
            'priority' => 100000,
            'method'   => 'onAcceptableValues', ))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::TEMPLATE_SELECT . '.gallery/player/static',
            'priority' => 100000,
            'method'   => 'onStaticPlayerTemplateSelection', ))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::TEMPLATE_SELECT . '.gallery/player/ajax',
            'priority' => 100000,
            'method'   => 'onAjaxPlayerTemplateSelection', ))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::TEMPLATE_PRE_RENDER . '.gallery/main',
            'priority' => 94000,
            'method'   => 'onGalleryTemplatePreRender', ))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::GALLERY_INIT_JS,
            'priority' => 96000,
            'method'   => 'onGalleryInitJs', ));

        $containerBuilder->register(
            'tubepress_player_impl_listeners_SoloPlayerListener',
            'tubepress_player_impl_listeners_SoloPlayerListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::HTML_GENERATION,
            'priority' => 98000,
            'method'   => 'onHtmlGeneration',
        ));
    }

    private function _registerDefaultPlayers(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_player_impl_JsPlayerLocation__jqmodal',
            'tubepress_player_impl_JsPlayerLocation'
        )->addArgument(tubepress_api_options_AcceptableValues::PLAYER_LOC_JQMODAL)
         ->addArgument('with jqModal')                                          //>(translatable)<)
         ->addArgument('gallery/players/jqmodal/static')
         ->addArgument('gallery/players/jqmodal/ajax')
         ->addTag('tubepress_spi_player_PlayerLocationInterface');

        $containerBuilder->register(
            'tubepress_player_impl_JsPlayerLocation__normal',
            'tubepress_player_impl_JsPlayerLocation'
        )->addArgument(tubepress_api_options_AcceptableValues::PLAYER_LOC_NORMAL)
         ->addArgument('normally (at the top of your gallery)')                 //>(translatable)<
         ->addArgument('gallery/players/normal/static')
         ->addArgument('gallery/players/normal/ajax')
         ->addTag('tubepress_spi_player_PlayerLocationInterface');

        $containerBuilder->register(
            'tubepress_player_impl_JsPlayerLocation__popup',
            'tubepress_player_impl_JsPlayerLocation'
        )->addArgument(tubepress_api_options_AcceptableValues::PLAYER_LOC_POPUP)
         ->addArgument('in a popup window')                 //>(translatable)<
         ->addArgument('gallery/players/popup/static')
         ->addArgument('gallery/players/popup/ajax')
         ->addTag('tubepress_spi_player_PlayerLocationInterface');

        $containerBuilder->register(
            'tubepress_player_impl_JsPlayerLocation__shadowbox',
            'tubepress_player_impl_JsPlayerLocation'
        )->addArgument(tubepress_api_options_AcceptableValues::PLAYER_LOC_SHADOWBOX)
         ->addArgument('with Shadowbox')                 //>(translatable)<
         ->addArgument('gallery/players/shadowbox/static')
         ->addArgument('gallery/players/shadowbox/ajax')
         ->addTag('tubepress_spi_player_PlayerLocationInterface');

        $containerBuilder->register(
            'tubepress_player_impl_SoloOrStaticPlayerLocation__solo',
            'tubepress_player_impl_SoloOrStaticPlayerLocation'
        )->addArgument(tubepress_api_options_AcceptableValues::PLAYER_LOC_SOLO)
         ->addArgument('in a new window on its own')                 //>(translatable)<
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
         ->addTag('tubepress_spi_player_PlayerLocationInterface');

        $containerBuilder->register(
            'tubepress_player_impl_SoloOrStaticPlayerLocation__static',
            'tubepress_player_impl_SoloOrStaticPlayerLocation'
        )->addArgument(tubepress_api_options_AcceptableValues::PLAYER_LOC_STATIC)
         ->addArgument('statically (page refreshes on each thumbnail click)')                 //>(translatable)<
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
         ->addArgument('gallery/players/static/static')
         ->addTag('tubepress_spi_player_PlayerLocationInterface');
    }

    private function _registerTemplatePaths(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_api_template_BasePathProvider__player',
            'tubepress_api_template_BasePathProvider'
        )->addArgument(array(
            TUBEPRESS_ROOT . '/src/add-ons/player/templates',
        ))->addTag('tubepress_spi_template_PathProviderInterface');
    }

    private function _registerOptions(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_api_options_Reference__player',
            'tubepress_api_options_Reference'
        )->addTag(tubepress_api_options_ReferenceInterface::_)
            ->addArgument(array(

                tubepress_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(

                    tubepress_api_options_Names::PLAYER_LOCATION          => 'normal',
                    tubepress_api_options_Names::EMBEDDED_SCROLL_ON       => true,
                    tubepress_api_options_Names::EMBEDDED_SCROLL_DURATION => 0,
                    tubepress_api_options_Names::EMBEDDED_SCROLL_OFFSET   => 0,
                ),

                tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                    tubepress_api_options_Names::PLAYER_LOCATION          => 'Play each video',                                      //>(translatable)<
                    tubepress_api_options_Names::EMBEDDED_SCROLL_ON       => 'Scroll page to embedded player after thumbnail click', //>(translatable)<
                    tubepress_api_options_Names::EMBEDDED_SCROLL_DURATION => 'Scroll duration (ms)',                                 //>(translatable)<
                    tubepress_api_options_Names::EMBEDDED_SCROLL_OFFSET   => 'Scroll offset (px)',                                   //>(translatable)<
                ),

                tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(
                    tubepress_api_options_Names::EMBEDDED_SCROLL_ON       => 'Only applies when the video player is already embedded on the page; i.e. does not apply to modal or popup players.',  //>(translatable)<
                    tubepress_api_options_Names::EMBEDDED_SCROLL_DURATION => 'Set to 0 for "instant" scroll.',  //>(translatable)<
                    tubepress_api_options_Names::EMBEDDED_SCROLL_OFFSET   => 'Set to 0 to scroll to the top of the embedded player. Negative or positive values here will scroll to above or below the player, respectively.',  //>(translatable)<

                ),
            ))->addArgument(array(

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

        $fieldMap = array(
            tubepress_api_options_ui_CategoryNames::EMBEDDED => array(
                tubepress_api_options_Names::PLAYER_LOCATION,
                tubepress_api_options_Names::EMBEDDED_SCROLL_ON,
                tubepress_api_options_Names::EMBEDDED_SCROLL_DURATION,
                tubepress_api_options_Names::EMBEDDED_SCROLL_OFFSET,
            ),
        );

        $containerBuilder->register(
            'tubepress_api_options_ui_BaseFieldProvider__player',
            'tubepress_api_options_ui_BaseFieldProvider'
        )->addArgument('field-provider-player')
         ->addArgument('Player')
         ->addArgument(false)
         ->addArgument(false)
         ->addArgument(array())
         ->addArgument($fieldReferences)
         ->addArgument($fieldMap)
         ->addTag('tubepress_spi_options_ui_FieldProviderInterface');
    }
}
