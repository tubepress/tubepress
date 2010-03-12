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

function_exists('tubepress_load_classes')
    || require(dirname(__FILE__) . '/../../../../tubepress_classloader.php');
tubepress_load_classes(array('org_tubepress_video_factory_VideoFactory'));

/**
 * Video factory for Vimeo
 */
abstract class org_tubepress_video_factory_AbstractVideoFactory implements org_tubepress_video_factory_VideoFactory
{
    private $_log;
    private $_tpom;
    
    public function setLog(org_tubepress_log_Log $log) { $this->_log = $log; }
    public function setOptionsManager(org_tubepress_options_manager_OptionsManager $tpom) { $this->_tpom = $tpom; }

    protected function getLog() { return $this->_log; }
    protected function getOptionsManager() { return $this->_tpom; }
    
    protected function isVideoBlackListed($id)
    {
        $blacklist = $this->_tpom->get(org_tubepress_options_category_Advanced::VIDEO_BLACKLIST);
        return strpos($blacklist, $id) !== FALSE;
    }
    
    //Grabbed from http://www.weberdev.com/get_example-4769.html
    protected static function _relativeTime($timestamp){
        $difference = time() - $timestamp;
        $periods = array("sec", "min", "hour", "day", "week", "month", "year", "decade");
        $lengths = array("60","60","24","7","4.35","12","10");
    
        if ($difference > 0) { // this was in the past
            $ending = "ago";
        } else { // this was in the future
            $difference = -$difference;
            $ending = "to go";
        }       
        for($j = 0; $difference >= $lengths[$j]; $j++) $difference /= $lengths[$j];
        $difference = round($difference);
        if($difference != 1) $periods[$j].= "s";
        $text = "$difference $periods[$j] $ending";
        return $text;
    }
    
    protected static function _seconds2HumanTime($length_seconds)
    {
        $seconds         = $length_seconds;
        $length          = intval($seconds / 60);
        $leftOverSeconds = $seconds % 60;
        if ($leftOverSeconds < 10) {
            $leftOverSeconds = "0" . $leftOverSeconds;
        }
        $length .= ":" . $leftOverSeconds;
        return $length;
    }
    
}
