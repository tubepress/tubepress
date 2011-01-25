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
 * A generic strategy.
 */
interface org_tubepress_api_patterns_Strategy
{
    /**
     * Returns true if this strategy is able to handle
     *  the request.
     *
     * @return boolean True if the strategy can handle the request, false otherwise.
     */
    function canHandle();
    
    /**
     * Execute the strategy.
     *
     * @return unknown The result of this strategy execution.
     */
    function execute();
    
    /**
     * Called *before* canHandle() and execute() to allow the strategy
     *  to initialize itself.
     *
     * @return void
     */
    function start();
    
    /**
     * Called *after* canHandle() and execute() to allow the strategy
     *  to tear itself down.
     *
     * @return void
     */
    function stop();
}
