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

class tubepress_core_impl_listeners_html_generation_ThumbGalleryListener
{
    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_core_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_core_api_collector_CollectorInterface
     */
    private $_videoCollector;

    /**
     * @var tubepress_core_api_http_RequestParametersInterface
     */
    private $_requestParams;

    /**
     * @var tubepress_core_api_template_TemplateFactoryInterface
     */
    private $_templateFactory;

    public function __construct(tubepress_api_log_LoggerInterface                    $logger,
                                tubepress_core_api_options_ContextInterface          $context,
                                tubepress_core_api_event_EventDispatcherInterface    $eventDispatcher,
                                tubepress_core_api_collector_CollectorInterface      $collector,
                                tubepress_core_api_http_RequestParametersInterface   $requestParams,
                                tubepress_core_api_template_TemplateFactoryInterface $templateFactory)
    {
        $this->_logger          = $logger;
        $this->_context         = $context;
        $this->_eventDispatcher = $eventDispatcher;
        $this->_videoCollector  = $collector;
        $this->_requestParams   = $requestParams;
        $this->_templateFactory = $templateFactory;
    }

    public function onHtmlGeneration(tubepress_core_api_event_EventInterface $event)
    {
        $galleryId   = $this->_context->get(tubepress_core_api_const_options_Names::GALLERY_ID);
        $shouldLog   = $this->_logger->isEnabled();

        if ($galleryId == '') {

            $this->_context->set(tubepress_core_api_const_options_Names::GALLERY_ID, mt_rand());
        }

        if ($shouldLog) {

            $this->_logger->debug(sprintf('Starting to build thumbnail gallery %s', $galleryId));
        }

        $template = $this->_templateFactory->fromFilesystem(array(

            'gallery.tpl.php',
            TUBEPRESS_ROOT . '/src/main/web/themes/default/gallery.tpl.php'
        ));
        $page = $this->_requestParams->getParamValueAsInt(tubepress_core_api_const_http_ParamName::PAGE, 1);

        /* first grab the videos */
        if ($shouldLog) {

            $this->_logger->debug('Asking provider for videos');
        }

        $feedResult = $this->_videoCollector->collectPage();

        $numVideos  = sizeof($feedResult->getVideos());

        if ($shouldLog) {

            $this->_logger->debug(sprintf('Provider has delivered %d videos', $numVideos));
        }

        /* send the template through the listeners */
        if ($this->_eventDispatcher->hasListeners(tubepress_core_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY)) {

            $templateEvent = $this->_eventDispatcher->newEventInstance($template, array(

                'page'             => $page,
                'videoGalleryPage' => $feedResult
            ));

            $this->_eventDispatcher->dispatch(

                tubepress_core_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY,
                $templateEvent
            );

            $template = $templateEvent->getSubject();
        }

        $html = $template->toString();

        /* send gallery HTML through the listeners */
        if ($this->_eventDispatcher->hasListeners(tubepress_core_api_const_event_EventNames::HTML_THUMBNAIL_GALLERY)) {

            $htmlEvent = $this->_eventDispatcher->newEventInstance($html, array(

                'page'             => $page,
                'videoGalleryPage' => $feedResult
            ));

            $this->_eventDispatcher->dispatch(

                tubepress_core_api_const_event_EventNames::HTML_THUMBNAIL_GALLERY,
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
