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
 * Adds a few JW Player template variables.
 */
class tubepress_addons_jwplayer_impl_listeners_template_JwPlayerTemplateVars
{
    public function onEmbeddedTemplate(tubepress_api_event_EventInterface $event)
    {
        $implName = $event->getArgument('embeddedImplementationName');

        if ($implName !== 'longtail') {

            return;
        }

        $context  = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $template = $event->getSubject();

        $toSet = array(

            tubepress_addons_jwplayer_api_const_template_Variable::COLOR_FRONT =>
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_FRONT,

            tubepress_addons_jwplayer_api_const_template_Variable::COLOR_LIGHT =>
                tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT,

            tubepress_addons_jwplayer_api_const_template_Variable::COLOR_SCREEN =>
                tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN,

            tubepress_addons_jwplayer_api_const_template_Variable::COLOR_BACK =>
                tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_BACK,
        );

        foreach ($toSet as $templateVariableName => $optionName) {

            $template->setVariable($templateVariableName, $context->get($optionName));
        }
    }
}