<?php
require_once BASE . '/sys/classes/org/tubepress/impl/message/WordPressMessageService.class.php';

class org_tubepress_message_WordPressMessageServiceTest extends TubePressUnitTest {

	private $_sut;

	private static $_poFiles;

	private static $_allTranslatableStrings;

	public static function setUpBeforeClass()
	{
	    self::$_poFiles = self::_getPoFiles();
	    self::$_allTranslatableStrings = self::_getAllTranslatableStrings();
	}

	function setUp()
	{
		$this->_sut = new org_tubepress_impl_message_WordPressMessageService();

		$__ = new PHPUnit_Extensions_MockFunction('__');
        $__->expects($this->any())->will($this->returnCallback(array($this, 'echoCallback')));
	}

	function testAllStringsPresent()
	{
        foreach (self::$_poFiles as $poFile) {

            $stringsInPoFile = self::_getStringsFromPoFile($poFile);

            $missingFromPoFile = array_diff(self::$_allTranslatableStrings, $stringsInPoFile);
            $extraInPoFile     = array_diff($stringsInPoFile, self::$_allTranslatableStrings);

            $ok = empty($missingFromPoFile) && empty($extraInPoFile);

            if (!$ok) {

                echo "\n\nThe following items are missing from $poFile\n\n";
                print_r($missingFromPoFile);

                echo "\n\nThe following items should be removed from $poFile\n\n";
                print_r($extraInPoFile);

                exit;
            }

            $this->assertTrue($ok);
        }
	}

	function testPoCompiles()
	{
		$testOpts = parse_ini_file(dirname(__FILE__) . '/../../../config/test.config');

		foreach (self::$_poFiles as $poFile) {

		    $this->assertTrue(self::_poFileCompiles($poFile, $testOpts['msgfmt_path']), "$poFile does not compile correctly");
		}
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

   	function echoCallback($key)
   	{
   	    return "[[$key]]";
   	}

   	private static function _getAllTranslatableStrings()
   	{
   	    $command = 'grep -r ">(translatable)<" ' . BASE . '/sys';
   	    exec($command, $results, $return);

   	    self::assertTrue($return === 0, "$command failed");
   	    self::assertTrue(count($results) > 0, 'grep didn\'t find any strings to translate');

   	    $strings = array();
   	    foreach ($results as $grepLine) {

   	        $result = preg_match_all("/^[^']*'(.+)'[^']*$/", $grepLine, $matches);

   	        if (!$result || count($matches) !== 2) {

   	            echo 'Found more than on match on ' . $grepLine . '. ' . var_export($matches, true);
   	            exit;
   	        }

   	        $strings[] = str_replace("\'", "'", $matches[1][0]);
   	    }

        return $strings;
   	}

   	private static function _poFileCompiles($file, $exec)
   	{
   	    $realPath = BASE . '/sys/i18n/' . $file;

   	    $outputfile = str_replace(array('.pot', '.po'), '.mo', $realPath);

   	    exec("msgfmt -o $outputfile $realPath", $results, $return);
   	    return $return === 0;
   	}

   	private static function _getPoFiles()
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

   	private static function _getStringsFromPoFile($file)
   	{
   	    $rawMatches = array();

   	    $potContents = file_get_contents(BASE . '/sys/i18n/' . $file);

   	    preg_match_all("/msgid\b.*/", $potContents, $rawMatches, PREG_SET_ORDER);

   	    $matches = array();

   	    foreach ($rawMatches as $rawMatch) {

   	        $r = $rawMatch[0];
   	        $r = str_replace("msgid \"", "", $r);
   	        $r = substr($r, 0, self::_rstrpos($r, "\""));
   	        if ($r == '') {
   	            continue;
   	        }
   	        $r = str_replace("\\\"", "\"", $r);
   	        $matches[] = $r;
   	    }

   	    return $matches;
   	}

   	private static function _rstrpos ($haystack, $needle){
   	    $index        = strpos(strrev($haystack), strrev($needle));
   	    $index        = strlen($haystack) - strlen($index) - $index;
   	    return $index;
   	}
}
