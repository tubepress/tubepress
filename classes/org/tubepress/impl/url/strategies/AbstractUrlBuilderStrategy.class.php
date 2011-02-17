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

function_exists('tubepress_load_classes')
    || require(dirname(__FILE__) . '/../../../../../tubepress_classloader.php');
tubepress_load_classes(array('org_tubepress_api_patterns_Strategy',
    'org_tubepress_api_const_options_values_ModeValue',
    'org_tubepress_api_options_OptionsManager',
    'org_tubepress_api_const_options_names_Advanced',
    'org_tubepress_api_const_options_names_Display',
    'org_tubepress_api_const_options_names_Embedded',
    'org_tubepress_api_const_options_names_Meta',
    'org_tubepress_api_url_Url',
    'org_tubepress_api_const_options_names_Feed'));

/**
 * Base URL builder functionality.
 */
abstract class org_tubepress_impl_url_strategies_AbstractUrlBuilderStrategy implements org_tubepress_api_patterns_Strategy
{
    /**
     * Returns true if this strategy is able to handle
     *  the request.
     *
     * @return boolean True if the strategy can handle the request, false otherwise.
     */
    public function canHandle()
    {
        $args = func_get_args();
        self::_checkArgs($args);
        
        return $args[0] === $this->_getHandledProviderName();
    }
    
    /**
     * Execute the strategy.
     *
     * @return unknown The result of this strategy execution.
     */
    public function execute()
    {
        $args = func_get_args();
        self::_checkArgs($args);
        
        /* single video */
        if ($args[1]) {
            return $this->_buildSingleVideoUrl($args[2]);
        }
        return $this->_buildGalleryUrl($args[2]);
    }
    
    protected abstract function _getHandledProviderName();
    
    protected abstract function _buildSingleVideoUrl($id);
    
    protected abstract function _buildGalleryUrl($page);
    
    /**
     * Called *before* canHandle() and execute() to allow the strategy
     *  to initialize itself.
     *
     * @return void
     */
    public function start()
    {
        //do nothing
    }
    
    /**
     * Called *after* canHandle() and execute() to allow the strategy
     *  to tear itself down.
     *
     * @return void
     */
    public function stop()
    {
        //do nothing
    }
    
    private static function _checkArgs($args)
    {
        if (sizeof($args) !== 3) {
            throw new Exception("Expected 3 args, only got " . sizeof($args));
        }
        if (!is_bool($args[1])) {
            throw new Exception("Arg 2 must be boolean");
        }
    }
}
