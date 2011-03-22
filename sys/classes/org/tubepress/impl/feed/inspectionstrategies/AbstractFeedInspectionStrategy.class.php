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
    || require dirname(__FILE__) . '/../../../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_api_patterns_Strategy'));

/**
 * Base functionality for feed inspection.
 */
abstract class org_tubepress_impl_feed_inspectionstrategies_AbstractFeedInspectionStrategy implements org_tubepress_api_patterns_Strategy
{
    /**
     * Called *before* canHandle() and execute() to allow the strategy
     *  to initialize itself.
     */
    public function start()
    {
        //do nothing
    }

    /**
     * Called *after* canHandle() and execute() to allow the strategy
     *  to tear itself down.
     */
    public function stop()
    {
        //do nothing
    }

    /**
     * Returns true if this strategy is able to handle
     *  the request.
     */
    public function canHandle()
    {
        /* grab the arguments */
        $args = func_get_args();
        
        self::_checkArgs($args);
        
        $providerName = $args[0];
        
        return $args[0] === $this->_getNameOfHandledProvider();
    }

    /**
     * Execute the strategy.
     */
    public function execute()
    {  
        /* grab the arguments */
        $args = func_get_args();
        
        self::_checkArgs($args);
        
        $rawFeed = $args[1];

        return $this->_count($rawFeed);
    }

    protected abstract function _getNameOfHandledProvider();

    protected abstract function _count($rawFeed);

    private static function _checkArgs($args)
    {
        /* a little sanity checking */
        if (count($args) !== 2) {
            throw new Exception(sprintf("Wrong argument count. Expects 2, you sent %d", count($args)));
        }
    }
}
