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
class tubepress_core_impl_listeners_template_PlayerLocationCoreVariables
{
    /**
     * @var tubepress_core_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_api_embedded_EmbeddedHtmlInterface
     */
    private $_embeddedHtml;

    public function __construct(tubepress_core_api_options_ContextInterface       $context,
                                tubepress_core_api_embedded_EmbeddedHtmlInterface $embeddedHtml)
    {
        $this->_context      = $context;
        $this->_embeddedHtml = $embeddedHtml;
    }

    public function onPlayerTemplate(tubepress_core_api_event_EventInterface $event)
    {
        $galleryId = $this->_context->get(tubepress_core_api_const_options_Names::GALLERY_ID);

        $template = $event->getSubject();
        $video    = $event->getArgument('video');

        $template->setVariable(tubepress_core_api_const_template_Variable::EMBEDDED_SOURCE, $this->_embeddedHtml->getHtml($video->getId()));
        $template->setVariable(tubepress_core_api_const_template_Variable::GALLERY_ID, $galleryId);
        $template->setVariable(tubepress_core_api_const_template_Variable::VIDEO, $video);
        $template->setVariable(tubepress_core_api_const_template_Variable::EMBEDDED_WIDTH, $this->_context->get(tubepress_core_api_const_options_Names::EMBEDDED_WIDTH));
    }
}
