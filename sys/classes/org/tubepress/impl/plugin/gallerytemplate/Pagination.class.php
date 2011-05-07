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

class_exists('TubePress') || require dirname(__FILE__) . '/../../../../../TubePress.class.php';
TubePress::loadClasses(array(
    'org_tubepress_api_const_plugin_FilterPoint',
    'org_tubepress_api_const_options_names_Display',
    'org_tubepress_api_const_template_Variable',
    'org_tubepress_api_options_OptionsManager',
    'org_tubepress_api_pagination_Pagination',
    'org_tubepress_api_plugin_PluginManager',
    'org_tubepress_impl_ioc_IocContainer',
));

/**
 * Handles applying pagination to the gallery template.
 */
class org_tubepress_impl_plugin_gallerytemplate_Pagination
{
    
    public function alter_galleryTemplate(org_tubepress_api_template_Template $template, org_tubepress_api_provider_ProviderResult $providerResult, $galleryId)
    {
        $ioc               = org_tubepress_impl_ioc_IocContainer::getInstance();
        $tpom              = $ioc->get('org_tubepress_api_options_OptionsManager');
        $paginationService = $ioc->get('org_tubepress_api_pagination_Pagination');
        $pm                = $ioc->get('org_tubepress_api_plugin_PluginManager');
        $pagination        = $paginationService->getHtml($providerResult->getEffectiveTotalResultCount());
        $pagination        = $pm->runFilters(org_tubepress_api_const_plugin_FilterPoint::HTML_PAGINATION, $pagination);
        
        if ($tpom->get(org_tubepress_api_const_options_names_Display::PAGINATE_ABOVE)) {
            $template->setVariable(org_tubepress_api_const_template_Variable::PAGINATION_TOP, $pagination);
        }
        if ($tpom->get(org_tubepress_api_const_options_names_Display::PAGINATE_BELOW)) {
            $template->setVariable(org_tubepress_api_const_template_Variable::PAGINATION_BOTTOM, $pagination);
        }

        return $template;
    }
}
