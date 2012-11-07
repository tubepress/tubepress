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
 * Registers videos with the JS player API.
 */
class tubepress_plugins_core_impl_filters_embeddedhtml_PlayerJavaScriptApi
{
    public function onEmbeddedHtml(tubepress_api_event_TubePressEvent $event)
    {
        $context   = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();

        if (! $context->get(tubepress_api_const_options_names_Embedded::ENABLE_JS_API)) {

        	return;
        }

        $html    = $event->getSubject();
        $videoId = $event->getArgument('videoId');
        $final   = "$html<script type=\"text/javascript\">TubePressPlayerApi.register('$videoId');</script>";

        $event->setSubject($final);
    }
}
