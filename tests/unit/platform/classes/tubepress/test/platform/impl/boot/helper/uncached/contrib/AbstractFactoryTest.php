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

abstract class tubepress_test_platform_impl_boot_helper_uncached_contrib_AbstractFactoryTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_platform_impl_boot_helper_uncached_contrib_AbstractFactory
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrlFactory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLangUtils;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockStringUtils;

    public function onSetup()
    {
        $this->_mockLogger      = $this->mock(tubepress_platform_api_log_LoggerInterface::_);
        $this->_mockLangUtils   = $this->mock(tubepress_platform_api_util_LangUtilsInterface::_);
        $this->_mockUrlFactory  = $this->mock(tubepress_platform_api_url_UrlFactoryInterface::_);
        $this->_mockStringUtils = $this->mock(tubepress_platform_api_util_StringUtilsInterface::_);

        $this->_mockLangUtils->shouldReceive('isAssociativeArray')->andReturnUsing(function ($candidate) {

            $util = new tubepress_platform_impl_util_LangUtils();

            return $util->isAssociativeArray($candidate);
        });

        $this->_mockLangUtils->shouldReceive('isSimpleArrayOfStrings')->andReturnUsing(function ($candidate) {

            $util = new tubepress_platform_impl_util_LangUtils();

            return $util->isSimpleArrayOfStrings($candidate);
        });

        $this->_mockUrlFactory->shouldReceive('fromString')->andReturnUsing(function ($candidate) {

            $factory = new tubepress_platform_impl_url_puzzle_UrlFactory();

            return $factory->fromString($candidate);
        });

        $this->_mockStringUtils->shouldReceive('endsWith')->andReturnUsing(function ($haystack, $needle) {

            $utils = new tubepress_platform_impl_util_StringUtils();

            return $utils->endsWith($haystack, $needle);
        });

        $this->_mockLogger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);
        $this->_mockLogger->shouldReceive('debug');

        $this->_sut = $this->buildSut(
            $this->_mockLogger,
            $this->_mockUrlFactory,
            $this->_mockLangUtils,
            $this->_mockStringUtils
        );
    }

    /**
     * @return tubepress_platform_impl_boot_helper_uncached_contrib_AbstractFactory
     */
    protected abstract function buildSut(tubepress_platform_api_log_LoggerInterface       $logger,
                                         tubepress_platform_api_url_UrlFactoryInterface   $urlFactory,
                                         tubepress_platform_api_util_LangUtilsInterface   $langUtils,
                                         tubepress_platform_api_util_StringUtilsInterface $stringUtils);

    protected function getSut()
    {
        return $this->_sut;
    }

    protected function getMockLogger()
    {
        return $this->_mockLogger;
    }

    /**
     * @dataProvider getBadScreenshots
     */
    public function testBadScreenshots($candidate, $message)
    {
        $data = array(
            'screenshots' => $candidate
        );

        $this->confirmFailures($data, array($message));
    }

    public function getBadScreenshots()
    {
        return array(

            array('x',                                                  'Screenshots must be an array'),
            array(array('foo'),                                         'Screenshot 1 has an invalid URL'),
            array(array('foo.png'),                                     'Screenshot 1 has an invalid URL'),
            array(array(array('foo.png')),                              'Screenshot 1 must have exactly two URLs'),
            array(array(array('foo.png', 'http://foo.com/bar.png')),    'Screenshot 1 has an invalid thumbnail URL'),
            array(array(array('http://foo.com/bar.png', 'foo.png')),    'Screenshot 1 has an invalid full-size URL'),
        );
    }

    /**
     * @dataProvider getBadUrls
     */
    public function testBadHomepage($candidate)
    {
        $data = array(
            'urls' => array(
                'homepage' => $candidate
            ));

        $this->confirmFailures($data, array('Invalid homepage URL'));
    }

    /**
     * @dataProvider getBadUrls
     */
    public function testBadDocs($candidate)
    {
        $data = array(
            'urls' => array(
                'documentation' => $candidate
            ));

        $this->confirmFailures($data, array('Invalid documentation URL'));
    }

    /**
     * @dataProvider getBadUrls
     */
    public function testBadDemo($candidate)
    {
        $data = array(
            'urls' => array(
                'demo' => $candidate
            ));

        $this->confirmFailures($data, array('Invalid demo URL'));
    }

    /**
     * @dataProvider getBadUrls
     */
    public function testBadDownload($candidate)
    {
        $data = array(
            'urls' => array(
                'download' => $candidate
            ));

        $this->confirmFailures($data, array('Invalid download URL'));
    }

    /**
     * @dataProvider getBadUrls
     */
    public function testBadBugs($candidate)
    {
        $data = array(
            'urls' => array(
                'bugTracker' => $candidate
            ));

        $this->confirmFailures($data, array('Invalid bugTracker URL'));
    }

    public function getBadUrls()
    {
        return array(

            array(''),
            array('/something'),
            array('hi'),
            array(3),
            array(new stdClass()),
            array(array()),
        );
    }

    /**
     * @dataProvider getBadKeywords
     */
    public function testBadKeywords($candidate, $message)
    {
        $data = array(
            'keywords' => $candidate
        );

        $this->confirmFailures($data, array($message));
    }

    public function getBadKeywords()
    {
        return array(

            array('', 'Keywords must be an array of strings'),
            array(array(), 'Keywords must be an array of strings'),
            array(array('foo' => 'bar'), 'Keywords must be an array of strings'),
            array(array(new stdClass()), 'Keywords must be an array of strings'),
            array(array('foo', str_repeat('x', 31)), 'keywords must be between 1 and 30 characters'),
        );
    }

    /**
     * @dataProvider getBadDescriptions
     */
    public function testBadDescriptions($candidate, $message)
    {
        $data = array(
            'description' => $candidate
        );

        $this->confirmFailures($data, array($message));
    }

    public function getBadDescriptions()
    {
        return array(

            array(new stdClass(), 'Non-string description'),
            array(str_repeat('x', 5001), 'description must be between 1 and 5000 characters'),
        );
    }

    /**
     * @dataProvider getBadLicenses
     */
    public function testBadLicenses($candidate, $message)
    {
        $data = array(
            'licenses' => $candidate
        );

        $this->confirmFailures($data, array($message));
    }

    public function getBadLicenses()
    {
        return array(

            array(null, 'Missing licenses'),
            array(new stdClass(), 'Non-array data for licenses'),
            array(array(), 'Missing licenses'),
            array(array(array()), 'License 1 is missing URL attribute'),
            array(array(array('foo' => 'bar')), 'License 1 is missing URL attribute'),
            array(array(array('url' => '')), 'License 1 has an invalid URL attribute'),
            array(array(array('url' => new stdClass())), 'License 1 has an invalid URL attribute'),
            array(array(array('url' => 'http://foo.bar/license.txt', 'foo' => 'bar')), 'License 1 has attributes other than url and type'),
            array(array(array('url' => 'http://foo.bar/license.txt', 'foo' => new stdClass())), 'License 1 has attributes other than url and type'),
            array(array(array('url' => 'http://foo.bar/license.txt', 'type' => new stdClass())), 'License 1 has an invalid type attribute'),
        );
    }

    /**
     * @dataProvider getBadAuthors
     */
    public function testBadAuthors($candidate, $message)
    {
        $data = array(
            'authors' => $candidate
        );

        $this->confirmFailures($data, array($message));
    }

    public function getBadAuthors()
    {
        return array(

            array(null, 'Missing authors'),
            array(new stdClass(), 'Non-array data for authors'),
            array(array(), 'Missing authors'),
            array(array(array()), 'Author 1 is missing name'),
            array(array(array('foo' => 'bar')), 'Author 1 is missing name'),
            array(array(array('name' => 'some name', 'foo' => 'bar')), 'Author 1 has attributes other than name, email, and url'),
            array(array(array('name' => 'some name', 'email' => new stdClass())), 'Author 1 has an invalid email attribute'),
            array(array(array('name' => 'some name', 'url' => new stdClass())), 'Author 1 has an invalid URL attribute'),
        );
    }

    /**
     * @dataProvider getBadTitles
     */
    public function testBadTitle($candidate, $message)
    {
        $data = array(
            'title' => $candidate
        );

        $this->confirmFailures($data, array($message));
    }

    public function getBadTitles()
    {
        return array(

            array('', 'title must be between 1 and 255 characters'),
            array(array(), 'Non-string title'),
            array(null, 'Missing title'),
            array(str_repeat('x', 256), 'title must be between 1 and 255 characters'),
        );
    }

    /**
     * @dataProvider getBadNames
     */
    public function testBadName($candidate, $message)
    {
        $data = array(
            'name' => $candidate
        );

        $this->confirmFailures($data, array($message));
    }

    public function getBadNames()
    {
        return array(

            array(null, 'Missing name'),
            array('', 'Add-on and theme names must be all lowercase, 100 characters or less, and contain only alphanumerics, dots, dashes, underscores, and slashes'),
            array('()@*#', 'Add-on and theme names must be all lowercase, 100 characters or less, and contain only alphanumerics, dots, dashes, underscores, and slashes'),
            array(str_repeat('x', 101), 'Add-on and theme names must be all lowercase, 100 characters or less, and contain only alphanumerics, dots, dashes, underscores, and slashes'),
            array(array(), 'Add-on and theme names must be strings'),
        );
    }

    /**
     * @dataProvider getDataTestFailedVersions
     */
    public function testFailedVersions($candidate, $message)
    {
        $data = array(
            'version' => $candidate
        );

        $this->confirmFailures($data, array($message));
    }

    public function getDataTestFailedVersions()
    {
        return array(

            array('', 'Empty version'),
            array(null, 'Missing version'),
            array('()@*#', 'Malformed version'),
            array('tubepress', 'Malformed version'),
            array('1.1.b', 'Malformed version'),
        );
    }

    /**
     * @return tubepress_platform_impl_contrib_AbstractContributable
     */
    protected function fromManifest(array $data = array())
    {
        $finalData = array_merge($this->_getMinimalValidData(), $data);

        return $this->_sut->fromManifestData(sys_get_temp_dir() . '/m.json', $finalData);
    }

    protected function confirmFailures(array $data, array $expectedErrors)
    {
        $this->_mockLogger->shouldReceive('error')->once()->with('The following errors were detected when processing ' . sys_get_temp_dir() . '/m.json');
        foreach ($expectedErrors as $expectedError) {

            $this->_mockLogger->shouldReceive('error')->once()->with($expectedError);
        }
        $actualErrors = $this->fromManifest($data);
        $this->assertEquals($expectedErrors, $actualErrors);
    }

    public function testBasics()
    {
        $this->setupForBasicsTest();

        $valid = $this->fromManifest();

        $this->assertEquals('some-name', $valid->getName());
        $this->assertInstanceOf('tubepress_platform_api_version_Version', $valid->getVersion());
        $this->assertEquals('1.2.3', $valid->getVersion()->__toString());
        $this->assertEquals('some title', $valid->getTitle());

        $authors = $valid->getAuthors();
        $this->assertTrue(is_array($authors));
        $this->assertCount(2, $authors);
        $firstAuthor = $authors[0];
        $this->assertInstanceOf('tubepress_platform_api_collection_MapInterface', $firstAuthor);
        $this->assertTrue($firstAuthor->count() === 2);
        $this->assertEquals('author name', $firstAuthor->get('name'));
        $this->assertInstanceOf('tubepress_platform_api_url_UrlInterface', $firstAuthor->get('url'));
        $this->assertEquals('http://author.com/foo', (string) $firstAuthor->get('url'));
        $secondAuthor = $authors[1];
        $this->assertInstanceOf('tubepress_platform_api_collection_MapInterface', $secondAuthor);
        $this->assertTrue($secondAuthor->count() === 2);
        $this->assertEquals('other author name', $secondAuthor->get('name'));
        $this->assertEquals('fake@email.com', $secondAuthor->get('email'));
        $licenses = $valid->getLicenses();
        $this->assertTrue(is_array($licenses));
        $this->assertCount(1, $licenses);
        $license = $licenses[0];
        $this->assertInstanceOf('tubepress_platform_api_collection_MapInterface', $license);
        $this->assertTrue($license->count() === 2);
        $this->assertInstanceOf('tubepress_platform_api_url_UrlInterface', $license->get('url'));
        $this->assertEquals('http://license.com/text.html', (string) $license->get('url'));
        $this->assertEquals('some license type', $license->get('type'));
    }

    protected function setupForBasicsTest()
    {
        //override point
    }


    private function _getMinimalValidData()
    {
        return array(

            'name'    => 'some-name',
            'version' => '1.2.3',
            'title'   => 'some title',
            'authors' => array(
                array(
                    'name' => 'author name',
                    'url'  => 'http://author.com/foo'
                ),
                array(
                    'name'  => 'other author name',
                    'email' => 'fake@email.com'
                ),
            ),
            'licenses' => array(
                array(
                    'url'  => 'http://license.com/text.html',
                    'type' => 'some license type'
                )
            )
        );
    }
}