<?php

require_once BASE . '/sys/classes/org/tubepress/impl/factory/commands/YouTubeFactoryCommand.class.php';

class org_tubepress_impl_factory_commands_YouTubeFactoryCommandTest extends TubePressUnitTest {

    private $_sut;
    private $_playlistFeed;

    function setUp()
    {
    	parent::setUp();
        $this->_playlistFeed = file_get_contents(dirname(__FILE__) . '/../feeds/playlist.xml');
        $this->_sut          = new org_tubepress_impl_factory_commands_YouTubeFactoryCommand();
    }

    function testGetMultiple()
    {
        $context = new stdClass();
        $context->feed = $this->_playlistFeed;

        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Meta::DESC_LIMIT)->andReturn(0);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS)->andReturn(true);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Meta::RELATIVE_DATES)->andReturn(false);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Meta::DATEFORMAT)->andReturn('M j, Y');

        $this->_sut->execute($context);
        $result = $context->returnValue;

        $this->assertTrue(is_array($result));
        $this->assertEquals(1, count($result));
        $video = $result[0];
        $this->assertEquals('3hough', $video->getAuthorDisplayName());
        $this->assertEquals('3hough', $video->getAuthorUid());
        $this->assertEquals('Music', $video->getCategory());
        $this->assertEquals('N/A', $video->getCommentCount());
        $this->assertEquals(<<<EOT
A mashup song by ComaR, of The Jackson 5's "I Want You Back" and Justice's "D.A.N.C.E." I just made the video.

-FAIR USE-
"Copyright Disclaimer Under Section 107 of the Copyright Act 1976, allowance is made for "fair use" for purposes such as criticism, comment, news reporting, teaching, scholarship, and research. Fair use is a use permitted by copyright statute that might otherwise be infringing. Non-profit, educational or personal use tips the balance in favor of fair use."
EOT
        , $video->getDescription());
        $this->assertEquals('2:43', $video->getDuration());
        $this->assertEquals('http://www.youtube.com/watch?v=zfaMzjDAGuA&feature=youtube_gdata', $video->getHomeUrl());
        $this->assertEquals('zfaMzjDAGuA', $video->getId());
        $this->assertEquals(array('Jackson 5', 'Justice', 'Michael Jackson'), $video->getKeywords());
        $this->assertEquals('N/A', $video->getLikesCount());
        $this->assertEquals('5.00', $video->getRatingAverage());
        $this->assertEquals('30', $video->getRatingCount());
        $this->assertTrue(preg_match('/http:\/\/i\.ytimg\.com\/vi\/zfaMzjDAGuA\/(?:[0123]|default)\.jpg/', $video->getThumbnailUrl()) === 1, $video->getThumbnailUrl());
        $this->assertEquals('', $video->getTimeLastUpdated());
        $this->assertEquals('Sep 17, 2009', $video->getTimePublished());
        $this->assertEquals('10,778', $video->getViewCount());
    }

    function testCanHandleMultiple()
    {
        $context = new stdClass();
        $context->feed = $this->_playlistFeed;

        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Meta::DESC_LIMIT)->andReturn(0);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS)->andReturn(false);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Meta::RELATIVE_DATES)->andReturn(false);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Meta::DATEFORMAT)->andReturn('M j, Y');

        $this->assertTrue($this->_sut->execute($context));
    }

    function testCannotHandle()
    {
        $context = new stdClass();
        $context->feed = 'bla';

        $this->assertFalse($this->_sut->execute($context));
    }
}

