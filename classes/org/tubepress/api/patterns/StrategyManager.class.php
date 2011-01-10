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

/**
 * Finds the first strategy to execute and executes it.
 */
interface org_tubepress_api_patterns_StrategyManager
{
    /**
     * Finds and executes a strategy for the given name.
     * 
     * @param string       $tagName The strategy tag name.
     * @param unknown_type $value   The arguments to send to the strategy.
     */
    function executeStrategy($tagName);
    
    /**
     * Register a new strategy for the given tag name.
     * 
     * @param string                              $tagName  The strategy tag name.
     * @param org_tubepress_api_patterns_Strategy $strategy The strategy implementation.
     */
    function registerStrategy($tagName, org_tubepress_api_patterns_Strategy $strategy);
    
    /**
     * Register new strategies for the given tag name.
     * 
     * @param string $tagName    The strategy tag name.
     * @param array  $strategies The strategy implementations as an array.
     */
    function registerStrategies($tagName, $strategies);
}
