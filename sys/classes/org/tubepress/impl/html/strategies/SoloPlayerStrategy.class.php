<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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

tubepress_load_classes(array('org_tubepress_api_patterns_Strategy',
    'org_tubepress_api_const_options_names_Display',
    'org_tubepress_api_player_Player',
    'org_tubepress_impl_log_Log',
    'org_tubepress_api_querystring_QueryStringService'));

/**
 * HTML-generation strategy that implements the "solo" player strategy.
 */
class org_tubepress_impl_html_strategies_SoloPlayerStrategy implements org_tubepress_api_patterns_Strategy
{
    const LOG_PREFIX = 'Solo Player Strategy';

    private $_ioc;
    private $_videoId;

    /**
     * Called *before* canHandle() and execute() to allow the strategy
     *  to initialize itself.
     *
     * @return void
     */
    public function start()
    {
        $this->_ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
    }

    /**
     * Called *after* canHandle() and execute() to allow the strategy
     *  to tear itself down.
     *
     * @return void
     */
    public function stop()
    {
        unset($this->_ioc);
        unset($this->_videoId);
    }

    /**
     * Returns true if this strategy is able to handle
     *  the request.
     *
     * @return boolean True if the strategy can handle the request, false otherwise.
     */
    public function canHandle()
    {
        $tpom       = $this->_ioc->get('org_tubepress_api_options_OptionsManager');
        $playerName = $tpom->get(org_tubepress_api_const_options_names_Display::CURRENT_PLAYER_NAME);

        if ($playerName !== org_tubepress_api_player_Player::SOLO) {
            return false;
        }

        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Solo player detected. Checking query string for video ID.');

        /* see if we have a custom video ID set */
        $qss     = $this->_ioc->get('org_tubepress_api_querystring_QueryStringService');
        $videoId = $qss->getCustomVideo($_GET);

        if ($videoId == '') {
            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Solo player in use, but no video ID set in URL.');
            return false;
        }

        $this->_videoId = $videoId;
        return true;
    }

    /**
     * Execute the strategy.
     *
     * @return unknown The result of this strategy execution.
     */
    public function execute()
    {
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Building single video with ID %s', $this->_videoId);

        $single = $this->_ioc->get('org_tubepress_api_single_SingleVideo');

        return $single->getSingleVideoHtml($this->_videoId, $this->_ioc);
    }
}
