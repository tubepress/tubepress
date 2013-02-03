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
 * Applies core player template variables.
 */
class tubepress_plugins_core_impl_filters_playertemplate_CoreVariables
{
    public function onPlayerTemplate(tubepress_api_event_TubePressEvent $event)
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
