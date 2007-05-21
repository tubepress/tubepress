<?php
/**
 * TubePressStatic.php
 * 
 * A bunch of "static" utilities that are used throughout the app
 * 
 * Copyright (C) 2007 Eric D. Hough (http://ehough.com)
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

class TubePressStatic
{    
    /**
     * Take a PEAR error object and return a prettified message
     */
    function bail($msg, $error)
    {
        $returnMsg = sprintf("%s (%s)<br /><br />", $msg, $error->message);
        
        foreach ($error->getBackTrace() as $back) {
            if (strpos($back['file'], "plugins/tubepress") === false) {
                continue;
            }
            if (!(strpos($back['file'], "lib/PEAR") === false)) {
                continue;
            }
            $returnMsg .= 
                sprintf("%s line %s <br />",
                    substr($back['file'], strpos($back['file'], "tubepress")),
                    $back['line']);
        }
        return $returnMsg;
    }

    /**
     * Returns true if we're in a mode that supports pagination
     */
    function areWePaging($options)
    {
        //TODO: fix me!
        $searchBy = $options->getValue(TP_OPT_SEARCHBY);
        if (($searchBy == TP_SRCH_USER)
            || ($searchBy == TP_SRCH_TAG)
            || ($searchBy == TP_SRCH_REL)) {
                return true;
        }
        return false;
    }
    
    /**
     * Returns what's in the address bar (obviously, only http, not https)
     */
    function fullURL()
    {
        return "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    }
    
    /**
     * Right now the only two "actions" that we do are either print out a
     * gallery or print out a single video. An "action" is loosely defined
     * as something that fills the page with data that we generate.
     */
    function determineNextAction($options)
    {
        if ($options->getValue(TP_OPT_PLAYIN) == TP_PLAYIN_NW
            && isset($_GET[TP_VID_PARAM])) {
                return "SINGLEVIDEO";
            }
                
    }
}
?>
