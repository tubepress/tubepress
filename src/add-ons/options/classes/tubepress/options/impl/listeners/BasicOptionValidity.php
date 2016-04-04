<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_options_impl_listeners_BasicOptionValidity
{
    /**
     * @var tubepress_api_options_ReferenceInterface
     */
    private $_optionsReference;

    /**
     * @var tubepress_api_options_AcceptableValuesInterface
     */
    private $_acceptableValues;

    /**
     * @var tubepress_api_util_LangUtilsInterface
     */
    private $_langUtils;

    /**
     * @var tubepress_api_translation_TranslatorInterface
     */
    private $_translator;

    public function __construct(tubepress_api_options_ReferenceInterface        $optionsReference,
                                tubepress_api_options_AcceptableValuesInterface $acceptableValues,
                                tubepress_api_translation_TranslatorInterface   $translator,
                                tubepress_api_util_LangUtilsInterface           $langUtils)
    {
        $this->_optionsReference = $optionsReference;
        $this->_acceptableValues = $acceptableValues;
        $this->_langUtils        = $langUtils;
        $this->_translator       = $translator;
    }

    public function onOption(tubepress_api_event_EventInterface $event)
    {
        $errors      = $event->getSubject();
        $optionName  = $event->getArgument('optionName');
        $optionValue = $event->getArgument('optionValue');

        if (!$this->_optionsReference->optionExists($optionName)) {

            $error    = $this->_translator->trans('No option with name "%s".');     //>(translatable)<
            $error    = sprintf($error, $optionName);
            $errors[] = $error;

            $event->setSubject($errors);

            return;
        }

        if ($this->_optionsReference->isBoolean($optionName) && !is_bool($optionValue)) {

            $error    = $this->_translator->trans('"%s" can only be "true" or "false". You supplied "%s".');  //>(translatable)<
            $error    = sprintf($error, $this->_getLabel($optionName), $optionValue);
            $errors[] = $error;

            $event->setSubject($errors);

            return;
        }

        $acceptableValues = $this->_acceptableValues->getAcceptableValues($optionName);

        if ($acceptableValues !== null) {

            if ($this->_langUtils->isAssociativeArray($acceptableValues)) {

                $values = array_keys($acceptableValues);

            } else {

                $values = array_values($acceptableValues);
            }

            if (!in_array($optionValue, $values)) {

                $error    = $this->_translator->trans('"%s" must be one of "%s". You supplied "%s".');   //>(translatable)<
                $error    = sprintf($error, $this->_getLabel($optionName), implode(', ', $values), $optionValue);
                $errors[] = $error;

                $event->setSubject($errors);
            }
        }
    }

    private function _getLabel($optionName)
    {
        if ($this->_optionsReference->getUntranslatedLabel($optionName)) {

            $label = $this->_optionsReference->getUntranslatedLabel($optionName);

            return $this->_translator->trans($label);
        }

        return $optionName;
    }
}
