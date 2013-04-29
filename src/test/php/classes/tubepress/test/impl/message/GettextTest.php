<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_test_impl_message_GettextTest extends tubepress_test_TubePressUnitTest
{
    private static $_i18nDirectoryPath;

    private static $_poFiles;

    private static $_allTranslatableStrings;

    public static function setUpBeforeClass()
    {
        self::$_i18nDirectoryPath = TUBEPRESS_ROOT . '/src/main/resources/i18n';

        self::$_poFiles = self::_getPoFiles();

        self::$_allTranslatableStrings = self::_getAllTranslatableStrings();
    }

    public function testAllStringsPresent()
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

    public function testPoCompiles()
    {
        foreach (self::$_poFiles as $poFile) {

            $this->assertTrue(self::_poFileCompiles($poFile, 'msgfmt', "$poFile does not compile correctly"));
        }
    }

    private static function _getAllTranslatableStrings()
    {
        $command = 'grep -r ">(translatable)<" ' . TUBEPRESS_ROOT . '/src/main/php';
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

        $proStrings = file(TUBEPRESS_ROOT . '/src/test/resources/gettext/pro-strings.txt');
        $proStrings = array_map(array('tubepress_impl_util_StringUtils', 'removeNewLines'), $proStrings);

        $strings = array_merge($strings, $proStrings);

        return $strings;
    }

    private function _poFileCompiles($file, $exec)
    {
        $realPath = self::$_i18nDirectoryPath . '/' . $file;

        $outputfile = str_replace(array('.pot', '.po'), '.mo', $realPath);

        $possibleMsgFmtLocations = array(

            '/usr/bin/msgfmt',
            '/opt/local/bin/msgfmt'
        );

        $msgFmtPath = null;

        foreach ($possibleMsgFmtLocations as $possibleMsgFmtLocation) {

            if (is_executable($possibleMsgFmtLocation)) {

                $msgFmtPath = $possibleMsgFmtLocation;
                break;
            }
        }

        if (!$msgFmtPath) {

            $this->markTestSkipped('msgfmt does not exist on this installation');
            return true;
        }

        $msgFmt = tubepress_impl_util_StringUtils::removeNewLines($msgFmtPath);

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