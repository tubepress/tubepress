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
 * Adds some core variables to the single video template.
 */
class tubepress_addons_core_impl_listeners_template_SingleVideoCoreVariables
{
    public function onSingleVideoTemplate(tubepress_api_event_EventInterface $event)
    {
        $video    = $event->getArgument('video');
        $template = $event->getSubject();

        $context        = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $embedded       = tubepress_impl_patterns_sl_ServiceLocator::getEmbeddedHtmlGenerator();
        $embeddedString = $embedded->getHtml($video->getId());
        $width          = $context->get(tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH);

        /* apply it to the template */
        $template->setVariable(tubepress_api_const_template_Variable::EMBEDDED_SOURCE, $embeddedString);
        $template->setVariable(tubepress_api_const_template_Variable::EMBEDDED_WIDTH, $width);
        $template->setVariable(tubepress_api_const_template_Variable::VIDEO, $video);
    }
}