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
 * Adds some core variables to the single video template.
 */
class tubepress_plugins_core_impl_filters_singlevideotemplate_CoreVariables
{
    public function onSingleVideoTemplate(tubepress_api_event_TubePressEvent $event)
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