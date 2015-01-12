<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_app_impl_listeners_template_pre_OptionsGuiSorter
{
    private static $_categorySortMap = array(
        tubepress_app_api_options_ui_CategoryNames::GALLERY_SOURCE,
        tubepress_app_api_options_ui_CategoryNames::THUMBNAILS,
        tubepress_app_api_options_ui_CategoryNames::EMBEDDED,
        tubepress_app_api_options_ui_CategoryNames::THEME,
        tubepress_app_api_options_ui_CategoryNames::META,
        tubepress_app_api_options_ui_CategoryNames::FEED,
        tubepress_app_api_options_ui_CategoryNames::CACHE,
        tubepress_app_api_options_ui_CategoryNames::ADVANCED,
    );

    private static $_providerSortMap = array(

        'field-provider-core',
        'field-provider-youtube',
        'field-provider-vimeo',
        'field-provider-jwplayer5',
        'field-provider-wordpress',
    );

    public function onOptionsGuiTemplate(tubepress_lib_api_event_EventInterface $event)
    {
        $existingArgs = $event->getSubject();

        $this->_sortCategories($existingArgs);
        $this->_sortProviders($existingArgs);

        $event->setSubject($existingArgs);
    }

    private function _sortProviders(array &$existingArgs)
    {
        if (!isset($existingArgs['categoryIdToProviderIdToFieldsMap'])) {

            return;
        }

        $map = &$existingArgs['categoryIdToProviderIdToFieldsMap'];

        foreach ($map as $categoryId => $providerMap) {

            $map[$categoryId] = $this->_sortProviderMap($providerMap);
        }
    }

    private function _sortProviderMap(array &$providerMap)
    {
        $toReturn = array();

        foreach (self::$_providerSortMap as $providerId) {

            if (isset($providerMap[$providerId])) {

                $toReturn[$providerId] = $providerMap[$providerId];
            }
        }

        foreach ($providerMap as $id => $fields) {

            if (!isset($toReturn[$id])) {

                $toReturn[$id] = $fields;
            }
        }

        return $toReturn;
    }

    private function _sortCategories(array &$existingArgs)
    {
        if (!isset($existingArgs['categories'])) {

            return;
        }

        /**
         * @var $newCategories tubepress_app_api_options_ui_ElementInterface[]
         */
        $newCategories = array();

        /**
         * @var $existingCategories tubepress_app_api_options_ui_ElementInterface[]
         */
        $existingCategories = $existingArgs['categories'];

        foreach (self::$_categorySortMap as $categoryId) {

            foreach ($existingCategories as $category) {

                if ($category->getId() === $categoryId) {

                    $newCategories[] = $category;
                    break;
                }
            }
        }

        foreach ($existingCategories as $category) {

            $alreadyAdded = false;

            foreach ($newCategories as $newCategory) {

                if ($newCategory->getId() === $category->getId()) {

                    $alreadyAdded = true;
                    break;
                }
            }

            if (!$alreadyAdded) {

                $newCategories[] = $category;
            }
        }

        $existingArgs['categories'] = $newCategories;
    }
}