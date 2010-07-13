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

/**
 * Logger
 */
class org_tubepress_log_Log
{
    private static $_birthDate;
    private static $_enabled = false;
    
    public static function log()
    {
        if (org_tubepress_log_Log::$_enabled) {
            $numArgs = func_num_args();
            $prefix = func_get_arg(0);
            $message = func_get_arg(1);
            
            /* how many milliseconds have elapsed? */
            $time = (microtime(true) - org_tubepress_log_Log::$_birthDate) * 1000;
            
            if ($numArgs > 2) {
                $args = func_get_args();
                $message = vsprintf($message, array_slice($args, 2, count($args)));
            }
            
            /* print it! */
            printf("%s ms > (%s) > %s (memory used: %s)<br /><br />", $time, $prefix, $message, number_format(memory_get_usage()));
        }
    }

    public static function setEnabled($enabled, $getVars)
    {
        org_tubepress_log_Log::$_enabled = $enabled
            && isset($getVars['tubepress_debug'])
            && $getVars['tubepress_debug'] == 'true';
        
        if (org_tubepress_log_Log::$_enabled) {
            org_tubepress_log_Log::$_birthDate = microtime(true);
        }
    }
    
}