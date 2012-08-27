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

/**
 * Applies core player template variables.
 */
class org_tubepress_impl_plugin_filters_playertemplate_CoreVariables
{
    public function alter_playerTemplate(org_tubepress_api_template_Template $template, org_tubepress_api_video_Video $video, $videoProviderName, $playerName)
    {
        $ioc       = org_tubepress_impl_ioc_IocContainer::getInstance();
        $embedded  = $ioc->get(org_tubepress_api_embedded_EmbeddedHtmlGenerator::_);
        $context   = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $galleryId = $context->get(org_tubepress_api_const_options_names_Advanced::GALLERY_ID);

        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_SOURCE, $embedded->getHtml($video->getId()));
        $template->setVariable(org_tubepress_api_const_template_Variable::GALLERY_ID, $galleryId);
        $template->setVariable(org_tubepress_api_const_template_Variable::VIDEO, $video);
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_WIDTH, $context->get(org_tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH));

        return $template;
    }
}
