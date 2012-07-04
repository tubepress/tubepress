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
    'org_tubepress_api_const_options_values_PlayerImplementationValue',
    'org_tubepress_api_ioc_IocService',
    'org_tubepress_api_exec_ExecutionContext',
    'org_tubepress_api_provider_Provider',
    'org_tubepress_api_url_Url',
    'org_tubepress_impl_embedded_commands_AbstractEmbeddedCommand',
));

/**
 * Embedded player command for the JW FLV player
 */
class org_tubepress_impl_embedded_commands_JwFlvCommand extends org_tubepress_impl_embedded_commands_AbstractEmbeddedCommand
{
    protected function _canHandle($providerName, $videoId, org_tubepress_api_ioc_IocService $ioc, org_tubepress_api_exec_ExecutionContext $context)
    {
        return $providerName === org_tubepress_api_provider_Provider::YOUTUBE
            && $context->get(org_tubepress_api_const_options_names_Embedded::PLAYER_IMPL) === org_tubepress_api_const_options_values_PlayerImplementationValue::LONGTAIL;
    }

    protected function _getTemplatePath($providerName, $videoId, org_tubepress_api_ioc_IocService $ioc, org_tubepress_api_exec_ExecutionContext $context)
    {
        return 'embedded_flash/longtail.tpl.php';
    }

    protected function _getEmbeddedDataUrl($providerName, $videoId, org_tubepress_api_ioc_IocService $ioc, org_tubepress_api_exec_ExecutionContext $context)
    {
        return new org_tubepress_api_url_Url(sprintf('http://www.youtube.com/watch?v=%s', $videoId));
    }

    protected function _getEmbeddedImplName()
    {
        return 'longtail';
    }
}