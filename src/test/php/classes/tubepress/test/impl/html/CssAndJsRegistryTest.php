<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_impl_html_CssAndJsRegistry
 */
class tubepress_test_impl_html_CssRegistryTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_html_CssAndJsRegistry
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_impl_html_CssAndJsRegistry();
    }

    public function testDequeueStyle()
    {
        $result1 = $this->_sut->enqueueStyle('x', 'http://foo.baz', array('y', 'z'));

        $this->assertTrue($result1);

        $this->assertTrue($this->_sut->dequeueStyle('x'));
    }

    public function testDequeueStyleNoSuch()
    {
        $this->assertFalse($this->_sut->dequeueStyle('x'));
    }

    public function testGetStyle()
    {
        $this->assertNull($this->_sut->getStyle('x'));

        $result1 = $this->_sut->enqueueStyle('x', 'http://foo.baz', array('y', 'z'));

        $this->assertTrue($result1);

        $style = $this->_sut->getStyle('x');

        $this->assertEquals(array(

            'url' => 'http://foo.baz',
            'dependencies' => array('y', 'z'),
            'media' => 'all'
        ), $style);
    }

    public function testGetStyleNoSuchStyle()
    {
        $this->assertNull($this->_sut->getStyle('x'));
    }

    public function testQueueWithDep2()
    {
        $result1 = $this->_sut->enqueueStyle('x', 'http://foo.baz', array('y', 'z'));
        $result2 = $this->_sut->enqueueStyle('y', 'http://foo.baz', array('h'));
        $result3 = $this->_sut->enqueueStyle('z', 'http://foo.bah', array('a', 'f', 'g'));
        $result4 = $this->_sut->enqueueStyle('h', 'http://foo.baa');
        $result5 = $this->_sut->enqueueStyle('a', 'http://foo.bw');
        $result6 = $this->_sut->enqueueStyle('f', 'http://foo.bsaw');
        $result7 = $this->_sut->enqueueStyle('g', 'http://foo.bagw');

        $this->assertTrue($result1 && $result2 && $result3 && $result4 && $result5 && $result6 && $result7);

        $this->assertEquals(array('g', 'f', 'a', 'h', 'z', 'y', 'x'), $this->_sut->getStyleHandlesForDisplay());
    }

    public function testQueueWithDep1()
    {
        $result1 = $this->_sut->enqueueStyle('1a', 'http://foo.baz', array('2a'));
        $result2 = $this->_sut->enqueueStyle('2a', 'http://foo.baz');
        $result3 = $this->_sut->enqueueStyle('3a', 'http://foo.bah', array('1a', '2a', '4a'));
        $result4 = $this->_sut->enqueueStyle('4a', 'http://foo.baa');
        $result5 = $this->_sut->enqueueStyle('5a', 'http://foo.baw');

        $this->assertTrue($result1 && $result2 && $result3 && $result4 && $result5);

        $this->assertEquals(array('2a', '4a', '1a', '5a', '3a'), $this->_sut->getStyleHandlesForDisplay());
    }

    public function testQueueWithBadDep()
    {
        $result = $this->_sut->enqueueStyle('handle', 'http://foo.bar', array('x'), 'screen');

        $this->assertTrue($result);

        $this->assertEquals(array(), $this->_sut->getStyleHandlesForDisplay());
    }

    public function testQueue()
    {
        $result = $this->_sut->enqueueStyle('handle', 'http://foo.bar', array(), 'screen');

        $this->assertTrue($result);

        $this->assertEquals(array('handle'), $this->_sut->getStyleHandlesForDisplay());
    }

    public function testQueueNonStringMedia()
    {
        $result = $this->_sut->enqueueStyle('handle', 'http://foo.bar', array(), array());

        $this->assertFalse($result);
    }

    public function testQueueEmptyStringMedia()
    {
        $result = $this->_sut->enqueueStyle('handle', 'http://foo.bar', array(), '');

        $this->assertFalse($result);
    }

    public function testInvalidUrl()
    {
        $result = $this->_sut->enqueueStyle('handle', 'xyz');

        $this->assertFalse($result);
    }

    public function testQueueNonStringUrl()
    {
        $result = $this->_sut->enqueueStyle('handle', array());

        $this->assertFalse($result);
    }

    public function testQueueEmptyStringUrl()
    {
        $result = $this->_sut->enqueueStyle('', '');

        $this->assertFalse($result);
    }

    public function testQueueNonStringHandle()
    {
        $result = $this->_sut->enqueueStyle(array(), 'http://foo.bar');

        $this->assertFalse($result);
    }

    public function testQueueEmptyStringHandle()
    {
        $result = $this->_sut->enqueueStyle('', 'http://foo.bar');

        $this->assertFalse($result);
    }



    public function testDequeueScript()
    {
        $result1 = $this->_sut->enqueueScript('x', 'http://foo.baz', array('y', 'z'));

        $this->assertTrue($result1);

        $this->assertTrue($this->_sut->dequeueScript('x'));
    }

    public function testDequeueScriptNoSuch()
    {
        $this->assertFalse($this->_sut->dequeueScript('x'));
    }

    public function testGetScript()
    {
        $this->assertNull($this->_sut->getScript('x'));

        $result1 = $this->_sut->enqueueScript('x', 'http://foo.baz', array('y', 'z'));

        $this->assertTrue($result1);

        $style = $this->_sut->getScript('x');

        $this->assertEquals(array(

            'url' => 'http://foo.baz',
            'dependencies' => array('y', 'z'),
        ), $style);
    }

    public function testGetScriptNoSuchScript()
    {
        $this->assertNull($this->_sut->getScript('x'));
    }

    public function testQueueScriptWithDep2()
    {
        $result1 = $this->_sut->enqueueScript('x', 'http://foo.baz', array('y', 'z'));
        $result2 = $this->_sut->enqueueScript('y', 'http://foo.baz', array('h'));
        $result3 = $this->_sut->enqueueScript('z', 'http://foo.bah', array('a', 'f', 'g'));
        $result4 = $this->_sut->enqueueScript('h', 'http://foo.baa');
        $result5 = $this->_sut->enqueueScript('a', 'http://foo.bw');
        $result6 = $this->_sut->enqueueScript('f', 'http://foo.bsaw');
        $result7 = $this->_sut->enqueueScript('g', 'http://foo.bagw');

        $this->assertTrue($result1 && $result2 && $result3 && $result4 && $result5 && $result6 && $result7);

        $this->assertEquals(array('g', 'f', 'a', 'h', 'z', 'y', 'x'), $this->_sut->getScriptHandlesForDisplay());
    }

    public function testQueueScriptWithDep1()
    {
        $result1 = $this->_sut->enqueueScript('1a', 'http://foo.baz', array('2a'));
        $result2 = $this->_sut->enqueueScript('2a', 'http://foo.baz');
        $result3 = $this->_sut->enqueueScript('3a', 'http://foo.bah', array('1a', '2a', '4a'));
        $result4 = $this->_sut->enqueueScript('4a', 'http://foo.baa');
        $result5 = $this->_sut->enqueueScript('5a', 'http://foo.baw');

        $this->assertTrue($result1 && $result2 && $result3 && $result4 && $result5);

        $this->assertEquals(array('2a', '4a', '1a', '5a', '3a'), $this->_sut->getScriptHandlesForDisplay());
    }

    public function testQueueScriptWithBadDep()
    {
        $result = $this->_sut->enqueueScript('handle', 'http://foo.bar', array('x'));

        $this->assertTrue($result);

        $this->assertEquals(array(), $this->_sut->getScriptHandlesForDisplay());
    }

    public function testQueueScript()
    {
        $result = $this->_sut->enqueueScript('handle', 'http://foo.bar', array());

        $this->assertTrue($result);

        $this->assertEquals(array('handle'), $this->_sut->getScriptHandlesForDisplay());
    }

    public function testInvalidScriptUrl()
    {
        $result = $this->_sut->enqueueScript('handle', 'xyz');

        $this->assertFalse($result);
    }

    public function testQueueScriptNonStringUrl()
    {
        $result = $this->_sut->enqueueScript('handle', array());

        $this->assertFalse($result);
    }

    public function testQueueScriptEmptyStringUrl()
    {
        $result = $this->_sut->enqueueScript('', '');

        $this->assertFalse($result);
    }

    public function testQueueScriptNonStringHandle()
    {
        $result = $this->_sut->enqueueScript(array(), 'http://foo.bar');

        $this->assertFalse($result);
    }

    public function testQueueScriptEmptyStringHandle()
    {
        $result = $this->_sut->enqueueScript('', 'http://foo.bar');

        $this->assertFalse($result);
    }
}
