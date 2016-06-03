<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_wordpress_impl_listeners_wpaction_UpdateMessageListener
{
    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_api_template_TemplatingInterface
     */
    private $_templating;

    public function __construct(tubepress_api_options_ContextInterface     $context,
                                tubepress_api_template_TemplatingInterface $templating)
    {
        $this->_context    = $context;
        $this->_templating = $templating;
    }

    public function onAction_in_plugin_update_message(tubepress_api_event_EventInterface $event)
    {
        if (!$this->_context->get(tubepress_api_options_Names::TUBEPRESS_API_KEY)) {

            $html = $this->_templating->renderTemplate('wordpress/in-update-message');

            echo $html;
        }
    }
}
