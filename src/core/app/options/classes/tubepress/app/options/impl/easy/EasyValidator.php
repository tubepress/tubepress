<?php
/**
 * Copyright 2006  2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 *
 */
class tubepress_app_options_impl_easy_EasyValidator
{
    private static $_TYPES = array(

        'positiveInteger',
        'nonNegativeInteger',
        'oneOrMoreWordChars',
        'oneOrMoreWordCharsPlusHyphen',
        'zeroOrMoreWordChars',
        'hexColor',
        'youTubeVideoId'
    );

    private static $_REGEXES = array(

        'positiveInteger'              => '/[1-9][0-9]{0,6}/',
        'nonNegativeInteger'           => '/0|[1-9][0-9]{0,6}/',
        'oneOrMoreWordChars'           => '/\w+/',
        'oneOrMoreWordCharsPlusHyphen' => '/[\w-]+/',
        'zeroOrMoreWordChars'          => '/\w*/',
        'hexColor'                     => '/^([0-9a-f]{1,2}){3}$/i',
        'youTubeVideoId'               => '/[a-zA-Z0-9_-]{11}/'
    );

    /**
     * @var string
     */
    private $_type;

    /**
     * @var tubepress_app_options_api_ReferenceInterface
     */
    private $_reference;

    /**
     * @var tubepress_lib_translation_api_TranslatorInterface
     */
    private $_translator;

    public function __construct(                                                   $type,
                                tubepress_app_options_api_ReferenceInterface      $reference,
                                tubepress_lib_translation_api_TranslatorInterface $translator)
    {
        if (!in_array($type, self::$_TYPES)) {

            throw new InvalidArgumentException('Invalid type: ' . $type);
        }

        $this->_reference  = $reference;
        $this->_type       = $type;
        $this->_translator = $translator;
    }

    public function onOption(tubepress_lib_event_api_EventInterface $event)
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

            $error    = $this->_translator->_('Invalid value supplied for "%s".');      //>(translatable)<
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

            return $this->_translator->_($label);
        }

        return $optionName;
    }
}