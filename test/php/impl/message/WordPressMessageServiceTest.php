<?php
require_once BASE . '/sys/classes/org/tubepress/impl/message/WordPressMessageService.class.php';

class org_tubepress_message_WordPressMessageServiceTest extends TubePressUnitTest {

	private $_sut;

	function setUp()
	{
		$this->_sut = new org_tubepress_impl_message_WordPressMessageService();

		$__ = new PHPUnit_Extensions_MockFunction('__');
        $__->expects($this->any())->will($this->returnCallback(array($this, 'echoCallback')));
	}

	function testPoCompiles()
	{
		$testOpts = parse_ini_file(dirname(__FILE__) . '/../../../test.config');
		$files = $this->getPoFiles();
		foreach ($files as $file) {
			$realPath = BASE . '/sys/i18n/' . $file;
			$outputfile = str_replace(array('.pot', '.po'), '.mo', $realPath);
			exec($testOpts['msgfmt_path'] . " -o $outputfile $realPath", $results, $return);
			$this->assertTrue($return === 0);
		}
		dirname(__FILE__) . '/../../../../i18n/tubepress.mo';
	}

// 	function testPotFileHasRightEntries()
// 	{
// 		$files = $this->getPoFiles();
// 		foreach ($files as $file) {
// 			$this->performSyncCheck($file);
// 	    }
// 	}

	function performSyncCheck($file)
	{
		$rawMatches = array();
		$potContents = file_get_contents(BASE . '/sys/i18n/' . $file);
		preg_match_all("/msgid\b.*/", $potContents, $rawMatches, PREG_SET_ORDER);
		$matches = array();
		foreach ($rawMatches as $rawMatch) {
			$r = $rawMatch[0];
			$r = str_replace("msgid \"", "", $r);
			$r = substr($r, 0, $this->rstrpos($r, "\""));
			if ($r == '') {
				continue;
			}
			$r = str_replace("\\\"", "\"", $r);
			$matches[] = $r;
		}
		$vals = array_values($msgs);
		$diff1 = array_diff($vals, $matches);
		$diff2 = array_diff($matches, $vals);
		$ok = empty($diff1) && empty($diff2);
		if (!$ok) {
			echo "\n\nThe following items are missing from $file\n\n";
			print_r(array_diff($vals, $matches));

			echo "\n\nThe following items should be removed from $file\n\n";
			print_r(array_diff($matches, $vals));
		}
		$this->assertTrue($ok);
	}

	function getPoFiles()
	{
		$files = array();
		$handle = opendir(BASE . '/sys/i18n/');
	    while (false !== ($file = readdir($handle))) {
	        if ($file == "." || $file == "..") {
				continue;
	    	}
	    	if (1 == preg_match('/.*\.po.*/', $file)) {
				$files[] = $file;
	    	}
		}
		closedir($handle);
		return $files;
	}

	function testGetKeyNoExists()
	{
        $this->assertEquals('', $this->_sut->_(''));
        $this->assertEquals('', $this->_sut->_(null));
	}

	function testGetKey()
	{
	    $result = $this->_sut->_('foo') === "[[foo]]";
	     
	    if (!$result) {
	         
	        print "$key did not resolve to $value";
	    }
	     
	    $this->assertTrue($result);
	}

	function rstrpos ($haystack, $needle){
    	$index        = strpos(strrev($haystack), strrev($needle));
        $index        = strlen($haystack) - strlen($index) - $index;
        return $index;
   	}

   	function echoCallback($key)
   	{
   	    return "[[$key]]";
   	}
}