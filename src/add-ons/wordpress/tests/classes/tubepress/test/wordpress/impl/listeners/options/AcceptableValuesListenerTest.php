<?php
/*
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
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

            $this->_mockResourceRepo,
            new tubepress_util_impl_LangUtils()
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
            $user2, $user1,
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
            $status2, $status1,
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
            $type2, $type1,
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
            'index.php' => 'default',
            'hello.php' => 'Hello',
            'hiya.php'  => 'How Are You',
        ));

        $this->_sut->onWpPostTemplate($this->_mockEvent);
    }

    /**
     * @dataProvider getDataTerms
     */
    public function testTerms($method, $resourceRepoMethod, $incoming, $expected = null)
    {
        $term1 = new stdClass();
        $term2 = new stdClass();

        $term1->slug = 'term1-slug';
        $term1->name = 'Term 1';

        $term2->slug = 'term2-slug';
        $term2->name = 'Term 2';

        $fakeTerms = array(
            $term2, $term1,
        );
        $this->_mockResourceRepo->shouldReceive($resourceRepoMethod)->once()->andReturn($fakeTerms);

        $this->_mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn($incoming);

        if ($incoming) {

            $this->_mockEvent->shouldReceive('setArgument')->once()->with('optionValue', $expected);
        }

        $this->_sut->$method($this->_mockEvent);
    }

    public function getDataTerms()
    {
        $methods = array(
            array('onWpPostTags', 'getAllTags'),
            array('onWpPostCategories', 'getAllCategories'),
        );

        $toReturn = array();

        foreach ($methods as $set) {

            $toReturn[] = array($set[0], $set[1], '', null);
            $toReturn[] = array($set[0], $set[1], 'foo', null);
            $toReturn[] = array($set[0], $set[1], 'term1-slug, foo', 'term1-slug');
            $toReturn[] = array($set[0], $set[1], 'term1-slug, foo,    term2-slug', 'term1-slug,term2-slug');
            $toReturn[] = array($set[0], $set[1], 'term1-slug, term1-slug', 'term1-slug');
        }

        return $toReturn;
    }

    private function _setupEventForSubjectSet(array $array)
    {
        asort($array);

        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturnNull();
        $this->_mockEvent->shouldReceive('setSubject')->once()->with($array);
    }
}
