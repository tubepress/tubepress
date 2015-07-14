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

/**
 * @covers tubepress_url_impl_puzzle_PuzzleBasedQuery<extended>
 */
class tubepress_test_url_impl_puzzle_PuzzleBasedQueryTest extends tubepress_api_test_TubePressUnitTest
{
    public function testCanCastToString()
    {
        $q = new tubepress_url_impl_puzzle_PuzzleBasedQuery(new puzzle_Query(array('foo' => 'baz', 'bar' => 'bam boozle')));
        $this->assertEquals('foo=baz&bar=bam%20boozle', (string) $q);
    }

    public function testCanDisableUrlEncoding()
    {
        $q = new tubepress_url_impl_puzzle_PuzzleBasedQuery(new puzzle_Query(array('bar' => 'bam boozle')));
        $q->setEncodingType(false);
        $this->assertEquals('bar=bam boozle', (string) $q);
    }

    public function testCanSpecifyRfc1783UrlEncodingType()
    {
        $q = new tubepress_url_impl_puzzle_PuzzleBasedQuery(new puzzle_Query(array('bar abc' => 'bam boozle')));
        $q->setEncodingType(tubepress_url_impl_puzzle_PuzzleBasedQuery::RFC1738_ENCODING);
        $this->assertEquals('bar+abc=bam+boozle', (string) $q);
    }

    public function testCanSpecifyRfc3986UrlEncodingType()
    {
        $q = new tubepress_url_impl_puzzle_PuzzleBasedQuery(new puzzle_Query(array('bar abc' => 'bam boozle', 'ሴ' => 'hi')));
        $q->setEncodingType(tubepress_url_impl_puzzle_PuzzleBasedQuery::RFC3986_ENCODING);
        $this->assertEquals('bar%20abc=bam%20boozle&%E1%88%B4=hi', (string) $q);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testValidatesEncodingType()
    {
        $q = new tubepress_url_impl_puzzle_PuzzleBasedQuery(new puzzle_Query(array('bar' => 'bam boozle')));
        $q->setEncodingType('foo');
    }

    public function testAggregatesMultipleValues()
    {
        $q = new tubepress_url_impl_puzzle_PuzzleBasedQuery(new puzzle_Query(array('foo' => array('bar', 'baz'))));
        $this->assertEquals('foo%5B0%5D=bar&foo%5B1%5D=baz', (string) $q);
    }

    public function testAllowsMultipleValuesPerKey()
    {
        $q = new tubepress_url_impl_puzzle_PuzzleBasedQuery(new puzzle_Query());
        $q->add('facet', 'size');
        $q->add('facet', 'width');
        $q->add('facet.field', 'foo');

        $this->assertEquals('facet%5B0%5D=size&facet%5B1%5D=width&facet.field=foo', (string) $q);
    }

    public function testAllowsZeroValues()
    {
        $query = new tubepress_url_impl_puzzle_PuzzleBasedQuery(new puzzle_Query(array(
            'foo' => 0,
            'baz' => '0',
            'bar' => null,
            'boo' => false
        )));
        $this->assertEquals('foo=0&baz=0&bar&boo=', (string) $query);
    }

    public function testCanDisableUrlEncodingDecoding()
    {
        $q = new tubepress_url_impl_puzzle_PuzzleBasedQuery(puzzle_Query::fromString('foo=bar+baz boo%20', false));
        $this->assertEquals('bar+baz boo%20', $q->get('foo'));
        $this->assertEquals('foo=bar+baz boo%20', (string) $q);
    }

    public function testCanChangeUrlEncodingDecodingToRfc1738()
    {
        $q = new tubepress_url_impl_puzzle_PuzzleBasedQuery(puzzle_Query::fromString('foo=bar+baz', tubepress_url_impl_puzzle_PuzzleBasedQuery::RFC1738_ENCODING));
        $this->assertEquals('bar baz', $q->get('foo'));
        $this->assertEquals('foo=bar+baz', (string) $q);
    }

    public function testCanChangeUrlEncodingDecodingToRfc3986()
    {
        $q = new tubepress_url_impl_puzzle_PuzzleBasedQuery(puzzle_Query::fromString('foo=bar%20baz', tubepress_url_impl_puzzle_PuzzleBasedQuery::RFC3986_ENCODING));
        $this->assertEquals('bar baz', $q->get('foo'));
        $this->assertEquals('foo=bar%20baz', (string) $q);
    }
}