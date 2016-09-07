<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_wordpress_impl_wp_ResourceRepository
 */
class tubepress_test_wordpress_impl_wp_ResourceRepositoryTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_wp_ResourceRepository
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockWpFunctions;

    public function onSetup()
    {
        $this->_mockWpFunctions = $this->mock(tubepress_wordpress_impl_wp_WpFunctions::_);

        $this->_sut = new tubepress_wordpress_impl_wp_ResourceRepository(
            $this->_mockWpFunctions
        );
    }

    public function testStatuses()
    {
        $status1 = new stdClass();
        $status2 = new stdClass();
        $status3 = new stdClass();
        $status4 = new stdClass();
        $status5 = new stdClass();

        $status1->name = 'status1name';
        $status2->name = 'status2name';
        $status3->name = 'auto-draft';
        $status4->name = 'inherit';
        $status5->name = 'future';

        $fakestatuss = array(
            $status2, $status1, $status5, $status4, $status3,
        );

        $this->_mockWpFunctions->shouldReceive('get_post_stati')->once()->with(
            array(), 'objects'
        )->andReturn($fakestatuss);

        $actual = $this->_sut->getAllUsablePostStatuses();

        $expected = array(
            $status2, $status1,
        );

        $this->assertEquals($expected, $actual);
    }

    public function testTypes()
    {
        $type1 = new stdClass();
        $type2 = new stdClass();
        $type3 = new stdClass();
        $type4 = new stdClass();
        $type5 = new stdClass();

        $type1->name = 'type1name';
        $type2->name = 'type2name';
        $type3->name = 'revision';
        $type4->name = 'attachment';
        $type5->name = 'nav_menu_item';

        $fakeTypes = array(
            $type2, $type1, $type5, $type4, $type3,
        );

        $this->_mockWpFunctions->shouldReceive('get_post_types')->once()->with(
            array('public' => true), 'objects'
        )->andReturn($fakeTypes);

        $actual = $this->_sut->getAllUsablePostTypes();

        $expected = array(
            $type2, $type1,
        );

        $this->assertEquals($expected, $actual);
    }

    public function testAuthors()
    {
        $author1 = new stdClass();
        $author2 = new stdClass();

        $fakeAuthors = array(
            $author2, $author1,
        );

        $this->_mockWpFunctions->shouldReceive('get_users')->once()->with(array(
            'who' => 'author',
        ))->andReturn($fakeAuthors);

        $actual = $this->_sut->getAuthors();

        $this->assertSame($fakeAuthors, $actual);
    }

    public function testTemplates()
    {
        $fakeTemplates = array(

            'hello.php'   => 'hello there',
            'goodbye.php' => 'goodbye now',
        );

        $fakeTheme = $this->mock('stdClass');
        $fakeTheme->shouldReceive('get_page_templates')->once()->andReturn($fakeTemplates);

        $this->_mockWpFunctions->shouldReceive('wp_get_theme')->once()->andReturn($fakeTheme);

        $actual   = $this->_sut->getPageTemplates();
        $expected = array(
            'index.php'   => 'default',
            'hello.php'   => 'hello there',
            'goodbye.php' => 'goodbye now',
        );

        $this->assertEquals($expected, $actual);
    }

    public function testCategories()
    {
        $term1 = new stdClass();
        $term2 = new stdClass();

        $fakeTerms = array(
            $term2, $term1,
        );

        $this->_mockWpFunctions->shouldReceive('get_categories')->once()->with(array(
            'hide_empty' => false,
        ))->andReturn($fakeTerms);

        $actual = $this->_sut->getAllCategories();

        $this->assertSame($fakeTerms, $actual);
    }

    public function testTags()
    {
        $term1 = new stdClass();
        $term2 = new stdClass();

        $fakeTerms = array(
            $term2, $term1,
        );

        $this->_mockWpFunctions->shouldReceive('get_tags')->once()->with(array(
            'hide_empty' => false,
        ))->andReturn($fakeTerms);

        $actual = $this->_sut->getAllTags();

        $this->assertSame($fakeTerms, $actual);
    }
}
