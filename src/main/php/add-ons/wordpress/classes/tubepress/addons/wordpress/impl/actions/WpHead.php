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

class tubepress_addons_wordpress_impl_actions_WpHead
{
    /**
     * Filter the content (which may be empty).
     */
    public final function action(tubepress_api_event_EventInterface $event)
    {
        $wordPressFunctionWrapper = tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_WpFunctionsInterface::_);

        /* no need to print anything in the head of the admin section */
        if ($wordPressFunctionWrapper->is_admin()) {

            return;
        }

        $hh = tubepress_impl_patterns_sl_ServiceLocator::getCssAndJsHtmlGenerator();

        /* this inline JS helps initialize TubePress */
        print $hh->getCssHtml();
        print $hh->getJsHtml();
    }
}
