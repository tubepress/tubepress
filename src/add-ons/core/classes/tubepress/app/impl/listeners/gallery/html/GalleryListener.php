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
 *
 */
class tubepress_app_impl_listeners_gallery_html_GalleryListener
{
    /**
     * @var tubepress_lib_api_http_RequestParametersInterface
     */
    private $_requestParameters;

    /**
     * @var tubepress_platform_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_app_api_media_CollectorInterface
     */
    private $_collector;

    /**
     * @var tubepress_lib_api_template_TemplatingInterface
     */
    private $_templating;

    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    public function __construct(tubepress_platform_api_log_LoggerInterface          $logger,
                                tubepress_app_api_options_ContextInterface          $context,
                                tubepress_lib_api_http_RequestParametersInterface   $requestParams,
                                tubepress_app_api_media_CollectorInterface          $collector,
                                tubepress_lib_api_template_TemplatingInterface      $templating)
    {
        $this->_logger            = $logger;
        $this->_context           = $context;
        $this->_requestParameters = $requestParams;
        $this->_collector         = $collector;
        $this->_templating        = $templating;
    }

    public function onHtmlGeneration(tubepress_lib_api_event_EventInterface $event)
    {
        $galleryId   = $this->_context->get(tubepress_app_api_options_Names::HTML_GALLERY_ID);
        $shouldLog   = $this->_logger->isEnabled();

        if ($galleryId == '') {

            $galleryId = mt_rand();
            $this->_context->setEphemeralOption(tubepress_app_api_options_Names::HTML_GALLERY_ID, $galleryId);
        }

        if ($shouldLog) {

            $this->_logger->debug(sprintf('Starting to build thumbnail gallery %s', $galleryId));
        }

        $pageNumber = $this->_requestParameters->getParamValueAsInt('tubepress_page', 1);

        /* first grab the items */
        if ($shouldLog) {

            $this->_logger->debug('Asking collector for a page.');
        }

        $mediaPage = $this->_collector->collectPage($pageNumber);
        $itemCount = sizeof($mediaPage->getItems());

        if ($shouldLog) {

            $this->_logger->debug(sprintf('Collector has delivered %d item(s)', $itemCount));
        }

        $templateVars = array(

            'mediaPage'  => $mediaPage,
            'pageNumber' => $pageNumber,
        );

        $html = $this->_templating->renderTemplate('gallery', $templateVars);

        /* we're done. tie up */
        if ($shouldLog) {

            $this->_logger->debug(sprintf('Done assembling gallery %d', $galleryId));
        }

        $event->setSubject($html);
        $event->stopPropagation();
    }
}