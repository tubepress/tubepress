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
|| require dirname(__FILE__) . '/../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_api_patterns_StrategyManager',
    'org_tubepress_impl_log_Log',
    'org_tubepress_api_patterns_Strategy'));

class org_tubepress_impl_patterns_StrategyManagerImpl implements org_tubepress_api_patterns_StrategyManager
{
    const LOG_PREFIX = 'Strategy Manager';
    
    private $_strategies;

    public function __construct()
    {
        $this->_strategies = array();
    }

    /**
     * Finds and executes a strategy for the given name.
     * 
     * @param string       $tagName The strategy tag name.
     */
    public function executeStrategy($tagName)
    {
        /* no callbacks registered for this strategy? */
        if (!isset($this->_strategies[$tagName])) {
            throw new Exception("No strategies defined for $tagName");
        }

        /* run the first strategy that wants to handle this */
        foreach ($this->_strategies[$tagName] as $strategy) {

            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Seeing if "%s" wants to handle "%s"', get_class($strategy), $tagName);
            
            $strategy->start();

            if ($strategy->canHandle()) {
                
                org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Handling "%s" with "%s"', $tagName, get_class($strategy));
                $returnValue = $strategy->execute();
                $strategy->stop();
                return $returnValue;
            }

            $strategy->stop();
        }

        throw new Exception("No strategies were able to handle $tagName");
    }

    /**
     * Register a new strategy for the given tag name.
     * 
     * @param string                              $tagName  The strategy tag name.
     * @param org_tubepress_api_patterns_Strategy $strategy The strategy implementation.
     */
    public function registerStrategy($tagName, org_tubepress_api_patterns_Strategy $strategy) {

        /* sanity check on the strategy name */
        if (!is_string($tagName)) {
            throw new Exception('Only strings can be used to identify a strategy');
        }

        /* first time we're seeing this strategy name? */
        if (!isset($this->_strategies[$tagName])) {
            $this->_strategies[$tagName] = array();
        }

        /* everything looks good. */
        array_push($this->_strategies[$tagName], $strategy);
    }
    
    /**
     * Register new strategies for the given tag name.
     * 
     * @param string $tagName    The strategy tag name.
     * @param array  $strategies The strategy implementations as an array.
     */
    function registerStrategies($tagName, $strategies)
    {
        if (!is_array($strategies)) {
            throw new Exception('Second argument to registerStrategies() must be an array');
        }
        
        foreach ($strategies as $strategy) {
            $this->registerStrategy($tagName, $strategy);
        }
    }
}

?>
