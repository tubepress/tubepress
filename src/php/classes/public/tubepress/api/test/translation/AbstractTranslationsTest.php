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

abstract class tubepress_api_test_translation_AbstractTranslationsTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var string
     */
    private $_msgfmtExecutable;

    /**
     * @var string
     */
    private $_grepExecutable;

    protected abstract function getPoFilePaths();

    protected abstract function getSearchPaths();

    public function onSetup()
    {
        $this->_findMsgfmtExecutable();
        $this->_findGrepExecutable();

        if (!isset($this->_msgfmtExecutable) || !isset($this->_grepExecutable)) {

            return;
        }

        $this->_ensurePoFilesIntact();

        foreach ($this->getSearchPaths() as $dir) {

            if (!is_dir($dir) || !is_readable($dir)) {

                throw new InvalidArgumentException("$dir is not a readable directory");
            }
        }
    }

    public function testMessagesIntegrity()
    {
        $stringsFromSearchPaths = $this->_collectStringsFromSrc();
        $stringsFromPoFiles     = $this->_collectStringsFromPoFiles();
        $missingFromPo          = array_diff($stringsFromSearchPaths, $stringsFromPoFiles);
        $missingFromSrc         = array_diff($stringsFromPoFiles, $stringsFromSearchPaths);

        if (!empty($missingFromPo)) {

            $this->_printFailure($missingFromPo, 'PO files', 'the source');
            return;
        }

        if (!empty($missingFromSrc)) {

            $this->_printFailure($missingFromSrc, 'the source', 'PO files');
            return;
        }

        $this->assertTrue(true);
    }

    private function _collectStringsFromPoFiles()
    {
        $toReturn = array();

        foreach ($this->getPoFilePaths() as $poFile) {

            $toReturn = array_merge($toReturn, $this->_collectStringsFromPoFile($poFile));
        }

        $toReturn = array_unique($toReturn);

        return $toReturn;
    }

    private function _collectStringsFromSrc()
    {
        $toReturn = array();

        foreach ($this->getSearchPaths() as $searchPath) {

            $toReturn = array_merge($toReturn, $this->_collectStringsFromPath($searchPath));
        }

        $toReturn = array_unique($toReturn);

        return $toReturn;
    }

    private function _collectStringsFromPath($path)
    {
        $toReturn = array();

        $command = $this->_grepExecutable . ' -r ">(transl' . 'atable)<" ' . $path;

        exec($command, $results, $return);

        if ($return !== 0) {

            throw new RuntimeException("$command failed");
        }

        if (count($results) === 0) {

            return $toReturn;
        }

        foreach ($results as $grepLine) {

            $result = preg_match_all("/^[^']*'(.+)'[^']*$/", $grepLine, $matches);

            if (!$result || count($matches) !== 2) {

                throw new RuntimeException('Found more than on match on ' . $grepLine . '. ' . var_export($matches, true));
            }

            $toReturn[] = str_replace("\'", "'", $matches[1][0]);
        }

        return $toReturn;
    }

    private function _findMsgfmtExecutable()
    {
        $candidate = $this->_findExecutable('msgfmt');

        if ($candidate) {

            $this->_msgfmtExecutable = $candidate;
        }
    }

    private function _findGrepExecutable()
    {
        $candidate = $this->_findExecutable('grep');

        if ($candidate) {

            $this->_grepExecutable = $candidate;
        }
    }

    private function _findExecutable($name)
    {
        exec("which $name", $results, $return);

        if ($return !== 0) {

            $this->markTestSkipped("`which msgfmt` failed");
            return null;
        }

        if (count($results) !== 1) {

            $this->markTestSkipped('Could not determine path to msgfmt executable');
            return null;
        }

        $candidate = $results[0];

        if (!is_executable($candidate)) {

            $this->markTestSkipped("$candidate is not executable");
            return null;
        }

        return $candidate;
    }

    private function _ensurePoFilesIntact()
    {
        foreach ($this->getPoFilePaths() as $file) {

            if (!is_file($file) || !is_readable($file)) {

                throw new InvalidArgumentException("$file is not a readable file");
            }

            if (!$this->_poFileCompiles($file)) {

                throw new RuntimeException("$file does not compile correctly");
            }
        }
    }

    private function _poFileCompiles($file)
    {
        exec($this->_msgfmtExecutable . " -c $file", $output, $return);

        return $return === 0;
    }

    private function _collectStringsFromPoFile($file)
    {
        $fileHandler = new Sepia\FileHandler($file);
        $poParser    = new Sepia\PoParser($fileHandler);
        $entries     = $poParser->parse();
        $toReturn    = array();

        foreach ($entries as $entry) {

            if (!isset($entry['msgid'])) {

                throw new RuntimeException("$file has an entry with no msgid");
            }

            $toReturn[] = $entry['msgid'][0];
        }

        $toReturn = array_unique($toReturn);

        return $toReturn;
    }

    private function _printFailure(array $missingStrings, $missingFrom, $foundIn)
    {
        $message  = "The following msgids are missing from the $missingFrom, but they are contained in $foundIn\n\n";
        $message .= implode("\n\n", $missingStrings);

        $this->fail($message);
    }
}