<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Hooks JW Player into TubePress.
 */
class tubepress_addons_jwplayer_impl_Bootstrap
{
    public function boot()
    {
        /**
         * @var $eventDispatcher tubepress_api_event_EventDispatcherInterface
         */
        $eventDispatcher            = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();

        $eventDispatcher->addListenerService(

            tubepress_api_const_event_EventNames::BOOT_COMPLETE,
            array('tubepress_addons_jwplayer_impl_listeners_boot_JwPlayerOptionsRegistrar', 'onBoot')
        );

        $eventDispatcher->addListenerService(

            tubepress_api_const_event_EventNames::TEMPLATE_EMBEDDED,
            array('tubepress_addons_jwplayer_impl_listeners_template_JwPlayerTemplateVars', 'onEmbeddedTemplate')
        );
    }
}