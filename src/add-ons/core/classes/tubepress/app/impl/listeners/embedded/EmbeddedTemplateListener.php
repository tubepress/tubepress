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
 * Core variables for the embedded template.
 */
class tubepress_app_impl_listeners_embedded_EmbeddedTemplateListener
{
    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    public function __construct(tubepress_app_api_options_ContextInterface $context)
    {
        $this->_context = $context;
    }

    public function onEmbeddedTemplatePreRender(tubepress_lib_api_event_EventInterface $event)
    {
        /**
         * @var $existingVars array
         */
        $existingVars = $event->getSubject();
        $dataUrl      = $event->getArgument('dataUrl');

        $embedWidth  = $this->_context->get(tubepress_app_api_options_Names::EMBEDDED_WIDTH);
        $embedHeight = $this->_context->get(tubepress_app_api_options_Names::EMBEDDED_HEIGHT);

        $vars = array(

            tubepress_app_api_template_VariableNames::EMBEDDED_DATA_URL  => $dataUrl,
            tubepress_app_api_template_VariableNames::EMBEDDED_WIDTH_PX  => $embedWidth,
            tubepress_app_api_template_VariableNames::EMBEDDED_HEIGHT_PX => $embedHeight,
        );

        $existingVars = array_merge($existingVars, $vars);

        $event->setSubject($existingVars);
    }
}