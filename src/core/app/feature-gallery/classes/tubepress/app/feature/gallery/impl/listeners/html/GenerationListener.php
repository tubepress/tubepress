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
class tubepress_app_feature_gallery_impl_listeners_html_GenerationListener
{
    /**
     * @var tubepress_lib_event_api_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_app_http_api_RequestParametersInterface
     */
    private $_requestParameters;

    /**
     * @var tubepress_platform_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_app_media_provider_api_CollectorInterface
     */
    private $_collector;

    /**
     * @var tubepress_lib_template_api_TemplateFactoryInterface
     */
    private $_templateFactory;

    /**
     * @var tubepress_app_options_api_ContextInterface
     */
    private $_context;

    public function __construct(tubepress_platform_api_log_LoggerInterface                    $logger,
                                tubepress_app_options_api_ContextInterface          $context,
                                tubepress_lib_event_api_EventDispatcherInterface    $eventDispatcher,
                                tubepress_app_http_api_RequestParametersInterface   $requestParams,
                                tubepress_app_media_provider_api_CollectorInterface $collector,
                                tubepress_lib_template_api_TemplateFactoryInterface $templateFactory)
    {
        $this->_logger            = $logger;
        $this->_context           = $context;
        $this->_eventDispatcher   = $eventDispatcher;
        $this->_requestParameters = $requestParams;
        $this->_collector         = $collector;
        $this->_templateFactory   = $templateFactory;
    }

    public function onHtmlGeneration(tubepress_lib_event_api_EventInterface $event)
    {
        $galleryId   = $this->_context->get(tubepress_app_html_api_Constants::OPTION_GALLERY_ID);
        $shouldLog   = $this->_logger->isEnabled();

        if ($galleryId == '') {

            $galleryId = mt_rand();
            $this->_context->setEphemeralOption(tubepress_app_html_api_Constants::OPTION_GALLERY_ID, $galleryId);
        }

        if ($shouldLog) {

            $this->_logger->debug(sprintf('Starting to build thumbnail gallery %s', $galleryId));
        }

        $template = $this->_templateFactory->fromFilesystem(array(

            'gallery.tpl.php',
            TUBEPRESS_ROOT . '/web/themes/default/gallery.tpl.php'
        ));
        $pageNumber = $this->_requestParameters->getParamValueAsInt(tubepress_lib_http_api_Constants::PARAM_NAME_PAGE, 1);

        /* first grab the items */
        if ($shouldLog) {

            $this->_logger->debug('Asking collector for a page.');
        }

        $mediaPage = $this->_collector->collectPage();
        $itemCount = sizeof($mediaPage->getItems());

        if ($shouldLog) {

            $this->_logger->debug(sprintf('Collector has delivered %d item(s)', $itemCount));
        }

        /* send the template through the listeners */
        if ($this->_eventDispatcher->hasListeners(tubepress_app_feature_gallery_api_Constants::EVENT_TEMPLATE_THUMBNAIL_GALLERY)) {

            $templateEvent = $this->_eventDispatcher->newEventInstance($template, array(

                'page'       => $mediaPage,
                'pageNumber' => $pageNumber,
            ));

            $this->_eventDispatcher->dispatch(

                tubepress_app_feature_gallery_api_Constants::EVENT_TEMPLATE_THUMBNAIL_GALLERY,
                $templateEvent
            );

            $template = $templateEvent->getSubject();
        }

        $html = $template->toString();

        /* send gallery HTML through the listeners */
        if ($this->_eventDispatcher->hasListeners(tubepress_app_feature_gallery_api_Constants::EVENT_HTML_THUMBNAIL_GALLERY)) {

            $htmlEvent = $this->_eventDispatcher->newEventInstance($html, array(

                'page'       => $mediaPage,
                'pageNumber' => $pageNumber,
            ));

            $this->_eventDispatcher->dispatch(

                tubepress_app_feature_gallery_api_Constants::EVENT_HTML_THUMBNAIL_GALLERY,
                $htmlEvent
            );

            $html = $htmlEvent->getSubject();
        }

        /* we're done. tie up */
        if ($shouldLog) {

            $this->_logger->debug(sprintf('Done assembling gallery %d', $galleryId));
        }

        $event->setSubject($html);
        $event->stopPropagation();
    }
}