<?php

/**
 * Copyright 2013 Eric D. Hough (eric@tubepress.com)
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