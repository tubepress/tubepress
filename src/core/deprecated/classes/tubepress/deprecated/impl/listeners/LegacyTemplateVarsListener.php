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
 * This listener is responsible for populating the template with the following
 * variables:
 *
 * tubepress_api_const_template_Variable::EMBEDDED_IMPL_NAME
 */
class tubepress_deprecated_impl_listeners_LegacyTemplateVarsListener
{
    /**
     * @var tubepress_app_options_api_ContextInterface
     */
    private $_context;

    public function __construct(tubepress_app_options_api_ContextInterface $context)
    {
        $this->_context = $context;
    }

    public function onTemplate(tubepress_lib_event_api_EventInterface $event)
    {
        /**
         * @var $template tubepress_lib_template_api_TemplateInterface
         */
        $template = $event->getSubject();

        $template->setVariable(tubepress_api_const_template_Variable::EMBEDDED_IMPL_NAME,
            $this->_context->get(tubepress_app_embedded_api_Constants::OPTION_PLAYER_IMPL));
    }
}