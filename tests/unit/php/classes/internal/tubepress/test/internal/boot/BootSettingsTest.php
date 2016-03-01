<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 * @covers tubepress_internal_boot_BootSettings<extended>
 */
class tubepress_test_internal_boot_BootSettingsTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_internal_boot_BootSettings
     */
    private $_sut;

    /**
     * @var string
     */
    private $_userContentDirectory;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockLogger;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockUrlFactory;

    public function onSetup()
    {
        $this->_mockLogger     = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockUrlFactory = $this->mock(tubepress_api_url_UrlFactoryInterface::_);

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_mockUrlFactory->shouldReceive('fromString')->andReturnUsing(array($this, '__callbackRealUrlFactory'));

        $this->_sut = new tubepress_internal_boot_BootSettings($this->_mockLogger, $this->_mockUrlFactory);

        $this->_userContentDirectory = sys_get_temp_dir() . '/tubepress-boot-settings-test/';

        if (is_dir($this->_userContentDirectory)) {

            $this->recursivelyDeleteDirectory($this->_userContentDirectory);
        }

        mkdir($this->_userContentDirectory . '/config', 0777, true);
    }

    public function onTearDown()
    {
        $this->recursivelyDeleteDirectory($this->_userContentDirectory);
    }

    public function __callbackRealUrlFactory($incoming)
    {
        $realFactory = new tubepress_url_impl_puzzle_UrlFactory();

        return $realFactory->fromString($incoming);
    }

    /**
     * @dataProvider getDataSerializationEncoding
     */
    public function testGetSerializationEncoding($expected, $entry)
    {
        define('TUBEPRESS_CONTENT_DIRECTORY', $this->_userContentDirectory);

        $this->_writeBootConfig(<<<EOF
<?php
return array(
    'system' => array(
        'cache' => array(

            'serializationEncoding'  => '$entry'
        )
    )
);
EOF
        );

        $actual = $this->_sut->getSerializationEncoding();

        $this->assertEquals($expected, $actual);
    }

    public function getDataSerializationEncoding()
    {
        return array(

            array('base64', 'base64'),
            array('base64', ''),
            array('base64', 'foobar'),
            array('none', 'none'),
            array('urlencode', 'urlencode'),
        );
    }

    public function testShouldClearSystemCache()
    {
        $this->assertFalse($this->_sut->shouldClearCache());

        $_GET['tubepress_clear_system_cache'] = 'true';

        $this->assertTrue($this->_sut->shouldClearCache());
    }

    public function testShouldClearSystemCacheCustomKey()
    {
        define('TUBEPRESS_CONTENT_DIRECTORY', $this->_userContentDirectory);

        $this->_writeBootConfig(<<<EOF
<?php
return array(
    'system' => array(
        'cache' => array(

            'killerKey'  => 'hello'
        )
    )
);
EOF
        );

        $this->assertFalse($this->_sut->shouldClearCache());

        $_GET['hello'] = true;

        $this->assertFalse($this->_sut->shouldClearCache());

        $_GET['hello'] = 'true';

        $this->assertTrue($this->_sut->shouldClearCache());
    }

    public function testAddonBlacklist()
    {
        define('TUBEPRESS_CONTENT_DIRECTORY', $this->_userContentDirectory);

        $this->_writeBootConfig(<<<EOF
<?php
return array(
    'system' => array(
        'add-ons' => array(

            'blacklist' => array('some', 'thing', 'else'),
        )
    )
);
EOF
        );

        $actual = $this->_sut->getAddonBlacklistArray();

        $this->assertTrue(is_array($actual));
        $this->assertEquals(array('some', 'thing', 'else'), $actual);
    }

    /**
     * @dataProvider getDataClassloaderEnabled
     */
    public function testIsClassloaderEnabled($entry)
    {
        $asString = $entry ? 'true' : 'false';

        define('TUBEPRESS_CONTENT_DIRECTORY', $this->_userContentDirectory);

        $this->_writeBootConfig(<<<EOF
<?php
return array(
    'system' => array(
        'classloader' => array(

            'enabled'  => $asString,
        )
    )
);
EOF
        );

        $actual = $this->_sut->isClassLoaderEnabled();

        $this->assertEquals($entry, $actual);
    }

    public function getDataClassloaderEnabled()
    {
        return array(

            array(true),
            array(false),
        );
    }

    /**
     * @dataProvider getDataSystemCacheEnabled
     */
    public function testIsSystemCacheEnabled($entry)
    {
        $asString = $entry ? 'true' : 'false';

        define('TUBEPRESS_CONTENT_DIRECTORY', $this->_userContentDirectory);

        $this->_writeBootConfig(<<<EOF
<?php
return array(
    'system' => array(
        'cache' => array(

            'enabled'  => $asString,
        )
    )
);
EOF
        );

        $actual = $this->_sut->isSystemCacheEnabled();

        $this->assertEquals($entry, $actual);
    }

    public function getDataSystemCacheEnabled()
    {
        return array(

            array(true),
            array(false),
        );
    }

    public function testGetPathToSystemCacheDirectoryExisting()
    {
        define('TUBEPRESS_CONTENT_DIRECTORY', $this->_userContentDirectory);

        $path = $this->_userContentDirectory . 'foo';
        mkdir($path . '/tubepress-99.99.99', 0755, true);

        if (!is_dir($path) || !is_writable($path)) {

            $this->fail('Could not create test dir.');
            return;
        }

        $this->_writeBootConfig(<<<EOF
<?php
return array(
    'system' => array(
        'cache' => array(

            'directory'  => '$path'
        )
    )
);
EOF
        );

        $actual = $this->_sut->getPathToSystemCacheDirectory();

        $this->assertEquals($path . '/tubepress-99.99.99', $actual);
    }

    public function testGetPathToSystemCacheDirectoryNonExisting()
    {
        define('TUBEPRESS_CONTENT_DIRECTORY', $this->_userContentDirectory);

        $path = $this->_userContentDirectory . 'foo';

        $this->_writeBootConfig(<<<EOF
<?php
return array(
    'system' => array(
        'cache' => array(

            'directory'  => '$path'
        )
    )
);
EOF
        );

        $actual = $this->_sut->getPathToSystemCacheDirectory();

        $this->assertEquals($path . '/tubepress-99.99.99', $actual);
    }

    public function testContainerStoragePathNonWritableDirectory()
    {

        $this->_writeBootConfig(<<<EOF
<?php
return array(
    'system' => array(
        'cache' => array(

            'directory'  => '/sdfkklsjdflkslkjsklfjskljsflksjdfklsjklsjfksldfjsdf'
        )
    )
);
EOF
        );

        $actual = $this->_sut->getPathToSystemCacheDirectory();

        $this->assertRegExp('~/[^/]+/tubepress-system-cache-[a-f0-9]+~', $actual);
    }

    /**
     * @dataProvider getDataUserContentDirectory
     */
    public function testGetUserContentDirectory(array $defines, $expected)
    {
        foreach ($defines as $key => $value) {

            define($key, $value);
        }

        $actual = $this->_sut->getUserContentDirectory();

        $this->assertEquals($expected, $actual);
    }

    public function getDataUserContentDirectory()
    {
        return array(

            array(array('ABSPATH' => 'blue/', 'DB_NAME' => 'database_name'), 'blue/wp-content/tubepress-content'),
            array(array('ABSPATH' => 'blue/', 'DB_NAME' => 'database_name', 'WP_CONTENT_DIR' => 'bob'), 'bob/tubepress-content'),
            array(array('TUBEPRESS_CONTENT_DIRECTORY' => 'foobar'), 'foobar'),
        );
    }

    /**
     * @dataProvider getDataUrls
     */
    public function testUrls($key, $candidate, $getter, $expected)
    {
        define('TUBEPRESS_CONTENT_DIRECTORY', $this->_userContentDirectory);

        $this->_writeBootConfig(<<<EOF
<?php
return array(
    'user' => array(
        'urls' => array(

            '$key'  => '$candidate'
        )
    )
);
EOF
        );

        $actual = $this->_sut->$getter();

        $this->assertEquals($expected, $actual);
    }

    public function getDataUrls()
    {
        return array(

            array('base',        'http://foo.com', 'getUrlBase',         'http://foo.com'),
            array('base',        '/foo',           'getUrlBase',         '/foo'),
            array('ajax',        'http://foo.com', 'getUrlAjaxEndpoint', 'http://foo.com'),
            array('ajax',        '/foo',           'getUrlAjaxEndpoint', '/foo'),
            array('userContent', 'http://foo.com', 'getUrlUserContent',  'http://foo.com'),
            array('userContent', '/foo',           'getUrlUserContent',  '/foo'),
        );
    }

    public function testBadBaseUrl()
    {
        define('TUBEPRESS_CONTENT_DIRECTORY', $this->_userContentDirectory);

        $this->_mockLogger->shouldReceive('error')->once()->with('Unable to parse base URL from settings.php');

        $this->_writeBootConfig(<<<EOF
<?php
return array(
    'user' => array(
        'urls' => array(
            'base'  => new stdClass()
        )
    )
);
EOF
        );

        $this->assertEquals(null, $this->_sut->getUrlBase());
    }

    public function testInvalidPhpInSettingsPhp()
    {
        define('TUBEPRESS_CONTENT_DIRECTORY', $this->_userContentDirectory);

        $this->_writeBootConfig(<<<EOF
this should be php
EOF
        );

        $this->_verifyAllDefaults();
    }

    public function testDefaultsNoSettingsPhp()
    {
        define('TUBEPRESS_CONTENT_DIRECTORY', $this->_userContentDirectory);

        $this->_verifyAllDefaults();
    }

    /**
     * @dataProvider getBootConfigsThatShouldResultInDefaults
     */
    public function testDefaults($filename)
    {
        define('TUBEPRESS_CONTENT_DIRECTORY', $this->_userContentDirectory);

        $bootConfigAsString = file_get_contents(TUBEPRESS_ROOT . '/tests/unit/php/classes/internal/fixtures/boot-settings-files/' . $filename . '.php');

        $this->_writeBootConfig($bootConfigAsString);

        $this->_verifyAllDefaults();
    }

    public function getBootConfigsThatShouldResultInDefaults()
    {
        return array(
            array('missing-blacklist'),
            array('missing-cache-config'),
            array('non-array-blacklist'),
            array('non-array-cache-config'),
            array('non-boolean-classloader-enablement'),
            array('non-returning'),
            array('non-string-cache-killer'),
        );
    }

    private function _writeBootConfig($contents)
    {
        file_put_contents($this->_userContentDirectory . 'config/settings.php', $contents);
    }

    private function _verifyAllDefaults()
    {
        $actual = $this->_sut->getAddonBlacklistArray();

        $this->assertTrue(is_array($actual));
        $this->assertTrue(empty($actual), 'Add-on blacklist is not empty as it should be.');

        $actual = $this->_sut->getAddonBlacklistArray();

        $this->assertTrue(is_array($actual));
        $this->assertTrue(empty($actual));

        $actual = $this->_sut->isClassLoaderEnabled();
        $this->assertTrue($actual);

        $actual = $this->_sut->getPathToSystemCacheDirectory();
        $this->assertTrue(is_writable($actual));
        $this->assertTrue(is_dir($actual));

        $actual = $this->_sut->isSystemCacheEnabled();
        $this->assertTrue($actual);

        $this->assertNull($this->_sut->getUrlAjaxEndpoint());
        $this->assertNull($this->_sut->getUrlBase());
        $this->assertNull($this->_sut->getUrlUserContent());

        $this->recursivelyDeleteDirectory($this->_sut->getPathToSystemCacheDirectory());
    }
}