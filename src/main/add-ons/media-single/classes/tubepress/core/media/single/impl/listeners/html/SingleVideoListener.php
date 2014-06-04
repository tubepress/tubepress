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
 * HTML generation command that generates HTML for a single video + meta info.
 */
class tubepress_core_media_single_impl_listeners_html_SingleVideoListener
{
    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_event_api_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_core_provider_api_CollectorInterface
     */
    private $_collector;

    /**
     * @var tubepress_core_template_api_TemplateFactoryInterface
     */
    private $_templateFactory;

    public function __construct(tubepress_api_log_LoggerInterface                    $logger,
                                tubepress_core_options_api_ContextInterface          $context,
                                tubepress_core_event_api_EventDispatcherInterface    $eventDispatcher,
                                tubepress_core_provider_api_CollectorInterface       $collector,
                                tubepress_core_template_api_TemplateFactoryInterface $templateFactory)
    {
        $this->_logger          = $logger;
        $this->_context         = $context;
        $this->_eventDispatcher = $eventDispatcher;
        $this->_collector       = $collector;
        $this->_templateFactory = $templateFactory;
    }

    public function onHtmlGeneration(tubepress_core_event_api_EventInterface $event)
    {
        $videoId = $this->_context->get(tubepress_core_media_single_api_Constants::OPTION_VIDEO);

        if ($videoId == '') {

            return;
        }

        $videoId = $this->_context->get(tubepress_core_media_single_api_Constants::OPTION_VIDEO);

        if ($this->_logger->isEnabled()) {

            $this->_logger->debug(sprintf('Building single video with ID %s', $videoId));
        }

        $this->_getSingleVideoHtml($event, $videoId);
    }

    private function _getSingleVideoHtml(tubepress_core_event_api_EventInterface $event, $videoId)
    {
        $template = $this->_templateFactory->fromFilesystem(array(

            'single_video.tpl.php',
            TUBEPRESS_ROOT . '/src/main/web/themes/default/single_video.tpl.php'
        ));

        /* grab the video from the provider */
        if ($this->_logger->isEnabled()) {

            $this->_logger->debug(sprintf('Asking provider for video with ID %s', $videoId));
        }

        $video = $this->_collector->collectSingle($videoId);

        if ($this->_eventDispatcher->hasListeners(tubepress_core_media_single_api_Constants::EVENT_SINGLE_ITEM_TEMPLATE)) {

            $templateEvent = $this->_eventDispatcher->newEventInstance(

                $template,
                array(

                    'video' => $video
                )
            );

            $this->_eventDispatcher->dispatch(

                tubepress_core_media_single_api_Constants::EVENT_SINGLE_ITEM_TEMPLATE,
                $templateEvent
            );

            $template = $templateEvent->getSubject();
        }

        $html = $template->toString();

        if ($this->_eventDispatcher->hasListeners(tubepress_core_media_single_api_Constants::EVENT_SINGLE_ITEM_HTML)) {

            $htmlEvent = $this->_eventDispatcher->newEventInstance($html);

            $this->_eventDispatcher->dispatch(

                tubepress_core_media_single_api_Constants::EVENT_SINGLE_ITEM_HTML,
                $htmlEvent
            );

            $html = $htmlEvent->getSubject();
        }

        $event->setSubject($html);
        $event->stopPropagation();
    }
}
