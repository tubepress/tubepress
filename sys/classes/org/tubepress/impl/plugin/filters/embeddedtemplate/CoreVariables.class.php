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
    'org_tubepress_api_const_options_names_Advanced',
    'org_tubepress_api_const_options_names_Embedded',
    'org_tubepress_api_const_template_Variable',
    'org_tubepress_api_exec_ExecutionContext',
    'org_tubepress_api_template_Template',
    'org_tubepress_api_url_Url',
    'org_tubepress_impl_embedded_EmbeddedPlayerUtils',
    'org_tubepress_impl_ioc_IocContainer',
));

/**
 * Core variables for the embedded template.
 */
class org_tubepress_impl_plugin_filters_embeddedtemplate_CoreVariables
{
    public function alter_embeddedTemplate(org_tubepress_api_template_Template $template, $videoId, $videoProviderName,
                                           org_tubepress_api_url_Url $dataUrl, $embeddedImplName)
    {
        global $tubepress_base_url;

        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();
        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);

        $fullscreen      = $context->get(org_tubepress_api_const_options_names_Embedded::FULLSCREEN);
        $playerColor     = org_tubepress_impl_embedded_EmbeddedPlayerUtils::getSafeColorValue($context->get(org_tubepress_api_const_options_names_Embedded::PLAYER_COLOR), '999999');
        $playerHighlight = org_tubepress_impl_embedded_EmbeddedPlayerUtils::getSafeColorValue($context->get(org_tubepress_api_const_options_names_Embedded::PLAYER_HIGHLIGHT), 'FFFFFF');
        $autoPlay        = $context->get(org_tubepress_api_const_options_names_Embedded::AUTOPLAY);
        $embedWidth      = $context->get(org_tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH);
        $embedHeight     = $context->get(org_tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT);

        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_DATA_URL, $dataUrl->toString(true));
        $template->setVariable(org_tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL, $tubepress_base_url);
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_AUTOSTART, org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToString($autoPlay));
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_WIDTH, $embedWidth);
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_HEIGHT, $embedHeight);
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_COLOR_PRIMARY, $playerColor);
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_COLOR_HIGHLIGHT, $playerHighlight);
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_FULLSCREEN, org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToString($fullscreen));
        $template->setVariable(org_tubepress_api_const_template_Variable::VIDEO_ID, $videoId);

        return $template;
    }
}