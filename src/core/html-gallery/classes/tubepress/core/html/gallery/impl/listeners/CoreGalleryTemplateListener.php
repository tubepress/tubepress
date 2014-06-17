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

    public function __construct(tubepress_core_options_api_ContextInterface   $context,
                                tubepress_core_options_api_ReferenceInterface $optionReference,
                                tubepress_core_player_api_PlayerHtmlInterface $playerHtml)
    {
        parent::__construct($context, $optionReference);

        $this->_playerHtml = $playerHtml;
    }

    public function onGalleryTemplate(tubepress_core_event_api_EventInterface $event)
    {
        /**
         * @var $template tubepress_core_template_api_TemplateInterface
         */
        $template = $event->getSubject();

        //shim for old templates :(
        $template->setVariable(tubepress_core_embedded_api_Constants::TEMPLATE_VAR_IMPL_NAME, 'x');

        $this->_setItemArrayAndGalleryId($event, $template);
        $this->_setThumbnailSizes($template);
        $this->_setPlayerLocationStuff($event, $template);
    }

    private function _setPlayerLocationStuff(tubepress_core_event_api_EventInterface      $event,
                                            tubepress_core_template_api_TemplateInterface $template)
    {
        $playerName     = $this->getExecutionContext()->get(tubepress_core_player_api_Constants::OPTION_PLAYER_LOCATION);
        $player         = $this->findCurrentPlayerLocation();
        $providerResult = $event->getArgument('page');
        $mediaItems     = $providerResult->getItems();
        $galleryId      = $this->getExecutionContext()->get(tubepress_core_html_api_Constants::OPTION_GALLERY_ID);
        $playerHtml     = '';

        if ($player && $player->displaysHtmlOnInitialGalleryLoad()) {

            $playerHtml = $this->_playerHtml->getHtml($mediaItems[0], $galleryId);
        }

        $template->setVariable(tubepress_core_player_api_Constants::TEMPLATE_VAR_HTML, $playerHtml);
        $template->setVariable(tubepress_core_player_api_Constants::TEMPLATE_VAR_NAME, $playerName);
    }

    private function _setItemArrayAndGalleryId(tubepress_core_event_api_EventInterface       $event,
                                               tubepress_core_template_api_TemplateInterface $template)
    {
        $mediaItemPage = $event->getArgument('page');
        $mediaItems    = $mediaItemPage->getItems();
        $galleryId     = $this->getExecutionContext()->get(tubepress_core_html_api_Constants::OPTION_GALLERY_ID);

        $template->setVariable(tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_MEDIA_ITEM_ARRAY, $mediaItems);
        $template->setVariable(tubepress_core_html_api_Constants::TEMPLATE_VAR_GALLERY_ID, $galleryId);
    }

    private function _setThumbnailSizes(tubepress_core_template_api_TemplateInterface $template)
    {
        $thumbWidth  = $this->getExecutionContext()->get(tubepress_core_html_gallery_api_Constants::OPTION_THUMB_WIDTH);
        $thumbHeight = $this->getExecutionContext()->get(tubepress_core_html_gallery_api_Constants::OPTION_THUMB_HEIGHT);

        $template->setVariable(tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_THUMBNAIL_WIDTH, $thumbWidth);
        $template->setVariable(tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_THUMBNAIL_HEIGHT, $thumbHeight);
    }
}