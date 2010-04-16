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
    || require(dirname(__FILE__) . '/../../../tubepress_classloader.php');
tubepress_load_classes(array('org_tubepress_shortcode_ShortcodeService',
    'org_tubepress_options_category_Advanced',
    'org_tubepress_options_manager_OptionsManager',
    'org_tubepress_options_validation_InputValidationService',
    'org_tubepress_player_Player',
    'org_tubepress_options_category_Display'));

/**
 * Handles some tasks related to the query string
 */
class org_tubepress_shortcode_SimpleShortcodeService implements org_tubepress_shortcode_ShortcodeService
{
    private $_log;
    private $_logPrefix;
    private $_inputValidationService;
    
    public function __construct()
    {
        $this->_logPrefix = "Simple Shortcode Service";
    }
    
   /**
     * This function is used to parse a shortcode into options that TubePress can use.
     *
     * @param string                  $content The haystack in which to search
     * @param org_tubepress_options_manager_OptionsManager &$tpom   The TubePress options manager
     * 
     * @return void
     */
    public function parse($content, org_tubepress_options_manager_OptionsManager $tpom, $mergeWithExistingOptions = false)
    {    
        /* what trigger word are we using? */
        $keyword = $tpom->get(org_tubepress_options_category_Advanced::KEYWORD);
        
        if (!$this->somethingToParse($content, $keyword)) {
            return;
        }
        
        $customOptions = array();  
          
        /* Match everything in square brackets after the trigger */
        $regexp = "\[$keyword\b(.*)\]";
        
        $this->_log->log($this->_logPrefix, 'Regular expression for content is %s', $regexp);
        
        preg_match("/$regexp/", $content, $matches);
        
        if (sizeof($matches) === 0) {
            $this->_log->log($this->_logPrefix, 'No shortcodes detected in content');
            return;
        }

        $this->_log->log($this->_logPrefix, 'Found a shortcode: %s', $matches[0]);
        
        $tpom->setShortcode($matches[0]);

        /* Anything matched? */
        if (isset($matches[1]) && $matches[1] != "") {
            
            $text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $matches[1]);
            $text = $this->_convertQuotes($text);
            $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s*,)?(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s*,)?(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s*,)?(?:\s|$)/';    
        
            if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {
                
                $this->_log->log($this->_logPrefix, 'Custom options detected in shortcode: %s', $matches[0]);    
            
                $customOptions = $this->_parseCustomOption($customOptions, $match);
                
                $this->_applyOptions($tpom, $customOptions, $mergeWithExistingOptions);
            }
        } else {
            $this->_log->log($this->_logPrefix, 'No custom options detected in shortcode: %s', $matches[0]);
        }
    }

    private function _parseCustomOption($customOptions, $match)
    {
        foreach ($match as $m) {

            if (!empty($m[1])) {
                $name = $m[1];
                $value = $this->_normalizeValue($m[2]);
            } elseif (!empty($m[3])) {
                $name = $m[3];
                $value = $this->_normalizeValue($m[4]);
            } elseif (!empty($m[5])) {
                $name = $m[5];
                $value = $this->_normalizeValue($m[6]);
            }
            
            $this->_log->log($this->_logPrefix, 'Custom shortcode detected: %s = %s', $name, (string)$value);
            
            try {
                $this->_inputValidationService->validate($name, $value);
                $customOptions[$name] = $value;
            } catch (Exception $e) {
                $this->_log->log($this->_logPrefix, 'Ignoring invalid value for "%s" option: %s', $name, $e->getMessage());
            }
        }
        return $customOptions;
    }
    
    public function somethingToParse($content, $trigger = "tubepress")
    {
        return strpos($content, '[' . $trigger) !== false;
    }
    
    public function getHtml($shortCodeContent, org_tubepress_ioc_IocService $iocService)
    {
    	$tpom = $iocService->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
    	$log  = $iocService->get(org_tubepress_ioc_IocService::LOG);
    	
    	/* parse the first shortcode we find */
    	$this->parse($shortCodeContent, $tpom);

        /* user wants to display a single video with meta info */
        if ($tpom->get(org_tubepress_options_category_Gallery::VIDEO) != '') {
            $videoId = $tpom->get(org_tubepress_options_category_Gallery::VIDEO);
            $log->log($this->_logPrefix, 'Building single video with ID %s', $videoId);
            $singleVideoGenerator = $iocService->get(org_tubepress_ioc_IocService::SINGLE_VIDEO);
            return $singleVideoGenerator->getSingleVideoHtml($videoId);
        }
        $log->log($this->_logPrefix, 'No video ID set in shortcode.');
        
        $qss = $iocService->get(org_tubepress_ioc_IocService::QUERY_STRING_SERVICE);
        
        /* see if the users wants to display just the video in the query string */
        $playerName = $tpom->get(org_tubepress_options_category_Display::CURRENT_PLAYER_NAME);
        if ($playerName == org_tubepress_player_Player::SOLO) {
        	$log->log($this->_logPrefix, 'Solo player detected. Checking query string for video ID');
        	$videoId = $qss->getCustomVideo($_GET);
        	if ($videoId != '') {
        		$log->log($this->_logPrefix, 'Building single video with ID %s', $videoId);
        		$singleVideoGenerator = $iocService->get(org_tubepress_ioc_IocService::SINGLE_VIDEO);
                return $singleVideoGenerator->getSingleVideoHtml($videoId);
        	}
        	$log->log($this->_logPrefix, 'Solo player in use, but no video ID set in URL. Will display a gallery instead.', $videoId);
        }
        
        $galleryId = $qss->getGalleryId($_GET);
		if ($galleryId == '') {
			$galleryId = mt_rand();	
		}
        
        /* normal gallery */
        $log->log($this->_logPrefix, 'Starting to build gallery %s', $galleryId);
        $gallery = $iocService->get(org_tubepress_ioc_IocService::GALLERY);
        return $gallery->getHtml($galleryId); 
    }
    
    public function setLog(org_tubepress_log_Log $log) { $this->_log = $log; }
    public function setInputValidationService(org_tubepress_options_validation_InputValidationService $service) { $this->_inputValidationService = $service; }
    
    private function _applyOptions($tpom, $opts, $merge)
    {
        if ($merge) {
            $tpom->mergeCustomOptions($opts);
        } else {
            $tpom->setCustomOptions($opts);
        }
    }

    /**
     * Replaces weird quotes with normal ones. Fun.
     */
    private function _convertQuotes($text)
    {
        $converted = str_replace(array("&#8216", "&#8217", "&#8242;"), "'", $text);
        return str_replace(array("&#34", "&#8220;", "&#8221;", "&#8243;"), "\"", $converted);
    }
    
    /**
     * Strips out ugly slashes and converts boolean
     *
     * @param string $nameOrValue The raw option name or value
     * 
     * @return string The cleaned up option name or value
     */
    private function _normalizeValue($value)
    {
        $cleanValue = trim(stripcslashes($value));   
        if ($cleanValue == "true") {
            return true;
        }
        if ($cleanValue == "false") {
            return false;
        }
        return $cleanValue;
    }
    
}

