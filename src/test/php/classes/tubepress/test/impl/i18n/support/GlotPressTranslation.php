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
class tubepress_test_impl_i18n_support_GlotPressTranslation
{
    private static $_POSSIBLE_MSGCAT_LOCATIONS = array(

        '/usr/bin/msgcat',
        '/opt/local/bin/msgcat'
    );

    private $_locale;

    public function __construct($locale)
    {
        $this->_locale = $locale;
    }

    public function spitToPoFile($file)
    {
        $executable = null;
        foreach (self::$_POSSIBLE_MSGCAT_LOCATIONS as $location) {

            if (is_executable($location)) {

                $executable = $location;
                break;
            }
        }
        if (!$executable) {

            throw new RuntimeException('msgcat is not available on this installation');
        }

        $urls = array(
            '',
            '/administration',
            '/environments/wordpress',
            '/addons/vimeo',
            '/addons/youtube',
            '/addons/jw-player',
        );

        $individualFiles = array();

        foreach ($urls as $url) {

            $individualFiles[] = $this->_getFileAt('http://translate.tubepress.com/api/projects/tubepress' . $url . '/' . $this->_locale . '/default/export-translations');
        }

        $command = $executable . ' ' . implode(' ', $individualFiles) .
            ' -u --no-wrap --no-location --use-first --lang=' . $this->_locale . ' -o ' . $file;

        exec($command, $output, $return);

        if ($return !== 0) {

            throw new RuntimeException("$command failed");
        }

        foreach ($individualFiles as $individualFile) {

            unlink($individualFile);
        }
    }

    private function _getFileAt($url)
    {
        $tmpFile = tempnam(null, 'tubepress-i18n-test-');
        $fp      = fopen($tmpFile, 'w+');
        $handle  = curl_init($url);

        curl_setopt_array($handle, array(

            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_HEADER         => 0,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_0,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_URL            => $url,
            CURLOPT_FILE           => $fp,
        ));

        // The option doesn't work with safe mode or when open_basedir is set.
        // Disable HEAD when making HEAD requests.
        if (!ini_get('safe_mode') && !ini_get('open_basedir')) {

            curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
        }

        $response = curl_exec($handle);

        if (curl_getinfo($handle, CURLINFO_HTTP_CODE) !== 200) {

            curl_close($handle);
            fclose($fp);
            unlink($tmpFile);
            throw new RuntimeException("$url didn't return an HTTP 200");
        }

        curl_close($handle);
        fclose($fp);

        return $tmpFile;
    }
}
