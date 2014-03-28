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

class tubepress_addons_wordpress_impl_actions_AdminMenu
{
    /**
     * Filter the content (which may be empty).
     */
    public final function action(tubepress_api_event_EventInterface $event)
    {
        $wpFunctionWrapper = tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_WpFunctionsInterface::_);

        $wpFunctionWrapper->add_options_page('TubePress Options', 'TubePress', 'manage_options',
            'tubepress', array($this, 'runOptionsPage'));
    }

    public function runOptionsPage()
    {
        /**
         * @var $optionsPage tubepress_addons_wordpress_impl_OptionsPage
         */
        $optionsPage = tubepress_impl_patterns_sl_ServiceLocator::getService('wordpress.optionsPage');

        $optionsPage->run();
    }
}
