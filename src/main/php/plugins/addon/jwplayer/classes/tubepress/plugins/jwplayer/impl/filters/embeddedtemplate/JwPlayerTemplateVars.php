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
 * Adds a few JW Player template variables.
 */
class tubepress_plugins_jwplayer_impl_filters_embeddedtemplate_JwPlayerTemplateVars
{
    public function onEmbeddedTemplate(tubepress_api_event_TubePressEvent $event)
    {
        $implName = $event->getArgument('embeddedImplementationName');

        if ($implName !== 'longtail') {

            return;
        }

        $context  = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
        $template = $event->getSubject();

        $template->setVariable(tubepress_plugins_jwplayer_api_const_template_Variable::COLOR_FRONT,
            $context->get(tubepress_plugins_jwplayer_api_const_options_names_Embedded::COLOR_FRONT));

        $template->setVariable(tubepress_plugins_jwplayer_api_const_template_Variable::COLOR_LIGHT,
            $context->get(tubepress_plugins_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT));

        $template->setVariable(tubepress_plugins_jwplayer_api_const_template_Variable::COLOR_SCREEN,
            $context->get(tubepress_plugins_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN));

        $template->setVariable(tubepress_plugins_jwplayer_api_const_template_Variable::COLOR_BACK,
            $context->get(tubepress_plugins_jwplayer_api_const_options_names_Embedded::COLOR_BACK));
    }
}