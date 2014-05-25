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
class tubepress_core_impl_listeners_html_generation_SearchInputListener
{
    /**
     * @var tubepress_core_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_core_api_template_TemplateFactoryInterface
     */
    private $_templateFactory;

    public function __construct(tubepress_core_api_options_ContextInterface          $context,
                                tubepress_core_api_event_EventDispatcherInterface    $eventDispatcher,
                                tubepress_core_api_template_TemplateFactoryInterface $templateFactory)
    {
        $this->_context         = $context;
        $this->_eventDispatcher = $eventDispatcher;
        $this->_templateFactory = $templateFactory;
    }

    public function onHtmlGeneration(tubepress_core_api_event_EventInterface $event)
    {
        if ($this->_context->get(tubepress_core_api_const_options_Names::OUTPUT) !== tubepress_core_api_const_options_ValidValues::OUTPUT_SEARCH_INPUT) {

            return;
        }

        $template = $this->_templateFactory->fromFilesystem(array(

            'search/search_input.tpl.php',
            TUBEPRESS_ROOT . '/src/main/web/themes/default/search/search_input.tpl.php'
        ));

        $templateEvent = $this->_eventDispatcher->newEventInstance($template);

        $this->_eventDispatcher->dispatch(tubepress_core_api_const_event_EventNames::TEMPLATE_SEARCH_INPUT, $templateEvent);

        $template  = $templateEvent->getSubject();
        $html      = $template->toString();
        $htmlEvent = $this->_eventDispatcher->newEventInstance($html);

        $this->_eventDispatcher->dispatch(tubepress_core_api_const_event_EventNames::HTML_SEARCH_INPUT, $htmlEvent);

        $html = $htmlEvent->getSubject();

        $event->setSubject($html);
        $event->stopPropagation();
    }
}