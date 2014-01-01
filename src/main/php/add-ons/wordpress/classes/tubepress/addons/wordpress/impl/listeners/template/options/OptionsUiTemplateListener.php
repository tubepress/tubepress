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

class tubepress_addons_wordpress_impl_listeners_template_options_OptionsUiTemplateListener
{
    const TEMPLATE_VAR_BOX_ARRAY = 'tubepress_addons_wordpress_impl_listeners_template_options_OptionsUiTemplateListener__boxArray';

    public function onOptionsUiTemplate(tubepress_api_event_EventInterface $event)
    {
        /**
         * @var $template ehough_contemplate_api_Template
         */
        $template        = $event->getSubject();
        $messageService  = tubepress_impl_patterns_sl_ServiceLocator::getMessageService();

        $template->setVariable("pageTitle", $messageService->_('TubePress Options'));                                                                                                                                                                                                                       //>(translatable)<                                                                                                                                                                                                                                                                                                      //>(translatable)<
        $template->setVariable("introBlurb", $messageService->_('Here you can set the default options for TubePress. Each option here can be overridden on a per page/post basis with TubePress shortcodes. See the <a href="http://tubepress.com/documentation" target="_blank">documentation</a> for more information.')); //>(translatable)<

        $environmentDetector = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();
        $toEncode = array();

        if (! $environmentDetector->isPro()) {

            $toEncode[] = $this->_generateBox('You\'re Missing Out!', 'http://tubepress.com/snippets/wordpress/youre-missing-out.php');
        }

        $toEncode[] = $this->_generateBox('TubePress News', 'http://tubepress.com/snippets/wordpress/latest-news.php');
        $toEncode[] = $this->_generateBox('Need Help?', 'http://tubepress.com/snippets/wordpress/need-help.php');

        $template->setVariable(self::TEMPLATE_VAR_BOX_ARRAY, json_encode($toEncode));
    }

    private function _generateBox($title, $url) {

        return array('title' => $title, 'url' => $url);
    }
}