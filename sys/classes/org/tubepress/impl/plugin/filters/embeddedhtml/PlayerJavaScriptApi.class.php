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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_options_names_Embedded',
    'org_tubepress_api_const_template_Variable',
    'org_tubepress_api_video_Video',
    'org_tubepress_impl_ioc_IocContainer',
));

/**
 * Registers videos with the JS player API.
 */
class org_tubepress_impl_plugin_filters_embeddedhtml_PlayerJavaScriptApi
{
	private static $_logPrefix = 'Player API Embedded HTML Filter';
	
    public function alter_embeddedHtml($html, $videoId, $videoProviderName, $embeddedImplName)
    {
        $ioc       = org_tubepress_impl_ioc_IocContainer::getInstance();
        $context   = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);

        if (! $context->get(org_tubepress_api_const_options_names_Embedded::ENABLE_JS_API)) {

        	org_tubepress_impl_log_Log::log(self::$_logPrefix, 'JS API is disabled');
        	
            return $html;
        }

        return $html . $this->_getPlayerRegistryJs($videoId);
    }

    private function _getPlayerRegistryJs($videoId)
    {
        return "<script type=\"text/javascript\">TubePressPlayerApi.register('$videoId');</script>";
    }
}
