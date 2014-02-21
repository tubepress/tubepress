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

class tubepress_addons_wordpress_impl_OptionsPage
{
    /**
     * Filter the content (which may be empty).
     */
    public function run()
    {
        /* get the form handler */
        $optionsForm   = tubepress_impl_patterns_sl_ServiceLocator::getOptionsPage();
        $hrps          = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
        $errors        = array();
        $justSubmitted = false;

        /* are we updating? */
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $hrps->hasParam('tubepress_save')) {

            $justSubmitted = true;

            $errors = $optionsForm->onSubmit();
        }

        print $optionsForm->getHtml($errors, $justSubmitted);
    }
}
