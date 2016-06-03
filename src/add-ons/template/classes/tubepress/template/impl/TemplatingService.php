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

class tubepress_template_impl_TemplatingService implements tubepress_api_template_TemplatingInterface
{
    /**
     * @var \Symfony\Component\Templating\EngineInterface
     */
    private $_delegate;

    /**
     * @var tubepress_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    public function __construct(\Symfony\Component\Templating\EngineInterface $delegate,
                                tubepress_api_event_EventDispatcherInterface  $eventDispatcher)
    {
        $this->_delegate        = $delegate;
        $this->_eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function renderTemplate($originalTemplateName, array $templateVars = array())
    {
        /*
         * First dispatch the template name.
         */
        $nameSelectionEvent = $this->_eventDispatcher->newEventInstance($originalTemplateName, $templateVars);
        $this->_eventDispatcher->dispatch(tubepress_api_event_Events::TEMPLATE_SELECT . ".$originalTemplateName", $nameSelectionEvent);
        $newTemplateName = $nameSelectionEvent->getSubject();

        /*
         * Fire the pre-render event for the original name.
         */
        $preRenderEvent = $this->_eventDispatcher->newEventInstance($nameSelectionEvent->getArguments());
        $this->_eventDispatcher->dispatch(tubepress_api_event_Events::TEMPLATE_PRE_RENDER . ".$originalTemplateName", $preRenderEvent);

        if ($originalTemplateName !== $newTemplateName) {

            /*
             * Fire the pre-render event for the new name.
             */
            $preRenderEvent = $this->_eventDispatcher->newEventInstance($preRenderEvent->getSubject());
            $this->_eventDispatcher->dispatch(tubepress_api_event_Events::TEMPLATE_PRE_RENDER . ".$newTemplateName", $preRenderEvent);
        }

        /*
         * Render!
         */
        $result = $this->_delegate->render($newTemplateName, $preRenderEvent->getSubject());

        if ($originalTemplateName !== $newTemplateName) {

            /*
             * Fire the post-render event.
             */
            $newPostRenderEvent = $this->_eventDispatcher->newEventInstance($result, $preRenderEvent->getSubject());
            $this->_eventDispatcher->dispatch(tubepress_api_event_Events::TEMPLATE_POST_RENDER . ".$newTemplateName", $newPostRenderEvent);
            $result = $newPostRenderEvent->getSubject();
        }

        /*
         * Fire the post-render event.
         */
        $originalPostRenderEvent = $this->_eventDispatcher->newEventInstance($result, $preRenderEvent->getSubject());
        $this->_eventDispatcher->dispatch(tubepress_api_event_Events::TEMPLATE_POST_RENDER . ".$originalTemplateName", $originalPostRenderEvent);

        return $originalPostRenderEvent->getSubject();
    }
}
