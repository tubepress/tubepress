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
 * Core variables for the embedded template.
 */
class tubepress_plugins_core_filters_embeddedtemplate_CoreVariables
{
    public function onEmbeddedTemplate(tubepress_api_event_EmbeddedTemplateConstruction $event)
    {
        global $tubepress_base_url;

        $context = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
        $template = $event->getSubject();
        $dataUrl = $event->getArgument(tubepress_api_event_EmbeddedTemplateConstruction::ARGUMENT_DATA_URL);
        $videoId = $event->getArgument(tubepress_api_event_EmbeddedTemplateConstruction::ARGUMENT_VIDEO_ID);

        $fullscreen      = $context->get(tubepress_api_const_options_names_Embedded::FULLSCREEN);
        $playerColor     = tubepress_impl_embedded_EmbeddedPlayerUtils::getSafeColorValue($context->get(tubepress_api_const_options_names_Embedded::PLAYER_COLOR), '999999');
        $playerHighlight = tubepress_impl_embedded_EmbeddedPlayerUtils::getSafeColorValue($context->get(tubepress_api_const_options_names_Embedded::PLAYER_HIGHLIGHT), 'FFFFFF');
        $autoPlay        = $context->get(tubepress_api_const_options_names_Embedded::AUTOPLAY);
        $embedWidth      = $context->get(tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH);
        $embedHeight     = $context->get(tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT);

        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_DATA_URL, $dataUrl->toString(true));
        $template->setVariable(org_tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL, $tubepress_base_url);
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_AUTOSTART, tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToString($autoPlay));
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_WIDTH, $embedWidth);
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_HEIGHT, $embedHeight);
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_COLOR_PRIMARY, $playerColor);
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_COLOR_HIGHLIGHT, $playerHighlight);
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_FULLSCREEN, tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToString($fullscreen));
        $template->setVariable(org_tubepress_api_const_template_Variable::VIDEO_ID, $videoId);

        return $template;
    }
}