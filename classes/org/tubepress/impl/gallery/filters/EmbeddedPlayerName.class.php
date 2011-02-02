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
class org_tubepress_impl_gallery_filters_EmbeddedPlayerName
{
    public function filter($template)
    {
        $template->setVariable(org_tubepress_api_template_Template::EMBEDDED_IMPL_NAME, self::_getEmbeddedServiceName());
    }

    private static function _getEmbeddedServiceName()
    {
        $ioc    = org_tubepress_impl_ioc_IocContainer::getInstance();
        $tpom   = $ioc->get('org_tubepress_api_options_OptionsManager');
        $stored = $tpom->get(org_tubepress_api_const_options_Embedded::PLAYER_IMPL);
        
        if ($stored === org_tubepress_api_embedded_EmbeddedPlayer::LONGTAIL) {
            return $stored;
        }
        
        $pc = $ioc->get('org_tubepress_api_provider_ProviderCalculator');
        
        return $pc->calculateCurrentVideoProvider();
    }
}
