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
tubepress_load_classes(array('org_tubepress_log_Log'));

/**
 * Logging implementation that spits out statements to HTML
 */
class org_tubepress_log_LogImpl implements org_tubepress_log_Log
{
    private $_birthDate;
    private $_enabled;
    private $_buffer;
    
    public function __construct()
    {
        /* record when this guy was born so we have a reference */
        $this->_birthDate = microtime(true);
        $this->_enabled = false;
        $this->_buffer = array();
    }
    
    public function log()
    {
        if ($this->_enabled) {
            $numArgs = func_num_args();
            $prefix = func_get_arg(0);
            $message = func_get_arg(1);
            
            /* how many milliseconds have elapsed? */
            $time = (microtime(true) - $this->_birthDate) * 1000;
            
            if ($numArgs > 2) {
                $args = func_get_args();
                $message = vsprintf($message, array_slice($args, 2, count($args)));
            }
            
            /* print it! */
            printf("%s ms > (%s) > %s<br /><br />", $time, $prefix, $message);
        }
    }

    public function setEnabled($enabled, $getVars)
    {
        $this->_enabled = $enabled
            && isset($getVars['tubepress_debug'])
            && $getVars['tubepress_debug'] == 'true';
    }
}