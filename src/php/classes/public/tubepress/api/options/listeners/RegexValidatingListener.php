<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @api
 * @since 4.0.0
 */
class tubepress_api_options_listeners_RegexValidatingListener extends tubepress_api_options_listeners_AbstractValidatingListener
{
    const TYPE_INTEGER_POSITIVE                = 'positiveInteger';
    const TYPE_INTEGER_NONNEGATIVE             = 'nonNegativeInteger';
    const TYPE_INTEGER                         = 'integer';
    const TYPE_ONE_OR_MORE_WORDCHARS           = 'oneOrMoreWordChars';
    const TYPE_ONE_OR_MORE_WORDCHARS_OR_HYPHEN = 'oneOrMoreWordCharsPlusHyphen';
    const TYPE_ZERO_OR_MORE_WORDCHARS          = 'zeroOrMoreWordChars';
    const TYPE_STRING_HEXCOLOR                 = 'hexColor';
    const TYPE_STRING_YOUTUBE_VIDEO_ID         = 'youTubeVideoId';
    const TYPE_TWO_DIGIT_COUNTRY_CODE          = 'twoDigitCountryCode';
    const TYPE_TWO_DIGIT_LANGUAGE_CODE         = 'twoDigitLanguageCode';
    const TYPE_DOMAIN                          = 'domain';
    const TYPE_DOM_ELEMENT_ID_OR_NAME          = 'domElementIdOrName';

    private static $_TYPES = array(

        self::TYPE_INTEGER_POSITIVE,
        self::TYPE_INTEGER_NONNEGATIVE,
        self::TYPE_INTEGER,
        self::TYPE_ONE_OR_MORE_WORDCHARS,
        self::TYPE_ONE_OR_MORE_WORDCHARS_OR_HYPHEN,
        self::TYPE_ZERO_OR_MORE_WORDCHARS,
        self::TYPE_STRING_HEXCOLOR,
        self::TYPE_STRING_YOUTUBE_VIDEO_ID,
        self::TYPE_TWO_DIGIT_COUNTRY_CODE,
        self::TYPE_TWO_DIGIT_LANGUAGE_CODE,
        self::TYPE_DOMAIN,
        self::TYPE_DOM_ELEMENT_ID_OR_NAME,
    );

    private static $_REGEXES = array(

        self::TYPE_INTEGER_POSITIVE                => '/^[1-9][0-9]{0,6}$/',
        self::TYPE_INTEGER_NONNEGATIVE             => '/^0|[1-9][0-9]{0,6}$/',
        self::TYPE_INTEGER                         => '/^0|-?[1-9][0-9]{0,6}$/',
        self::TYPE_ONE_OR_MORE_WORDCHARS           => '/^\w+$/',
        self::TYPE_ONE_OR_MORE_WORDCHARS_OR_HYPHEN => '/^[\w-]+$/',
        self::TYPE_ZERO_OR_MORE_WORDCHARS          => '/^\w*$/',
        self::TYPE_STRING_HEXCOLOR                 => '/^([0-9a-f]{1,2}){3}$/i',
        self::TYPE_STRING_YOUTUBE_VIDEO_ID         => '/^[a-zA-Z0-9_-]{11}$/',
        self::TYPE_TWO_DIGIT_LANGUAGE_CODE         => '/^[a-z]{2}$/',
        self::TYPE_TWO_DIGIT_COUNTRY_CODE          => '/^[A-Z]{2}$/',
        self::TYPE_DOMAIN                          => '/^(?!\-)(?:[a-zA-Z\d\-]{0,62}[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63}$/',
        self::TYPE_DOM_ELEMENT_ID_OR_NAME          => '/^[a-z]+[a-z0-9\-_:\.]*$/i',
    );

    /**
     * @var string
     */
    private $_type;

    public function __construct(                                              $type,
                                tubepress_api_options_ReferenceInterface      $reference,
                                tubepress_api_translation_TranslatorInterface $translator)
    {
        parent::__construct($reference, $translator);

        if (!in_array($type, self::$_TYPES)) {

            throw new InvalidArgumentException('Invalid type: ' . $type);
        }

        $this->_type = $type;
    }

    public function onOption(tubepress_api_event_EventInterface $event)
    {
        $this->onOptionValidation($event);
    }

    /**
     * @param $optionName
     * @param $optionValue
     *
     * @return boolean
     */
    protected function isValid($optionName, $optionValue)
    {
        if (!is_scalar($optionValue)) {

            return false;
        }

        $finalRegex = self::$_REGEXES[$this->_type];

        return preg_match_all($finalRegex, (string) $optionValue, $matches) >= 1 &&
            $matches[0][0] === (string) $optionValue;
    }
}