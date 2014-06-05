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

class tubepress_wordpress_impl_actions_WpHead
{
    /**
     * @var tubepress_core_html_api_HtmlGeneratorInterface
     */
    private $_htmlGenerator;

    /**
     * @var tubepress_wordpress_spi_WpFunctionsInterface
     */
    private $_wpFunctions;

    public function __construct(tubepress_core_html_api_HtmlGeneratorInterface $htmlGenerator,
                                tubepress_wordpress_spi_WpFunctionsInterface   $wpFunctions)
    {
        $this->_htmlGenerator = $htmlGenerator;
        $this->_wpFunctions   = $wpFunctions;
    }

    /**
     * Filter the content (which may be empty).
     */
    public final function action(tubepress_core_event_api_EventInterface $event)
    {
        /* no need to print anything in the head of the admin section */
        if ($this->_wpFunctions->is_admin()) {

            return;
        }

        /* this inline JS helps initialize TubePress */
        print $this->_htmlGenerator->getCssHtml();
        print $this->_htmlGenerator->getJsHtml();
    }
}
