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
 * Handles generation of the HTML for an embedded player. This expects exactly 3 GET
 * paramters: embedName (the string name of the embedded player implementation),
 * video (the video ID to load), meta (true/false whether or not to include video meta info)
 */
class tubepress_plugins_core_impl_http_PlayerPluggableAjaxCommandService extends tubepress_impl_http_AbstractPluggableAjaxCommandService
{
    /**
     * @return string The command name that this handler responds to.
     */
    public final function getCommandName()
    {
        return 'playerHtml';
    }

    protected function getStatusCodeToHtmlMap()
    {
        $executionContext = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
        $player           = tubepress_impl_patterns_ioc_KernelServiceLocator::getPlayerHtmlGenerator();
        $provider         = tubepress_impl_patterns_ioc_KernelServiceLocator::getVideoCollector();
        $qss              = tubepress_impl_patterns_ioc_KernelServiceLocator::getHttpRequestParameterService();
        $sp               = tubepress_impl_patterns_ioc_KernelServiceLocator::getShortcodeParser();

        $shortcode = rawurldecode($qss->getParamValue(tubepress_spi_const_http_ParamName::SHORTCODE));
        $videoId   = $qss->getParamValue(tubepress_spi_const_http_ParamName::VIDEO);

        /* gather up the options */
        $sp->parse($shortcode);

        if ($executionContext->get(tubepress_api_const_options_names_Embedded::LAZYPLAY)) {

            $executionContext->set(tubepress_api_const_options_names_Embedded::AUTOPLAY, true);
        }

        /* grab the video! */
        $video = $provider->collectSingleVideo($videoId);

        if ($video === null) {

            return array(404 => "Video $videoId not found");
        }

        $title = rawurlencode($video->getTitle());
        $html  = rawurlencode($player->getHtml($video));

        return array(200 => "{ \"title\" : \"$title\", \"html\" : \"$html\" }");
    }
}

