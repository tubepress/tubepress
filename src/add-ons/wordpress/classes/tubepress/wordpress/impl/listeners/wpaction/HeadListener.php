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

class tubepress_wordpress_impl_listeners_wpaction_HeadListener
{
    /**
     * @var tubepress_wordpress_impl_wp_WpFunctions
     */
    private $_wpFunctions;

    /**
     * @var tubepress_api_html_HtmlGeneratorInterface
     */
    private $_htmlGenerator;

    public function __construct(tubepress_wordpress_impl_wp_WpFunctions   $wpFunctions,
                                tubepress_api_html_HtmlGeneratorInterface $htmlGenerator)
    {
        $this->_wpFunctions   = $wpFunctions;
        $this->_htmlGenerator = $htmlGenerator;
    }

    public function onAction_wp_head(tubepress_api_event_EventInterface $event)
    {
        /* no need to print anything in the head of the admin section */
        if ($this->_wpFunctions->is_admin()) {

            return;
        }

        /* this inline JS helps initialize TubePress */
        echo $this->_htmlGenerator->getCSS();
        echo $this->_htmlGenerator->getJS();
    }
}
