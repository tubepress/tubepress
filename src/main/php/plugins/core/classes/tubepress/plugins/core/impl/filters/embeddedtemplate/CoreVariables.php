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
class tubepress_plugins_core_impl_filters_embeddedtemplate_CoreVariables
{
    public function onEmbeddedTemplate(tubepress_api_event_TubePressEvent $event)
    {
        global $tubepress_base_url;

        $context  = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
        $template = $event->getSubject();
        $dataUrl  = $event->getArgument('dataUrl');
        $videoId  = $event->getArgument('videoId');

        $autoPlay    = $context->get(tubepress_api_const_options_names_Embedded::AUTOPLAY);
        $embedWidth  = $context->get(tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH);
        $embedHeight = $context->get(tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT);

        $vars = array(

            tubepress_api_const_template_Variable::EMBEDDED_DATA_URL  => $dataUrl->toString(true),
            tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL => $tubepress_base_url,
            tubepress_api_const_template_Variable::EMBEDDED_AUTOSTART => tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToString($autoPlay),
            tubepress_api_const_template_Variable::EMBEDDED_WIDTH     => $embedWidth,
            tubepress_api_const_template_Variable::EMBEDDED_HEIGHT    => $embedHeight,
            tubepress_api_const_template_Variable::VIDEO_ID           => $videoId,
        );

        foreach ($vars as $key => $value) {

            $template->setVariable($key, $value);
        }

        return $template;
    }
}