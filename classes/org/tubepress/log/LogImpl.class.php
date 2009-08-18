<?php
/**
 * Copyright 2006, 2007, 2008, 2009 Eric D. Hough (http://ehough.com)
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
    
    public function __construct()
    {
        /* record when this guy was born so we have a reference */
        $this->_birthDate = microtime(true);
        $this->_enabled = false;
    }
    
    public function log($prefix, $message)
    {
        if ($this->_enabled) {
            /* how many milliseconds have elapsed? */
            $time = (microtime(true) - $this->_birthDate) * 1000;
            
            /* print it! */
            printf("%s ms > (%s) > %s<br /><br />", 
                $time, $prefix, $message);
        }
    }

    public function setEnabled($enabled)
    {
        $this->_enabled = $enabled
            && isset($_GET['tubepress_debug'])
            && ($_GET['tubepress_debug'] == 'true');
    }
}