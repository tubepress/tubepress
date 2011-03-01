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
     * Executes the given strategies.
     *
     * @param array $strategyInstances An array of org_tubepress_api_patterns_Strategy class names to execute.
     *
     * @return unknown The result of the strategy execution.
     */
    function executeStrategy($strategyInstances);
}
