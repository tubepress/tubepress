<?php
/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
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
 * HTML-generation strategy that implements the "solo" player strategy.
 */
class org_tubepress_impl_gallery_strategies_SoloPlayerStrategy implements org_tubepress_api_strategy_Strategy
{
    private $_ioc;
    private $_videoId;

    public function start()
    {
        $this->_ioc  = org_tubepress_impl_ioc_IocContainer::getInstance();
    }

    public function stop()
    {
        unset($this->_ioc);
        unset($this->_videoId);
    }

    public function canHandle()
    {
        $tpom       = $this->_ioc->get('org_tubepress_options_manager_OptionsManager');
        $playerName = $tpom->get(org_tubepress_options_category_Display::CURRENT_PLAYER_NAME);

        if ($playerName !== org_tubepress_player_Player::SOLO) {
            return false;
        }

        org_tubepress_log_Log::log(self::LOG_PREFIX, 'Solo player detected. Checking query string for video ID.');

        /* see if we have a custom video ID set */
        $qss     = $this->_ioc->get('org_tubepress_querystring_QueryStringService');
        $videoId = $qss->getCustomVideo($_GET);

        if ($videoId == '') {
            org_tubepress_log_Log::log(self::LOG_PREFIX, 'Solo player in use, but no video ID set in URL.');
            return false;
        }

        $this->_videoId = $videoId;
        return true;
    }

    public function execute()
    {
        org_tubepress_log_Log::log(self::LOG_PREFIX, 'Building single video with ID %s', $videoId);

        $single = $this->_ioc->get('org_tubepress_single_SingleVideo');

        return $single->getSingleVideoHtml($videoId, $this->_ioc);
    }

    public function getName()
    {
        return 'Solo Player HTML Generation Strategy';
    }
}

?>
