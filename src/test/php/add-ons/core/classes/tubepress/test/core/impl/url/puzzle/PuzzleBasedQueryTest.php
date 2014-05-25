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
 * @covers tubepress_core_impl_url_puzzle_PuzzleBasedQuery<extended>
 */
class tubepress_test_core_impl_url_puzzle_PuzzleBasedQueryTest extends tubepress_test_TubePressUnitTest
{
    public function testCanCastToString()
    {
        $q = new tubepress_core_impl_url_puzzle_PuzzleBasedQuery(new puzzle_Query(array('foo' => 'baz', 'bar' => 'bam boozle')));
        $this->assertEquals('foo=baz&bar=bam%20boozle', (string) $q);
    }

    public function testCanDisableUrlEncoding()
    {
        $q = new tubepress_core_impl_url_puzzle_PuzzleBasedQuery(new puzzle_Query(array('bar' => 'bam boozle')));
        $q->setEncodingType(false);
        $this->assertEquals('bar=bam boozle', (string) $q);
    }

    public function testCanSpecifyRfc1783UrlEncodingType()
    {
        $q = new tubepress_core_impl_url_puzzle_PuzzleBasedQuery(new puzzle_Query(array('bar abc' => 'bam boozle')));
        $q->setEncodingType(puzzle_Query::RFC1738);
        $this->assertEquals('bar+abc=bam+boozle', (string) $q);
    }

    public function testCanSpecifyRfc3986UrlEncodingType()
    {
        $q = new tubepress_core_impl_url_puzzle_PuzzleBasedQuery(new puzzle_Query(array('bar abc' => 'bam boozle', 'ሴ' => 'hi')));
        $q->setEncodingType(puzzle_Query::RFC3986);
        $this->assertEquals('bar%20abc=bam%20boozle&%E1%88%B4=hi', (string) $q);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testValidatesEncodingType()
    {
        $q = new tubepress_core_impl_url_puzzle_PuzzleBasedQuery(new puzzle_Query(array('bar' => 'bam boozle')));
        $q->setEncodingType('foo');
    }

    public function testAggregatesMultipleValues()
    {
        $q = new tubepress_core_impl_url_puzzle_PuzzleBasedQuery(new puzzle_Query(array('foo' => array('bar', 'baz'))));
        $this->assertEquals('foo%5B0%5D=bar&foo%5B1%5D=baz', (string) $q);
    }

    public function testAllowsMultipleValuesPerKey()
    {
        $q = new tubepress_core_impl_url_puzzle_PuzzleBasedQuery(new puzzle_Query());
        $q->add('facet', 'size');
        $q->add('facet', 'width');
        $q->add('facet.field', 'foo');

        $this->assertEquals('facet%5B0%5D=size&facet%5B1%5D=width&facet.field=foo', (string) $q);
    }

    public function parseQueryProvider()
    {
        return array(
            // Ensure that multiple query string values are allowed per value
            array('q=a&q=b', array('q' => array('a', 'b'))),
            // Ensure that PHP array style query string values are parsed
            array('q[]=a&q[]=b', array('q' => array('a', 'b'))),
            // Ensure that a single PHP array style query string value is parsed into an array
            array('q[]=a', array('q' => array('a'))),
            // Ensure that decimals are allowed in query strings
            array('q.a=a&q.b=b', array(
                'q.a' => 'a',
                'q.b' => 'b'
            )),
            // Ensure that query string values are percent decoded
            array('q%20a=a%20b', array('q a' => 'a b')),
            // Ensure null values can be added
            array('q&a', array('q' => null, 'a' => null)),
        );
    }

    /**
     * @dataProvider parseQueryProvider
     */
    public function testParsesQueries($query, $data)
    {
        $query = new tubepress_core_impl_url_puzzle_PuzzleBasedQuery(puzzle_Query::fromString($query));
        $this->assertEquals($data, $query->toArray());
    }

    public function testProperlyDealsWithDuplicateQueryValues()
    {
        $query = new tubepress_core_impl_url_puzzle_PuzzleBasedQuery(puzzle_Query::fromString('foo=a&foo=b&?µ=c'));
        $this->assertEquals(array('a', 'b'), $query->get('foo'));
        $this->assertEquals('c', $query->get('?µ'));
    }

    public function testAllowsNullQueryValues()
    {
        $query = new tubepress_core_impl_url_puzzle_PuzzleBasedQuery(puzzle_Query::fromString('foo'));
        $this->assertEquals('foo', (string) $query);
        $query->set('foo', null);
        $this->assertEquals('foo', (string) $query);
    }

    public function testAllowsFalsyQueryValues()
    {
        $query = new tubepress_core_impl_url_puzzle_PuzzleBasedQuery(puzzle_Query::fromString('0'));
        $this->assertEquals('0', (string) $query);
        $query->set('0', '');
        $this->assertSame('0=', (string) $query);
    }

    public function testConvertsPlusSymbolsToSpaces()
    {
        $query = new tubepress_core_impl_url_puzzle_PuzzleBasedQuery(puzzle_Query::fromString('var=foo+bar'));
        $this->assertEquals('foo bar', $query->get('var'));
    }

    public function testFromStringDoesntMangleZeroes()
    {
        $query = new tubepress_core_impl_url_puzzle_PuzzleBasedQuery(puzzle_Query::fromString('var=0'));
        $this->assertSame('0', $query->get('var'));
    }

    public function testAllowsZeroValues()
    {
        $query = new tubepress_core_impl_url_puzzle_PuzzleBasedQuery(new puzzle_Query(array(
            'foo' => 0,
            'baz' => '0',
            'bar' => null,
            'boo' => false
        )));
        $this->assertEquals('foo=0&baz=0&bar&boo=', (string) $query);
    }

    public function testFromStringDoesntStripTrailingEquals()
    {
        $query = new tubepress_core_impl_url_puzzle_PuzzleBasedQuery(puzzle_Query::fromString('data=mF0b3IiLCJUZWFtIERldiJdfX0='));
        $this->assertEquals('mF0b3IiLCJUZWFtIERldiJdfX0=', $query->get('data'));
    }

    public function testGuessesIfDuplicateAggregatorShouldBeUsed()
    {
        $query = new tubepress_core_impl_url_puzzle_PuzzleBasedQuery(puzzle_Query::fromString('test=a&test=b'));
        $this->assertEquals('test=a&test=b', (string) $query);
    }

    public function testGuessesIfDuplicateAggregatorShouldBeUsedAndChecksForPhpStyle()
    {
        $query = new tubepress_core_impl_url_puzzle_PuzzleBasedQuery(puzzle_Query::fromString('test[]=a&test[]=b'));
        $this->assertEquals('test%5B0%5D=a&test%5B1%5D=b', (string) $query);
    }

    public function testCastingToAndCreatingFromStringWithEmptyValuesIsFast()
    {
        $this->assertEquals('', (string) new tubepress_core_impl_url_puzzle_PuzzleBasedQuery(puzzle_Query::fromString('')));
    }

    public function testCanClearAllDataOrSpecificKeys()
    {
        $query = new tubepress_core_impl_url_puzzle_PuzzleBasedQuery(puzzle_Query::fromString('test=a&test=b'));

        $query->merge(array(
            'test' => 'value1',
            'test2' => 'value2'
        ));

        // Clear a specific parameter by name
        $query->remove('test');

        $this->assertEquals($query->toArray(), array(
            'test2' => 'value2'
        ));

        // Clear all parameters
        $query->clear();

        $this->assertEquals($query->toArray(), array());
    }

    public function testProvidesKeys()
    {
        $query = new tubepress_core_impl_url_puzzle_PuzzleBasedQuery(puzzle_Query::fromString(''));
        
        $this->assertEquals(array(), $query->getKeys());
        $query->merge(array(
            'test1' => 'value1',
            'test2' => 'value2'
        ));
        $this->assertEquals(array('test1', 'test2'), $query->getKeys());
        // Returns the cached array previously returned
        $this->assertEquals(array('test1', 'test2'), $query->getKeys());
        $query->remove('test1');
        $this->assertEquals(array('test2'), $query->getKeys());
        $query->add('test3', 'value3');
        $this->assertEquals(array('test2', 'test3'), $query->getKeys());
    }

    public function testChecksIfHasKey()
    {
        $query = new tubepress_core_impl_url_puzzle_PuzzleBasedQuery(puzzle_Query::fromString(''));
        
        $this->assertFalse($query->hasKey('test'));
        $query->add('test', 'value');
        $this->assertEquals(true, $query->hasKey('test'));
        $query->add('test2', 'value2');
        $this->assertEquals(true, $query->hasKey('test'));
        $this->assertEquals(true, $query->hasKey('test2'));
        $this->assertFalse($query->hasKey('testing'));
        $this->assertEquals(false, $query->hasKey('AB-C', 'junk'));
    }

    public function testChecksIfHasValue()
    {
        $query = new tubepress_core_impl_url_puzzle_PuzzleBasedQuery(puzzle_Query::fromString(''));
        $this->assertFalse($query->hasValue('value'));
        $query->add('test', 'value');
        $this->assertEquals('test', $query->hasValue('value'));
        $query->add('test2', 'value2');
        $this->assertEquals('test', $query->hasValue('value'));
        $this->assertEquals('test2', $query->hasValue('value2'));
        $this->assertFalse($query->hasValue('val'));
    }

    public function testAddParamsByMerging()
    {
        $query = new tubepress_core_impl_url_puzzle_PuzzleBasedQuery(puzzle_Query::fromString(''));
        $params = array(
            'test' => 'value1',
            'test2' => 'value2',
            'test3' => array('value3', 'value4')
        );

        // Add some parameters
        $query->merge($params);

        // Add more parameters by merging them in
        $query->merge(array(
            'test' => 'another',
            'different_key' => 'new value'
        ));

        $this->assertEquals(array(
            'test' => array('value1', 'another'),
            'test2' => 'value2',
            'test3' => array('value3', 'value4'),
            'different_key' => 'new value'
        ), $query->toArray());
    }

    public function testAllowsFunctionalFilter()
    {
        $query = new tubepress_core_impl_url_puzzle_PuzzleBasedQuery(puzzle_Query::fromString(''));
        $query->merge(array(
            'fruit' => 'apple',
            'number' => 'ten',
            'prepositions' => array('about', 'above', 'across', 'after'),
            'same_number' => 'ten'
        ));

        $filtered = $query->filter(array($this, '__callback_testAllowsFunctionalFilter'));

        $this->assertNotSame($filtered, $query);

        $this->assertEquals(array(
            'number' => 'ten',
            'same_number' => 'ten'
        ), $filtered->toArray());
    }

    public function __callback_testAllowsFunctionalFilter($key, $value)
    {
        return $value == 'ten';
    }

    public function testAllowsFunctionalMapping()
    {
        $query = new tubepress_core_impl_url_puzzle_PuzzleBasedQuery(puzzle_Query::fromString(''));
        $query->merge(array(
            'number_1' => 1,
            'number_2' => 2,
            'number_3' => 3
        ));

        $mapped = $query->map(array($this, '__callback_testAllowsFunctionalMapping'));

        $this->assertNotSame($mapped, $query);

        $this->assertEquals(array(
            'number_1' => 1,
            'number_2' => 4,
            'number_3' => 9
        ), $mapped->toArray());
    }

    public function __callback_testAllowsFunctionalMapping($key, $value)
    {
        return $value * $value;
    }

    public function testCanReplaceAllData()
    {
        $query = new tubepress_core_impl_url_puzzle_PuzzleBasedQuery(puzzle_Query::fromString(''));
        $this->assertSame($query, $query->replace(array(
            'a' => '123'
        )));

        $this->assertEquals(array(
            'a' => '123'
        ), $query->toArray());
    }
}