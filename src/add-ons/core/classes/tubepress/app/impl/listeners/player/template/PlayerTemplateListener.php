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
class tubepress_app_impl_listeners_player_template_PlayerTemplateListener
{
    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    public function __construct(tubepress_app_api_options_ContextInterface $context)
    {
        $this->_context = $context;
    }

    public function onPlayerTemplatePreRender(tubepress_lib_api_event_EventInterface $event)
    {
        $galleryId = $this->_context->get(tubepress_app_api_options_Names::HTML_GALLERY_ID);

        /**
         * @var $existingVars array
         */
        $existingVars = $event->getSubject();
        $mediaItem    = $event->getArgument('item');
        $toSet        = array(
            tubepress_app_api_template_VariableNames::HTML_WIDGET_ID    => $galleryId,
            tubepress_app_api_template_VariableNames::MEDIA_ITEM        => $mediaItem,
            tubepress_app_api_template_VariableNames::EMBEDDED_WIDTH_PX => $this->_context->get(tubepress_app_api_options_Names::EMBEDDED_WIDTH),
        );

        $existingVars = array_merge($existingVars, $toSet);

        $event->setSubject($existingVars);
    }
}
