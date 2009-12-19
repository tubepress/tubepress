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
tubepress_load_classes(array('org_tubepress_pagination_PaginationService',
    'org_tubepress_options_category_Display',
    'net_php_pear_Net_URL2',
    'org_tubepress_message_MessageService',
    'org_tubepress_querystring_QueryStringService',
    'org_tubepress_options_manager_OptionsManager'));

/**
 * General purpose cache for TubePress
 */
class org_tubepress_pagination_DiggStylePaginationService implements org_tubepress_pagination_PaginationService
{
    private $_messageService;
    private $_tpom;
    private $_queryStringService;
    
    public function getHtml($vidCount)
    {
        $currentPage = $this->_queryStringService->getPageNum($_GET);
        $vidsPerPage = $this->_tpom->get(org_tubepress_options_category_Display::RESULTS_PER_PAGE);

        $newurl = new net_php_pear_Net_URL2($this->_queryStringService->getFullUrl($_SERVER));
        $newurl->unsetQueryVariable("tubepress_page");

        $result = $this->_diggStyle($this->_messageService, $currentPage, $vidCount, $vidsPerPage, 1, $newurl->getURL(), "tubepress_page");

        /* if we're using Ajax for pagination, remove all the hrefs */
    	if ($this->_tpom->get(org_tubepress_options_category_Display::AJAX_PAGINATION)) {
    		$result = preg_replace('/rel="nofollow" href="[^"]*tubepress_page=([0-9]+)[^"]*"/', 'rel="page=${1}"', $result);
    	}

	return $result;
    }
    
    public function setMessageService(org_tubepress_message_MessageService $messageService) { $this->_messageService = $messageService; }
    public function setQueryStringService(org_tubepress_querystring_QueryStringService $queryStringService) { $this->_queryStringService = $queryStringService; }
    public function setOptionsManager(org_tubepress_options_manager_OptionsManager $tpom) { $this->_tpom = $tpom; }
    
    //function to return the pagination string
    private function _diggStyle(org_tubepress_message_MessageService $messageService, $page = 1, $totalitems, $limit = 15, $adjacents = 1, $targetpage = "/", $pagestring = "?page=")
    {   
        //defaults
        if(!$adjacents) $adjacents = 1;
        if(!$limit) $limit = 15;
        if(!$page) $page = 1;
        if(!$targetpage) $targetpage = "/";
        
        //other vars
        $prev = $page - 1;                                    //previous page is page - 1
        $next = $page + 1;                                    //next page is page + 1
        $lastpage = ceil($totalitems / $limit);                //lastpage is = total items / items per page, rounded up.
        $lpm1 = $lastpage - 1;                                //last page minus 1
        
        /* 
            Now we apply our rules and draw the pagination object. 
            We're actually saving the code to a variable in case we want to draw it more than once.
        */
        $pagination = "";
        
        $url = new net_php_pear_Net_URL2($targetpage);
    
        if($lastpage > 1)
        {    
            $pagination .= "<div class=\"pagination\"";
    
            $pagination .= ">";
    
            //previous button
            if ($page > 1) {
                $url->setQueryVariable($pagestring, $prev);
                $newurl = $url->getURL();
                $pagination .= "<a rel=\"nofollow\" href=\"$newurl\">" . $messageService->_("prev") . "</a>";
            }
            else
                $pagination .= "<span class=\"disabled\">" . $messageService->_("prev") . "</span>";    
    
            //pages    
            if ($lastpage < 7 + ($adjacents * 2))    //not enough pages to bother breaking it up
            {    
                for ($counter = 1; $counter <= $lastpage; $counter++)
                {
                    if ($counter == $page)
                        $pagination .= "<span class=\"current\">$counter</span>";
                    else {
                        $url->setQueryVariable($pagestring, $counter);
                        $newurl = $url->getURL();
                        $pagination .= "<a rel=\"nofollow\" href=\"$newurl\">$counter</a>";
                    }            
                }
            }
            elseif($lastpage >= 7 + ($adjacents * 2))    //enough pages to hide some
            {
                //close to beginning; only hide later pages
                if($page < 1 + ($adjacents * 3))        
                {
                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
                    {
                        if ($counter == $page)
                            $pagination .= "<span class=\"current\">$counter</span>";
                        else {
                            $url->setQueryVariable($pagestring, $counter);
                            $newurl = $url->getURL();
                            $pagination .= "<a rel=\"nofollow\" href=\"$newurl\">$counter</a>";
                        }                
                    }
                    $pagination .= "...";
                    $url->setQueryVariable($pagestring, $lpm1);
                    $newurl = $url->getURL();
                    $pagination .= " <a rel=\"nofollow\" href=\"$newurl\">$lpm1</a>";
                    $url->setQueryVariable($pagestring, $lastpage);
                    $newurl = $url->getURL();
                    $pagination .= "<a rel=\"nofollow\" href=\"$newurl\">$lastpage</a>";        
                }
                //in middle; hide some front and some back
                elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
                {
                    $url->setQueryVariable($pagestring, 1);
                    $newurl = $url->getURL();
                    $pagination .= "<a rel=\"nofollow\" href=\"$newurl\">1</a>";
                    $url->setQueryVariable($pagestring, 2);
                    $newurl = $url->getURL();
                    $pagination .= "<a rel=\"nofollow\" href=\"$newurl\">2</a>";
                    $pagination .= "...";
                    for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
                    {
                        if ($counter == $page)
                            $pagination .= "<span class=\"current\">$counter</span>";
                        else {
                            $url->setQueryVariable($pagestring, $counter);
                            $newurl = $url->getURL();
                            $pagination .= " <a rel=\"nofollow\" href=\"$newurl\">$counter</a>";
                        }            
                    }
                    $pagination .= "...";
                    
                    $url->setQueryVariable($pagestring, $lpm1);
                    $newurl = $url->getURL();
                    $pagination .= " <a rel=\"nofollow\" href=\"$newurl\">$lpm1</a>";
                    $url->setQueryVariable($pagestring, $lastpage);
                    $newurl = $url->getURL();
                    $pagination .= " <a rel=\"nofollow\" href=\"$newurl\">$lastpage</a>";        
                }
                //close to end; only hide early pages
                else
                {
                    $url->setQueryVariable($pagestring, 1);
                    $newurl = $url->getURL();
                    $pagination .= "<a rel=\"nofollow\" href=\"$newurl\">1</a>";
                    $url->setQueryVariable($pagestring, 2);
                    $newurl = $url->getURL();
                    $pagination .= "<a rel=\"nofollow\" href=\"$newurl\">2</a>";
                    $pagination .= "...";
                    for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++)
                    {
                        if ($counter == $page)
                            $pagination .= "<span class=\"current\">$counter</span>";
                        else {
                            $url->setQueryVariable($pagestring, $counter);
                            $newurl = $url->getURL();
                            $pagination .= " <a rel=\"nofollow\" href=\"$newurl\">$counter</a>";    
                        }
                                            
                    }
                }
            }
            //next button
            if ($page < $counter - 1) {
                $url->setQueryVariable($pagestring, $next);
                $newurl = $url->getURL();
                $pagination .= "<a rel=\"nofollow\" href=\"$newurl\">" . $messageService->_("next") . "</a>";
            } else {
                $pagination .= "<span class=\"disabled\">" . $messageService->_("next") . "</span>";
            }
            $pagination .= "</div>\n";
        }
        
        return $pagination;
    }
}
