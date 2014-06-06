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
 * Applies the embedded service name to the template.
 */
class tubepress_core_html_gallery_impl_listeners_CoreGalleryTemplateListener extends tubepress_core_html_gallery_impl_listeners_AbstractGalleryListener
{
    /**
     * @var tubepress_core_player_api_PlayerHtmlInterface
     */
    private $_playerHtml;

    /**
     * @var tubepress_core_media_provider_api_MediaProviderInterface[]
     */
    private $_mediaProviders;

    /**
     * @var tubepress_core_translation_api_TranslatorInterface
     */
    private $_translator;

    public function __construct(tubepress_core_options_api_ContextInterface        $context,
                                tubepress_core_options_api_ReferenceInterface      $optionReference,
                                tubepress_core_player_api_PlayerHtmlInterface      $playerHtml,
                                tubepress_core_translation_api_TranslatorInterface $translator)
    {
        parent::__construct($context, $optionReference);

        $this->_playerHtml = $playerHtml;
        $this->_translator = $translator;
    }

    public function onGalleryTemplate(tubepress_core_event_api_EventInterface $event)
    {
        /**
         * @var $template tubepress_core_template_api_TemplateInterface
         */
        $template = $event->getSubject();

        $this->_setItemArrayAndGalleryId($event, $template);
        $this->_setThumbnailSizes($template);
        $this->_setPlayerLocationStuff($event, $template);
        $this->_setVideoMetaDisplay($template);
    }

    public function setMediaProviders(array $providers)
    {
        $this->_mediaProviders = $providers;
    }

    private function _setVideoMetaDisplay(tubepress_core_template_api_TemplateInterface $template)
    {
        $metaNames  = $this->_getAllMetaOptionNames();
        $shouldShow = array();
        $labels     = array();

        foreach ($metaNames as $metaName) {

            if (!$this->getOptionReference()->optionExists($metaName)) {

                $shouldShow[$metaName] = false;
                $labels[$metaName]     = '';
                continue;
            }

            $shouldShow[$metaName] = $this->getExecutionContext()->get($metaName);
            $untranslatedLabel     = $this->getOptionReference()->getUntranslatedLabel($metaName);
            $labels[$metaName]     = $this->_translator->_($untranslatedLabel);
        }

        $template->setVariable(tubepress_core_template_api_const_VariableNames::META_SHOULD_SHOW, $shouldShow);
        $template->setVariable(tubepress_core_template_api_const_VariableNames::META_LABELS, $labels);
    }

    private function _setPlayerLocationStuff(tubepress_core_event_api_EventInterface      $event,
                                            tubepress_core_template_api_TemplateInterface $template)
    {
        $playerName     = $this->getExecutionContext()->get(tubepress_core_player_api_Constants::OPTION_PLAYER_LOCATION);
        $player         = $this->findCurrentPlayerLocation();
        $providerResult = $event->getArgument('page');
        $videos         = $providerResult->getItems();
        $galleryId      = $this->getExecutionContext()->get(tubepress_core_html_api_Constants::OPTION_GALLERY_ID);
        $playerHtml     = '';

        if ($player && $player->displaysHtmlOnInitialGalleryLoad()) {

            $playerHtml = $this->_playerHtml->getHtml($videos[0], $galleryId);
        }

        $template->setVariable(tubepress_core_template_api_const_VariableNames::PLAYER_HTML, $playerHtml);
        $template->setVariable(tubepress_core_template_api_const_VariableNames::PLAYER_NAME, $playerName);
    }

    private function _setItemArrayAndGalleryId(tubepress_core_event_api_EventInterface       $event,
                                               tubepress_core_template_api_TemplateInterface $template)
    {
        $videoGalleryPage = $event->getArgument('page');
        $videoArray  = $videoGalleryPage->getItems();
        $galleryId   = $this->getExecutionContext()->get(tubepress_core_html_api_Constants::OPTION_GALLERY_ID);

        $template->setVariable(tubepress_core_template_api_const_VariableNames::VIDEO_ARRAY, $videoArray);
        $template->setVariable(tubepress_core_template_api_const_VariableNames::GALLERY_ID, $galleryId);
    }

    private function _setThumbnailSizes(tubepress_core_template_api_TemplateInterface $template)
    {
        $thumbWidth  = $this->getExecutionContext()->get(tubepress_core_html_gallery_api_Constants::OPTION_THUMB_WIDTH);
        $thumbHeight = $this->getExecutionContext()->get(tubepress_core_html_gallery_api_Constants::OPTION_THUMB_HEIGHT);

        $template->setVariable(tubepress_core_template_api_const_VariableNames::THUMBNAIL_WIDTH, $thumbWidth);
        $template->setVariable(tubepress_core_template_api_const_VariableNames::THUMBNAIL_HEIGHT, $thumbHeight);
    }

    private function _getAllMetaOptionNames()
    {
        $toReturn = array();

        foreach ($this->_mediaProviders as $mediaProvider) {

            $toReturn = array_merge($toReturn, $mediaProvider->getMetaOptionNames());
        }

        return array_unique($toReturn);
    }
}