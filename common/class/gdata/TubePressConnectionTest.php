<?php
/**
 * Copyright 2006, 2007, 2008 Eric D. Hough (http://ehough.com)
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
 * This bit of code simply tests your connection to YouTube
 */

include dirname(__FILE__) . "/../../../tubepress_classloader.php";

print "You should see YouTube's homepage load below...<br /><br />";
tubepress_run_connection_test("http://www.youtube.com");
print "Now you should see a bunch of XML below...<br /><br />";
tubepress_run_connection_test("http://gdata.youtube.com/feeds/api/standardfeeds/top_rated", true);


function tubepress_run_connection_test($url, $escape = false) {
    $req =& new HTTP_Request($url);
    $result = $req->sendRequest();
    if (!PEAR::isError($result)) {
        $data = $req->getResponseBody();
        if ($escape) {
        	$data = htmlentities($data);
        }
        print_r($data);
    } else {
	    print_r($result);
    }
}

?>