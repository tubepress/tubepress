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
class tubepress_core_html_gallery_impl_listeners_template_CoreVariables
{
    /**
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    public function __construct(tubepress_core_options_api_ContextInterface $context)
    {
        $this->_context = $context;
    }

    public function onGalleryTemplate(tubepress_core_event_api_EventInterface $event)
    {
        $videoGalleryPage = $event->getArgument('page');
        $template         = $event->getSubject();

        $videoArray  = $videoGalleryPage->getItems();
        $thumbWidth  = $this->_context->get(tubepress_core_html_gallery_api_Constants::OPTION_THUMB_WIDTH);
        $thumbHeight = $this->_context->get(tubepress_core_html_gallery_api_Constants::OPTION_THUMB_HEIGHT);
        $galleryId   = $this->_context->get(tubepress_core_html_api_Constants::OPTION_GALLERY_ID);

        /* add some core template variables */
        $template->setVariable(tubepress_core_template_api_const_VariableNames::VIDEO_ARRAY, $videoArray);
        $template->setVariable(tubepress_core_template_api_const_VariableNames::GALLERY_ID, $galleryId);
        $template->setVariable(tubepress_core_template_api_const_VariableNames::THUMBNAIL_WIDTH, $thumbWidth);
        $template->setVariable(tubepress_core_template_api_const_VariableNames::THUMBNAIL_HEIGHT, $thumbHeight);
    }
}