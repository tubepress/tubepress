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
 * HTML generation command that generates HTML for a single video + meta info.
 */
class tubepress_app_impl_listeners_search_html_SearchInputListener
{
    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_lib_api_template_TemplatingInterface
     */
    private $_templating;

    public function __construct(tubepress_app_api_options_ContextInterface     $context,
                                tubepress_lib_api_template_TemplatingInterface $templating)
    {
        $this->_context    = $context;
        $this->_templating = $templating;
    }

    public function onHtmlGeneration(tubepress_lib_api_event_EventInterface $event)
    {
        if ($this->_context->get(tubepress_app_api_options_Names::HTML_OUTPUT) !== tubepress_app_api_options_AcceptableValues::OUTPUT_SEARCH_INPUT) {

            return;
        }

        $html = $this->_templating->renderTemplate('search/input', array());

        $event->setSubject($html);
        $event->stopPropagation();
    }
}