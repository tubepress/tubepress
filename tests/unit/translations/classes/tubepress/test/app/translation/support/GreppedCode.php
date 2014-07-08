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
 * A collection of strings that need translations.
 */
class tubepress_test_app_translation_support_GreppedCode extends tubepress_test_app_translation_support_AbstractTranslation
{
    public function __construct()
    {
        parent::__construct('grepped code base');
    }

    public function fetchStrings()
    {
        $codeBaseDirectories = array(

            TUBEPRESS_ROOT . '/src/core'
        );

        $toReturn = array_map('rtrim', file(TUBEPRESS_ROOT . '/tests/unit/translations/extra-strings.txt'));

        foreach ($codeBaseDirectories as $codeBaseDirectory) {

            $toReturn = array_merge($toReturn, $this->_pullStringsFromCodeBase($codeBaseDirectory));
        }

        return array_fill_keys($toReturn, $toReturn);
    }

    private function _pullStringsFromCodeBase($base)
    {
        $command = 'grep -r ">(translatable)<" ' . $base;

        exec($command, $results, $return);

        if ($return !== 0) {

            throw new RuntimeException("$command failed");
        }

        if (count($results) === 0) {

            throw new RuntimeException("grep didn't find any strings to translate in $base");
        }

        $strings = array();

        foreach ($results as $grepLine) {

            $result = preg_match_all("/^[^']*'(.+)'[^']*$/", $grepLine, $matches);

            if (!$result || count($matches) !== 2) {

                throw new RuntimeException('Found more than on match on ' . $grepLine . '. ' . var_export($matches, true));
            }

            $strings[] = str_replace("\'", "'", $matches[1][0]);
        }

        return $strings;
    }
}