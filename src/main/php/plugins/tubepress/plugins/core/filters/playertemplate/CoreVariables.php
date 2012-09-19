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
class tubepress_plugins_core_filters_playertemplate_CoreVariables
{
    public function onPlayerTemplate(tubepress_api_event_PlayerTemplateConstruction $event)
    {
        $embedded  = tubepress_impl_patterns_ioc_KernelServiceLocator::getEmbeddedHtmlGenerator();
        $context   = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
        $galleryId = $context->get(tubepress_api_const_options_names_Advanced::GALLERY_ID);

        $template = $event->getSubject();
        $video    = $event->getArgument(tubepress_api_event_PlayerTemplateConstruction::ARGUMENT_VIDEO);

        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_SOURCE, $embedded->getHtml($video->getId()));
        $template->setVariable(org_tubepress_api_const_template_Variable::GALLERY_ID, $galleryId);
        $template->setVariable(org_tubepress_api_const_template_Variable::VIDEO, $video);
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_WIDTH, $context->get(tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH));
    }
}
