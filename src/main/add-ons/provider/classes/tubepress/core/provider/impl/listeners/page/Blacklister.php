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
 * Filters out any videos that the user has in their blacklist.
 */
class tubepress_core_provider_impl_listeners_page_Blacklister
{
    private $_logger;

    /**
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    public function __construct(tubepress_api_log_LoggerInterface           $logger,
                                tubepress_core_options_api_ContextInterface $context)
    {
        $this->_logger  = $logger;
        $this->_context = $context;
    }

    public function onVideoGalleryPage(tubepress_core_event_api_EventInterface $event)
    {
        /**
         * @var $videos tubepress_core_provider_api_MediaItem[]
         */
        $videos         = $event->getSubject()->getItems();
        $blacklist      = $this->_context->get(tubepress_core_provider_api_Constants::OPTION_VIDEO_BLACKLIST);
        $videosToKeep   = array();
        $blacklistCount = 0;

        foreach ($videos as $video) {

            /**
             * @var $provider tubepress_core_provider_api_MediaProviderInterface
             */
            $provider = $video->getAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_PROVIDER);

            $id = $video->getAttribute($provider->getAttributeNameOfItemId());

            /* keep videos without an ID or that aren't blacklisted */
            if (!isset($id) || $this->_isNotBlacklisted($id, $blacklist)) {

                $videosToKeep[] = $video;

            } else {

                $blacklistCount++;
            }
        }

        /* modify the feed result */
        $event->getSubject()->setItems($videosToKeep);
    }

    protected function _isNotBlacklisted($id, $blacklist)
    {
        if (strpos($blacklist, $id) !== false) {

            if ($this->_logger->isEnabled()) {

                $this->_logger->debug(sprintf('Video with ID %s is blacklisted. Skipping it.', $id));
            }

            return false;
        }
        return true;
    }
}