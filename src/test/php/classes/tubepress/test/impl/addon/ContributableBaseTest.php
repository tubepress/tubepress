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
 * @covers tubepress_impl_contrib_ContributableBase<extended>
 */
class tubepress_test_impl_player_ContributableBaseTest extends tubepress_test_TubePressUnitTest
{
    public function testNormalConstruction1()
    {
        $sut = new tubepress_impl_contrib_ContributableBase(

            'name',
            '1.0.0',
            'description',
            array('name' => 'eric'),
            array(array('url' => 'http://tubepress.com'))
        );

        $this->assertEquals('name', $sut->getName());
        $this->assertEquals('1.0.0', (string) $sut->getVersion());
        $this->assertEquals('description', $sut->getTitle());
        $this->assertEquals(array('name' => 'eric'), $sut->getAuthor());
        $this->assertEquals(array(array('url' => 'http://tubepress.com')), $sut->getLicenses());
    }

    public function testNormalConstruction2()
    {
        $sut = new tubepress_impl_contrib_ContributableBase(

            'name',
            tubepress_spi_version_Version::parse('2.3.1'),
            'description',
            array('name' => 'eric', 'url' => 'http://foo.bar'),
            array(array('url' => 'http://tubepress.com'))
        );

        $this->assertEquals('name', $sut->getName());
        $this->assertEquals('2.3.1', (string) $sut->getVersion());
        $this->assertEquals('description', $sut->getTitle());
        $this->assertEquals(array('name' => 'eric', 'url' => 'http://foo.bar'), $sut->getAuthor());
        $this->assertEquals(array(array('url' => 'http://tubepress.com')), $sut->getLicenses());
    }

    public function testScreenshots()
    {
        $sut = $this->_buildValidContributable();

        $sut->setScreenshots(array('http://foo.bar/one.png'));
        $this->assertEquals(array('http://foo.bar/one.png'), $sut->getScreenshots());

        $sut->setScreenshots(array('foo/one.png'));
        $this->assertEquals(array('foo/one.png'), $sut->getScreenshots());
    }

    public function testBadUrlScreenshots()
    {
        $sut = $this->_buildValidContributable();

        $this->setExpectedException('InvalidArgumentException', 'httphttphttp is not a valid screenshot URL');

        $sut->setScreenshots(array('httphttphttp'));
    }

    public function testSetNonStringScreenshots()
    {
        $sut = $this->_buildValidContributable();

        $this->setExpectedException('InvalidArgumentException', 'Screenshots must be strings.');

        $sut->setScreenshots(array(new stdClass()));
    }

    public function testSetNonImageScreenshots()
    {
        $sut = $this->_buildValidContributable();

        $this->setExpectedException('InvalidArgumentException', 'Screenshot URLs must end with .png or .jpg');

        $sut->setScreenshots(array('one/two.pdf'));
    }

    public function testSetGetDescription()
    {
        $sut = new tubepress_impl_contrib_ContributableBase(

            'name',
            '1.0.0',
            'description',
            array('name' => 'eric'),
            array(array('url' => 'http://tubepress.com'))
        );

        $sut->setDescription('something');
        $this->assertEquals('something', $sut->getDescription());
    }

    public function testGetSetBugsUrl()
    {
        $addon = $this->_buildValidContributable();

        $addon->setBugTrackerUrl('http://foo.bar');
        $this->assertEquals('http://foo.bar', $addon->getBugTrackerUrl());
    }

    public function testGetSetDownloadUrl()
    {
        $addon = $this->_buildValidContributable();

        $addon->setDownloadUrl('http://foo.bar');
        $this->assertEquals('http://foo.bar', $addon->getDownloadUrl());
    }

    public function testGetSetDemoUrl()
    {
        $addon = $this->_buildValidContributable();

        $addon->setDemoUrl('http://foo.bar');
        $this->assertEquals('http://foo.bar', $addon->getDemoUrl());
    }

    public function testGetSetDocsUrl()
    {
        $addon = $this->_buildValidContributable();

        $addon->setDocumentationUrl('http://foo.bar');
        $this->assertEquals('http://foo.bar', $addon->getDocumentationUrl());
    }

    public function testGetSetHomePageUrl()
    {
        $addon = $this->_buildValidContributable();

        $addon->setHomepageUrl('http://foo.bar');
        $this->assertEquals('http://foo.bar', $addon->getHomepageUrl());
    }

    public function testGetSetKeywords()
    {
        $addon = $this->_buildValidContributable();

        $addon->setKeywords(array('foo', 'bar'));
        $this->assertEquals(array('foo', 'bar'), $addon->getKeywords());
    }

    public function testInvalidKeywordBadChars()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid keyword: $$%');

        $addon = $this->_buildValidContributable();

        $addon->setKeywords(array('$$%'));
    }

    public function testInvalidKeywordTooLong()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid keyword: sldkfjskldfjsdklfgjdklfgjdklgjsaklfjsflgkjdcbkljsklfjsdfklfgjdkljgsdff');

        $addon = $this->_buildValidContributable();

        $addon->setKeywords(array('sldkfjskldfjsdklfgjdklfgjdklgjsaklfjsflgkjdcbkljsklfjsdfklfgjdkljgsdff'));
    }

    public function testInvalidKeywordTooShort()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid keyword: ');

        $addon = $this->_buildValidContributable();

        $addon->setKeywords(array(''));
    }

    public function testSetNonStringKeyword()
    {
        $this->setExpectedException('InvalidArgumentException', 'Keywords must be strings');

        $addon = $this->_buildValidContributable();

        $addon->setKeywords(array('foo', array()));
    }

    public function testSetNonStringDescription()
    {
        $this->setExpectedException('InvalidArgumentException', 'Description must be a string');

        $addon = $this->_buildValidContributable();

        $addon->setDescription(array());
    }

    public function testInvalidLicenseAttribute()
    {
        $this->setExpectedException('InvalidArgumentException', 'Only \'url\' and \'type\' attributes are supported for licenses');

        new tubepress_impl_addon_AddonBase(

            'name',
            tubepress_spi_version_Version::parse('2.3.1'),
            'description',
            array('name' => 'eric', 'url' => 'http://foo.bar'),
            array(array('url' => 'http://foo.bar', 'foo' => 'bar')),
            'tubepress_impl_player_AddonBaseTest'
        );
    }

    public function testInvalidLicenseUrl()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid license URL: bar');

        new tubepress_impl_addon_AddonBase(

            'name',
            tubepress_spi_version_Version::parse('2.3.1'),
            'description',
            array('name' => 'eric', 'url' => 'http://foo.bar'),
            array(array('url' => 'bar')),
            'tubepress_impl_player_AddonBaseTest'
        );
    }

    public function testMissingLicenseIsMissingUrl()
    {
        $this->setExpectedException('InvalidArgumentException', 'License is missing URL');

        new tubepress_impl_addon_AddonBase(

            'name',
            tubepress_spi_version_Version::parse('2.3.1'),
            'description',
            array('name' => 'eric', 'url' => 'http://foo.bar'),
            array(array('foo' => 'bar')),
            'tubepress_impl_player_AddonBaseTest'
        );
    }

    public function testMissingLicense()
    {
        $this->setExpectedException('InvalidArgumentException', 'Missing licenses');

        new tubepress_impl_addon_AddonBase(

            'name',
            tubepress_spi_version_Version::parse('2.3.1'),
            'description',
            array('name' => 'eric', 'url' => 'http://foo.bar'),
            array(),
            'tubepress_impl_player_AddonBaseTest'
        );
    }

    public function testInvalidAuthorUrl()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid author URL: bar');

        new tubepress_impl_addon_AddonBase(

            'name',
            '1.0.0',
            'description',
            array('name' => 'eric', 'url' => 'bar'),
            array(array('url' => 'http://tubepress.com')),
            'tubepress_impl_player_AddonBaseTest'
        );
    }

    public function testInvalidAuthorAttribute()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid author attribute: foo');

        new tubepress_impl_addon_AddonBase(

            'name',
            '1.0.0',
            'description',
            array('name' => 'eric', 'foo' => 'bar'),
            array(array('url' => 'http://tubepress.com')),
            'tubepress_impl_player_AddonBaseTest'
        );
    }

    public function testMissingAuthorName()
    {
        $this->setExpectedException('InvalidArgumentException', 'Must include author name');

        new tubepress_impl_addon_AddonBase(

            'name',
            '1.0.0',
            'description',
            array('foo' => 'eric'),
            array(array('url' => 'http://tubepress.com')),
            'tubepress_impl_player_AddonBaseTest'
        );
    }

    public function testInvalidTitleTooLong()
    {
        $this->setExpectedException('InvalidArgumentException', 'Titles must be 255 characters or less');

        new tubepress_impl_addon_AddonBase(

            'foobar',
            '1.2.3',
            'wlkjwkljrwklejfwklfjsdklfjsdklfjsdgkljgkljdfkljsdfklaejerklwjfsklfjskldjskldfjsklfjsklgjwekltjsdklvjhxdjkvhsklfjsdjkfhsdjklfhsdfjhsdfsg'
                . 'wlkjwkljrwklejfwklfjsdklfjsdklfjsdgkljgkljdfkljsdfklaejerklwjfsklfjskldjskldfjsklfjsklgjwekltjsdklvjhxdjkvhsklfjsdjkfhsdjklfhsdfjhsdfsg',
            array('name' => 'eric'),
            array(array('url' => 'http://tubepress.com')),
            'tubepress_impl_player_AddonBaseTest'
        );
    }

    public function testInvalidTitleNonString()
    {
        $this->setExpectedException('InvalidArgumentException', 'Title must be a string');

        new tubepress_impl_addon_AddonBase(

            'foobar',
            '1.2.3',
            array(),
            array('name' => 'eric'),
            array(array('url' => 'http://tubepress.com')),
            'tubepress_impl_player_AddonBaseTest'
        );
    }

    public function testInvalidNameBadChars()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid name.');

        new tubepress_impl_addon_AddonBase(

            '@#*(&*&',
            '1.2.3',
            'description',
            array('name' => 'eric'),
            array(array('url' => 'http://tubepress.com')),
            'tubepress_impl_player_AddonBaseTest'
        );
    }

    public function testInvalidNameTooLong()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid name.');

        new tubepress_impl_addon_AddonBase(

            'wlkjwkljrwklejfwklfjsdklfjsdklfjsdgkljgkljdfkljsdfklaejerklwjfsklfjskldjskldfjsklfjsklgjwekltjsdklvjhxdjkvhsklfjsdjkfhsdjklfhsdfjhsdfsg',
            '1.2.3',
            'description',
            array('name' => 'eric'),
            array(array('url' => 'http://tubepress.com')),
            'tubepress_impl_player_AddonBaseTest'
        );
    }

    public function testInvalidNameTooShort()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid name.');

        new tubepress_impl_addon_AddonBase(

            '',
            '1.2.3',
            'description',
            array('name' => 'eric'),
            array(array('url' => 'http://tubepress.com')),
            'tubepress_impl_player_AddonBaseTest'
        );
    }

    public function testNonStringName()
    {
        $this->setExpectedException('InvalidArgumentException', 'Name must be a string');

        new tubepress_impl_addon_AddonBase(

            array(),
            '1.2.3',
            'description',
            array('name' => 'eric'),
            array(array('url' => 'http://tubepress.com')),
            'tubepress_impl_player_AddonBaseTest'
        );
    }

    public function testBadVersion()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid version: x is not a number');

        new tubepress_impl_addon_AddonBase(

            'name',
            'x.y.z',
            'description',
            array('name' => 'eric'),
            array(array('url' => 'http://tubepress.com')),
            'tubepress_impl_player_AddonBaseTest'
        );
    }

    private function _buildValidContributable()
    {
        return new tubepress_impl_addon_AddonBase(

            'name',
            tubepress_spi_version_Version::parse('2.3.1'),
            'description',
            array('name' => 'eric', 'url' => 'http://foo.bar'),
            array(array('url' => 'http://foo.bar'))
        );
    }
}