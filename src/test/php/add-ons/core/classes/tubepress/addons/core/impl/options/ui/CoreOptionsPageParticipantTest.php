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
class tubepress_addons_core_impl_options_ui_CoreOptionsPageParticipantTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_options_ui_CoreOptionsPageParticipant
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFieldBuilder;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    public function onSetup()
    {
        $this->_sut                 = new tubepress_addons_core_impl_options_ui_CoreOptionsPageParticipant();
        $this->_mockFieldBuilder    = $this->createMockSingletonService(tubepress_spi_options_ui_FieldBuilder::_);
        $this->_mockEventDispatcher = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);
    }

    public function testCacheTab()
    {
        $map = array(

            tubepress_api_const_options_names_Cache::CACHE_ENABLED => tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Cache::CACHE_DIR => tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Cache::CACHE_LIFETIME_SECONDS => tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Cache::CACHE_CLEAN_FACTOR => tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME,
        );

        $this->_testTab($map, tubepress_impl_options_ui_tabs_CacheTab::TAB_NAME);
    }

    public function testEmbeddedTab()
    {
        $map = array(

            tubepress_api_const_options_names_Embedded::PLAYER_LOCATION => tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Embedded::PLAYER_IMPL => tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT => tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH => tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Embedded::LAZYPLAY => tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Embedded::SHOW_INFO => tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Embedded::AUTONEXT => tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Embedded::AUTOPLAY => tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Embedded::LOOP => tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Embedded::ENABLE_JS_API => tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME,
        );

        $this->_testTab($map, tubepress_impl_options_ui_tabs_EmbeddedTab::TAB_NAME);
    }

    public function testFeedTab()
    {
        $map = array(

            tubepress_api_const_options_names_Feed::ORDER_BY => tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Feed::PER_PAGE_SORT => tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP => tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Feed::VIDEO_BLACKLIST => tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME,
        );

        $this->_testTab($map, tubepress_impl_options_ui_tabs_FeedTab::TAB_NAME);
    }

    public function testMetaTab()
    {
        $map = array(

            'metadropdown' => 'tubepress_impl_options_ui_fields_MetaMultiSelectField',
            tubepress_api_const_options_names_Meta::DATEFORMAT => tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Meta::RELATIVE_DATES => tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Meta::DESC_LIMIT => tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME,
        );

        $this->_testTab($map, tubepress_impl_options_ui_tabs_MetaTab::TAB_NAME);
    }

    public function testThemeTab()
    {
        $map = array(

            tubepress_api_const_options_names_Thumbs::THEME => tubepress_impl_options_ui_fields_ThemeField::FIELD_CLASS_NAME
        );

        $this->_testTab($map, tubepress_impl_options_ui_tabs_ThemeTab::TAB_NAME);
    }

    public function testThumbsTab()
    {
        $map = array(

            tubepress_api_const_options_names_Thumbs::THUMB_HEIGHT => tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Thumbs::THUMB_WIDTH => tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION => tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Thumbs::FLUID_THUMBS => tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Thumbs::PAGINATE_ABOVE => tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW => tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Thumbs::HQ_THUMBS => tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS => tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE => tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME,
        );

        $this->_testTab($map, tubepress_impl_options_ui_tabs_ThumbsTab::TAB_NAME);
    }

    public function testAdvancedTab()
    {
        $map = array(

            tubepress_api_const_options_names_Advanced::DEBUG_ON =>    tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Advanced::KEYWORD =>     tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Advanced::HTTPS =>       tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME,
            tubepress_api_const_options_names_Advanced::HTTP_METHOD => tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME,
        );

        $this->_testTab($map, tubepress_impl_options_ui_tabs_AdvancedTab::TAB_NAME);
    }

    public function testGetFriendlyName()
    {
        $result = $this->_sut->getFriendlyName();

        $this->assertEquals('Core', $result);
    }

    public function testGetName()
    {
        $result = $this->_sut->getName();

        $this->assertEquals('core', $result);
    }

    private function _testTab($map, $tabName)
    {
        $index = 0;
        $order = array();

        foreach ($map as $key => $value) {

            $val = $index++;

            $order[] = $val;
            $this->_mockFieldBuilder->shouldReceive('build')->once()->with($key, $value)->andReturn($val);
        }

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTIONS_UI_FIELDS_FOR_TAB, ehough_mockery_Mockery::on(function ($event) {

            return $event instanceof tubepress_api_event_EventInterface && is_array($event->getSubject()) && $event->getArgument('participant') instanceof tubepress_spi_options_ui_PluggableOptionsPageParticipant;
        }));

        $result = $this->_sut->getFieldsForTab($tabName);

        $this->assertEquals($order, $result);
    }
}