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
 * Adds some core variables to the single video template.
 */
class tubepress_plugins_core_impl_filters_singlevideotemplate_CoreVariables
{
    public function onSingleVideoTemplate(tubepress_api_event_TubePressEvent $event)
    {
        $video    = $event->getArgument('video');
        $template = $event->getSubject();

        $context        = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $embedded       = tubepress_impl_patterns_sl_ServiceLocator::getEmbeddedHtmlGenerator();
        $embeddedString = $embedded->getHtml($video->getId());
        $width          = $context->get(tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH);

        /* apply it to the template */
        $template->setVariable(tubepress_api_const_template_Variable::EMBEDDED_SOURCE, $embeddedString);
        $template->setVariable(tubepress_api_const_template_Variable::EMBEDDED_WIDTH, $width);
        $template->setVariable(tubepress_api_const_template_Variable::VIDEO, $video);
    }
}