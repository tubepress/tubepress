<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

class tubepress_impl_wordpress_WordPressMessageServiceTest extends TubePressUnitTest
{
	private $_sut;

    private static $_i18nDirectoryPath;

	private static $_poFiles;

	private static $_allTranslatableStrings;

	public static function setUpBeforeClass()
	{
        self::$_i18nDirectoryPath = realpath(dirname(__FILE__) . '/../../../../../../main/resources/i18n');

	    self::$_poFiles = self::_getPoFiles();

	    self::$_allTranslatableStrings = self::_getAllTranslatableStrings();
	}

	function setUp()
	{
        $wrapper = \Mockery::mock(tubepress_spi_wordpress_WordPressFunctionWrapper::_);
        $wrapper->shouldReceive('__')->andReturnUsing(function ($key) {

            return "[[$key]]";
        });

        tubepress_impl_wordpress_WordPressServiceLocator::setWordPressFunctionWrapper($wrapper);

		$this->_sut = new tubepress_impl_wordpress_WordPressMessageService($wrapper);
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
		foreach (self::$_poFiles as $poFile) {

		    $this->assertTrue(self::_poFileCompiles($poFile, 'msgfmt', "$poFile does not compile correctly"));
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

	        print "foo did not resolve to [[foo]]";
	    }

	    $this->assertTrue($result);
	}

   	private static function _getAllTranslatableStrings()
   	{
   	    $command = 'grep -r ">(translatable)<" ' . dirname(__FILE__) . '/../../../../../../main';
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
   	    $realPath = self::$_i18nDirectoryPath . '/' . $file;

   	    $outputfile = str_replace(array('.pot', '.po'), '.mo', $realPath);

        $msgFmt = `which msgfmt`;
        $msgFmt = tubepress_impl_util_StringUtils::removeNewLines($msgFmt);

        $toRun = "$msgFmt -o $outputfile $realPath";

   	    exec($toRun, $output, $return);

        return $return === 0;
   	}

   	private static function _getPoFiles()
   	{
   	    $files = array();

   	    $handle = opendir(self::$_i18nDirectoryPath);

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

   	    $potContents = file_get_contents(self::$_i18nDirectoryPath . '/' . $file);

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

   	private static function _rstrpos ($haystack, $needle)
    {
   	    $index = strpos(strrev($haystack), strrev($needle));
   	    $index = strlen($haystack) - strlen($index) - $index;

   	    return $index;
   	}
}
