<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * HTML generation command that generates HTML for a single video + meta info.
 */
class tubepress_plugins_core_impl_shortcode_SearchInputShortcodeHandler implements tubepress_spi_shortcode_ShortcodeHandler
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
        $execContext = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();

        return $execContext->get(tubepress_api_const_options_names_Output::OUTPUT) === tubepress_api_const_options_values_OutputValue::SEARCH_INPUT;
    }

    public function getHtml()
    {
        $th       = tubepress_impl_patterns_ioc_KernelServiceLocator::getThemeHandler();
        $pm       = tubepress_impl_patterns_ioc_KernelServiceLocator::getEventDispatcher();
        $template = $th->getTemplateInstance('search/search_input.tpl.php', TUBEPRESS_ROOT . '/src/main/resources/default-themes/default');

        if ($pm->hasListeners(tubepress_api_const_event_CoreEventNames::SEARCH_INPUT_TEMPLATE_CONSTRUCTION)) {

            $event = new tubepress_api_event_TubePressEvent($template);

            $pm->dispatch(

                tubepress_api_const_event_CoreEventNames::SEARCH_INPUT_TEMPLATE_CONSTRUCTION,
                $event
            );

            $template = $event->getSubject();
        }

        $html = $template->toString();

        if ($pm->hasListeners(tubepress_api_const_event_CoreEventNames::SEARCH_INPUT_HTML_CONSTRUCTION)) {

            $event = new tubepress_api_event_TubePressEvent($html);

            $pm->dispatch(

                tubepress_api_const_event_CoreEventNames::SEARCH_INPUT_HTML_CONSTRUCTION,
                $event
            );

            $html = $event->getSubject();
        }

        return $html;
    }
}
