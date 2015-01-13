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
 * @covers tubepress_platform_impl_url_puzzle_PuzzleBasedUrl<extended>
 */
class tubepress_test_lib_url_impl_puzzle_PuzzleBasedUrlTest extends tubepress_test_TubePressUnitTest
{
    const RFC3986_BASE = "http://a/b/c/d;p?q";

    /**
     * @dataProvider dataProviderGetToString
     */
    public function testToString($url, $expectedAsString)
    {
        if (!($url instanceof tubepress_platform_api_url_UrlInterface)) {

            $urlFactory = new tubepress_platform_impl_url_puzzle_UrlFactory();
            $url        = $urlFactory->fromString($url);
        }

        $this->assertEquals($expectedAsString, $url->toString());
    }

    public function dataProviderGetToString()
    {
        $toReturn = array(
            array('/', '/'),
            array('', ''),
            array('//foo', '//foo'),
            array('http://foo.bar/some/thing', 'http://foo.bar/some/thing'),
            array('http://foo.bar/some/thing/', 'http://foo.bar/some/thing/'),
            array('http://foo.bar', 'http://foo.bar'),
            array('http://foo.bar/', 'http://foo.bar/'),
        );

        $urlFactory = new tubepress_platform_impl_url_puzzle_UrlFactory();

        $url = $urlFactory->fromString('http://foo.bar/some/thing');
        $url->removeSchemeAndAuthority();
        $toReturn[] = array($url, '/some/thing');

        $url = $urlFactory->fromString('http://foo.bar/');
        $url->removeSchemeAndAuthority();
        $toReturn[] = array($url, '/');

        $url = $urlFactory->fromString('http://foo.bar');
        $url->removeSchemeAndAuthority();
        $url->addPath('/foo/bar');
        $toReturn[] = array($url, '/foo/bar');

        $url = $urlFactory->fromString('http://foo.bar');
        $url->removeSchemeAndAuthority();
        $url->addPath('foo/bar');
        $toReturn[] = array($url, '/foo/bar');

        $url = $urlFactory->fromString('http://foo.bar/');
        $url->removeSchemeAndAuthority();
        $url->addPath('/foo/bar');
        $toReturn[] = array($url, '/foo/bar');

        return $toReturn;
    }

    public function testEmptyUrl()
    {
        $url = new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl(puzzle_Url::fromString(''));
        $this->assertEquals('', (string) $url);
    }

    public function testPortIsDeterminedFromScheme()
    {
        $u = new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl(puzzle_Url::fromString('http://www.test.com/'));
        $this->assertEquals(80, $u->getPort());
        $u = new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl(puzzle_Url::fromString('https://www.test.com/'));
        $this->assertEquals(443, $u->getPort());
        $u = new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl(puzzle_Url::fromString('ftp://www.test.com/'));
        $this->assertEquals(21, $u->getPort());
        $u = new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl(puzzle_Url::fromString('http://www.test.com:8192/'));
        $this->assertEquals(8192, $u->getPort());
        $u = new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl(puzzle_Url::fromString('foo://www.test.com/'));
        $this->assertEquals(null, $u->getPort());
    }

    public function testRemovesDefaultPortWhenSettingScheme()
    {
        $url = new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl(puzzle_Url::fromString('http://www.test.com/'));
        $url->setPort(80);
        $url->setScheme('https');
        $this->assertEquals(443, $url->getPort());
    }

    public function testValidatesUrlPartsInFactory()
    {
        $url = new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl(puzzle_Url::fromString('/index.php'));
        $this->assertEquals('/index.php', (string) $url);
        $this->assertFalse($url->isAbsolute());

        $url = 'http://michael:test@test.com:80/path/123?q=abc#test';
        $u = new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl(puzzle_Url::fromString($url));
        $this->assertEquals('http://michael:test@test.com/path/123?q=abc#test', (string) $u);
        $this->assertTrue($u->isAbsolute());
    }

    public function testAllowsFalsyUrlParts()
    {
        $url = new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl(puzzle_Url::fromString('http://a:50/0?0#0'));
        $this->assertSame('a', $url->getHost());
        $this->assertEquals(50, $url->getPort());
        $this->assertSame('/0', $url->getPath());
        $this->assertEquals('0', (string) $url->getQuery());
        $this->assertSame('0', $url->getFragment());
        $this->assertEquals('http://a:50/0?0#0', (string) $url);

        $url = new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl(puzzle_Url::fromString(''));
        $this->assertSame('', (string) $url);

        $url = new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl(puzzle_Url::fromString('0'));
        $this->assertSame('0', (string) $url);
    }

    public function testBuildsRelativeUrlsWithFalsyParts()
    {
        $url = puzzle_Url::buildUrl(array('path' => '/0'));
        $this->assertSame('/0', $url);

        $url = puzzle_Url::buildUrl(array('path' => '0'));
        $this->assertSame('0', $url);
    }

    public function testUrlStoresParts()
    {
        $url = new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl(puzzle_Url::fromString('http://test:pass@www.test.com:8081/path/path2/?a=1&b=2#fragment'));
        $this->assertEquals('http', $url->getScheme());
        $this->assertEquals('test', $url->getUsername());
        $this->assertEquals('pass', $url->getPassword());
        $this->assertEquals('www.test.com', $url->getHost());
        $this->assertEquals(8081, $url->getPort());
        $this->assertEquals('/path/path2/', $url->getPath());
        $this->assertEquals('fragment', $url->getFragment());
        $this->assertEquals('a=1&b=2', (string) $url->getQuery());

        $this->assertEquals(array(
            'fragment' => 'fragment',
            'host' => 'www.test.com',
            'pass' => 'pass',
            'path' => '/path/path2/',
            'port' => 8081,
            'query' => 'a=1&b=2',
            'scheme' => 'http',
            'user' => 'test'
        ), $url->getParts());
    }

    public function testHandlesPathsCorrectly()
    {
        $url = new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl(puzzle_Url::fromString('http://www.test.com'));
        $this->assertEquals('', $url->getPath());
        $url->setPath('test');
        $this->assertEquals('test', $url->getPath());

        $url->setPath('/test/123/abc');
        $this->assertEquals(array('', 'test', '123', 'abc'), $url->getPathSegments());

        $parts = parse_url('http://www.test.com/test');
        $parts['path'] = '';
        $this->assertEquals('http://www.test.com', puzzle_Url::buildUrl($parts));
        $parts['path'] = 'test';
        $this->assertEquals('http://www.test.com/test', puzzle_Url::buildUrl($parts));
    }

    public function testAddsQueryIfPresent()
    {
        $this->assertEquals('?foo=bar', puzzle_Url::buildUrl(array(
            'query' => 'foo=bar'
        )));
    }

    public function testAddsToPath()
    {
        // Does nothing here
        $this->assertEquals('http://e.com/base?a=1', (string) new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl(puzzle_Url::fromString('http://e.com/base?a=1')->addPath(false)));
        $this->assertEquals('http://e.com/base?a=1', (string) new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl(puzzle_Url::fromString('http://e.com/base?a=1')->addPath('')));
        $this->assertEquals('http://e.com/base?a=1', (string) new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl(puzzle_Url::fromString('http://e.com/base?a=1')->addPath('/')));
        $this->assertEquals('http://e.com/base/0', (string) new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl(puzzle_Url::fromString('http://e.com/base')->addPath('0')));

        $this->assertEquals('http://e.com/base/relative?a=1', (string) new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl(puzzle_Url::fromString('http://e.com/base?a=1')->addPath('relative')));
        $this->assertEquals('http://e.com/base/relative?a=1', (string) new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl(puzzle_Url::fromString('http://e.com/base?a=1')->addPath('/relative')));
    }

    /**
     * URL combination data provider
     *
     * @return array
     */
    public function urlCombineDataProvider()
    {
        return array(
            // Specific test cases
            array('http://www.example.com/',           'http://www.example.com/', 'http://www.example.com/'),
            array('http://www.example.com/path',       '/absolute', 'http://www.example.com/absolute'),
            array('http://www.example.com/path',       '/absolute?q=2', 'http://www.example.com/absolute?q=2'),
            array('http://www.example.com/',           '?q=1', 'http://www.example.com/?q=1'),
            array('http://www.example.com/path',       'http://test.com', 'http://test.com'),
            array('http://www.example.com:8080/path',  'http://test.com', 'http://test.com'),
            array('http://www.example.com:8080/path',  '?q=2#abc', 'http://www.example.com:8080/path?q=2#abc'),
            array('http://www.example.com/path',       'http://u:a@www.example.com/', 'http://u:a@www.example.com/'),
            array('/path?q=2', 'http://www.test.com/', 'http://www.test.com/path?q=2'),
            array('http://api.flickr.com/services/',   'http://www.flickr.com/services/oauth/access_token', 'http://www.flickr.com/services/oauth/access_token'),
            array('https://www.example.com/path',       '//foo.com/abc', 'https://foo.com/abc'),
            // RFC 3986 test cases
            array(self::RFC3986_BASE, 'g:h',           'g:h'),
            array(self::RFC3986_BASE, 'g',             'http://a/b/c/g'),
            array(self::RFC3986_BASE, './g',           'http://a/b/c/g'),
            array(self::RFC3986_BASE, 'g/',            'http://a/b/c/g/'),
            array(self::RFC3986_BASE, '/g',            'http://a/g'),
            array(self::RFC3986_BASE, '//g',           'http://g'),
            array(self::RFC3986_BASE, '?y',            'http://a/b/c/d;p?y'),
            array(self::RFC3986_BASE, 'g?y',           'http://a/b/c/g?y'),
            array(self::RFC3986_BASE, '#s',            'http://a/b/c/d;p?q#s'),
            array(self::RFC3986_BASE, 'g#s',           'http://a/b/c/g#s'),
            array(self::RFC3986_BASE, 'g?y#s',         'http://a/b/c/g?y#s'),
            array(self::RFC3986_BASE, ';x',            'http://a/b/c/;x'),
            array(self::RFC3986_BASE, 'g;x',           'http://a/b/c/g;x'),
            array(self::RFC3986_BASE, 'g;x?y#s',       'http://a/b/c/g;x?y#s'),
            array(self::RFC3986_BASE, '',              self::RFC3986_BASE),
            array(self::RFC3986_BASE, '.',             'http://a/b/c/'),
            array(self::RFC3986_BASE, './',            'http://a/b/c/'),
            array(self::RFC3986_BASE, '..',            'http://a/b/'),
            array(self::RFC3986_BASE, '../',           'http://a/b/'),
            array(self::RFC3986_BASE, '../g',          'http://a/b/g'),
            array(self::RFC3986_BASE, '../..',         'http://a/'),
            array(self::RFC3986_BASE, '../../',        'http://a/'),
            array(self::RFC3986_BASE, '../../g',       'http://a/g'),
            array(self::RFC3986_BASE, '../../../g',    'http://a/g'),
            array(self::RFC3986_BASE, '../../../../g', 'http://a/g'),
            array(self::RFC3986_BASE, '/./g',          'http://a/g'),
            array(self::RFC3986_BASE, '/../g',         'http://a/g'),
            array(self::RFC3986_BASE, 'g.',            'http://a/b/c/g.'),
            array(self::RFC3986_BASE, '.g',            'http://a/b/c/.g'),
            array(self::RFC3986_BASE, 'g..',           'http://a/b/c/g..'),
            array(self::RFC3986_BASE, '..g',           'http://a/b/c/..g'),
            array(self::RFC3986_BASE, './../g',        'http://a/b/g'),
            array(self::RFC3986_BASE, './g/.',         'http://a/b/c/g/'),
            array(self::RFC3986_BASE, 'g/./h',         'http://a/b/c/g/h'),
            array(self::RFC3986_BASE, 'g/../h',        'http://a/b/c/h'),
            array(self::RFC3986_BASE, 'g;x=1/./y',     'http://a/b/c/g;x=1/y'),
            array(self::RFC3986_BASE, 'g;x=1/../y',    'http://a/b/c/y'),
            array(self::RFC3986_BASE, 'http:g',        'http:g'),
        );
    }

    /**
     * @dataProvider urlCombineDataProvider
     */
    public function testCombinesUrls($a, $b, $c)
    {
        $this->assertEquals($c, (string) new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl(puzzle_Url::fromString($a)->combine($b)));
    }

    public function testHasGettersAndSetters()
    {
        $url = new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl(puzzle_Url::fromString('http://www.test.com/'));
        $this->assertEquals('example.com', $url->setHost('example.com')->getHost());
        $this->assertEquals('8080', $url->setPort(8080)->getPort());
        $this->assertEquals('/foo/bar', $url->setPath('/foo/bar')->getPath());
        $this->assertEquals('a', $url->setPassword('a')->getPassword());
        $this->assertEquals('b', $url->setUsername('b')->getUsername());
        $this->assertEquals('abc', $url->setFragment('abc')->getFragment());
        $this->assertEquals('https', $url->setScheme('https')->getScheme());
        $this->assertEquals('a=123', (string) $url->setQuery('a=123')->getQuery());
        $this->assertEquals('https://b:a@example.com:8080/foo/bar?a=123#abc', (string) $url);
        $this->assertEquals('b=boo', (string) $url->setQuery(new tubepress_platform_impl_url_puzzle_PuzzleBasedQuery(new puzzle_Query(array(
            'b' => 'boo'
        ))))->getQuery());
        $this->assertEquals('https://b:a@example.com:8080/foo/bar?b=boo#abc', (string) $url);
    }

    public function testSetQueryAcceptsArray()
    {
        $url = new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl(puzzle_Url::fromString('http://www.test.com'));
        $url->setQuery(new tubepress_platform_impl_url_puzzle_PuzzleBasedQuery(new puzzle_Query(array('a' => 'b'))));
        $this->assertEquals('http://www.test.com?a=b', (string) $url);
    }

    public function removeDotSegmentsProvider()
    {
        return array(
            array('/foo/..', '/'),
            array('//foo//..', '//foo/'),
            array('/foo//', '/foo//'),
            array('/foo/../..', '/'),
            array('/foo/../.', '/'),
            array('/./foo/..', '/'),
            array('/./foo', '/foo'),
            array('/./foo/', '/foo/'),
            array('*', '*'),
            array('/foo', '/foo'),
            array('/abc/123/../foo/', '/abc/foo/'),
            array('/a/b/c/./../../g', '/a/g'),
            array('/b/c/./../../g', '/g'),
            array('/b/c/./../../g', '/g'),
            array('/c/./../../g', '/g'),
            array('/./../../g', '/g'),
            array('foo', 'foo'),
        );
    }

    /**
     * @dataProvider removeDotSegmentsProvider
     */
    public function testRemoveDotSegments($path, $result)
    {
        $url = new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl(puzzle_Url::fromString('http://www.example.com'));
        $url->setPath($path)->removeDotSegments();
        $this->assertEquals($result, $url->getPath());
    }

    public function testSettingHostWithPortModifiesPort()
    {
        $url = new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl(puzzle_Url::fromString('http://www.example.com'));
        $url->setHost('foo:8983');
        $this->assertEquals('foo', $url->getHost());
        $this->assertEquals(8983, $url->getPort());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testValidatesUrlCanBeParsed()
    {
        new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl(puzzle_Url::fromString('foo:////'));
    }

    public function testConvertsSpecialCharsInPathWhenCastingToString()
    {
        $url = new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl(puzzle_Url::fromString('http://foo.com/baz bar?a=b'));
        $url->addPath('?');
        $this->assertEquals('http://foo.com/baz%20bar/%3F?a=b', (string) $url);
    }

    public function testAuthority()
    {
        $url = new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl(puzzle_Url::fromString('http://eric:pass@foo.com:344/baz/bar?a=b'));

        $this->assertEquals('eric:pass@foo.com:344', $url->getAuthority());
    }
}