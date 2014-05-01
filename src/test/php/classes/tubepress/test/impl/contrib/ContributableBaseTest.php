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
class tubepress_test_impl_contrib_ContributableBaseTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrlFactory;

    public function onSetup()
    {
        $this->_mockFinderFactory = $this->createMockSingletonService('ehough_finder_FinderFactoryInterface');
        $this->_mockUrlFactory    = $this->createMockSingletonService(tubepress_api_url_UrlFactoryInterface::_);
    }

    public function testNormalConstruction1()
    {
        $sut = new tubepress_impl_contrib_ContributableBase(

            'NAme',
            '1.0.0',
            'description',
            array('name' => 'eric'),
            array(array('type' => 'proprietary')),
            $this->_mockUrlFactory
        );

        $this->assertEquals('name', $sut->getName());
        $this->assertEquals('1.0.0', (string) $sut->getVersion());
        $this->assertEquals('description', $sut->getTitle());
        $this->assertEquals(array('name' => 'eric'), $sut->getAuthor());
        $this->assertEquals(array(array('type' => 'proprietary')), $sut->getLicenses());
    }

    public function testNormalConstruction2()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://foo.bar');

        $sut = new tubepress_impl_contrib_ContributableBase(

            'name',
            tubepress_api_version_Version::parse('2.3.1'),
            'description',
            array('name' => 'eric', 'url' => 'http://foo.bar'),
            array(array('type' => 'proprietary')),
            $this->_mockUrlFactory
        );

        $this->assertEquals('name', $sut->getName());
        $this->assertEquals('2.3.1', (string) $sut->getVersion());
        $this->assertEquals('description', $sut->getTitle());
        $this->assertEquals(array('name' => 'eric', 'url' => 'http://foo.bar'), $sut->getAuthor());
        $this->assertEquals(array(array('type' => 'proprietary')), $sut->getLicenses());
    }

    public function testInvalidNameBadChars()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid name.');

        new tubepress_impl_contrib_ContributableBase(

            '@#*(&*&',
            '1.2.3',
            'description',
            array('name' => 'eric'),
            array(array('url' => 'http://tubepress.com')),
            $this->_mockUrlFactory
        );
    }

    public function testInvalidNameTooLong()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid name.');

        new tubepress_impl_contrib_ContributableBase(

            'wlkjwkljrwklejfwklfjsdklfjsdklfjsdgkljgkljdfkljsdfklaejerklwjfsklfjskldjskldfjsklfjsklgjwekltjsdklvjhxdjkvhsklfjsdjkfhsdjklfhsdfjhsdfsg',
            '1.2.3',
            'description',
            array('name' => 'eric'),
            array(array('url' => 'http://tubepress.com')),
            $this->_mockUrlFactory
        );
    }

    public function testInvalidNameTooShort()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid name.');

        new tubepress_impl_contrib_ContributableBase(

            '',
            '1.2.3',
            'description',
            array('name' => 'eric'),
            array(array('url' => 'http://tubepress.com')),
            $this->_mockUrlFactory
        );
    }

    public function testNonStringName()
    {
        $this->setExpectedException('InvalidArgumentException', 'Name must be a string');

        new tubepress_impl_contrib_ContributableBase(

            array(),
            '1.2.3',
            'description',
            array('name' => 'eric'),
            array(array('url' => 'http://tubepress.com')),
            $this->_mockUrlFactory
        );
    }

    public function testBadVersion()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid version: x is not a number');

        new tubepress_impl_contrib_ContributableBase(

            'name',
            'x.y.z',
            'description',
            array('name' => 'eric'),
            array(array('url' => 'http://tubepress.com')),
            $this->_mockUrlFactory
        );
    }

    public function testInvalidTitleTooLong()
    {
        $this->setExpectedException('InvalidArgumentException', 'Title must be 255 characters or less.');

        new tubepress_impl_contrib_ContributableBase(

            'foobar',
            '1.2.3',
            'wlkjwkljrwklejfwklfjsdklfjsdklfjsdgkljgkljdfkljsdfklaejerklwjfsklfjskldjskldfjsklfjsklgjwekltjsdklvjhxdjkvhsklfjsdjkfhsdjklfhsdfjhsdfsg'
            . 'wlkjwkljrwklejfwklfjsdklfjsdklfjsdgkljgkljdfkljsdfklaejerklwjfsklfjskldjskldfjsklfjsklgjwekltjsdklvjhxdjkvhsklfjsdjkfhsdjklfhsdfjhsdfsg',
            array('name' => 'eric'),
            array(array('url' => 'http://tubepress.com')),
            $this->_mockUrlFactory
        );
    }

    public function testInvalidTitleNonString()
    {
        $this->setExpectedException('InvalidArgumentException', 'Title must be a string');

        new tubepress_impl_contrib_ContributableBase(

            'foobar',
            '1.2.3',
            array(),
            array('name' => 'eric'),
            array(array('url' => 'http://tubepress.com')),
            $this->_mockUrlFactory
        );
    }

    public function testInvalidAuthorUrl()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('bar')->andThrow(new InvalidArgumentException());

        $this->setExpectedException('InvalidArgumentException', 'Invalid author URL.');

        new tubepress_impl_contrib_ContributableBase(

            'name',
            '1.0.0',
            'description',
            array('name' => 'eric', 'url' => 'bar'),
            array(array('url' => 'http://tubepress.com')),
            $this->_mockUrlFactory
        );
    }

    public function testInvalidAuthorAttribute()
    {
        $this->setExpectedException('InvalidArgumentException', 'Author information must only include name, email, and/or URL');

        new tubepress_impl_contrib_ContributableBase(

            'name',
            '1.0.0',
            'description',
            array('name' => 'eric', 'foo' => 'bar'),
            array(array('url' => 'http://tubepress.com')),
            $this->_mockUrlFactory
        );
    }

    public function testMissingAuthorName()
    {
        $this->setExpectedException('InvalidArgumentException', 'Must include author name');

        new tubepress_impl_contrib_ContributableBase(

            'name',
            '1.0.0',
            'description',
            array('foo' => 'eric'),
            array(array('url' => 'http://tubepress.com')),
            $this->_mockUrlFactory
        );
    }

    public function testInvalidAuthorEmail()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://foo.bar');

        $this->setExpectedException('InvalidArgumentException', 'Author email is invalid.');

        new tubepress_impl_contrib_ContributableBase(

            'name',
            '1.0.0',
            'description',
            array('name' => 'eric', 'url' => 'http://foo.bar', 'email' => 'xyz'),
            array(array('type' => 'foobar')),
            $this->_mockUrlFactory
        );
    }

    public function testAuthor()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://foo.bar');

        $contrib = new tubepress_impl_contrib_ContributableBase(

            'name',
            '1.0.0',
            'description',
            array('name' => 'eric', 'url' => 'http://foo.bar', 'email' => 'no@yes.com'),
            array(array('type' => 'foobar')),
            $this->_mockUrlFactory
        );

        $author = $contrib->getAuthor();

        $this->assertEquals('eric', $author['name']);
        $this->assertEquals('http://foo.bar', $author['url']);
        $this->assertEquals('no@yes.com', $author['email']);
    }

    public function testInvalidLicenseAttribute()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://foo.bar');

        $this->setExpectedException('InvalidArgumentException', 'Only \'url\' and \'type\' attributes are supported for licenses');

        new tubepress_impl_contrib_ContributableBase(

            'name',
            tubepress_api_version_Version::parse('2.3.1'),
            'description',
            array('name' => 'eric', 'url' => 'http://foo.bar'),
            array(array('type' => 'proprietary', 'foo' => 'bar')),
            $this->_mockUrlFactory
        );
    }

    public function testInvalidLicenseUrl()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://foo.bar');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('bar')->andThrow(new InvalidArgumentException());

        $this->setExpectedException('InvalidArgumentException', 'Invalid license URL.');

        new tubepress_impl_contrib_ContributableBase(

            'name',
            tubepress_api_version_Version::parse('2.3.1'),
            'description',
            array('name' => 'eric', 'url' => 'http://foo.bar'),
            array(array('type' => 'proprietary', 'url' => 'bar')),
            $this->_mockUrlFactory
        );
    }

    public function testMissingLicenseIsMissingType()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://foo.bar');

        $this->setExpectedException('InvalidArgumentException', 'License is missing type');

        new tubepress_impl_contrib_ContributableBase(

            'name',
            tubepress_api_version_Version::parse('2.3.1'),
            'description',
            array('name' => 'eric', 'url' => 'http://foo.bar'),
            array(array('foo' => 'bar')),
            $this->_mockUrlFactory
        );
    }

    public function testMissingLicense()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://foo.bar');

        $this->setExpectedException('InvalidArgumentException', 'Must include at least one license.');

        new tubepress_impl_contrib_ContributableBase(

            'name',
            tubepress_api_version_Version::parse('2.3.1'),
            'description',
            array('name' => 'eric', 'url' => 'http://foo.bar'),
            array(),
            $this->_mockUrlFactory
        );
    }

    public function testLicense()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://foo.bar');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://foo.bar/');

        $contrib = new tubepress_impl_contrib_ContributableBase(

            'name',
            tubepress_api_version_Version::parse('2.3.1'),
            'description',
            array('name' => 'eric', 'url' => 'http://foo.bar'),
            array(array('type' => 'foobar', 'url' => 'http://foo.bar/')),
            $this->_mockUrlFactory
        );

        $licenses = $contrib->getLicenses();
        $this->assertTrue(is_array($licenses));
        $this->assertCount(1, $licenses);

        $license = $licenses[0];
        $this->assertTrue(is_array($license));
        $this->assertCount(2, $license);

        $this->assertEquals('foobar', $license['type']);
        $this->assertEquals('http://foo.bar/', $license['url']);
    }

    public function testSetGetDescription()
    {
        $sut = new tubepress_impl_contrib_ContributableBase(

            'name',
            '1.0.0',
            'description',
            array('name' => 'eric'),
            array(array('type' => 'proprietary')),
            $this->_mockUrlFactory
        );

        $sut->setDescription('something');
        $this->assertEquals('something', $sut->getDescription());
    }

    public function testSetNonStringDescription()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://foo.bar');

        $this->setExpectedException('InvalidArgumentException', 'Description must be a string');

        $addon = $this->_buildValidContributable();

        $addon->setDescription(array());
    }

    public function testDescriptionTooLong()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://foo.bar');

        $this->setExpectedException('InvalidArgumentException', 'Description must be 2000 characters or less.');

        $addon = $this->_buildValidContributable();

        $addon->setDescription(str_repeat('x', 2001));
    }

    public function testGetSetKeywords()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://foo.bar');

        $addon = $this->_buildValidContributable();

        $addon->setKeywords(array('foo', 'bar'));
        $this->assertEquals(array('foo', 'bar'), $addon->getKeywords());
    }

    public function testInvalidKeywordTooLong()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://foo.bar');

        $this->setExpectedException('InvalidArgumentException', 'Each keyword must be 100 characters or less.');

        $addon = $this->_buildValidContributable();

        $addon->setKeywords(array(str_repeat('x', 101)));
    }

    public function testInvalidKeywordTooShort()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://foo.bar');

        $this->setExpectedException('InvalidArgumentException', 'Keywords must not be empty.');

        $addon = $this->_buildValidContributable();

        $addon->setKeywords(array(''));
    }

    public function testSetNonStringKeyword()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://foo.bar');

        $this->setExpectedException('InvalidArgumentException', 'Each keyword must be a string');

        $addon = $this->_buildValidContributable();

        $addon->setKeywords(array('foo', array()));
    }

    public function testGetSetBugsUrl()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->twice()->with('http://foo.bar');

        $addon = $this->_buildValidContributable();

        $addon->setBugTrackerUrl('http://foo.bar');
        $this->assertEquals('http://foo.bar', $addon->getBugTrackerUrl());
    }

    public function testInvalidBugsUrl()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://foo.bar');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('xyz')->andThrow(new InvalidArgumentException());

        $this->setExpectedException('InvalidArgumentException', 'Invalid bug tracker URL.');

        $addon = $this->_buildValidContributable();

        $addon->setBugTrackerUrl('xyz');
    }

    public function testGetSetDownloadUrl()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->twice()->with('http://foo.bar');

        $addon = $this->_buildValidContributable();

        $addon->setDownloadUrl('http://foo.bar');
        $this->assertEquals('http://foo.bar', $addon->getDownloadUrl());
    }

    public function testInvalidDownloadUrl()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://foo.bar');

        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('xyz')->andThrow(new InvalidArgumentException());

        $this->setExpectedException('InvalidArgumentException', 'Invalid download URL.');

        $addon = $this->_buildValidContributable();

        $addon->setDownloadUrl('xyz');
    }

    public function testGetSetDemoUrl()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->twice()->with('http://foo.bar');

        $addon = $this->_buildValidContributable();

        $addon->setDemoUrl('http://foo.bar');
        $this->assertEquals('http://foo.bar', $addon->getDemoUrl());
    }

    public function testInvalidDemoUrl()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://foo.bar');

        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('xyz')->andThrow(new InvalidArgumentException());

        $this->setExpectedException('InvalidArgumentException', 'Invalid demo URL.');

        $addon = $this->_buildValidContributable();

        $addon->setDemoUrl('xyz');
    }

    public function testGetSetDocsUrl()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->twice()->with('http://foo.bar');

        $addon = $this->_buildValidContributable();

        $addon->setDocumentationUrl('http://foo.bar');
        $this->assertEquals('http://foo.bar', $addon->getDocumentationUrl());
    }

    public function testInvalidDocsUrl()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://foo.bar');

        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('xyz')->andThrow(new InvalidArgumentException());

        $this->setExpectedException('InvalidArgumentException', 'Invalid documentation URL.');

        $addon = $this->_buildValidContributable();

        $addon->setDocumentationUrl('xyz');
    }

    public function testGetSetHomePageUrl()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->twice()->with('http://foo.bar');

        $addon = $this->_buildValidContributable();

        $addon->setHomepageUrl('http://foo.bar');
        $this->assertEquals('http://foo.bar', $addon->getHomepageUrl());
    }

    public function testInvalidHomepageUrl()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://foo.bar');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('xyz')->andThrow(new InvalidArgumentException());

        $this->setExpectedException('InvalidArgumentException', 'Invalid homepage URL.');

        $addon = $this->_buildValidContributable();

        $addon->setHomepageUrl('xyz');
    }

    public function testScreenshots()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://foo.bar');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://foo.bar/one.png');

        $sut = $this->_buildValidContributable();

        $sut->setScreenshots(array('http://foo.bar/one.png'));
        $this->assertEquals(array('http://foo.bar/one.png' => 'http://foo.bar/one.png'), $sut->getScreenshots());

        $sut->setScreenshots(array('foo/one.png'));
        $this->assertEquals(array('foo/one.png' => 'foo/one.png'), $sut->getScreenshots());

        $sut->setScreenshots(array('foo/one.png' => 'bar/two.jpg'));
        $this->assertEquals(array('foo/one.png' => 'bar/two.jpg'), $sut->getScreenshots());
    }

    public function testBadUrlScreenshots()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://foo.bar');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('httphttphttp')->andThrow(new InvalidArgumentException());

        $sut = $this->_buildValidContributable();

        $this->setExpectedException('InvalidArgumentException', 'Invalid screenshot URL');

        $sut->setScreenshots(array('httphttphttp'));
    }

    public function testSetNonStringScreenshots()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://foo.bar');

        $sut = $this->_buildValidContributable();

        $this->setExpectedException('InvalidArgumentException', 'Screenshot URLs must be a string');

        $sut->setScreenshots(array(new stdClass()));
    }

    public function testSetNonImageScreenshots()
    {
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://foo.bar');

        $sut = $this->_buildValidContributable();

        $this->setExpectedException('InvalidArgumentException', 'Each screenshot URL must end with one of: .png, .jpg');

        $sut->setScreenshots(array('one/two.pdf'));
    }

    private function _buildValidContributable()
    {
        return new tubepress_impl_contrib_ContributableBase(

            'name',
            tubepress_api_version_Version::parse('2.3.1'),
            'description',
            array('name' => 'eric', 'url' => 'http://foo.bar'),
            array(array('type' => 'proprietary')),
            $this->_mockUrlFactory
        );
    }
}