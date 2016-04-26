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
 * HTML generation command that generates HTML for a single video + meta info.
 */
class tubepress_single_impl_listeners_SingleItemListener
{
    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_api_media_CollectorInterface
     */
    private $_collector;

    /**
     * @var tubepress_api_template_TemplatingInterface
     */
    private $_templating;

    public function __construct(tubepress_api_log_LoggerInterface          $logger,
                                tubepress_api_options_ContextInterface     $context,
                                tubepress_api_media_CollectorInterface     $collector,
                                tubepress_api_template_TemplatingInterface $templating)
    {
        $this->_logger     = $logger;
        $this->_context    = $context;
        $this->_collector  = $collector;
        $this->_templating = $templating;
    }

    public function onHtmlGeneration(tubepress_api_event_EventInterface $event)
    {
        $mediaItemId = $this->_context->get(tubepress_api_options_Names::SINGLE_MEDIA_ITEM_ID);

        if ($mediaItemId == '') {

            return;
        }

        /* grab the media item from the provider */
        if ($this->_logger->isEnabled()) {

            $this->_logger->debug(sprintf('Asking provider for video with ID %s', $mediaItemId));
        }

        $mediaItem = $this->_collector->collectSingle($mediaItemId);
        $eventArgs = array('itemId' => $mediaItemId);

        if ($mediaItem !== null) {

            $eventArgs['mediaItem'] = $mediaItem;
        }

        $singleHtml = $this->_templating->renderTemplate('single/main', $eventArgs);

        $event->setSubject($singleHtml);
        $event->stopPropagation();
    }
}
