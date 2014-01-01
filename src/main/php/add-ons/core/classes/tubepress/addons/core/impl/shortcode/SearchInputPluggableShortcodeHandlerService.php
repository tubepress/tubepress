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
     * @return string The name of this video provider. Never empty or null. All lowercase alphanumerics and dashes.
     */
    function getName()
    {
        return 'search-input';
    }

    /**
     * @return boolean True if this handler is interested in generating HTML, false otherwise.
     */
    function shouldExecute()
    {
        $execContext = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();

        return $execContext->get(tubepress_api_const_options_names_Output::OUTPUT) === tubepress_api_const_options_values_OutputValue::SEARCH_INPUT;
    }

    public function getHtml()
    {
        $th       = tubepress_impl_patterns_sl_ServiceLocator::getThemeHandler();
        $pm       = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();
        $template = $th->getTemplateInstance('search/search_input.tpl.php', TUBEPRESS_ROOT . '/src/main/resources/default-themes/default');

        if ($pm->hasListeners(tubepress_api_const_event_EventNames::TEMPLATE_SEARCH_INPUT)) {

            $event = new tubepress_spi_event_EventBase($template);

            $pm->dispatch(

                tubepress_api_const_event_EventNames::TEMPLATE_SEARCH_INPUT,
                $event
            );

            $template = $event->getSubject();
        }

        $html = $template->toString();

        if ($pm->hasListeners(tubepress_api_const_event_EventNames::HTML_SEARCH_INPUT)) {

            $event = new tubepress_spi_event_EventBase($html);

            $pm->dispatch(

                tubepress_api_const_event_EventNames::HTML_SEARCH_INPUT,
                $event
            );

            $html = $event->getSubject();
        }

        return $html;
    }
}
