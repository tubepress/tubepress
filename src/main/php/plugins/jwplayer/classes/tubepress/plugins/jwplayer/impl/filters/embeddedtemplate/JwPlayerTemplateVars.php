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
 * Adds a few JW Player template variables.
 */
class tubepress_plugins_jwplayer_impl_filters_embeddedtemplate_JwPlayerTemplateVars
{
    public function onEmbeddedTemplate(tubepress_api_event_TubePressEvent $event)
    {
        $implName = $event->getArgument('embeddedImplementationName');

        if ($implName !== 'longtail') {

            return;
        }

        $context  = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $template = $event->getSubject();

        $toSet = array(

            tubepress_plugins_jwplayer_api_const_template_Variable::COLOR_FRONT =>
            tubepress_plugins_jwplayer_api_const_options_names_Embedded::COLOR_FRONT,

            tubepress_plugins_jwplayer_api_const_template_Variable::COLOR_LIGHT =>
                tubepress_plugins_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT,

            tubepress_plugins_jwplayer_api_const_template_Variable::COLOR_SCREEN =>
                tubepress_plugins_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN,

            tubepress_plugins_jwplayer_api_const_template_Variable::COLOR_BACK =>
                tubepress_plugins_jwplayer_api_const_options_names_Embedded::COLOR_BACK,
        );

        foreach ($toSet as $templateVariableName => $optionName) {

            $template->setVariable($templateVariableName, $context->get($optionName));
        }
    }
}