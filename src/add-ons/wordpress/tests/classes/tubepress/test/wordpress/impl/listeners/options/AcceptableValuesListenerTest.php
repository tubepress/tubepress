<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_wordpress_impl_listeners_options_AcceptableValuesListener
 */
class tubepress_test_wordpress_impl_listeners_options_AcceptableValuesListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_listeners_options_AcceptableValuesListener
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockWpFunctions;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockEvent;

    public function onSetup()
    {
        $this->_mockWpFunctions = $this->mock(tubepress_wordpress_impl_wp_WpFunctions::_);
        $this->_mockEvent       = $this->mock('tubepress_api_event_EventInterface');
        $this->_sut             = new tubepress_wordpress_impl_listeners_options_AcceptableValuesListener(

            $this->_mockWpFunctions
        );
    }

    public function testUsers()
    {
        $user1 = new stdClass();
        $user2 = new stdClass();

        $user1->user_login   = 'user1';
        $user1->display_name = 'User 1';

        $user2->user_login   = 'user2';
        $user2->display_name = 'User 2';

        $fakeUsers = array(
            $user2, $user1
        );
        $this->_mockWpFunctions->shouldReceive('get_users')->once()->with(array('who' => 'author'))->andReturn($fakeUsers);

        $this->_setupEventForSubjectSet(array(
            'user1' => 'User 1',
            'user2' => 'User 2',
        ));

        $this->_sut->onWpUser($this->_mockEvent);
    }

    public function testStatus()
    {
        $this->_mockWpFunctions->shouldReceive('get_post_stati')->once()->andReturn(array(
            'status2', 'status1', 'auto-draft', 'inherit',
        ));

        $this->_setupEventForSubjectSet(array(
            'status1' => 'status1',
            'status2' => 'status2',
        ));

        $this->_sut->onWpPostStatus($this->_mockEvent);
    }

    public function testTypes()
    {
        $this->_mockWpFunctions->shouldReceive('get_post_types')->once()->with(array('public' => true))->andReturn(array(
            'type2', 'type1',
        ));

        $this->_setupEventForSubjectSet(array(
            'type1' => 'type1',
            'type2' => 'type2',
        ));

        $this->_sut->onWpPostType($this->_mockEvent);
    }

    public function testTemplates()
    {
        $this->_mockWpFunctions->shouldReceive('get_page_templates')->once()->andReturn(array(
            'Hello'       => 'hello.php',
            'How Are You' => 'hiya.php',
        ));

        $this->_setupEventForSubjectSet(array(
            'hello.php' => 'Hello (hello.php)',
            'hiya.php'  => 'How Are You (hiya.php)',
            'index.php' => 'default (index.php)',
        ));

        $this->_sut->onWpPostTemplate($this->_mockEvent);
    }

    public function testCategories()
    {
        $category1 = new stdClass();
        $category2 = new stdClass();

        $category1->slug = 'category1-slug';
        $category1->name = 'Cat 1';

        $category2->slug = 'category2-slug';
        $category2->name = 'Cat 2';

        $fakeCats = array(
            $category2, $category1
        );
        $this->_mockWpFunctions->shouldReceive('get_categories')->once()->with(
            array('hide_empty' => false))->andReturn($fakeCats);

        $this->_setupEventForSubjectSet(array(
            'category1-slug' => 'Cat 1',
            'category2-slug' => 'Cat 2',
        ));

        $this->_sut->onWpPostCategories($this->_mockEvent);
    }

    public function testTags()
    {
        $tag1 = new stdClass();
        $tag2 = new stdClass();

        $tag1->slug = 'tag1-slug';
        $tag1->name = 'Tag 1';

        $tag2->slug = 'tag2-slug';
        $tag2->name = 'Tag 2';

        $fakeTags = array(
            $tag2, $tag1
        );
        $this->_mockWpFunctions->shouldReceive('get_tags')->once()->with(
            array('hide_empty' => false))->andReturn($fakeTags);

        $this->_setupEventForSubjectSet(array(
            'tag1-slug' => 'Tag 1',
            'tag2-slug' => 'Tag 2',
        ));

        $this->_sut->onWpPostTags($this->_mockEvent);
    }

    private function _setupEventForSubjectSet(array $array)
    {
        asort($array);

        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturnNull();
        $this->_mockEvent->shouldReceive('setSubject')->once()->with($array);
    }
}