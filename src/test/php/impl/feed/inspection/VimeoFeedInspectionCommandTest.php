<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class tubepress_impl_feed_inspection_VimeoFeedInspectionCommandTest extends PHPUnit_Framework_TestCase
{

    private $_sut;

    function setUp()
    {
        $this->_sut = new tubepress_impl_feed_inspection_VimeoFeedInspectionCommand();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    function testCannotHandle()
    {
        $context = new ehough_chaingang_impl_StandardContext();
        $context->put(tubepress_impl_feed_FeedInspectorChain::CHAIN_KEY_PROVIDER_NAME, tubepress_spi_provider_Provider::YOUTUBE);
        $context->put(tubepress_impl_feed_FeedInspectorChain::CHAIN_KEY_RAW_FEED, 'something');

        $this->assertFalse($this->_sut->execute($context));
    }

    /**
     * @expectedException RuntimeException
     */
    function testVimeoError()
    {
        $wrapper = new stdClass;
        $error   = new stdClass;

        $wrapper->stat = 'fail';
        $wrapper->err  = $error;
        $error->msg    = 'You failed';

        $context = new ehough_chaingang_impl_StandardContext();
        $context->put(tubepress_impl_feed_FeedInspectorChain::CHAIN_KEY_PROVIDER_NAME, tubepress_spi_provider_Provider::VIMEO);
        $context->put(tubepress_impl_feed_FeedInspectorChain::CHAIN_KEY_RAW_FEED, serialize($wrapper));

        $this->assertTrue($this->_sut->execute($context));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    function testCannotUnserializeHandle()
    {
        $context = new ehough_chaingang_impl_StandardContext();
        $context->put(tubepress_impl_feed_FeedInspectorChain::CHAIN_KEY_PROVIDER_NAME, tubepress_spi_provider_Provider::VIMEO);
        $context->put(tubepress_impl_feed_FeedInspectorChain::CHAIN_KEY_RAW_FEED, 'something');

        $this->assertTrue($this->_sut->execute($context));
    }

    function testCount()
    {
        $context = new ehough_chaingang_impl_StandardContext();
        $context->put(tubepress_impl_feed_FeedInspectorChain::CHAIN_KEY_PROVIDER_NAME, tubepress_spi_provider_Provider::VIMEO);
        $context->put(tubepress_impl_feed_FeedInspectorChain::CHAIN_KEY_RAW_FEED, $this->getSampleFeed());

        $this->assertTrue($this->_sut->execute($context));

        $result = $context->get(tubepress_impl_feed_FeedInspectorChain::CHAIN_KEY_COUNT);
        $this->assertEquals(11, $result);
    }

    function getSampleFeed()
    {
        return file_get_contents(dirname(__FILE__) . '/../../../../resources/feeds/vimeo.txt');
    }
}

