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
    
    /**
     * Finds and executes a strategy for the given name.
     */
    public function executeStrategy($strategies)
    {
        /* run the first strategy that wants to handle this */
        foreach ($strategies as $strategy) {

            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Seeing if "%s" wants to handle execution', get_class($strategy));
            
            $strategy->start();

            if ($strategy->canHandle()) {
                
                org_tubepress_impl_log_Log::log(self::LOG_PREFIX, '%s will handle execution', get_class($strategy));
                $returnValue = $strategy->execute();
                $strategy->stop();
                return $returnValue;
            }

            $strategy->stop();
        }

        throw new Exception("None of the supplied strategies were able to handle the execution");
    }

}

?>
