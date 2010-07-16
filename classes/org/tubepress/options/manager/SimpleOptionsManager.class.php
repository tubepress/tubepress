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
    || require dirname(__FILE__) . '/../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_options_manager_OptionsManager',
    'org_tubepress_options_storage_StorageManager',
    'org_tubepress_options_reference_OptionsReference',
    'org_tubepress_options_validation_InputValidationService',
    'org_tubepress_video_feed_provider_Provider'));

/**
 * Holds the current options for TubePress. This is the default options,
 * usually in persistent storage somewhere, and custom options parsed
 * from a shortcode
 */
class org_tubepress_options_manager_SimpleOptionsManager implements org_tubepress_options_manager_OptionsManager
{
    private $_customOptions = array();
    private $_tpsm;
    private $_validationService;
    private $_shortcode;

    /**
     * Gets the value of an option
     *
     * @param string $optionName The name of the option
     * 
     * @return unknown The option value
     */
    public function get($optionName)
    {
        /* get the value, either from the shortcode or the db */
        $value = array_key_exists($optionName, $this->_customOptions) ?
            $this->_customOptions[$optionName] : $this->_tpsm->get($optionName);

        /* get a valid value for this option */
        try {
            $this->_validationService->validate($optionName, $value);
        } catch (Exception $e) {
            $value = org_tubepress_options_reference_OptionsReference::getDefaultValue($optionName);
        }
        return $value;
    }

    /**
     * Sets the value of an option
     *
     * @param string  $optionName  The name of the option
     * @param unknown $optionValue The option value
     * 
     * @return void
     */
    public function set($optionName, $optionValue)
    {
        $this->_customOptions[$optionName] = $optionValue;
    }

    /**
     * Sets the options that differ from the default options.
     *
     * @param array $customOpts The custom options.
     * 
     * @return void
     */
    public function setCustomOptions($customOpts)
    {
        $this->_customOptions = $customOpts;
    }

    /**
     * Gets the options that differ from the default options.
     * 
     * @return array The options that differ from the default options.
     */
    public function getCustomOptions()
    {
        return $this->_customOptions;
    }

    /**
     * Set the current shortcode.
     *
     * @param string $newTagString The current shortcode
     * 
     * @return void
     */
    public function setShortcode($newTagString)
    {
        $this->_shortcode = $newTagString;
    }

    /**
     * Get the current shortcode
     *
     * @return string The current shortcode
     */
    public function getShortcode()
    {
        return $this->_shortcode;
    }

    /**
     * Figures out which provider (YouTube, Vimeo, or Local) that's in use.
     *
     * @return string The provider name.
     */
    public function calculateCurrentVideoProvider()
    {
        $video = $this->get(org_tubepress_options_category_Gallery::VIDEO);

        /* vimeo video IDs are always just numbers */
        if (is_numeric($video) === true) {
            return org_tubepress_video_feed_provider_Provider::VIMEO;
        }

        if (preg_match_all('/^.*\.[A-Za-z]{3}$/', $video, $arr, PREG_PATTERN_ORDER) === 1) {
            return org_tubepress_video_feed_provider_Provider::DIRECTORY;
        }

        /* requested a single video, and it's not vimeo or directory, so must be youtube */
        if ($video != '') {
            return org_tubepress_video_feed_provider_Provider::YOUTUBE;
        }

        /* calculate based on gallery content */
        $currentMode = $this->get(org_tubepress_options_category_Gallery::MODE);
        if (strpos($currentMode, 'vimeo') === 0) {
            return org_tubepress_video_feed_provider_Provider::VIMEO;
        }
        if (strpos($currentMode, 'directory') === 0) {
            return org_tubepress_video_feed_provider_Provider::DIRECTORY;
        }
        return org_tubepress_video_feed_provider_Provider::YOUTUBE;
    }

    /**
     * Set the options storage manager.
     *
     * @param org_tubepress_options_storage_StorageManager $tpsm The options storage manager.
     *
     * @return void
     */
    public function setStorageManager(org_tubepress_options_storage_StorageManager $tpsm)
    {
        $this->_tpsm = $tpsm;
    }

    /**
     * Set the input validation service.
     *
     * @param org_tubepress_options_validation_InputValidationService $valService The input validation service.
     *
     * @return void
     */
    public function setInputValidationService(org_tubepress_options_validation_InputValidationService $valService)
    {
        $this->_validationService = $valService;
    }
}
