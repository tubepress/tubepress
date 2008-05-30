<?php
class TubePressNetwork {
    
    /**
     * Fetches the RSS from YouTube (or from cache)
     * 
     * @param TubePressStorageManager $tpsm The TubePressStorageManager
     * 
     * @return DOMDocument The raw RSS from YouTube
     */
    public function getRss(TubePressOptionsManager $tpom)
    {
        /* Grab the video XML from YouTube */
        $request = TubePressGalleryUrl::get($tpom);
        TubePressNetwork::_urlPostProcessing($request, $tpom);
        
        $cache = new Cache_Lite(array("cacheDir" => sys_get_temp_dir()));

        if (!($data = $cache->get($request))) {
            $req =& new HTTP_Request($request);
            if (!PEAR::isError($req->sendRequest())) {
                $data = $req->getResponseBody();
            }
            $cache->save($data, $request);
        }
        
        $doc = new DOMDocument();
        
        if (substr($data, 0, 5) != "<?xml") {
            return $doc;
        }
    
        $doc->loadXML($data);
        return $doc;
    }
    
/**
     * Appends some global query parameters on the request
     * before we fire it off to YouTube
     *
     * @param string                &$request The request to be manipulated
     * @param TubePressStorage_v160 $stored   The TubePressStorage object
     * 
     * @return void
     */
    private static function _urlPostProcessing(&$request, 
        TubePressOptionsManager $tpom)
    {
        
        $perPage = $tpom->get(TubePressDisplayOptions::RESULTS_PER_PAGE);
        $filter  = $tpom->get(TubePressAdvancedOptions::FILTER);
        $order   = $tpom->get(TubePressDisplayOptions::ORDER_BY);
        $mode    = $tpom->get(TubePressGalleryOptions::MODE);
        
        $currentPage = TubePressStatic::getPageNum();
        
        $start = ($currentPage * $perPage) - $perPage + 1;
        
        if ($start + $val > 1000) {
            $val = 1000 - $start;
        }
        
        $requestURL = new Net_URL($request);
        $requestURL->addQueryString("start-index", $start);
        $requestURL->addQueryString("max-results", $perPage);
        
        if ($filter) {
            $requestURL->addQueryString("racy", "exclude");
        } else {
            $requestURL->addQueryString("racy", "include");
        }
      
        if ($mode != TubePressGallery::PLAYLIST) {
            $requestURL->addQueryString("orderby", $order);
        }       
        
        $request = $requestURL->getURL();
    }
}