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

tubepress_load_classes(array('org_tubepress_api_provider_ProviderCalculator'));

/**
 * Applies the embedded service name to the template.
 */
class org_tubepress_impl_filters_template_EmbeddedPlayerName
{
    public function filter($template, $feedResult, $galleryId)
    {
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_IMPL_NAME, self::_getEmbeddedServiceName());
        
        return $template;
    }

    private static function _getEmbeddedServiceName()
    {
        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();
        $tpom         = $ioc->get('org_tubepress_api_options_OptionsManager');
        $stored       = $tpom->get(org_tubepress_api_const_options_names_Embedded::PLAYER_IMPL);
        $pc           = $ioc->get('org_tubepress_api_provider_ProviderCalculator');
        $providerName = $pc->calculateCurrentVideoProvider();
        
        if ($stored === org_tubepress_api_const_options_values_PlayerImplementationValue::LONGTAIL
            && $providerName !== org_tubepress_api_provider_Provider::VIMEO) {
            return $stored;
        }
        
        return $providerName;
    }
}

$ioc      = org_tubepress_impl_ioc_IocContainer::getInstance();
$fm       = $ioc->get('org_tubepress_api_patterns_FilterManager');
$instance = $ioc->get('org_tubepress_impl_filters_template_EmbeddedPlayerName');

$fm->registerFilter(org_tubepress_api_const_filters_ExecutionPoint::GALLERY_TEMPLATE, array($instance, 'filter'));
