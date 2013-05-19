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

        $template->setVariable(tubepress_impl_options_ui_DefaultFormHandler::TEMPLATE_VAR_TITLE, $messageService->_('TubePress Options'));                                                                                                                                                                                                                                                                                                                                 //>(translatable)<
        $template->setVariable(tubepress_impl_options_ui_DefaultFormHandler::TEMPLATE_VAR_INTRO, $messageService->_('Here you can set the default options for TubePress. Each option here can be overridden on a per page/post basis with TubePress shortcodes. See the <a href="http://tubepress.org/documentation">documentation</a> for more information.')); //>(translatable)<

        $environmentDetector = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();
        $toEncode = array();

        if (! $environmentDetector->isPro()) {

            $toEncode[] = $this->_generateBox('You\'re Missing Out!', 'http://tubepress.org/snippets/wordpress/youre-missing-out.php');
        }

        $toEncode[] = $this->_generateBox('TubePress News', 'http://tubepress.org/snippets/wordpress/latest-news.php');
        $toEncode[] = $this->_generateBox('Need Help?', 'http://tubepress.org/snippets/wordpress/need-help.php');

        $template->setVariable(self::TEMPLATE_VAR_BOX_ARRAY, json_encode($toEncode));
    }

    private function _generateBox($title, $url) {

        return array('title' => $title, 'url' => $url);
    }
}