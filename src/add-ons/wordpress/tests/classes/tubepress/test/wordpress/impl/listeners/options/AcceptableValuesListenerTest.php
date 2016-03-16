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
    private $_mockResourceRepo;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockEvent;

    public function onSetup()
    {
        $this->_mockResourceRepo = $this->mock('tubepress_wordpress_impl_wp_ResourceRepository');
        $this->_mockEvent        = $this->mock('tubepress_api_event_EventInterface');
        $this->_sut              = new tubepress_wordpress_impl_listeners_options_AcceptableValuesListener(

            $this->_mockResourceRepo
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
        $this->_mockResourceRepo->shouldReceive('getAuthors')->once()->andReturn($fakeUsers);

        $this->_setupEventForSubjectSet(array(
            'user1' => 'User 1',
            'user2' => 'User 2',
        ));

        $this->_sut->onWpUser($this->_mockEvent);
    }

    public function testStatus()
    {
        $status1 = new stdClass();
        $status2 = new stdClass();

        $status1->name = 'status1name';
        $status2->name = 'status2name';

        $status1->label = 'status 1 label';
        $status2->label = 'status 2 label';

        $this->_mockResourceRepo->shouldReceive('getAllUsablePostStatuses')->once()->andReturn(array(
            $status2, $status1
        ));

        $this->_setupEventForSubjectSet(array(
            'status1name' => 'status 1 label',
            'status2name' => 'status 2 label',
        ));

        $this->_sut->onWpPostStatus($this->_mockEvent);
    }

    public function testTypes()
    {
        $type1 = new stdClass();
        $type2 = new stdClass();

        $type1->name = 'type1name';
        $type2->name = 'type2name';

        $type1->labels = new stdClass();
        $type2->labels = new stdClass();

        $type1->labels->singular_name = 'type 1 label';
        $type2->labels->singular_name = 'type 2 label';

        $this->_mockResourceRepo->shouldReceive('getAllUsablePostTypes')->once()->andReturn(array(
            $type2, $type1
        ));

        $this->_setupEventForSubjectSet(array(
            'type1name' => 'type 1 label',
            'type2name' => 'type 2 label',
        ));

        $this->_sut->onWpPostType($this->_mockEvent);
    }

    public function testTemplates()
    {
        $this->_mockResourceRepo->shouldReceive('getPageTemplates')->once()->andReturn(array(
            'hiya.php'  => 'How Are You',
            'hello.php' => 'Hello',
        ));

        $this->_setupEventForSubjectSet(array(
            'hello.php' => 'Hello',
            'hiya.php'  => 'How Are You',
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
        $this->_mockResourceRepo->shouldReceive('getAllCategories')->once()->andReturn($fakeCats);

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
        $this->_mockResourceRepo->shouldReceive('getAllTags')->once()->andReturn($fakeTags);

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