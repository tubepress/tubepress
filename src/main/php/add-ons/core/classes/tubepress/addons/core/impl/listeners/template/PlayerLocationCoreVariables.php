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
class tubepress_addons_core_impl_listeners_template_PlayerLocationCoreVariables
{
    public function onPlayerTemplate(tubepress_api_event_EventInterface $event)
    {
        $embedded  = tubepress_impl_patterns_sl_ServiceLocator::getEmbeddedHtmlGenerator();
        $context   = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $galleryId = $context->get(tubepress_api_const_options_names_Advanced::GALLERY_ID);

        $template = $event->getSubject();
        $video    = $event->getArgument('video');

        $template->setVariable(tubepress_api_const_template_Variable::EMBEDDED_SOURCE, $embedded->getHtml($video->getId()));
        $template->setVariable(tubepress_api_const_template_Variable::GALLERY_ID, $galleryId);
        $template->setVariable(tubepress_api_const_template_Variable::VIDEO, $video);
        $template->setVariable(tubepress_api_const_template_Variable::EMBEDDED_WIDTH, $context->get(tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH));
    }
}
