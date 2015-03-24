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

/**
 * @api
 * @since 4.0.0
 */
class tubepress_app_api_listeners_options_RegexValidatingListener
{
    const TYPE_INTEGER_POSITIVE                = 'positiveInteger';
    const TYPE_INTEGER_NONNEGATIVE             = 'nonNegativeInteger';
    const TYPE_INTEGER                         = 'integer';
    const TYPE_ONE_OR_MORE_WORDCHARS           = 'oneOrMoreWordChars';
    const TYPE_ONE_OR_MORE_WORDCHARS_OR_HYPHEN = 'oneOrMoreWordCharsPlusHyphen';
    const TYPE_ZERO_OR_MORE_WORDCHARS          = 'zeroOrMoreWordChars';
    const TYPE_STRING_HEXCOLOR                 = 'hexColor';
    const TYPE_STRING_YOUTUBE_VIDEO_ID         = 'youTubeVideoId';

    private static $_TYPES = array(

        self::TYPE_INTEGER_POSITIVE,
        self::TYPE_INTEGER_NONNEGATIVE,
        self::TYPE_INTEGER,
        self::TYPE_ONE_OR_MORE_WORDCHARS,
        self::TYPE_ONE_OR_MORE_WORDCHARS_OR_HYPHEN,
        self::TYPE_ZERO_OR_MORE_WORDCHARS,
        self::TYPE_STRING_HEXCOLOR,
        self::TYPE_STRING_YOUTUBE_VIDEO_ID,
    );

    private static $_REGEXES = array(

        self::TYPE_INTEGER_POSITIVE                => '/^[1-9][0-9]{0,6}$/',
        self::TYPE_INTEGER_NONNEGATIVE             => '/^0|[1-9][0-9]{0,6}$/',
        self::TYPE_INTEGER                         => '/^0|-?[1-9][0-9]{0,6}$/',
        self::TYPE_ONE_OR_MORE_WORDCHARS           => '/^\w+$/',
        self::TYPE_ONE_OR_MORE_WORDCHARS_OR_HYPHEN => '/^[\w-]+$/',
        self::TYPE_ZERO_OR_MORE_WORDCHARS          => '/^\w*$/',
        self::TYPE_STRING_HEXCOLOR                 => '/^([0-9a-f]{1,2}){3}$/i',
        self::TYPE_STRING_YOUTUBE_VIDEO_ID         => '/^[a-zA-Z0-9_-]{11}$/'
    );

    /**
     * @var string
     */
    private $_type;

    /**
     * @var tubepress_app_api_options_ReferenceInterface
     */
    private $_reference;

    /**
     * @var tubepress_lib_api_translation_TranslatorInterface
     */
    private $_translator;

    public function __construct(                                                  $type,
                                tubepress_app_api_options_ReferenceInterface      $reference,
                                tubepress_lib_api_translation_TranslatorInterface $translator)
    {
        if (!in_array($type, self::$_TYPES)) {

            throw new InvalidArgumentException('Invalid type: ' . $type);
        }

        $this->_reference  = $reference;
        $this->_type       = $type;
        $this->_translator = $translator;
    }

    public function onOption(tubepress_lib_api_event_EventInterface $event)
    {
        $errors      = $event->getSubject();
        $optionName  = $event->getArgument('optionName');
        $optionValue = $event->getArgument('optionValue');

        if (!is_scalar($optionValue)) {

            return;
        }

        if (isset(self::$_REGEXES[$this->_type])) {

            if (preg_match_all(self::$_REGEXES[$this->_type], (string) $optionValue, $matches) >= 1 && $matches[0][0] === (string) $optionValue) {

                return;
            }

            $error    = $this->_translator->trans('Invalid value supplied for "%s".');      //>(translatable)<
            $error    = sprintf($error, $this->_getLabel($optionName));
            $errors[] = $error;

            $event->setSubject($errors);
        }

        return null;
    }

    private function _getLabel($optionName)
    {
        if ($this->_reference->getUntranslatedLabel($optionName)) {

            $label = $this->_reference->getUntranslatedLabel($optionName);

            return $this->_translator->trans($label);
        }

        return $optionName;
    }
}