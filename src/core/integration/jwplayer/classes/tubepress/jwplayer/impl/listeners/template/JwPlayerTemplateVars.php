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
class tubepress_jwplayer_impl_listeners_template_JwPlayerTemplateVars
{
    /**
     * @var tubepress_app_options_api_ContextInterface
     */
    private $_context;

    public function __construct(tubepress_app_options_api_ContextInterface $context)
    {
        $this->_context = $context;
    }

    public function onEmbeddedTemplate(tubepress_lib_event_api_EventInterface $event)
    {
        /**
         * @var $embeddedProvider tubepress_app_embedded_api_EmbeddedProviderInterface
         */
        $embeddedProvider = $event->getArgument('embeddedProvider');

        if (!($embeddedProvider instanceof tubepress_jwplayer_impl_embedded_JwPlayerEmbeddedProvider)) {

            return;
        }

        $template = $event->getSubject();

        $toSet = array(

            tubepress_jwplayer_api_Constants::OPTION_COLOR_FRONT,
            tubepress_jwplayer_api_Constants::OPTION_COLOR_LIGHT,
            tubepress_jwplayer_api_Constants::OPTION_COLOR_SCREEN,
            tubepress_jwplayer_api_Constants::OPTION_COLOR_BACK,
        );

        foreach ($toSet as $optionName) {

            $template->setVariable($optionName, $this->_context->get($optionName));
        }
    }
}