<?php

require_once BASE . '/sys/classes/org/tubepress/impl/filesystem/FsExplorer.class.php';

class org_tubepress_impl_filesystem_FsExplorerTest extends TubePressUnitTest
{
    private $_sut;

    function setUp()
    {
        parent::setUp();
        $this->tearDown();
        $this->_sut = new org_tubepress_impl_filesystem_FsExplorer();
        @mkdir('/tmp/tubepress-fs-test');
    }
    
    function tearDown()
    {
        exec('rm -rf /tmp/tubepress-fs-test');
    }

    function testCopyDir2Dir()
    {org_tubepress_impl_log_Log::setEnabled(true, array('tubepress_debug' => true));
        $source = '/tmp/tubepress-fs-test/source/one/two';
        mkdir($source, 0755, true);
        
        $this->assertTrue(is_dir('/tmp/tubepress-fs-test/source/one/two'));
        
        file_put_contents("$source/one.txt", mt_rand());
    
        mkdir('/tmp/tubepress-fs-test/dest/');
        $this->assertTrue(is_dir('/tmp/tubepress-fs-test/dest'));
        
        $this->_sut->copyDirectory('/tmp/tubepress-fs-test/source', '/tmp/tubepress-fs-test/dest/');

        $this->assertTrue(md5(file_get_contents('/tmp/tubepress-fs-test/source/one/two/one.txt')) === md5(file_get_contents('/tmp/tubepress-fs-test/dest/source/one/two/one.txt')));
    }
    
	function testLsDirs()
	{
	    $dir = realpath(BASE . '/sys/ui');
	    $expected = array("$dir/themes", "$dir/static", "$dir/templates");

		$result = $this->_sut->getDirectoriesInDirectory($dir, 'log prefix');
		self::assertArrayEquality($expected, $result);
	}

	function testLsFiles()
	{
	    $dir = realpath(BASE . '/sys/i18n');
	    $expected = array(
	        "$dir/tubepress-ar_SA.mo",
	        "$dir/tubepress-ar_SA.po",
            "$dir/tubepress-de_DE.mo",
            "$dir/tubepress-de_DE.po",
            "$dir/tubepress-es_ES.mo",
            "$dir/tubepress-es_ES.po",
            "$dir/tubepress-fr_FR.mo",
            "$dir/tubepress-fr_FR.po",
            "$dir/tubepress-he_IL.mo",
            "$dir/tubepress-he_IL.po",
            "$dir/tubepress-it_IT.mo",
            "$dir/tubepress-it_IT.po",
            "$dir/tubepress-pt_BR.mo",
            "$dir/tubepress-pt_BR.po",
            "$dir/tubepress-ru_RU.mo",
            "$dir/tubepress-ru_RU.po",
            "$dir/tubepress-sv_SE.mo",
            "$dir/tubepress-sv_SE.po",
            "$dir/tubepress.mo",
            "$dir/tubepress.pot"
	    );

		$result = $this->_sut->getFilenamesInDirectory($dir, 'log prefix');
		$difference = array_diff($expected, $result);
		$this->assertTrue(empty($difference));
	}

	function testGetBaseInstallationPath()
	{
		$result = $this->_sut->getTubePressBaseInstallationPath();
		$dirname = basename($result);
		$this->assertEquals('tubepress', $dirname);
	}
	
	function testGetBaseInstallationBasename()
	{
	    $result = $this->_sut->getTubePressInstallationDirectoryBaseName();
	    $this->assertEquals('tubepress', $result);
	}

	/**
	 * @expected Exception
	 */
	function testLsDirsNoSuchDir()
	{
	    $this->_sut->getDirectoriesInDirectory('fake dir', 'log prefix');
	}

	/**
	 * @expected Exception
	 */
	function testLsFilesNoSuchDir()
	{
	    $this->_sut->getFilenamesInDirectory('fake dir', 'log prefix');
	}
}
