<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_http_ParamName',
    'org_tubepress_api_const_plugin_FilterPoint',
    'org_tubepress_api_const_options_names_Display',
    'org_tubepress_api_const_template_Variable',
    'org_tubepress_api_exec_ExecutionContext',
    'org_tubepress_api_http_HttpRequestParameterService',
    'org_tubepress_api_plugin_PluginManager',
    'org_tubepress_api_url_Url',
    'org_tubepress_impl_ioc_IocContainer',
));

/**
 * Handles applying pagination to the gallery template.
 */
class org_tubepress_impl_plugin_filters_gallerytemplate_Pagination
{
    const DOTS = '<span class="tubepress_pagination_dots">...</span>';

    public function alter_galleryTemplate(org_tubepress_api_template_Template $template, org_tubepress_api_provider_ProviderResult $providerResult, $page, $providerName)
    {
        $ioc           = org_tubepress_impl_ioc_IocContainer::getInstance();
        $context       = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $pm            = $ioc->get(org_tubepress_api_plugin_PluginManager::_);
        $pagination    = $this->_getHtml($providerResult->getEffectiveTotalResultCount());
        $pagination    = $pm->runFilters(org_tubepress_api_const_plugin_FilterPoint::HTML_PAGINATION, $pagination);

        if ($context->get(org_tubepress_api_const_options_names_Thumbs::PAGINATE_ABOVE)) {
            $template->setVariable(org_tubepress_api_const_template_Variable::PAGINATION_TOP, $pagination);
        }
        if ($context->get(org_tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW)) {
            $template->setVariable(org_tubepress_api_const_template_Variable::PAGINATION_BOTTOM, $pagination);
        }

        return $template;
    }

    /**
     * Get the HTML for pagination.
     *
     * @param int $vidCount The total number of results in this gallery
     *
     * @return string The HTML for the pagination.
     */
    private function _getHtml($vidCount)
    {
        $ioc            = org_tubepress_impl_ioc_IocContainer::getInstance();
        $context        = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $messageService = $ioc->get(org_tubepress_api_message_MessageService::_);
        $qss            = $ioc->get(org_tubepress_api_querystring_QueryStringService::_);
        $hrps           = $ioc->get(org_tubepress_api_http_HttpRequestParameterService::_);

        $currentPage = $hrps->getParamValueAsInt(org_tubepress_api_const_http_ParamName::PAGE, 1);
        $vidsPerPage = $context->get(org_tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE);

        $newurl = new org_tubepress_api_url_Url($qss->getFullUrl($_SERVER));
        $newurl->unsetQueryVariable('tubepress_page');

        $result = $this->_diggStyle($vidCount, $messageService, $currentPage, $vidsPerPage, 1, $newurl->toString(), 'tubepress_page');

        /* if we're using Ajax for pagination, remove all the hrefs */
        if ($context->get(org_tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION)) {
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
                $pagination .= "<a rel=\"nofollow\" href=\"$newurl\">&laquo; " .
                    $messageService->_('prev') .                                     //>(translatable)<
                	'</a>';
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
                    $pagination .= self::DOTS;
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
                    $pagination .= self::DOTS;

                    for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                        if ($counter == $page) {
                            $pagination .= "<span class=\"current\">$counter</span>";
                        } else {
                            $url->setQueryVariable($pagestring, $counter);
                            $newurl      = $url->toString();
                            $pagination .= " <a rel=\"nofollow\" href=\"$newurl\">$counter</a>";
                        }
                    }
                    $pagination .= self::DOTS;

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
                    $pagination .= self::DOTS;

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
                $pagination .= "<a rel=\"nofollow\" href=\"$newurl\">" .
                    $messageService->_('next') .             //>(translatable)<
                	' &raquo;</a>';
            } else {
                $pagination .= '<span class="disabled">' .
                    $messageService->_('next') .             //>(translatable)<
                	' &raquo;</span>';
            }
            $pagination .= "</div>\n";
        }
        return $pagination;
    }
}
