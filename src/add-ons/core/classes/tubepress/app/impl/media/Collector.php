<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Simple media item collector.
 */
class tubepress_app_impl_media_Collector implements tubepress_app_api_media_CollectorInterface
{
    /**
     * @var tubepress_platform_api_log_LoggerInterface Logger.
     */
    private $_logger;

    /**
     * @var boolean Is debug enabled?
     */
    private $_isDebugEnabled;

    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_lib_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    public function __construct(tubepress_platform_api_log_LoggerInterface        $logger,
                                tubepress_app_api_options_ContextInterface        $context,
                                tubepress_lib_api_event_EventDispatcherInterface  $eventDispatcher)
    {
        $this->_context         = $context;
        $this->_eventDispatcher = $eventDispatcher;
        $this->_logger          = $logger;
        $this->_isDebugEnabled  = $logger->isEnabled();
    }

    /**
     * Collects a media gallery page.
     *
     * @return tubepress_app_api_media_MediaPage The media gallery page, never null.
     *
     * @api
     * @since 4.0.0
     */
    public function collectPage($currentPage)
    {
        $mediaSource = $this->_context->get(tubepress_app_api_options_Names::GALLERY_SOURCE);
        $eventArgs   = array(
            'pageNumber' => $currentPage
        );

        $collectionEvent = $this->_eventDispatcher->newEventInstance($mediaSource, $eventArgs);
        $this->_eventDispatcher->dispatch(tubepress_app_api_event_Events::MEDIA_PAGE_REQUEST, $collectionEvent);

        if (!$collectionEvent->hasArgument('mediaPage')) {

            throw new RuntimeException('No acceptable providers');
        }

        return $collectionEvent->getArgument('mediaPage');
    }

    /**
     * Fetch a single media item.
     *
     * @param string $id The media item ID to fetch.
     *
     * @return tubepress_app_api_media_MediaItem The media item, or null not found.
     *
     * @api
     * @since 4.0.0
     */
    public function collectSingle($id)
    {
        if ($this->_isDebugEnabled) {

            $this->_logger->debug(sprintf('Fetching item with ID <code>%s</code>', $id));
        }

        $collectionEvent = $this->_eventDispatcher->newEventInstance($id);
        $this->_eventDispatcher->dispatch(tubepress_app_api_event_Events::MEDIA_ITEM_REQUEST, $collectionEvent);

        if (!$collectionEvent->hasArgument('mediaItem')) {

            throw new RuntimeException('No acceptable providers for item');
        }

        return $collectionEvent->getArgument('mediaItem');
    }
}
