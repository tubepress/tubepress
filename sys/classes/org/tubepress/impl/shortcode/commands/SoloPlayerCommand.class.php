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
    'org_tubepress_api_const_http_ParamName',
    'org_tubepress_api_const_options_names_Embedded',
    'org_tubepress_api_const_options_names_Output',
    'org_tubepress_api_const_options_values_PlayerLocationValue',
    'org_tubepress_api_http_HttpRequestParameterService',
    'org_tubepress_spi_patterns_cor_Chain',
    'org_tubepress_spi_patterns_cor_Command',
    'org_tubepress_impl_shortcode_commands_SingleVideoCommand',
    'org_tubepress_impl_ioc_IocContainer',
    'org_tubepress_impl_log_Log',
));

/**
 * HTML-generation command that implements the "solo" player command.
 */
class org_tubepress_impl_shortcode_commands_SoloPlayerCommand implements org_tubepress_spi_patterns_cor_Command
{
    const LOG_PREFIX = 'Solo Player Command';

    /**
     * Execute the command.
     *
     * @param array $context An array of context elements (may be empty).
     *
     * @return boolean True if this command was able to handle the execution. False otherwise.
     */
    public function execute($context)
    {
        $ioc         = org_tubepress_impl_ioc_IocContainer::getInstance();
        $execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $playerName  = $execContext->get(org_tubepress_api_const_options_names_Embedded::PLAYER_LOCATION);

        if ($playerName !== org_tubepress_api_const_options_values_PlayerLocationValue::SOLO) {

            return false;
        }

        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Solo player detected. Checking query string for video ID.');

        /* see if we have a custom video ID set */
        $qss     = $ioc->get(org_tubepress_api_http_HttpRequestParameterService::_);
        $videoId = $qss->getParamValue(org_tubepress_api_const_http_ParamName::VIDEO);;

        if ($videoId == '') {

            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Solo player in use, but no video ID set in URL.');
            return false;
        }

        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Building single video with ID %s', $videoId);

        $result = $execContext->set(org_tubepress_api_const_options_names_Output::VIDEO, $videoId);

        if ($result !== true) {

            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Could not verify video ID.');
            return false;
        }

        /* display the results as a thumb gallery */
        $ioc->get(org_tubepress_spi_patterns_cor_Chain::_)->execute($context, array(

            'org_tubepress_impl_shortcode_commands_SingleVideoCommand'
        ));

        return true;
    }
}
