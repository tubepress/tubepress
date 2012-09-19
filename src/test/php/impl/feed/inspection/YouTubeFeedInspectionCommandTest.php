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
class tubepress_impl_feed_inspection_YouTubeFeedInspectionCommandTest extends PHPUnit_Framework_TestCase
{
    private $_sut;

    function setUp()
    {
        $this->_sut = new tubepress_impl_feed_inspection_YouTubeFeedInspectionCommand();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    function testCannotHandle()
    {
        $context = new ehough_chaingang_impl_StandardContext();
        $context->put(tubepress_impl_feed_FeedInspectorChain::CHAIN_KEY_PROVIDER_NAME, tubepress_spi_provider_Provider::VIMEO);
        $context->put(tubepress_impl_feed_FeedInspectorChain::CHAIN_KEY_RAW_FEED, 'something');

        $this->assertFalse($this->_sut->execute($context));
    }

    function testCanHandle()
    {
        $context = new ehough_chaingang_impl_StandardContext();
        $context->put(tubepress_impl_feed_FeedInspectorChain::CHAIN_KEY_PROVIDER_NAME, tubepress_spi_provider_Provider::YOUTUBE);
        $context->put(tubepress_impl_feed_FeedInspectorChain::CHAIN_KEY_RAW_FEED, $this->getSampleXml());

        $this->assertTrue($this->_sut->execute($context));
    }

    function testCount()
    {
        $context = new ehough_chaingang_impl_StandardContext();
        $context->put(tubepress_impl_feed_FeedInspectorChain::CHAIN_KEY_PROVIDER_NAME, tubepress_spi_provider_Provider::YOUTUBE);
        $context->put(tubepress_impl_feed_FeedInspectorChain::CHAIN_KEY_RAW_FEED, $this->getSampleXml());

        $this->assertTrue($this->_sut->execute($context));

        $this->_sut->execute($context);

        $result = $context->get(tubepress_impl_feed_FeedInspectorChain::CHAIN_KEY_COUNT);
        $this->assertEquals(100, $result);
    }

    function getSampleXml()
    {
        return <<<EOT
<xml version='1.0' encoding='UTF-8'>
    <feed xmlns='http://www.w3.org/2005/Atom'
        xmlns:openSearch='http://a9.com/-/spec/opensearch/1.1/'>
        <openSearch:totalResults>100</openSearch:totalResults>
        <entry /><entry /><entry />
    </feed>
</xml>
EOT;
    }
}

