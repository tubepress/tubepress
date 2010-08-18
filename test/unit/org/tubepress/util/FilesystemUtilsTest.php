<?php

require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/util/FilesystemUtils.class.php';
require_once dirname(__FILE__) . '/../../../../../test/unit/TubePressUnitTest.php';

class org_tubepress_util_FilesystemUtilsTest extends TubePressUnitTest
{
	function testLsDirs()
	{
	    $dir = realpath(dirname(__FILE__) . '/../../../../../ui');
	    $expected = array("$dir/themes", "$dir/lib");
            
		$result = org_tubepress_util_FilesystemUtils::getDirectoriesInDirectory($dir, 'log prefix');
		$difference = array_diff($expected, $result);
		$this->assertTrue(empty($difference));
	}

	function testLsFiles()
	{
	    $dir = realpath(dirname(__FILE__) . '/../../../../../i18n');
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
            
		$result = org_tubepress_util_FilesystemUtils::getFilenamesInDirectory($dir, 'log prefix');
		$difference = array_diff($expected, $result);
		$this->assertTrue(empty($difference));
	}
	
	function testGetBaseInstallationPath()
	{
		$result = org_tubepress_util_FilesystemUtils::getTubePressBaseInstallationPath();
		$dirname = basename($result);
		$this->assertEquals('tubepress', $dirname);
	}

	/**
	 * @expected Exception
	 */
	function testLsDirsNoSuchDir()
	{
	    org_tubepress_util_FilesystemUtils::getDirectoriesInDirectory('fake dir', 'log prefix');
	}

	/**
	 * @expected Exception
	 */
	function testLsFilesNoSuchDir()
	{
	    org_tubepress_util_FilesystemUtils::getFilenamesInDirectory('fake dir', 'log prefix');
	}
}
?>
