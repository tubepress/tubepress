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
    'org_tubepress_api_const_options_values_PlayerImplementationValue',
    'org_tubepress_api_const_template_Variable',
    'org_tubepress_api_exec_ExecutionContext',
    'org_tubepress_api_provider_Provider',
    'org_tubepress_api_provider_ProviderResult',
    'org_tubepress_api_template_Template',
    'org_tubepress_impl_ioc_IocContainer',
));

/**
 * Applies the embedded service name to the template.
 */
class org_tubepress_impl_plugin_filters_gallerytemplate_EmbeddedPlayerName
{
    public function alter_galleryTemplate(org_tubepress_api_template_Template $template, org_tubepress_api_provider_ProviderResult $providerResult, $page, $providerName)
    {
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_IMPL_NAME, self::_getEmbeddedServiceName($providerName));

        return $template;
    }

    private static function _getEmbeddedServiceName($providerName)
    {
        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();
        $context      = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $stored       = $context->get(org_tubepress_api_const_options_names_Embedded::PLAYER_IMPL);

        $longTailWithYouTube = $stored === org_tubepress_api_const_options_values_PlayerImplementationValue::LONGTAIL
            && $providerName === org_tubepress_api_provider_Provider::YOUTUBE;

        $embedPlusWithYouTube = $stored === org_tubepress_api_const_options_values_PlayerImplementationValue::EMBEDPLUS
            && $providerName === org_tubepress_api_provider_Provider::YOUTUBE;

        if ($longTailWithYouTube || $embedPlusWithYouTube) {
            return $stored;
        }

        return $providerName;
    }
}
