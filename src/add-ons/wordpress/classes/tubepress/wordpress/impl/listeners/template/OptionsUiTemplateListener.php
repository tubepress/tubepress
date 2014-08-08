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

class tubepress_wordpress_impl_listeners_template_OptionsUiTemplateListener implements tubepress_lib_api_template_PathProviderInterface
{
    public function onTemplateSelect(tubepress_lib_api_event_EventInterface $event)
    {
        $event->setSubject('options-ui/wp-settings-page');
    }

    /**
     * @return string[] A set of absolute filesystem directory paths
     *
     * @api
     * @since 4.0.0
     */
    public function getTemplateDirectories()
    {
        return array(
            TUBEPRESS_ROOT . '/src/add-ons/wordpress/resources/templates'
        );
    }
}