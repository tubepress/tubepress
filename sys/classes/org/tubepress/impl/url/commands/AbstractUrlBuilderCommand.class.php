<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require(dirname(__FILE__) . '/../../classloader/ClassLoader.class.php');
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_options_names_Advanced',
    'org_tubepress_api_const_options_names_Display',
    'org_tubepress_api_const_options_names_Embedded',
    'org_tubepress_api_const_options_names_Feed',
    'org_tubepress_api_const_options_names_Meta',
    'org_tubepress_api_const_options_names_Output',
    'org_tubepress_api_const_options_values_ModeValue',
    'org_tubepress_api_patterns_cor_Command',
    'org_tubepress_api_provider_Provider',
    'org_tubepress_api_url_Url',
    'org_tubepress_api_exec_ExecutionContext',
));

/**
 * Base URL builder functionality.
 */
abstract class org_tubepress_impl_url_commands_AbstractUrlBuilderCommand implements org_tubepress_api_patterns_cor_Command
{
    /**
     * Execute the command.
     *
     * @param array $context An array of context elements (may be empty).
     *
     * @return boolean True if this command was able to handle the execution. False otherwise.
     */
    public function execute($context)
    {
        if ($context->providerName !== $this->_getHandledProviderName()) {
            return false;
        }

        /* single video */
        if ($context->single) {

            $context->returnValue = $this->_buildSingleVideoUrl($context->arg);
        } else {

            $context->returnValue = $this->_buildGalleryUrl($context->arg);
        }

        return true;
    }

    protected abstract function _getHandledProviderName();

    protected abstract function _buildSingleVideoUrl($id);

    protected abstract function _buildGalleryUrl($page);
}
