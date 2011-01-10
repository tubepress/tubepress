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
 * HTML generation strategy that generates HTML for a single video + meta info.
 */
class org_tubepress_impl_gallery_strategies_SingleVideoStrategy implements org_tubepress_api_strategy_Strategy
{
    private $_ioc;
    private $_tpom;

    public function start()
    {
        $this->_ioc  = org_tubepress_impl_ioc_IocContainer::getInstance();
        $this->_tpom = $this->_ioc->get('org_tubepress_options_manager_OptionsManager');
    }

    public function stop()
    {
        unset($this->_ioc);
        unset($this->_tpom);
    }

    public function canHandle()
    {
        return $this->_tpom->get(org_tubepress_options_category_Gallery::VIDEO) != '';
    }

    public function execute()
    {    
        org_tubepress_log_Log::log(self::LOG_PREFIX, 'Building single video with ID %s', $videoId);

        $singleVideoGenerator = $this->_ioc->get('org_tubepress_single_SingleVideo');

        return $singleVideoGenerator->getSingleVideoHtml($videoId);
    }

    public function getName()
    {
        return 'Single Video HTML Generation Strategy';
    }
}

?>
