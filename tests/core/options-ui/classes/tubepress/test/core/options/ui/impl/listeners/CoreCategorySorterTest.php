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
 * @covers tubepress_core_options_ui_impl_listeners_CoreCategorySorter<extended>
 */
class tubepress_test_core_options_ui_impl_listeners_CoreCategorySorterTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_options_ui_impl_listeners_CoreCategorySorter
     */
    private $_sut;

    public function onSetup()
    {
        $this->_mockTranslator = $this->mock(tubepress_core_translation_api_TranslatorInterface::_);

        $this->_sut = new tubepress_core_options_ui_impl_listeners_CoreCategorySorter();
    }

    public function testSort()
    {
        $mockEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $mockCategories = array();
        $categoryIds = array(
            tubepress_core_media_provider_api_Constants::OPTIONS_UI_CATEGORY_GALLERY_SOURCE ,
            tubepress_core_html_gallery_api_Constants::OPTIONS_UI_CATEGORY_THUMBNAILS       ,
            tubepress_core_embedded_api_Constants::OPTIONS_UI_CATEGORY_EMBEDDED             ,
            tubepress_core_theme_api_Constants::OPTIONS_UI_CATEGORY_THEMES                  ,
            tubepress_core_media_item_api_Constants::OPTIONS_UI_CATEGORY_META               ,
            tubepress_core_media_provider_api_Constants::OPTIONS_UI_CATEGORY_FEED           ,
            tubepress_core_cache_api_Constants::OPTIONS_UI_CATEGORY_CACHE                   ,
            tubepress_core_options_ui_api_Constants::OPTIONS_UI_CATEGORY_ADVANCED           ,
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i'
        );
        shuffle($categoryIds);
        foreach ($categoryIds as $id) {

            $mockCategory = $this->mock('tubepress_core_options_ui_api_ElementInterface');
            $mockCategory->shouldReceive('getId')->atLeast(1)->andReturn($id);
            $mockCategories[] = $mockCategory;
        }

        $mockTemplate = $this->mock('tubepress_core_template_api_TemplateInterface');
        $mockTemplate->shouldReceive('getVariable')->once()->with('categories')->andReturn($mockCategories);
        $mockEvent->shouldReceive('getSubject')->once()->andReturn($mockTemplate);
        $mockTemplate->shouldReceive('setVariable')->once()->with('categories', ehough_mockery_Mockery::on(function ($arg) {

            $expected = array(
                tubepress_core_media_provider_api_Constants::OPTIONS_UI_CATEGORY_GALLERY_SOURCE ,
                tubepress_core_html_gallery_api_Constants::OPTIONS_UI_CATEGORY_THUMBNAILS       ,
                tubepress_core_embedded_api_Constants::OPTIONS_UI_CATEGORY_EMBEDDED             ,
                tubepress_core_theme_api_Constants::OPTIONS_UI_CATEGORY_THEMES                  ,
                tubepress_core_media_item_api_Constants::OPTIONS_UI_CATEGORY_META               ,
                tubepress_core_media_provider_api_Constants::OPTIONS_UI_CATEGORY_FEED           ,
                tubepress_core_cache_api_Constants::OPTIONS_UI_CATEGORY_CACHE                   ,
                tubepress_core_options_ui_api_Constants::OPTIONS_UI_CATEGORY_ADVANCED           ,
            );

            for ($x = 0; $x < count($expected); $x++) {

                if ($arg[$x]->getId() !== $expected[$x]) {

                    return false;
                }
            }
            return count($arg) > count($expected);
        }));

        $this->_sut->onOptionsPageTemplate($mockEvent);
        $this->assertTrue(true);
    }
}