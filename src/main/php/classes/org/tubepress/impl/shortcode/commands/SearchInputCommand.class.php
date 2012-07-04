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

org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_options_names_Output',
    'org_tubepress_api_const_options_values_OutputValue',
    'org_tubepress_api_const_plugin_FilterPoint',
    'org_tubepress_spi_patterns_cor_Command',
    'org_tubepress_api_plugin_PluginManager',
    'org_tubepress_api_theme_ThemeHandler',
    'org_tubepress_impl_ioc_IocContainer',
));

/**
 * HTML generation command that generates HTML for a single video + meta info.
 */
class org_tubepress_impl_shortcode_commands_SearchInputCommand implements org_tubepress_spi_patterns_cor_Command
{
    const LOG_PREFIX = 'Search Input Command';

    /**
     * Execute the command.
     *
     * @return unknown The result of this command execution.
     */
    public function execute($context)
    {
        $ioc         = org_tubepress_impl_ioc_IocContainer::getInstance();
        $execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);

        if ($execContext->get(org_tubepress_api_const_options_names_Output::OUTPUT) !== org_tubepress_api_const_options_values_OutputValue::SEARCH_INPUT) {
            return false;
        }

        $th       = $ioc->get(org_tubepress_api_theme_ThemeHandler::_);
        $pm       = $ioc->get(org_tubepress_api_plugin_PluginManager::_);
        $template = $th->getTemplateInstance($this->getTemplatePath());

        $template = $pm->runFilters(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_SEARCHINPUT, $template);
        $html     = $pm->runFilters(org_tubepress_api_const_plugin_FilterPoint::HTML_SEARCHINPUT, $template->toString());

        $context->returnValue = $html;

        /* signal that we've handled execution */
        return true;
    }

    protected function getTemplatePath()
    {
        return 'search/search_input.tpl.php';
    }

}
