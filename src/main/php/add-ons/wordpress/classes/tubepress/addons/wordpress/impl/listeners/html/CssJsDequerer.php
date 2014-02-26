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
 *
 */
class tubepress_addons_wordpress_impl_listeners_html_CssJsDequerer
{
    public function onCss(tubepress_api_event_EventInterface $event)
    {
        $styles = $event->getSubject();

        $this->_filter($styles);

        $event->setSubject($styles);
    }

    public function onJs(tubepress_api_event_EventInterface $event)
    {
        $scripts = $event->getSubject();

        $this->_filter($scripts);

        $event->setSubject($scripts);
    }

    private function _filter(array &$resources)
    {
        foreach ($resources as $handle => $url) {

            if ($handle === 'tubepress') {

                unset($resources[$handle]);
            }

            if (strpos($handle, 'tubepress-theme-') === 0) {

                unset($resources[$handle]);
            }
        }
    }
}