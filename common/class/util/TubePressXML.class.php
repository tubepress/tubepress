<?php
/**
 * TubePressXML.php
 * 
 * Does all our XML and REST dirty work
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

class TubePressXML
{
    /**
     * Constructor
     */
    private function __construct()
    {
        /* don't let anyone instantiate it */
    }

    /**
     * Connects to YouTube and returns raw XML
     */
    public static function fetch($request, $stored)
    {   
        
        /* We turn off error reporting here because Snoopy is very noisy if we
         * can't connect
         */
        error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

        $timeout = $stored->getAdvancedOptions()->get(TubePressAdvancedOptions::timeout)->getValue()->getCurrentValue();
        
        $snoopy = new Snoopy_TubePress();
        $snoopy->read_timeout = $timeout;

        if (!$snoopy->fetch($request)) {
            throw new Exception("Connection to YouTube refused");
        }
    
        if ($snoopy->timed_out) {
            throw new Exception("Connection timed out after " . $timeoout . " seconds"); 
        }
    
        if (strpos($snoopy->response_code, "200 OK") === false) {
            throw new Exception("YouTube did not respond with an HTTP OK: " . $snoopy->response_code);
        }
    
        error_reporting(E_ALL ^ E_NOTICE);

        return $snoopy->results;
    }
    
    /**
     * Takes YouTube's raw xml and tries to return an array of the videos
     */
    public static function toArray(&$youtube_xml)
    {
    
        class_exists('XML_Unserializer') || require(dirname(__FILE__) .
            '/../../../lib/PEAR/XML/XML_Serializer/Unserializer.php');
    
        $unserializer_options = array ('parseAttributes' => TRUE);

        $Unserializer = &new XML_Unserializer($unserializer_options);

        $status = $Unserializer->unserialize($youtube_xml);

        /* make sure we could read the xml */
        if (PEAR::isError($status)) {
            throw new Exception("Could not read YouTube's XML");
        }

        $result = $Unserializer->getUnserializedData();

        /* double check to make sure we have an array */
        if (!is_array($result)) {
            throw new Exception("XML unserialization error");
        }

        return $result;
    }
}
?>
