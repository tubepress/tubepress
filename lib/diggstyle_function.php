<?php
//function to return the pagination string
function diggstyle_getPaginationString(TubePressMessageService $messageService, $page = 1, $totalitems, $limit = 15, $adjacents = 1, $targetpage = "/", $pagestring = "?page=")
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
    
    $url = new Net_URL($targetpage);

    if($lastpage > 1)
    {    
        $pagination .= "<div class=\"pagination\"";

        $pagination .= ">";

        //previous button
        if ($page > 1) {
            $url->addQueryString($pagestring, $prev);
            $newurl = $url->getURL();
            $pagination .= "<a href=\"$newurl\">" . $messageService->_("prev") . "</a>";
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
                    $url->addQueryString($pagestring, $counter);
                    $newurl = $url->getURL();
                    $pagination .= "<a href=\"$newurl\">$counter</a>";
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
                        $url->addQueryString($pagestring, $counter);
                        $newurl = $url->getURL();
                        $pagination .= "<a href=\"$newurl\">$counter</a>";
                    }                
                }
                $pagination .= "...";
                $url->addQueryString($pagestring, $lpm1);
                $newurl = $url->getURL();
                $pagination .= " <a href=\"$newurl\">$lpm1</a>";
                $url->addQueryString($pagestring, $lastpage);
                $newurl = $url->getURL();
                $pagination .= "<a href=\"$newurl\">$lastpage</a>";        
            }
            //in middle; hide some front and some back
            elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
            {
                $url->addQueryString($pagestring, 1);
                $newurl = $url->getURL();
                $pagination .= "<a href=\"$newurl\">1</a>";
                $url->addQueryString($pagestring, 2);
                $newurl = $url->getURL();
                $pagination .= "<a href=\"$newurl\">2</a>";
                $pagination .= "...";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
                {
                    if ($counter == $page)
                        $pagination .= "<span class=\"current\">$counter</span>";
                    else {
                        $url->addQueryString($pagestring, $counter);
                        $newurl = $url->getURL();
                        $pagination .= " <a href=\"$newurl\">$counter</a>";
                    }            
                }
                $pagination .= "...";
                
                $url->addQueryString($pagestring, $lpm1);
                $newurl = $url->getURL();
                $pagination .= " <a href=\"$newurl\">$lpm1</a>";
                $url->addQueryString($pagestring, $lastpage);
                $newurl = $url->getURL();
                $pagination .= " <a href=\"$newurl\">$lastpage</a>";        
            }
            //close to end; only hide early pages
            else
            {
                $url->addQueryString($pagestring, 1);
                $newurl = $url->getURL();
                $pagination .= "<a href=\"$newurl\">1</a>";
                $url->addQueryString($pagestring, 2);
                $newurl = $url->getURL();
                $pagination .= "<a href=\"$newurl\">2</a>";
                $pagination .= "...";
                for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++)
                {
                    if ($counter == $page)
                        $pagination .= "<span class=\"current\">$counter</span>";
                    else {
                        $url->addQueryString($pagestring, $counter);
                        $newurl = $url->getURL();
                        $pagination .= " <a href=\"$newurl\">$counter</a>";    
                    }
                                        
                }
            }
        }
        //next button
        if ($page < $counter - 1) {
            $url->addQueryString($pagestring, $next);
            $newurl = $url->getURL();
            $pagination .= "<a href=\"$newurl\">" . $messageService->_("next") . "</a>";
        } else {
            $pagination .= "<span class=\"disabled\">" . $messageService->_("next") . "</span>";
        }
        $pagination .= "</div>\n";
    }
    
    return $pagination;

}
?>