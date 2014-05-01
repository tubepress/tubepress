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
class tubepress_addons_core_impl_shortcode_SearchInputPluggableShortcodeHandlerService implements tubepress_spi_shortcode_PluggableShortcodeHandlerService
{
    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    public function __construct(tubepress_api_options_ContextInterface $context, tubepress_api_event_EventDispatcherInterface $eventDispatcher)
    {
        $this->_context         = $context;
        $this->_eventDispatcher = $eventDispatcher;
    }

    /**
     * @return string The name of this video provider. Never empty or null. All lowercase alphanumerics and dashes.
     */
    function getName()
    {
        return 'search-input';
    }

    /**
     * @return boolean True if this handler is interested in generating HTML, false otherwise.
     */
    public function shouldExecute()
    {
        return $this->_context->get(tubepress_api_const_options_names_Output::OUTPUT) === tubepress_api_const_options_values_OutputValue::SEARCH_INPUT;
    }

    public function getHtml()
    {
        $th       = tubepress_impl_patterns_sl_ServiceLocator::getThemeHandler();
        $template = $th->getTemplateInstance('search/search_input.tpl.php', TUBEPRESS_ROOT . '/src/main/web/themes/default');

        if ($this->_eventDispatcher->hasListeners(tubepress_api_const_event_EventNames::TEMPLATE_SEARCH_INPUT)) {

            $event = new tubepress_spi_event_EventBase($template);

            $this->_eventDispatcher->dispatch(

                tubepress_api_const_event_EventNames::TEMPLATE_SEARCH_INPUT,
                $event
            );

            $template = $event->getSubject();
        }

        $html = $template->toString();

        if ($this->_eventDispatcher->hasListeners(tubepress_api_const_event_EventNames::HTML_SEARCH_INPUT)) {

            $event = new tubepress_spi_event_EventBase($html);

            $this->_eventDispatcher->dispatch(

                tubepress_api_const_event_EventNames::HTML_SEARCH_INPUT,
                $event
            );

            $html = $event->getSubject();
        }

        return $html;
    }
}
