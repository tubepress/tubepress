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
 * Handles applying the player HTML to the gallery template.
 */
class tubepress_plugins_core_impl_filters_gallerytemplate_Player
{
    public function onGalleryTemplate(tubepress_api_event_TubePressEvent $event)
    {
        $context        = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
        $htmlGenerator  = tubepress_impl_patterns_ioc_KernelServiceLocator::getPlayerHtmlGenerator();
        $playerName     = $context->get(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION);
        $providerResult = $event->getArgument('videoGalleryPage');
        $videos         = $providerResult->getVideos();
        $template       = $event->getSubject();
        $galleryId      = $context->get(tubepress_api_const_options_names_Advanced::GALLERY_ID);
        $playerHtml     = $playerHtml = $this->_showPlayerHtmlOnPageLoad($playerName) ?
            $htmlGenerator->getHtml($videos[0], $galleryId) : '';

        $template->setVariable(tubepress_api_const_template_Variable::PLAYER_HTML, $playerHtml);
        $template->setVariable(tubepress_api_const_template_Variable::PLAYER_NAME, $playerName);
    }

    private function _showPlayerHtmlOnPageLoad($playerName)
    {
        if ($playerName === 'normal' || $playerName === 'static') {

            return true;
        }

        return false;
    }
}