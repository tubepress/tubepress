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
 * Displays a WordPress-specific options form for TubePress.
 */
class tubepress_plugins_wordpress_impl_options_ui_WordPressOptionsFormHandler extends tubepress_impl_options_ui_AbstractFormHandler
{
    const TEMPLATE_VAR_BOX_ARRAY = 'tubepress_plugins_wordpress_impl_options_ui_WordPressOptionsFormHandler__boxArray';

    protected function onPreTemplateToString(ehough_contemplate_api_Template $template)
    {
        $environmentDetector = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();
        $jsonEncoder         = tubepress_impl_patterns_sl_ServiceLocator::getJsonEncoder();
        $toEncode = array();

        if (! $environmentDetector->isPro()) {

            $toEncode[] = $this->_generateBox('You\'re Missing Out!', 'http://tubepress.org/snippets/wordpress/youre-missing-out.php');
        }

        $toEncode[] = $this->_generateBox('TubePress News', 'http://tubepress.org/snippets/wordpress/latest-news.php');
        $toEncode[] = $this->_generateBox('Need Help?', 'http://tubepress.org/snippets/wordpress/need-help.php');

        $template->setVariable(tubepress_plugins_wordpress_impl_options_ui_WordPressOptionsFormHandler::TEMPLATE_VAR_BOX_ARRAY, $jsonEncoder->encode($toEncode));
    }

    protected final function getRelativeTemplatePath()
    {
        return 'src/main/php/plugins/wordpress/resources/templates/options_page.tpl.php';
    }

    private function _generateBox($title, $url) {

        return array('title' => $title, 'url' => $url);
    }
}
