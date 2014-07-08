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
class tubepress_app_options_ui_impl_listeners_CoreCategorySorter
{
    private static $_EXPECTED_ORDER = array(
        tubepress_app_media_provider_api_Constants::OPTIONS_UI_CATEGORY_GALLERY_SOURCE => 7,
        tubepress_app_feature_gallery_api_Constants::OPTIONS_UI_CATEGORY_THUMBNAILS    => 6,
        tubepress_app_embedded_api_Constants::OPTIONS_UI_CATEGORY_EMBEDDED             => 5,
        tubepress_app_theme_api_Constants::OPTIONS_UI_CATEGORY_THEMES                  => 4,
        tubepress_app_media_item_api_Constants::OPTIONS_UI_CATEGORY_META               => 3,
        tubepress_app_media_provider_api_Constants::OPTIONS_UI_CATEGORY_FEED           => 2,
        tubepress_app_apicache_api_Constants::OPTIONS_UI_CATEGORY_CACHE                => 1,
        tubepress_app_options_ui_api_Constants::OPTIONS_UI_CATEGORY_ADVANCED           => 0
    );

    public function onOptionsPageTemplate(tubepress_lib_event_api_EventInterface $event)
    {
        /**
         * @var $template tubepress_lib_template_api_TemplateInterface
         */
        $template = $event->getSubject();

        /**
         * @var $originalCategories tubepress_app_options_ui_api_ElementInterface[]
         */
        $originalCategories = $template->getVariable('categories');
        @usort($originalCategories, array($this, '__sort'));
        $template->setVariable('categories', $originalCategories);
    }

    public function __sort(tubepress_app_options_ui_api_ElementInterface $first,
                           tubepress_app_options_ui_api_ElementInterface $second)
    {
        $firstId      = $first->getId();
        $secondId     = $second->getId();
        $firstWeight  = isset(self::$_EXPECTED_ORDER[$firstId]) ? self::$_EXPECTED_ORDER[$firstId] : -1;
        $secondWeight = isset(self::$_EXPECTED_ORDER[$secondId]) ? self::$_EXPECTED_ORDER[$secondId] : -1;

        if ($firstWeight > $secondWeight) {

            return -1;
        }

        if ($firstWeight < $secondWeight) {

            return 1;
        }

        return 0;
    }
}