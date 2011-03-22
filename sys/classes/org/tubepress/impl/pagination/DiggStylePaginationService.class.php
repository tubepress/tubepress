<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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
    || require dirname(__FILE__) . '/../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_api_pagination_Pagination',
    'org_tubepress_api_const_options_names_Display',
    'org_tubepress_api_url_Url',
    'org_tubepress_api_message_MessageService',
    'org_tubepress_api_querystring_QueryStringService',
    'org_tubepress_api_options_OptionsManager',
    'org_tubepress_impl_ioc_IocContainer'));

/**
 * General purpose cache for TubePress
 */
class org_tubepress_impl_pagination_DiggStylePaginationService implements org_tubepress_api_pagination_Pagination
{
    /**
     * Get the HTML for pagination.
     *
     * @param int $vidCount The total number of results in this gallery
     *
     * @return string The HTML for the pagination.
     */
    public function getHtml($vidCount)
    {
        $ioc            = org_tubepress_impl_ioc_IocContainer::getInstance();
        $tpom           = $ioc->get('org_tubepress_api_options_OptionsManager');
        $messageService = $ioc->get('org_tubepress_api_message_MessageService');
        $qss            = $ioc->get('org_tubepress_api_querystring_QueryStringService');
        
        $currentPage = $qss->getPageNum($_GET);
        $vidsPerPage = $tpom->get(org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE);

        $newurl = new org_tubepress_api_url_Url($qss->getFullUrl($_SERVER));
        $newurl->unsetQueryVariable('tubepress_page');

        $result = $this->_diggStyle($vidCount, $messageService, $currentPage, $vidsPerPage, 1, $newurl->toString(), 'tubepress_page');

        /* if we're using Ajax for pagination, remove all the hrefs */
        if ($tpom->get(org_tubepress_api_const_options_names_Display::AJAX_PAGINATION)) {
            $result = preg_replace('/rel="nofollow" href="[^"]*tubepress_page=([0-9]+)[^"]*"/', 'rel="page=${1}"', $result);
        }

        return $result;
    }

    /**
     * Does the heavy lifting of generating pagination.
     *
     * @param int    $totalitems The total items in this gallery.
     * @param int    $page       The current page number.
     * @param int    $limit      How many videos per page.
     * @param int    $adjacents  How many adjacents.
     * @param string $targetpage The target page
     * @param string $pagestring The query parameter controlling the page number.
     *
     * @return The HTML for the pagination
     */
    private function _diggStyle($totalitems, org_tubepress_api_message_MessageService $messageService, $page = 1, $limit = 15, $adjacents = 1, $targetpage = '/', $pagestring = '?page=')
    {
        $prev       = $page - 1;
        $next       = $page + 1;
        $lastpage   = ceil($totalitems / $limit);
        $lpm1       = $lastpage - 1;
        $pagination = '';

        $url = new org_tubepress_api_url_Url($targetpage);

        if ($lastpage > 1) {
            $pagination .= '<div class="pagination">';
            if ($page > 1) {
                $url->setQueryVariable($pagestring, $prev);
                $newurl      = $url->toString();
                $pagination .= "<a rel=\"nofollow\" href=\"$newurl\">" . $messageService->_("prev") . '</a>';
            } else {
                $pagination .= '<span class="disabled">' . $messageService->_("prev") . '</span>';
            }

            if ($lastpage < 7 + ($adjacents * 2)) {
                for ($counter = 1; $counter <= $lastpage; $counter++) {
                    if ($counter == $page) {
                        $pagination .= "<span class=\"current\">$counter</span>";
                    } else {
                        $url->setQueryVariable($pagestring, $counter);
                        $newurl      = $url->toString();
                        $pagination .= "<a rel=\"nofollow\" href=\"$newurl\">$counter</a>";
                    }
                }
            } elseif ($lastpage >= 7 + ($adjacents * 2)) {

                if ($page < 1 + ($adjacents * 3)) {
                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                        if ($counter == $page) {
                            $pagination .= "<span class=\"current\">$counter</span>";
                        } else {
                            $url->setQueryVariable($pagestring, $counter);
                            $newurl      = $url->toString();
                            $pagination .= "<a rel=\"nofollow\" href=\"$newurl\">$counter</a>";
                        }
                    }
                    $pagination .= '...';
                    $url->setQueryVariable($pagestring, $lpm1);
                    $newurl      = $url->toString();
                    $pagination .= " <a rel=\"nofollow\" href=\"$newurl\">$lpm1</a>";
                    $url->setQueryVariable($pagestring, $lastpage);
                    $newurl      = $url->toString();
                    $pagination .= "<a rel=\"nofollow\" href=\"$newurl\">$lastpage</a>";
                } elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                    $url->setQueryVariable($pagestring, 1);
                    $newurl      = $url->toString();
                    $pagination .= "<a rel=\"nofollow\" href=\"$newurl\">1</a>";
                    $url->setQueryVariable($pagestring, 2);
                    $newurl      = $url->toString();
                    $pagination .= "<a rel=\"nofollow\" href=\"$newurl\">2</a>";
                    $pagination .= '...';

                    for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                        if ($counter == $page) {
                            $pagination .= "<span class=\"current\">$counter</span>";
                        } else {
                            $url->setQueryVariable($pagestring, $counter);
                            $newurl      = $url->toString();
                            $pagination .= " <a rel=\"nofollow\" href=\"$newurl\">$counter</a>";
                        }
                    }
                    $pagination .= '...';

                    $url->setQueryVariable($pagestring, $lpm1);
                    $newurl      = $url->toString();
                    $pagination .= " <a rel=\"nofollow\" href=\"$newurl\">$lpm1</a>";
                    $url->setQueryVariable($pagestring, $lastpage);
                    $newurl      = $url->toString();
                    $pagination .= " <a rel=\"nofollow\" href=\"$newurl\">$lastpage</a>";

                } else {
                    $url->setQueryVariable($pagestring, 1);
                    $newurl = $url->toString();
                    $pagination .= "<a rel=\"nofollow\" href=\"$newurl\">1</a>";
                    $url->setQueryVariable($pagestring, 2);
                    $newurl = $url->toString();
                    $pagination .= "<a rel=\"nofollow\" href=\"$newurl\">2</a>";
                    $pagination .= '...';

                    for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++) {
                        if ($counter == $page) {
                            $pagination .= "<span class=\"current\">$counter</span>";
                        } else {
                            $url->setQueryVariable($pagestring, $counter);
                            $newurl = $url->toString();
                            $pagination .= " <a rel=\"nofollow\" href=\"$newurl\">$counter</a>";
                        }
                    }
                }
            }
            if ($page < $counter - 1) {
                $url->setQueryVariable($pagestring, $next);
                $newurl      = $url->toString();
                $pagination .= "<a rel=\"nofollow\" href=\"$newurl\">" . $messageService->_('next') . '</a>';
            } else {
                $pagination .= '<span class="disabled">' . $messageService->_('next') . '</span>';
            }
            $pagination .= "</div>\n";
        }
        return $pagination;
    }
}
