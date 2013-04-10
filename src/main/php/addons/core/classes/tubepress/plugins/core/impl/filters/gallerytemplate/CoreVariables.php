<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Applies the embedded service name to the template.
 */
class tubepress_plugins_core_impl_filters_gallerytemplate_CoreVariables
{
    public function onGalleryTemplate(tubepress_api_event_TubePressEvent $event)
    {
        $context          = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $videoGalleryPage = $event->getArgument('videoGalleryPage');
        $template         = $event->getSubject();

        $videoArray  = $videoGalleryPage->getVideos();
        $thumbWidth  = $context->get(tubepress_api_const_options_names_Thumbs::THUMB_WIDTH);
        $thumbHeight = $context->get(tubepress_api_const_options_names_Thumbs::THUMB_HEIGHT);
        $galleryId   = $context->get(tubepress_api_const_options_names_Advanced::GALLERY_ID);

        /* add some core template variables */
        $template->setVariable(tubepress_api_const_template_Variable::VIDEO_ARRAY, $videoArray);
        $template->setVariable(tubepress_api_const_template_Variable::GALLERY_ID, $galleryId);
        $template->setVariable(tubepress_api_const_template_Variable::THUMBNAIL_WIDTH, $thumbWidth);
        $template->setVariable(tubepress_api_const_template_Variable::THUMBNAIL_HEIGHT, $thumbHeight);
    }
}