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

class tubepress_wordpress_impl_actions_AdminMenu
{
    /**
     * @var tubepress_wordpress_spi_WpFunctionsInterface
     */
    private $_wpFunctions;

    /**
     * @var tubepress_wordpress_impl_OptionsPage
     */
    private $_optionsPage;

    public function __construct(tubepress_wordpress_spi_WpFunctionsInterface $wpFunctions,
                                tubepress_wordpress_impl_OptionsPage         $optionsPage)
    {
        $this->_wpFunctions = $wpFunctions;
        $this->_optionsPage = $optionsPage;
    }

    /**
     * Filter the content (which may be empty).
     */
    public final function action(tubepress_core_event_api_EventInterface $event)
    {
        $this->_wpFunctions->add_options_page('TubePress Options', 'TubePress', 'manage_options',
            'tubepress', array($this, 'runOptionsPage'));
    }

    public function runOptionsPage()
    {
        $this->_optionsPage->run();
    }
}
