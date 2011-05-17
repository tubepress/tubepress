<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/filesystem/FsExplorer.class.php';

class org_tubepress_impl_filesystem_FsExplorerTest extends TubePressUnitTest
{
    private $_sut;
    
    function setUp()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_filesystem_FsExplorer();
        org_tubepress_impl_log_Log::setEnabled(false, array());
    }
    
	function testLsDirs()
	{
	    $dir = realpath(dirname(__FILE__) . '/../../../../../../sys/ui');
	    $expected = array("$dir/themes", "$dir/static", "$dir/templates");
            
		$result = $this->_sut->getDirectoriesInDirectory($dir, 'log prefix');
		TubePressArrayTestUtils::checkArrayEquality($expected, $result);
	}

	function testLsFiles()
	{
	    $dir = realpath(dirname(__FILE__) . '/../../../../../../sys/i18n');
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
