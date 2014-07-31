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
class tubepress_jwplayer5_impl_listeners_template_JwPlayerTemplateVars
{
    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    public function __construct(tubepress_app_api_options_ContextInterface $context)
    {
        $this->_context = $context;
    }

    public function onEmbeddedTemplate(tubepress_lib_api_event_EventInterface $event)
    {
        $embeddedProvider = $event->getArgument('embeddedProvider');

        if (!($embeddedProvider instanceof tubepress_jwplayer5_impl_listeners_embedded_EmbeddedListener)) {

            return;
        }

        $template = $event->getSubject();

        $toSet = array(

            tubepress_jwplayer5_api_OptionNames::COLOR_FRONT,
            tubepress_jwplayer5_api_OptionNames::COLOR_LIGHT,
            tubepress_jwplayer5_api_OptionNames::COLOR_SCREEN,
            tubepress_jwplayer5_api_OptionNames::COLOR_BACK,
        );

        foreach ($toSet as $optionName) {

            $template->setVariable($optionName, $this->_context->get($optionName));
        }
    }
}