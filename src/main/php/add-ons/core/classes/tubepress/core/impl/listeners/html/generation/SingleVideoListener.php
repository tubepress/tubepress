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
class tubepress_core_impl_listeners_html_generation_SingleVideoListener
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
    private $_collector;

    /**
     * @var tubepress_core_api_template_TemplateFactoryInterface
     */
    private $_templateFactory;

    public function __construct(tubepress_api_log_LoggerInterface                    $logger,
                                tubepress_core_api_options_ContextInterface          $context,
                                tubepress_core_api_event_EventDispatcherInterface    $eventDispatcher,
                                tubepress_core_api_collector_CollectorInterface      $collector,
                                tubepress_core_api_template_TemplateFactoryInterface $templateFactory)
    {
        $this->_logger          = $logger;
        $this->_context         = $context;
        $this->_eventDispatcher = $eventDispatcher;
        $this->_collector       = $collector;
        $this->_templateFactory = $templateFactory;
    }

    public function onHtmlGeneration(tubepress_core_api_event_EventInterface $event)
    {
        $videoId = $this->_context->get(tubepress_core_api_const_options_Names::VIDEO);

        if ($videoId == '') {

            return;
        }

        $videoId = $this->_context->get(tubepress_core_api_const_options_Names::VIDEO);

        if ($this->_logger->isEnabled()) {

            $this->_logger->debug(sprintf('Building single video with ID %s', $videoId));
        }

        $this->_getSingleVideoHtml($event, $videoId);
    }

    private function _getSingleVideoHtml(tubepress_core_api_event_EventInterface $event, $videoId)
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

        if ($this->_eventDispatcher->hasListeners(tubepress_core_api_const_event_EventNames::TEMPLATE_SINGLE_VIDEO)) {

            $templateEvent = $this->_eventDispatcher->newEventInstance(

                $template,
                array(

                    'video' => $video
                )
            );

            $this->_eventDispatcher->dispatch(

                tubepress_core_api_const_event_EventNames::TEMPLATE_SINGLE_VIDEO,
                $templateEvent
            );

            $template = $templateEvent->getSubject();
        }

        $html = $template->toString();

        if ($this->_eventDispatcher->hasListeners(tubepress_core_api_const_event_EventNames::HTML_SINGLE_VIDEO)) {

            $htmlEvent = $this->_eventDispatcher->newEventInstance($html);

            $this->_eventDispatcher->dispatch(

                tubepress_core_api_const_event_EventNames::HTML_SINGLE_VIDEO,
                $htmlEvent
            );

            $html = $htmlEvent->getSubject();
        }

        $event->setSubject($html);
        $event->stopPropagation();
    }
}
