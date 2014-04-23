<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Handles applying pagination to the gallery template.
 */
class tubepress_addons_core_impl_listeners_template_ThumbGalleryPagination
{
    const DOTS = '<span class="tubepress_pagination_dots">...</span>';

    public function onGalleryTemplate(tubepress_api_event_EventInterface $event)
    {
        $context        = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $pm             = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();
        $providerResult = $event->getArgument('videoGalleryPage');
        $template       = $event->getSubject();
        $pagination     = $this->_getHtml($providerResult->getTotalResultCount());

        $event = new tubepress_spi_event_EventBase($pagination);

        $pm->dispatch(

            tubepress_api_const_event_EventNames::HTML_PAGINATION,
            $event
        );

        $pagination = $event->getSubject();

        if ($context->get(tubepress_api_const_options_names_Thumbs::PAGINATE_ABOVE)) {

            $template->setVariable(tubepress_api_const_template_Variable::PAGINATION_TOP, $pagination);
        }

        if ($context->get(tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW)) {

            $template->setVariable(tubepress_api_const_template_Variable::PAGINATION_BOTTOM, $pagination);
        }
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
        $context        = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $messageService = tubepress_impl_patterns_sl_ServiceLocator::getMessageService();
        $qss            = tubepress_impl_patterns_sl_ServiceLocator::getQueryStringService();
        $hrps           = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();

        $currentPage = $hrps->getParamValueAsInt(tubepress_spi_const_http_ParamName::PAGE, 1);
        $vidsPerPage = $context->get(tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE);
        $newurl      = new ehough_curly_Url($qss->getFullUrl($_SERVER));

        if ($this->_themeHasPaginationTemplate()) {

            return $this->_paginationFromTemplate($vidCount, $currentPage, $vidsPerPage, $newurl);
        }

        return $this->_diggStyle($vidCount, $messageService, $currentPage, $vidsPerPage, 1, $newurl, 'tubepress_page');
    }

    private function _paginationFromTemplate($totalItems, $currentPage, $perPage, ehough_curly_Url $url)
    {
        $themeHandler   = tubepress_impl_patterns_sl_ServiceLocator::getThemeHandler();
        $messageService = tubepress_impl_patterns_sl_ServiceLocator::getMessageService();
        $template       = $themeHandler->getTemplateInstance('pagination.tpl.php', '');

        $url->setQueryVariable('tubepress_page', 'zQ12KeYf2ixV2h7l230e81QyE7Z5C54r5468pzQ12KeYf2ixV2h7l230e81QyE7Z5C54r5468p');

        $urlFormat = tubepress_impl_util_UrlUtils::getAsStringWithoutSchemeAndAuthority($url);
        $urlFormat = str_replace('%', '%%', $urlFormat);
        $urlFormat = str_replace('zQ12KeYf2ixV2h7l230e81QyE7Z5C54r5468pzQ12KeYf2ixV2h7l230e81QyE7Z5C54r5468p', '%d', $urlFormat);
        $vars      = array(

            tubepress_api_const_template_Variable::PAGINATION_CURRENT_PAGE     => $currentPage,
            tubepress_api_const_template_Variable::PAGINATION_TOTAL_ITEMS      => $totalItems,
            tubepress_api_const_template_Variable::PAGINATION_HREF_FORMAT      => $urlFormat,
            tubepress_api_const_template_Variable::PAGINATION_RESULTS_PER_PAGE => $perPage,
            tubepress_api_const_template_Variable::PAGINATION_TEXT_NEXT        => $messageService->_('next'),
            tubepress_api_const_template_Variable::PAGINATION_TEXT_PREV        => $messageService->_('prev'),
        );

        foreach ($vars as $name => $value) {

            $template->setVariable($name, $value);
        }

        $event = new tubepress_spi_event_EventBase($template);
        $pm    = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();
        $pm->dispatch(tubepress_api_const_event_EventNames::TEMPLATE_PAGINATION, $event);

        $template = $event->getSubject();

        return $template->toString();
    }

    private function _themeHasPaginationTemplate()
    {
        $themeHandler = tubepress_impl_patterns_sl_ServiceLocator::getThemeHandler();

        try {

            $themeHandler->getTemplateInstance('pagination.tpl.php', '');

        } catch (InvalidArgumentException $e) {

            return false;
        }

        return true;
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
     * @return string The HTML for the pagination
     */
    private function _diggStyle($totalitems, tubepress_spi_message_MessageService $messageService, $page = 1, $limit = 15, $adjacents = 1, ehough_curly_Url $url, $pagestring = '?page=')
    {
        $url->unsetQueryVariable('tubepress_page');

        $prev       = $page - 1;
        $next       = $page + 1;
        $lastpage   = ceil($totalitems / $limit);
        $lpm1       = $lastpage - 1;
        $pagination = '';

        if ($lastpage > 1) {
            $pagination .= '<div class="pagination">';
            if ($page > 1) {
                $url->setQueryVariable($pagestring, $prev);
                $newurl      = tubepress_impl_util_UrlUtils::getAsStringWithoutSchemeAndAuthority($url);
                $pagination .= $this->_buildAnchorOpener($newurl, true, $prev);
                $pagination .= "&laquo; " .
                    $messageService->_('prev') .                                     //>(translatable)<
                    '</a>';
            }

            if ($lastpage < 7 + ($adjacents * 2)) {
                for ($counter = 1; $counter <= $lastpage; $counter++) {
                    if ($counter == $page) {
                        $pagination .= "<span class=\"current\">$counter</span>";
                    } else {
                        $url->setQueryVariable($pagestring, $counter);
                        $newurl      = tubepress_impl_util_UrlUtils::getAsStringWithoutSchemeAndAuthority($url);
                        $pagination .= $this->_buildAnchorOpener($newurl, true, $counter);
                        $pagination .= "$counter</a>";
                    }
                }
            } elseif ($lastpage >= 7 + ($adjacents * 2)) {

                if ($page < 1 + ($adjacents * 3)) {
                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                        if ($counter == $page) {
                            $pagination .= "<span class=\"current\">$counter</span>";
                        } else {
                            $url->setQueryVariable($pagestring, $counter);
                            $newurl      = tubepress_impl_util_UrlUtils::getAsStringWithoutSchemeAndAuthority($url);
                            $pagination .= $this->_buildAnchorOpener($newurl, true, $counter);
                            $pagination .= "$counter</a>";
                        }
                    }
                    $pagination .= self::DOTS;
                    $url->setQueryVariable($pagestring, $lpm1);
                    $newurl      = tubepress_impl_util_UrlUtils::getAsStringWithoutSchemeAndAuthority($url);
                    $pagination .= ' ' . $this->_buildAnchorOpener($newurl, true, $lpm1);
                    $pagination .= "$lpm1</a>";
                    $url->setQueryVariable($pagestring, $lastpage);
                    $newurl      = tubepress_impl_util_UrlUtils::getAsStringWithoutSchemeAndAuthority($url);
                    $pagination .= $this->_buildAnchorOpener($newurl, true, $lastpage);
                    $pagination .= "$lastpage</a>";
                } elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                    $url->setQueryVariable($pagestring, 1);
                    $newurl      = tubepress_impl_util_UrlUtils::getAsStringWithoutSchemeAndAuthority($url);
                    $pagination .= $this->_buildAnchorOpener($newurl, true, 1);
                    $pagination .= "1</a>";
                    $url->setQueryVariable($pagestring, 2);
                    $newurl      = tubepress_impl_util_UrlUtils::getAsStringWithoutSchemeAndAuthority($url);
                    $pagination .= $this->_buildAnchorOpener($newurl, true, 2);
                    $pagination .= "2</a>";
                    $pagination .= self::DOTS;

                    for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                        if ($counter == $page) {
                            $pagination .= "<span class=\"current\">$counter</span>";
                        } else {
                            $url->setQueryVariable($pagestring, $counter);
                            $newurl      = tubepress_impl_util_UrlUtils::getAsStringWithoutSchemeAndAuthority($url);
                            $pagination .= ' ' . $this->_buildAnchorOpener($newurl, true, $counter);
                            $pagination .= "$counter</a>";
                        }
                    }
                    $pagination .= self::DOTS;

                    $url->setQueryVariable($pagestring, $lpm1);
                    $newurl      = tubepress_impl_util_UrlUtils::getAsStringWithoutSchemeAndAuthority($url);
                    $pagination .= ' ' . $this->_buildAnchorOpener($newurl, true, $lpm1);
                    $pagination .= "$lpm1</a>";
                    $url->setQueryVariable($pagestring, $lastpage);
                    $newurl      = tubepress_impl_util_UrlUtils::getAsStringWithoutSchemeAndAuthority($url);
                    $pagination .= ' ' . $this->_buildAnchorOpener($newurl, true, $lastpage);
                    $pagination .= "$lastpage</a>";

                } else {
                    $url->setQueryVariable($pagestring, 1);
                    $newurl = tubepress_impl_util_UrlUtils::getAsStringWithoutSchemeAndAuthority($url);
                    $pagination .= $this->_buildAnchorOpener($newurl, true, 1);
                    $pagination .= "1</a>";
                    $url->setQueryVariable($pagestring, 2);
                    $newurl = tubepress_impl_util_UrlUtils::getAsStringWithoutSchemeAndAuthority($url);
                    $pagination .= $this->_buildAnchorOpener($newurl, true, 1);
                    $pagination .= "2</a>";
                    $pagination .= self::DOTS;

                    for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++) {
                        if ($counter == $page) {
                            $pagination .= "<span class=\"current\">$counter</span>";
                        } else {
                            $url->setQueryVariable($pagestring, $counter);
                            $newurl = tubepress_impl_util_UrlUtils::getAsStringWithoutSchemeAndAuthority($url);
                            $pagination .= ' ' . $this->_buildAnchorOpener($newurl, true, $counter);
                            $pagination .= "$counter</a>";
                        }
                    }
                }
            }
            if ($page < $counter - 1) {
                $url->setQueryVariable($pagestring, $next);
                $newurl      = tubepress_impl_util_UrlUtils::getAsStringWithoutSchemeAndAuthority($url);
                $pagination .= $this->_buildAnchorOpener($newurl, true, $next);
                $pagination .= $messageService->_('next') .             //>(translatable)<
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

    private function _buildAnchorOpener($href, $noFollow, $page)
    {
        $a = '<a ';

        if ($noFollow) {

            $a .= 'rel="nofollow"';
        }

        $a .= " href=\"$href\" data-page=\"$page\">";

        return $a;
    }
}
