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
 * Simple media item collector.
 */
class tubepress_core_media_provider_impl_Collector implements tubepress_core_media_provider_api_CollectorInterface
{
    /**
     * @var tubepress_api_log_LoggerInterface Logger.
     */
    private $_logger;

    /**
     * @var boolean Is debug enabled?
     */
    private $_isDebugEnabled;

    /**
     * @var tubepress_core_media_provider_api_MediaProviderInterface[]
     */
    private $_mediaProviders = array();

    /**
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_event_api_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_core_http_api_RequestParametersInterface
     */
    private $_requestParams;

    public function __construct(tubepress_api_log_LoggerInterface                  $logger,
                                tubepress_core_options_api_ContextInterface        $context,
                                tubepress_core_event_api_EventDispatcherInterface  $eventDispatcher,
                                tubepress_core_http_api_RequestParametersInterface $requestParams)
    {
        $this->_context         = $context;
        $this->_eventDispatcher = $eventDispatcher;
        $this->_logger          = $logger;
        $this->_isDebugEnabled  = $logger->isEnabled();
        $this->_requestParams   = $requestParams;
    }

    /**
     * Collects a media gallery page.
     *
     * @return tubepress_core_media_provider_api_Page The media gallery page, never null.
     *
     * @api
     * @since 4.0.0
     */
    public function collectPage()
    {
        $mediaSource  = $this->_context->get(tubepress_core_media_provider_api_Constants::OPTION_GALLERY_SOURCE);
        $result       = null;
        $providerName = null;
        $currentPage  = $this->_getCurrentPage();

        if ($this->_isDebugEnabled) {

            $this->_logger->debug('There are ' . count($this->_mediaProviders) . ' media provider service(s) registered');

            $this->_logger->debug('Asking to see who wants to handle page ' . $currentPage . ' for gallery source "' . $mediaSource . '"');
        }

        foreach ($this->_mediaProviders as $mediaProvider) {

            $sources = $mediaProvider->getGallerySourceNames();

            if (in_array($mediaSource, $sources)) {

                if ($this->_isDebugEnabled) {

                    $this->_logger->debug($mediaProvider->getName() . ' chosen to handle page ' . $currentPage
                        . ' for gallery source "' . $mediaSource . '"');
                }

                $result = $mediaProvider->fetchPage($currentPage);

                break;
            }

            if ($this->_isDebugEnabled) {

                $this->_logger->debug($mediaProvider->getName() . ' cannot handle ' . $currentPage
                    . ' for gallery source "' . $mediaSource . '"');
            }
        }

        if ($result === null) {

            if ($this->_isDebugEnabled) {

                $this->_logger->debug('No media providers could handle this request');
            }

            $result = new tubepress_core_media_provider_api_Page();
        }

        $event = $this->_eventDispatcher->newEventInstance($result, array(
            'pageNumber' => $currentPage
        ));

        $this->_eventDispatcher->dispatch(

            tubepress_core_media_provider_api_Constants::EVENT_NEW_MEDIA_PAGE,
            $event
        );

        return $event->getSubject();
    }

    /**
     * Fetch a single media item.
     *
     * @param string $id The media item ID to fetch.
     *
     * @return tubepress_core_media_item_api_MediaItem The media item, or null not found.
     *
     * @api
     * @since 4.0.0
     */
    public function collectSingle($id)
    {
        if ($this->_isDebugEnabled) {

            $this->_logger->debug(sprintf('Fetching item with ID <code>%s</code>', $id));
        }

        foreach ($this->_mediaProviders as $mediaProvider) {

            if ($mediaProvider->recognizesItemId($id)) {

                if ($this->_isDebugEnabled) {

                    $this->_logger->debug($mediaProvider->getName() . ' recognizes item ID ' . $id);
                }

                return $mediaProvider->fetchSingle($id);
            }

            if ($this->_isDebugEnabled) {

                $this->_logger->debug($mediaProvider->getName() . ' does not recognize item ID ' . $id);
            }
        }

        return null;
    }

    public function setMediaProviders(array $providers)
    {
        $this->_mediaProviders = $providers;
    }

    private function _getCurrentPage()
    {
        $page = $this->_requestParams->getParamValueAsInt(tubepress_core_http_api_Constants::PARAM_NAME_PAGE, 1);

        if ($this->_isDebugEnabled) {

            $this->_logger->debug(sprintf('Current page number is %d', $page));
        }

        return $page;
    }
}
