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
 */
class tubepress_app_impl_listeners_media_DispatchingListener
{
    /**
     * @var tubepress_lib_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    public function __construct(tubepress_lib_api_event_EventDispatcherInterface $eventDispatcher)
    {
        $this->_eventDispatcher = $eventDispatcher;
    }

    public function onMediaPageRequest(tubepress_lib_api_event_EventInterface $event)
    {
        if (!$event->hasArgument('mediaPage')) {

            return;
        }

        $page       = $event->getArgument('mediaPage');
        $pageNumber = $event->getArgument('pageNumber');

        $event = $this->_eventDispatcher->newEventInstance($page, array(
            'pageNumber' => $pageNumber
        ));

        $page = $this->_dispatchAndReturnSubject($event, tubepress_app_api_event_Events::MEDIA_PAGE_NEW);

        $event->setArgument('mediaPage', $page);
    }

    public function onMediaItemRequest(tubepress_lib_api_event_EventInterface $event)
    {
        if (!$event->hasArgument('mediaItem')) {

            return;
        }

        $item  = $event->getArgument('mediaItem');
        $event = $this->_eventDispatcher->newEventInstance($item);
        $item  = $this->_dispatchAndReturnSubject($event, tubepress_app_api_event_Events::MEDIA_ITEM_NEW);

        $event->setArgument('mediaItem', $item);
    }

    private function _dispatchAndReturnSubject(tubepress_lib_api_event_EventInterface $event, $eventName)
    {
        $this->_eventDispatcher->dispatch($eventName, $event);

        return $event->getSubject();
    }
}