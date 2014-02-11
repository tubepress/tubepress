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
 * @covers tubepress_impl_boot_DefaultBootConfigService<extended>
 */
class tubepress_test_impl_boot_DefaultBootConfigServiceTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_boot_DefaultBootConfigService
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    /**
     * @var string
     */
    private $_userContentDirectory;

    public function onSetup()
    {
        $this->_sut                     = new tubepress_impl_boot_DefaultBootConfigService();
        $this->_mockEnvironmentDetector = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);

        $this->_sut->__setEnvironmentDetector($this->_mockEnvironmentDetector);

        $this->_userContentDirectory = sys_get_temp_dir() . '/default-boot-config-service-test/';

        if (is_dir($this->_userContentDirectory)) {

            $this->_rrmdir($this->_userContentDirectory);
        }

        mkdir($this->_userContentDirectory . '/config', 0777, true);
    }

    public function onTearDown()
    {
        unset($_GET['hello']);
        $this->_rrmdir($this->_userContentDirectory);
    }

    public function testFallbackAddonsBlacklist()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->once()->andReturn(
            TUBEPRESS_ROOT . '/no-such-dir'
        );

        $result = $this->_sut->getAddonBlacklistArray();

        $this->assertTrue(is_array($result));
        $this->assertTrue(empty($result));

        $result = $this->_sut->getAddonBlacklistArray();

        $this->assertTrue(is_array($result));
        $this->assertTrue(empty($result));
    }

    public function testCustomAddonsBlacklist()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->once()->andReturn(
            $this->_userContentDirectory
        );

        $this->_writeBootConfig(<<<EOF
<?php
return array(

    'cache' => array(

        'instance'  => new ehough_stash_Pool(new ehough_stash_driver_Ephemeral()),
        'killerKey' => 'hello',
    ),
    'add-ons' => array(

        'blacklist' => array('some', 'thing', 'else'),
    ),
    'classloader' => array(

        'enabled' => true,
    )
);
EOF
);

        $result = $this->_sut->getAddonBlacklistArray();

        $this->assertTrue(is_array($result));
        $this->assertEquals(array('some', 'thing', 'else'), $result);
    }

    public function testNonPhpBootFile()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->once()->andReturn(
            $this->_userContentDirectory
        );

        $this->_writeBootConfig(<<<EOF
this should be php
EOF
        );

        $result = $this->_sut->getAddonBlacklistArray();

        $this->assertTrue(is_array($result));
        $this->assertTrue(empty($result));
    }

    public function testClearCache()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->once()->andReturn(
            $this->_userContentDirectory
        );

        $this->_writeBootConfig(<<<EOF
<?php
return array(

    'cache' => array(

        'instance'  => new ehough_stash_Pool(new ehough_stash_driver_Ephemeral()),
        'killerKey' => 'hello',
    ),
    'add-ons' => array(

        'blacklist' => array('some', 'thing', 'else'),
    ),
    'classloader' => array(

        'enabled' => true,
    )
);
EOF
        );

        $_GET['hello'] = 'true';

        $result = $this->_sut->getAddonBlacklistArray();

        $this->assertTrue(is_array($result));
        $this->assertEquals(array('some', 'thing', 'else'), $result);
    }

    public function testNonReturningPhpBootFile()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->once()->andReturn(
            $this->_userContentDirectory
        );

        $this->_writeBootConfig(<<<EOF
<?php
\$x = 'x';
EOF
        );

        $result = $this->_sut->getAddonBlacklistArray();

        $this->assertTrue(is_array($result));
        $this->assertTrue(empty($result));
    }

    public function testMissingCacheConfig()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->once()->andReturn(
            $this->_userContentDirectory
        );

        $this->_writeBootConfig(<<<EOF
<?php
return array(

    'add-ons' => array(

        'blacklist' => array('some', 'thing', 'else'),
    ),
    'classloader' => array(

        'enabled' => true,
    )
);
EOF
        );

        $result = $this->_sut->getAddonBlacklistArray();

        $this->assertTrue(is_array($result));
        $this->assertTrue(empty($result));
    }

    public function testMissingBlacklist()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->once()->andReturn(
            $this->_userContentDirectory
        );

        $this->_writeBootConfig(<<<EOF
<?php
return array(

    'cache' => array(

        'instance'  => new ehough_stash_Pool(new ehough_stash_driver_Ephemeral()),
        'killerKey' => 'hello',
    ),
    'add-ons' => array(),
    'classloader' => array(

        'enabled' => true,
    )
);
EOF
        );

        $result = $this->_sut->getAddonBlacklistArray();

        $this->assertTrue(is_array($result));
        $this->assertTrue(empty($result));
    }

    public function testNonArrayCacheConfig()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->once()->andReturn(
            $this->_userContentDirectory
        );

        $this->_writeBootConfig(<<<EOF
<?php
return array(

    'cache' => 'hi',
    'add-ons' => array(

        'blacklist' => array('some', 'thing', 'else'),
    ),
    'classloader' => array(

        'enabled' => true,
    )
);
EOF
        );

        $result = $this->_sut->getAddonBlacklistArray();

        $this->assertTrue(is_array($result));
        $this->assertTrue(empty($result));
    }

    public function testNonStringKillerKey()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->once()->andReturn(
            $this->_userContentDirectory
        );

        $this->_writeBootConfig(<<<EOF
<?php
return array(

    'cache' => array(

        'instance'  => new ehough_stash_Pool(new ehough_stash_driver_Ephemeral()),
        'killerKey' => array(),
    ),
    'add-ons' => array(

        'blacklist' => array(),
    ),
    'classloader' => array(

        'enabled' => true,
    )
);
EOF
        );

        $result = $this->_sut->getAddonBlacklistArray();

        $this->assertTrue(is_array($result));
        $this->assertEquals(array(), $result);
    }

    public function testBadCache()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->once()->andReturn(
            $this->_userContentDirectory
        );

        $this->_writeBootConfig(<<<EOF
<?php
return array(

    'cache' => array(

        'instance'  => 3,
        'killerKey' => 'hello',
    ),
    'add-ons' => array(

        'blacklist' => array(),
    ),
    'classloader' => array(

        'enabled' => true,
    )
);
EOF
        );

        $result = $this->_sut->getAddonBlacklistArray();

        $this->assertTrue(is_array($result));
        $this->assertEquals(array(), $result);
    }

    public function testNonArrayBlacklist()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->once()->andReturn(
            $this->_userContentDirectory
        );

        $this->_writeBootConfig(<<<EOF
<?php
return array(

    'cache' => array(

        'instance'  => new ehough_stash_Pool(new ehough_stash_driver_Ephemeral()),
        'killerKey' => 'hello',
    ),
    'add-ons' => array(

        'blacklist' => 3,
    ),
    'classloader' => array(

        'enabled' => true,
    )
);
EOF
        );

        $result = $this->_sut->getAddonBlacklistArray();

        $this->assertTrue(is_array($result));
        $this->assertEquals(array(), $result);
    }

    public function testNonBooleanClassLoaderEnablement()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->once()->andReturn(
            $this->_userContentDirectory
        );

        $this->_writeBootConfig(<<<EOF
<?php
return array(

    'cache' => array(

        'instance'  => new ehough_stash_Pool(new ehough_stash_driver_Ephemeral()),
        'killerKey' => 'hello',
    ),
    'add-ons' => array(

        'blacklist' => array(),
    ),
    'classloader' => array(

        'enabled' => 'hello',
    )
);
EOF
        );

        $result = $this->_sut->getAddonBlacklistArray();

        $this->assertTrue(is_array($result));
        $this->assertEquals(array(), $result);
    }

    public function testFallbackConfig()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->once()->andReturn(

            TUBEPRESS_ROOT . '/src/main/resources/user-content-skeleton/tubepress-content'
        );

        $result = $this->_sut->getAddonBlacklistArray();

        $this->assertTrue(is_array($result));
        $this->assertEquals(array(), $result);

        $result = $this->_sut->isClassLoaderEnabled();
        $this->assertTrue($result);

        $result = $this->_sut->getBootCache();
        $this->assertInstanceOf('ehough_stash_interfaces_PoolInterface', $result);
    }

    private function _writeBootConfig($contents)
    {
        file_put_contents($this->_userContentDirectory . 'config/boot.php', $contents);
    }

    private function _rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") $this->_rrmdir($dir."/".$object); else unlink($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }
}