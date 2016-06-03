<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
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
class tubepress_gallery_impl_listeners_PaginationListener
{
    const DOTS = '<span class="tubepress_pagination_dots">...</span>';

    /**
     * @var tubepress_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_api_http_RequestParametersInterface
     */
    private $_requestParams;

    /**
     * @var tubepress_api_template_TemplatingInterface
     */
    private $_templating;

    /**
     * @var tubepress_theme_impl_CurrentThemeService
     */
    private $_currentThemeService;

    public function __construct(tubepress_api_options_ContextInterface        $context,
                                tubepress_api_url_UrlFactoryInterface         $urlFactory,
                                tubepress_api_http_RequestParametersInterface $requestParams,
                                tubepress_api_template_TemplatingInterface    $templating,
                                tubepress_theme_impl_CurrentThemeService      $currentThemeService,
                                tubepress_api_translation_TranslatorInterface $translator)
    {
        $this->_context             = $context;
        $this->_urlFactory          = $urlFactory;
        $this->_requestParams       = $requestParams;
        $this->_templating          = $templating;
        $this->_currentThemeService = $currentThemeService;
        $this->_translator          = $translator;
    }

    public function onGalleryTemplatePreRender(tubepress_api_event_EventInterface $event)
    {
        $currentTemplateVars = $event->getSubject();
        $mediaPage           = $currentTemplateVars['mediaPage'];
        $pagination          = $this->_getHtml($mediaPage->getTotalResultCount());
        $newTemplateVars     = array_merge($currentTemplateVars, array(
            tubepress_api_template_VariableNames::GALLERY_PAGINATION_HTML        => $pagination,
            tubepress_api_template_VariableNames::GALLERY_PAGINATION_SHOW_TOP    => (bool) $this->_context->get(tubepress_api_options_Names::GALLERY_PAGINATE_ABOVE),
            tubepress_api_template_VariableNames::GALLERY_PAGINATION_SHOW_BOTTOM => (bool) $this->_context->get(tubepress_api_options_Names::GALLERY_PAGINATE_BELOW),
        ));

        $event->setSubject($newTemplateVars);
    }

    private function _getHtml($vidCount)
    {
        $currentPage = $this->_requestParams->getParamValueAsInt('tubepress_page', 1);
        $vidsPerPage = $this->_context->get(tubepress_api_options_Names::FEED_ADJUSTED_RESULTS_PER_PAGE);

        if (!$vidsPerPage) {

            $vidsPerPage = $this->_context->get(tubepress_api_options_Names::FEED_RESULTS_PER_PAGE);
        }

        $newurl = $this->_urlFactory->fromCurrent();

        if (!$this->_isLegacyTheme()) {

            return $this->_paginationFromTemplate($vidCount, $currentPage, $vidsPerPage, $newurl);
        }

        return $this->_legacyPagination($vidCount, $currentPage, $vidsPerPage, 1, $newurl, 'tubepress_page');
    }

    private function _paginationFromTemplate($totalItems, $currentPage, $perPage, tubepress_api_url_UrlInterface $url)
    {
        $url->removeSchemeAndAuthority();
        $url->getQuery()->set('tubepress_page', '___page-number___');

        $vars = array(

            tubepress_api_template_VariableNames::GALLERY_PAGINATION_CURRENT_PAGE_NUMBER => $currentPage,
            tubepress_api_template_VariableNames::GALLERY_PAGINATION_TOTAL_ITEMS         => $totalItems,
            tubepress_api_template_VariableNames::GALLERY_PAGINATION_HREF_FORMAT         => "$url",
            tubepress_api_template_VariableNames::GALLERY_PAGINATION_RESULTS_PER_PAGE    => $perPage,
        );

        return $this->_templating->renderTemplate('gallery/pagination', $vars);
    }

    private function _isLegacyTheme()
    {
        $currentTheme     = $this->_currentThemeService->getCurrentTheme();
        $currentThemeName = $currentTheme->getName();

        if (strpos($currentThemeName, '/legacy') !== false) {

            return true;
        }

        if (strpos($currentTheme->getParentThemeName(), '/legacy') !== false) {

            return true;
        }

        if (strpos($currentThemeName, 'unknown/') === 0) {

            return true;
        }

        if (strpos($currentTheme->getParentThemeName(), 'unknown/') === 0) {

            return true;
        }

        return false;
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
    private function _legacyPagination($totalitems, $page = 1, $limit = 15, $adjacents = 1, tubepress_api_url_UrlInterface $url, $pagestring = '?page=')
    {
        $url->getQuery()->remove('tubepress_page');

        $prev       = $page - 1;
        $next       = $page + 1;
        $lastpage   = ceil($totalitems / $limit);
        $lpm1       = $lastpage - 1;
        $pagination = '';

        if ($lastpage > 1) {
            $pagination .= '<div class="pagination">';
            if ($page > 1) {
                $url->getQuery()->set($pagestring, $prev);
                $pagination .= $this->_buildAnchorOpener($url, true, $prev);
                $pagination .= '&laquo; ' .
                    $this->_translator->trans('prev') . //>(translatable)<
                    '</a>';
            }

            if ($lastpage < 7 + ($adjacents * 2)) {
                for ($counter = 1; $counter <= $lastpage; ++$counter) {
                    if ($counter == $page) {
                        $pagination .= "<span class=\"current\">$counter</span>";
                    } else {
                        $url->getQuery()->set($pagestring, $counter);
                        $pagination .= $this->_buildAnchorOpener($url, true, $counter);
                        $pagination .= "$counter</a>";
                    }
                }
            } elseif ($lastpage >= 7 + ($adjacents * 2)) {

                if ($page < 1 + ($adjacents * 3)) {
                    for ($counter = 1; $counter < 4 + ($adjacents * 2); ++$counter) {
                        if ($counter == $page) {
                            $pagination .= "<span class=\"current\">$counter</span>";
                        } else {
                            $url->getQuery()->set($pagestring, $counter);
                            $pagination .= $this->_buildAnchorOpener($url, true, $counter);
                            $pagination .= "$counter</a>";
                        }
                    }
                    $pagination .= self::DOTS;
                    $url->getQuery()->set($pagestring, $lpm1);
                    $pagination .= ' ' . $this->_buildAnchorOpener($url, true, $lpm1);
                    $pagination .= "$lpm1</a>";
                    $url->getQuery()->set($pagestring, $lastpage);
                    $pagination .= $this->_buildAnchorOpener($url, true, $lastpage);
                    $pagination .= "$lastpage</a>";
                } elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                    $url->getQuery()->set($pagestring, 1);
                    $pagination .= $this->_buildAnchorOpener($url, true, 1);
                    $pagination .= '1</a>';
                    $url->getQuery()->set($pagestring, 2);
                    $pagination .= $this->_buildAnchorOpener($url, true, 2);
                    $pagination .= '2</a>';
                    $pagination .= self::DOTS;

                    for ($counter = $page - $adjacents; $counter <= $page + $adjacents; ++$counter) {
                        if ($counter == $page) {
                            $pagination .= "<span class=\"current\">$counter</span>";
                        } else {
                            $url->getQuery()->set($pagestring, $counter);
                            $pagination .= ' ' . $this->_buildAnchorOpener($url, true, $counter);
                            $pagination .= "$counter</a>";
                        }
                    }
                    $pagination .= self::DOTS;

                    $url->getQuery()->set($pagestring, $lpm1);
                    $pagination .= ' ' . $this->_buildAnchorOpener($url, true, $lpm1);
                    $pagination .= "$lpm1</a>";
                    $url->getQuery()->set($pagestring, $lastpage);
                    $pagination .= ' ' . $this->_buildAnchorOpener($url, true, $lastpage);
                    $pagination .= "$lastpage</a>";

                } else {
                    $url->getQuery()->set($pagestring, 1);
                    $pagination .= $this->_buildAnchorOpener($url, true, 1);
                    $pagination .= '1</a>';
                    $url->getQuery()->set($pagestring, 2);
                    $pagination .= $this->_buildAnchorOpener($url, true, 1);
                    $pagination .= '2</a>';
                    $pagination .= self::DOTS;

                    for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; ++$counter) {
                        if ($counter == $page) {
                            $pagination .= "<span class=\"current\">$counter</span>";
                        } else {
                            $url->getQuery()->set($pagestring, $counter);
                            $pagination .= ' ' . $this->_buildAnchorOpener($url, true, $counter);
                            $pagination .= "$counter</a>";
                        }
                    }
                }
            }
            if ($page < $counter - 1) {
                $url->getQuery()->set($pagestring, $next);
                $pagination .= $this->_buildAnchorOpener($url, true, $next);
                $pagination .= $this->_translator->trans('next') . //>(translatable)<
                    ' &raquo;</a>';
            } else {
                $pagination .= '<span class="disabled">' .
                    $this->_translator->trans('next') . //>(translatable)<
                    ' &raquo;</span>';
            }
            $pagination .= "</div>\n";
        }

        return $pagination;
    }

    private function _buildAnchorOpener(tubepress_api_url_UrlInterface $href, $noFollow, $page)
    {
        $a = '<a ';

        if ($noFollow) {

            $a .= 'rel="nofollow"';
        }

        $a .= " href=\"$href\" data-page=\"$page\">";

        return $a;
    }
}
