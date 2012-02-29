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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_options_names_Embedded',
    'org_tubepress_api_ioc_IocService',
    'org_tubepress_api_exec_ExecutionContext',
    'org_tubepress_api_provider_Provider',
    'org_tubepress_api_url_Url',
    'org_tubepress_impl_embedded_EmbeddedPlayerUtils',
    'org_tubepress_impl_embedded_commands_AbstractEmbeddedCommand',
));

/**
 * Embedded player command for native Vimeo
 */
class org_tubepress_impl_embedded_commands_VimeoCommand extends org_tubepress_impl_embedded_commands_AbstractEmbeddedCommand
{
    const VIMEO_EMBEDDED_PLAYER_URL = 'http://player.vimeo.com/';
    const VIMEO_QUERYPARAM_AUTOPLAY = 'autoplay';
    const VIMEO_QUERYPARAM_TITLE    = 'title';
    const VIMEO_QUERYPARAM_BYLINE   = 'byline';
    const VIMEO_QUERYPARAM_COLOR    = 'color';
    const VIMEO_QUERYPARAM_LOOP     = 'loop';
    const VIMEO_QUERYPARAM_PORTRAIT = 'portrait';
    private static $_paramJsApi     = 'api';
    private static $_paramPlayerId  = 'player_id';

    protected function _canHandle($providerName, $videoId, org_tubepress_api_ioc_IocService $ioc, org_tubepress_api_exec_ExecutionContext $context)
    {
        return $providerName === org_tubepress_api_provider_Provider::VIMEO;
    }

    protected function _getTemplatePath($providerName, $videoId, org_tubepress_api_ioc_IocService $ioc, org_tubepress_api_exec_ExecutionContext $context)
    {
        return 'embedded_flash/vimeo.tpl.php';
    }

    protected function _getEmbeddedDataUrl($providerName, $videoId, org_tubepress_api_ioc_IocService $ioc, org_tubepress_api_exec_ExecutionContext $context)
    {
        $autoPlay = $context->get(org_tubepress_api_const_options_names_Embedded::AUTOPLAY);
        $color    = $context->get(org_tubepress_api_const_options_names_Embedded::PLAYER_COLOR);
        $showInfo = $context->get(org_tubepress_api_const_options_names_Embedded::SHOW_INFO);
        $loop     = $context->get(org_tubepress_api_const_options_names_Embedded::LOOP);
        $jsApi    = $context->get(org_tubepress_api_const_options_names_Embedded::ENABLE_JS_API);

        /* build the data URL based on these options */
        $link = new org_tubepress_api_url_Url(self::VIMEO_EMBEDDED_PLAYER_URL . "video/$videoId");
        $link->setQueryVariable(self::VIMEO_QUERYPARAM_AUTOPLAY, org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($autoPlay));
        $link->setQueryVariable(self::VIMEO_QUERYPARAM_COLOR, $color);
        $link->setQueryVariable(self::VIMEO_QUERYPARAM_LOOP, org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($loop));
        $link->setQueryVariable(self::VIMEO_QUERYPARAM_TITLE, org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($showInfo));
        $link->setQueryVariable(self::VIMEO_QUERYPARAM_BYLINE, org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($showInfo));
        $link->setQueryVariable(self::VIMEO_QUERYPARAM_PORTRAIT, org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($showInfo));

        if ($jsApi) {

            $link->setQueryVariable(self::$_paramJsApi, 1);
            $link->setQueryVariable(self::$_paramPlayerId, "tubepress-vimeo-player-$videoId");
        }

        return $link;
    }

    protected function _getEmbeddedImplName()
    {
        return 'vimeo';
    }
}
