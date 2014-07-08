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
 * Applies core player template variables.
 */
class tubepress_app_player_impl_listeners_template_PlayerTemplateListener
{
    /**
     * @var tubepress_app_options_api_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_app_embedded_api_EmbeddedHtmlInterface
     */
    private $_embeddedHtml;

    public function __construct(tubepress_app_options_api_ContextInterface       $context,
                                tubepress_app_embedded_api_EmbeddedHtmlInterface $embeddedHtml)
    {
        $this->_context      = $context;
        $this->_embeddedHtml = $embeddedHtml;
    }

    public function onPlayerTemplate(tubepress_lib_event_api_EventInterface $event)
    {
        $galleryId = $this->_context->get(tubepress_app_html_api_Constants::OPTION_GALLERY_ID);

        /**
         * @var $template tubepress_lib_template_api_TemplateInterface
         */
        $template  = $event->getSubject();
        $mediaItem = $event->getArgument('item');
        $toSet     = array(
            tubepress_app_html_api_Constants::TEMPLATE_VAR_GALLERY_ID        => $galleryId,
            tubepress_app_feature_single_api_Constants::TEMPLATE_VAR_MEDIA_ITEM => $mediaItem,
            tubepress_app_embedded_api_Constants::TEMPLATE_VAR_SOURCE        => $this->_embeddedHtml->getHtml($mediaItem->getId()),
            tubepress_app_embedded_api_Constants::TEMPLATE_VAR_WIDTH         => $this->_context->get(tubepress_app_embedded_api_Constants::OPTION_EMBEDDED_WIDTH),
        );

        foreach ($toSet as $name => $value) {

            $template->setVariable($name, $value);
        }
    }
}
