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
 * @covers tubepress_impl_patterns_toposort_TopologicalSort
 */
class tubepress_test_impl_patterns_toposort_TopologicalSortTest extends tubepress_test_TubePressUnitTest
{
    public function testBasic()
    {
        $nodes = array('1','2','3','4','5');
        $edges = array(

            array('1','2'),
            array('3','1'),
            array('3','4')
        );

        $actual   = tubepress_impl_patterns_toposort_TopologicalSort::sort($nodes, $edges);
        $expected = array('3', '5', '1', '4', '2');

        $this->assertEquals($expected, $actual);
    }

    public function testDuplicateEdge1()
    {
        $nodes = array('1','2','3','4','5');
        $edges = array(

            array('1', '2'),
            array('3', '1'),
            array('3', '4'),
            array('1', '2')
        );

        $actual   = tubepress_impl_patterns_toposort_TopologicalSort::sort($nodes, $edges);
        $expected = array('3', '5', '1', '4', '2');

        $this->assertEquals($expected, $actual);
    }

    public function testComplex()
    {
        $nodes = array('2', '3', '5', '7', '8', '9', '10', '11');
        $edges = array(

            array('7', '11'),
            array('7', '8'),
            array('5', '11'),
            array('3', '8'),
            array('3', '10'),
            array('11', '2'),
            array('11', '9'),
            array('11', '10'),
            array('8', '9')
        );

        $actual   = tubepress_impl_patterns_toposort_TopologicalSort::sort($nodes, $edges);
        $expected = array('3', '5', '7', '11', '8', '2', '10', '9');

        $this->assertEquals($expected, $actual);
    }

    public function testCyclic()
    {
        $nodes = array('1', '2', '3');
        $edges = array(

            array('1', '2'),
            array('2', '3'),
            array('3', '1')
        );

        $actual = tubepress_impl_patterns_toposort_TopologicalSort::sort($nodes, $edges);

        $this->assertNull($actual);
    }
}