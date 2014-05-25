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
class tubepress_core_impl_listeners_template_ThumbGalleryPagination
{
    const DOTS = '<span class="tubepress_pagination_dots">...</span>';

    /**
     * @var tubepress_core_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_core_api_translation_TranslatorInterface
     */
    private $_translator;

    /**
     * @var tubepress_core_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_core_api_http_RequestParametersInterface
     */
    private $_requestParams;
    
    /**
     * @var tubepress_core_api_template_TemplateFactoryInterface
     */
    private $_templateFactory;

    /**
     * @var tubepress_core_api_util_UrlUtilsInterface
     */
    private $_urlUtils;

    /**
     * @var tubepress_core_api_theme_ThemeLibraryInterface
     */
    private $_themeLibrary;
    
    public function __construct(tubepress_core_api_options_ContextInterface          $context,
                                tubepress_core_api_url_UrlFactoryInterface           $urlFactory,
                                tubepress_core_api_translation_TranslatorInterface   $translator,
                                tubepress_core_api_event_EventDispatcherInterface    $eventDispatcher,
                                tubepress_core_api_http_RequestParametersInterface   $requestParams,
                                tubepress_core_api_template_TemplateFactoryInterface $templateFactory,
                                tubepress_core_api_util_UrlUtilsInterface            $urlUtils,
                                tubepress_core_api_theme_ThemeLibraryInterface       $themeLibrary)
    {
        $this->_context           = $context;
        $this->_urlFactory        = $urlFactory;
        $this->_translator        = $translator;
        $this->_eventDispatcher   = $eventDispatcher;
        $this->_requestParams     = $requestParams;
        $this->_templateFactory   = $templateFactory;
        $this->_urlUtils          = $urlUtils;
        $this->_themeLibrary      = $themeLibrary;
    }

    public function onGalleryTemplate(tubepress_core_api_event_EventInterface $event)
    {
        $providerResult = $event->getArgument('videoGalleryPage');
        $template       = $event->getSubject();
        $pagination     = $this->_getHtml($providerResult->getTotalResultCount());

        $event = $this->_eventDispatcher->newEventInstance($pagination);

        $this->_eventDispatcher->dispatch(

            tubepress_core_api_const_event_EventNames::HTML_PAGINATION,
            $event
        );

        $pagination = $event->getSubject();
        $context    = $template->getContext();

        if ($this->_context->get(tubepress_core_api_const_options_Names::PAGINATE_ABOVE)) {

            $context[tubepress_core_api_const_template_Variable::PAGINATION_TOP] = $pagination;
        }

        if ($this->_context->get(tubepress_core_api_const_options_Names::PAGINATE_BELOW)) {

            $context[tubepress_core_api_const_template_Variable::PAGINATION_BOTTOM] = $pagination;
        }

        $template->setContext(array_merge($template->getContext(), $context));
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
        $currentPage = $this->_requestParams->getParamValueAsInt(tubepress_core_api_const_http_ParamName::PAGE, 1);
        $vidsPerPage = $this->_context->get(tubepress_core_api_const_options_Names::RESULTS_PER_PAGE);
        $newurl      = $this->_urlFactory->fromCurrent();

        if ($this->_themeHasPaginationTemplate()) {

            return $this->_paginationFromTemplate($vidCount, $currentPage, $vidsPerPage, $newurl);
        }

        return $this->_diggStyle($vidCount, $currentPage, $vidsPerPage, 1, $newurl, 'tubepress_page');
    }

    private function _paginationFromTemplate($totalItems, $currentPage, $perPage, tubepress_core_api_url_UrlInterface $url)
    {
        $template = $this->_templateFactory->fromFilesystem(array(
            
            'pagination.tpl.php',
            TUBEPRESS_ROOT . '/src/main/web/themes/default/pagination.tpl.php'
        ));

        $url->getQuery()->set('tubepress_page', 'zQ12KeYf2ixV2h7l230e81QyE7Z5C54r5468pzQ12KeYf2ixV2h7l230e81QyE7Z5C54r5468p');

        $urlFormat = $this->_urlUtils->getAsStringWithoutSchemeAndAuthority($url);
        $urlFormat = str_replace('%', '%%', $urlFormat);
        $urlFormat = str_replace('zQ12KeYf2ixV2h7l230e81QyE7Z5C54r5468pzQ12KeYf2ixV2h7l230e81QyE7Z5C54r5468p', '%d', $urlFormat);
        $vars      = array(

            tubepress_core_api_const_template_Variable::PAGINATION_CURRENT_PAGE     => $currentPage,
            tubepress_core_api_const_template_Variable::PAGINATION_TOTAL_ITEMS      => $totalItems,
            tubepress_core_api_const_template_Variable::PAGINATION_HREF_FORMAT      => $urlFormat,
            tubepress_core_api_const_template_Variable::PAGINATION_RESULTS_PER_PAGE => $perPage,
            tubepress_core_api_const_template_Variable::PAGINATION_TEXT_NEXT        => $this->_translator->_('next'),
            tubepress_core_api_const_template_Variable::PAGINATION_TEXT_PREV        => $this->_translator->_('prev'),
        );

        foreach ($vars as $key => $value) {

            $template->setVariable($key, $value);
        }

        $event = $this->_eventDispatcher->newEventInstance($template);
        $this->_eventDispatcher->dispatch(tubepress_core_api_const_event_EventNames::TEMPLATE_PAGINATION, $event);

        $template = $event->getSubject();

        return $template->toString();
    }

    private function _themeHasPaginationTemplate()
    {
        return $this->_themeLibrary->getAbsolutePathToTemplate('pagination.tpl.php') !== null;
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
    private function _diggStyle($totalitems, $page = 1, $limit = 15, $adjacents = 1, tubepress_core_api_url_UrlInterface $url, $pagestring = '?page=')
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
                $newurl      = $this->_urlUtils->getAsStringWithoutSchemeAndAuthority($url);
                $pagination .= $this->_buildAnchorOpener($newurl, true, $prev);
                $pagination .= "&laquo; " .
                    $this->_translator->_('prev') .                                     //>(translatable)<
                    '</a>';
            }

            if ($lastpage < 7 + ($adjacents * 2)) {
                for ($counter = 1; $counter <= $lastpage; $counter++) {
                    if ($counter == $page) {
                        $pagination .= "<span class=\"current\">$counter</span>";
                    } else {
                        $url->getQuery()->set($pagestring, $counter);
                        $newurl      = $this->_urlUtils->getAsStringWithoutSchemeAndAuthority($url);
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
                            $url->getQuery()->set($pagestring, $counter);
                            $newurl      = $this->_urlUtils->getAsStringWithoutSchemeAndAuthority($url);
                            $pagination .= $this->_buildAnchorOpener($newurl, true, $counter);
                            $pagination .= "$counter</a>";
                        }
                    }
                    $pagination .= self::DOTS;
                    $url->getQuery()->set($pagestring, $lpm1);
                    $newurl      = $this->_urlUtils->getAsStringWithoutSchemeAndAuthority($url);
                    $pagination .= ' ' . $this->_buildAnchorOpener($newurl, true, $lpm1);
                    $pagination .= "$lpm1</a>";
                    $url->getQuery()->set($pagestring, $lastpage);
                    $newurl      = $this->_urlUtils->getAsStringWithoutSchemeAndAuthority($url);
                    $pagination .= $this->_buildAnchorOpener($newurl, true, $lastpage);
                    $pagination .= "$lastpage</a>";
                } elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                    $url->getQuery()->set($pagestring, 1);
                    $newurl      = $this->_urlUtils->getAsStringWithoutSchemeAndAuthority($url);
                    $pagination .= $this->_buildAnchorOpener($newurl, true, 1);
                    $pagination .= "1</a>";
                    $url->getQuery()->set($pagestring, 2);
                    $newurl      = $this->_urlUtils->getAsStringWithoutSchemeAndAuthority($url);
                    $pagination .= $this->_buildAnchorOpener($newurl, true, 2);
                    $pagination .= "2</a>";
                    $pagination .= self::DOTS;

                    for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                        if ($counter == $page) {
                            $pagination .= "<span class=\"current\">$counter</span>";
                        } else {
                            $url->getQuery()->set($pagestring, $counter);
                            $newurl      = $this->_urlUtils->getAsStringWithoutSchemeAndAuthority($url);
                            $pagination .= ' ' . $this->_buildAnchorOpener($newurl, true, $counter);
                            $pagination .= "$counter</a>";
                        }
                    }
                    $pagination .= self::DOTS;

                    $url->getQuery()->set($pagestring, $lpm1);
                    $newurl      = $this->_urlUtils->getAsStringWithoutSchemeAndAuthority($url);
                    $pagination .= ' ' . $this->_buildAnchorOpener($newurl, true, $lpm1);
                    $pagination .= "$lpm1</a>";
                    $url->getQuery()->set($pagestring, $lastpage);
                    $newurl      = $this->_urlUtils->getAsStringWithoutSchemeAndAuthority($url);
                    $pagination .= ' ' . $this->_buildAnchorOpener($newurl, true, $lastpage);
                    $pagination .= "$lastpage</a>";

                } else {
                    $url->getQuery()->set($pagestring, 1);
                    $newurl = $this->_urlUtils->getAsStringWithoutSchemeAndAuthority($url);
                    $pagination .= $this->_buildAnchorOpener($newurl, true, 1);
                    $pagination .= "1</a>";
                    $url->getQuery()->set($pagestring, 2);
                    $newurl = $this->_urlUtils->getAsStringWithoutSchemeAndAuthority($url);
                    $pagination .= $this->_buildAnchorOpener($newurl, true, 1);
                    $pagination .= "2</a>";
                    $pagination .= self::DOTS;

                    for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++) {
                        if ($counter == $page) {
                            $pagination .= "<span class=\"current\">$counter</span>";
                        } else {
                            $url->getQuery()->set($pagestring, $counter);
                            $newurl = $this->_urlUtils->getAsStringWithoutSchemeAndAuthority($url);
                            $pagination .= ' ' . $this->_buildAnchorOpener($newurl, true, $counter);
                            $pagination .= "$counter</a>";
                        }
                    }
                }
            }
            if ($page < $counter - 1) {
                $url->getQuery()->set($pagestring, $next);
                $newurl      = $this->_urlUtils->getAsStringWithoutSchemeAndAuthority($url);
                $pagination .= $this->_buildAnchorOpener($newurl, true, $next);
                $pagination .= $this->_translator->_('next') .             //>(translatable)<
                    ' &raquo;</a>';
            } else {
                $pagination .= '<span class="disabled">' .
                    $this->_translator->_('next') .             //>(translatable)<
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
