<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
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
class tubepress_test_impl_i18n_support_PoFileTranslation extends tubepress_test_impl_i18n_support_AbstractTranslation
{
    private $_path;

    private $_locale;

    public function __construct($path, $locale)
    {
        parent::__construct("PO file at $path");

        $this->_path = $path;
    }

    public static function buildFromContents($contents, $locale)
    {
        $file = tempnam(null, 'tubepress-i18n-test-');

        file_put_contents($file, $contents);

        $instance = new self($file, $locale);

        $instance->getStrings();

        unlink($file);

        return $instance;
    }

    public function fetchStrings()
    {
        $potContents = $this->_read($this->_path);

        $toReturn = array();

        foreach ($potContents as $potEntry) {

            if ($potEntry['msgid'] == '') {

                continue;
            }

            $toReturn[$potEntry['msgid']] = $potEntry['msgstr'][0];
        }

        return $toReturn;
    }

    //https://github.com/clinisbut/PHP-po-parser/blob/master/poparser.php
    private function _read($pofile)
    {
        if (empty($pofile)) {

            throw new RuntimeException('Input file not defined.');

        } else if (file_exists($pofile) === false) {

            throw new RuntimeException('File does not exist: "'.htmlspecialchars($pofile).'".');

        } else if (is_readable($pofile) === false) {

            throw new RuntimeException('The file could not be read.');
        }

        // Comenzar su parsing
        $handle         = fopen($pofile, 'r');
        $hash           = array();
        $fuzzy          = false;
        $tcomment       = $ccomment = $reference = null;
        $entry          = $entryTemp = array();
        $state          = null;
        $just_new_entry = false;		// A new entry has ben just inserted

        while (!feof($handle)) {

            $line = trim(fgets($handle));

            if ($line==='') {

                if ($just_new_entry) {
                    // Two consecutive blank lines
                    continue;
                }

                // A new entry is found!
                $hash[]         = $entry;
                $entry          = array();
                $state          = null;
                $just_new_entry = true;

                continue;
            }

            $just_new_entry = false;
            $split          = preg_split('/\s/ ', $line, 2);
            $key            = $split[0];
            $data           = isset($split[1]) ? $split[1] : null;

            switch($key) {

                case '#,':	//flag
                    $entry['fuzzy'] = in_array('fuzzy', preg_split('/,\s*/', $data));
                    $entry['flags'] = $data;
                    break;

                case '#':	//translation-comments
                    $entryTemp['tcomment'] = $data;
                    $entry['tcomment']     = $data;
                    break;

                case '#.':	//extracted-comments
                    $entryTemp['ccomment'] = $data;
                    break;

                case '#:':	//reference
                    $entryTemp['reference'][] = addslashes($data);
                    $entry['reference'][]     = addslashes($data);
                    break;

                case '#|':	//msgid previous-untranslated-string
                    // start a new entry
                    break;

                case '#@':	// ignore #@ default
                    $entry['@'] = $data;
                    break;

                // old entry
                case '#~':
                    $key               = explode(' ', $data);
                    $entry['obsolete'] = true;

                    switch($key[0]) {

                        case 'msgid': $entry['msgid'] = trim($data,'"');
                            break;

                        case 'msgstr':	$entry['msgstr'][] = trim($data,'"');
                            break;

                        default:
                            break;
                    }

                    continue;
                    break;

                case 'msgctxt' :
                    // context
                case 'msgid' :
                    // untranslated-string
                case 'msgid_plural' :
                    // untranslated-string-plural
                    $state         = $key;
                    $entry[$state] = $data;
                    break;

                case 'msgstr' :
                    // translated-string
                    $state           = 'msgstr';
                    $entry[$state][] = $data;
                    break;

                default :

                    if (strpos($key, 'msgstr[') !== false) {
                        // translated-string-case-n
                        $state           = 'msgstr';
                        $entry[$state][] = $data;

                    } else {
                        // continued lines
                        //echo "O NDE ELSE:".$state.':'.$entry['msgid'];
                        switch($state) {

                            case 'msgctxt' :
                            case 'msgid' :
                            case 'msgid_plural' :
                                //$entry[$state] .= "\n" . $line;
                                if (is_string($entry[$state])) {
                                    // Convert it to array
                                    $entry[$state] = array($entry[$state]);
                                }
                                $entry[$state][] = $line;
                                break;

                            case 'msgstr' :
                                //Special fix where msgid is ""
                                if ($entry['msgid']=="\"\"") {

                                    $entry['msgstr'][] = trim($line,'"');

                                } else {
                                    //$entry['msgstr'][sizeof($entry['msgstr']) - 1] .= "\n" . $line;
                                    $entry['msgstr'][]= trim($line,'"');
                                }

                                break;

                            default :

                                throw new RuntimeException('Parse error!');
                        }
                    }
                    break;
            }
        }

        fclose($handle);

        // add final entry
        if ($state == 'msgstr') {
            $hash[] = $entry;
        }

        // Cleanup data, merge multiline entries, reindex hash for ksort
        $temp    = $hash;
        $entries = array ();

        foreach ($temp as $entry) {

            foreach ($entry as & $v) {

                $v = $this->_clean($v);

                if ($v === false) {
                    // parse error
                    return false;
                }
            }

            $id = is_array($entry['msgid'])? implode('',$entry['msgid']):$entry['msgid'];

            $entries[ $id ] = $entry;
        }

        return $entries;
    }

    private function _clean($x) {

        if (is_array($x)) {

            foreach ($x as $k => $v) {

                $x[$k] = $this->_clean($v);
            }

        } else {

            // Remove " from start and end
            if ($x == '') {

                return '';
            }

            if ($x[0] == '"') {

                $x = substr($x, 1, -1);
            }

            $x = stripcslashes($x);
        }

        return $x;
    }
}
