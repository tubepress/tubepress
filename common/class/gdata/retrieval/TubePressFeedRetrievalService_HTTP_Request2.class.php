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
 * Implementation of TubePressGdataService that uses PEAR's HTTP_Request class
 *
 */
class TubePressFeedRetrievalService_HTTP_Request2 implements TubePressFeedRetrievalService
{
	
    /**
     * Fetches the RSS from YouTube
     * 
     * @param TubePressOptionsManager $tpom The TubePress options manager
     * 
     * @return DOMDocument The raw RSS from YouTube
     */
    public function fetch($url)
    {   
        $data = $this->_fetchFromNetwork($url);

        $data = trim($data);
        
        $doc = new DOMDocument();

       if (substr($data,0,1) != "<") {
        	throw new Exception("YouTube returned non-xml: " . $data);
        }
        if ($doc->loadXML($data) === FALSE) {
        	throw new Exception("YouTube returned invalid XML: " . $data);
        }
        
        return $doc;
    }
    
    private function _fetchFromNetwork($request) {
    	$data = "";
    	$request = new Net_URL2($request);
    	$req = new HTTP_Request2($request);
    	$req->setAdapter(new HTTP_Request2_Adapter_Socket());

    	$response = $req->send();
       	$data = $response->getBody();
        return $data;
    }
}