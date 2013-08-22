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

class tubepress_test_impl_i18n_SynchronizationTest extends tubepress_test_TubePressUnitTest
{
    private static $_POSSIBLE_MSGFMT_LOCATIONS = array(

        '/usr/bin/msgfmt',
        '/opt/local/bin/msgfmt'
    );

    /**
     * @var tubepress_test_impl_i18n_support_AbstractTranslation
     */
    private static $_greppedCode;

    /**
     * @var tubepress_test_impl_i18n_support_AbstractTranslation[]
     */
    private static $_localPoFiles = array();

    public function onSetup()
    {
        if (isset(self::$_greppedCode)) {

            return;
        }

        $canExecute = false;

        foreach (self::$_POSSIBLE_MSGFMT_LOCATIONS as $possibleExecutable) {

            if (is_executable($possibleExecutable)) {

                $canExecute = true;
                break;
            }
        }

        if (!$canExecute) {

            $this->markTestSkipped('msgfmt is not available on this installation');
        }

        $i18nPath           = TUBEPRESS_ROOT . '/src/main/resources/i18n';
        self::$_greppedCode = new tubepress_test_impl_i18n_support_GreppedCode();

        foreach (tubepress_test_impl_i18n_support_LocaleUtils::$LOCALES as $locale) {

            $translation = new tubepress_test_impl_i18n_support_GlotPressTranslation($locale);
            $poFile      = $i18nPath . '/tubepress-' . tubepress_test_impl_i18n_support_LocaleUtils::localeProperlyCapitalized($locale) . '.po';

            $translation->spitToPoFile($poFile);

            if (!is_readable($poFile)) {

                throw new RuntimeException('Could not generate local PO file from GlotPress');
            }

            self::$_localPoFiles[] = new tubepress_test_impl_i18n_support_PoFileTranslation($poFile, $locale);
        }
    }

    public function testAllPoFilesPresent()
    {
        foreach ($this->_getListOfExpectedPoFiles() as $file) {

            $this->assertFileExists($file);
        }
    }

    public function testMsgIdsMatchUp()
    {
        foreach (self::$_localPoFiles as $localPoFile) {

            $this->_doMsgIdDiff(self::$_greppedCode, $localPoFile);
        }
    }

    public function testPoFilesCompile()
    {
        $poFiles = $this->_getListOfExpectedPoFiles();

        foreach ($poFiles as $poFile) {

            $this->assertTrue(self::_poFileCompiles($poFile, "$poFile does not compile correctly"));
        }
    }

    private function _getListOfExpectedPoFiles()
    {
        $i18nRoot = TUBEPRESS_ROOT . '/src/main/resources/i18n/';
        $toReturn = array();

        foreach (tubepress_test_impl_i18n_support_LocaleUtils::$LOCALES as $locale) {

            $toReturn[] = $i18nRoot . 'tubepress-' . tubepress_test_impl_i18n_support_LocaleUtils::localeProperlyCapitalized($locale) . '.po';
        }

        return $toReturn;
    }

    private function _poFileCompiles($file)
    {
        $outputfile = str_replace(array('.pot', '.po'), '.mo', $file);

        $msgFmtPath = null;

        foreach (self::$_POSSIBLE_MSGFMT_LOCATIONS as $possibleMsgFmtLocation) {

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

        $toRun = "$msgFmt -o $outputfile $file";

        exec($toRun, $output, $return);

        return $return === 0;
    }

    private function _doMsgIdDiff(tubepress_test_impl_i18n_support_AbstractTranslation $collection1, tubepress_test_impl_i18n_support_AbstractTranslation $collection2)
    {
        $msgIds1 = array_keys($collection1->getStrings());
        $msgIds2 = array_keys($collection2->getStrings());

        $missingFrom1 = array_diff($msgIds2, $msgIds1);
        $missingFrom2 = array_diff($msgIds1, $msgIds2);

        $ok = empty($missingFrom1) && empty($missingFrom2);

        if (!$ok) {

            $message = '';

            if (!empty($missingFrom1)) {

                $message .= "\n\nThe following msgids are missing from " . $collection1->getName() . ", as compared to " . $collection2->getName() . "\n\n";
                $message .= print_r($missingFrom1, true);
            }

            if (!empty($missingFrom2)) {

                $message .= "\n\nThe following msgids are missing from " . $collection2->getName() . ", as compared to " . $collection1->getName() . "\n\n";
                $message .= print_r($missingFrom2, true);
            }

            $this->fail($message);
        }

        $this->assertTrue(true);
    }
}