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

class tubepress_test_impl_i18n_support_LocaleUtils
{
    public static $LOCALES = array('ar', 'de', 'el', 'es', 'fa', 'fi', 'fr', 'he', 'hi', 'it', 'ja', 'ko', 'nb', 'pl', 'pt-br', 'ru', 'sv', 'zh-cn', 'zh-tw');

    public static function localeProperlyCapitalized($locale)
    {
        if (strpos($locale, '-') === false) {

            return $locale;
        }

        $firstPart = substr($locale, 0, 2);
        $secondPart = substr($locale, 3);

        return $firstPart . '_' . strtoupper($secondPart);
    }
}