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
 * @since 5.0.0
 */
abstract class tubepress_api_options_listeners_AbstractValidatingListener
{
    /**
     * @var tubepress_api_options_ReferenceInterface
     */
    private $_reference;

    /**
     * @var tubepress_api_translation_TranslatorInterface
     */
    private $_translator;

    public function __construct(tubepress_api_options_ReferenceInterface      $reference,
                                tubepress_api_translation_TranslatorInterface $translator)
    {
        $this->_reference  = $reference;
        $this->_translator = $translator;
    }

    public function onOptionValidation(tubepress_api_event_EventInterface $event)
    {
        $errors      = $event->getSubject();
        $optionName  = $event->getArgument('optionName');
        $optionValue = $event->getArgument('optionValue');

        if (!$this->isValid($optionName, $optionValue)) {

            $template = $this->getErrorMessageTemplate();
            $error    = $this->_translator->trans($template);
            $error    = sprintf($error, $this->_getLabel($optionName));
            $errors[] = $error;

            $event->setSubject($errors);
            $event->stopPropagation();
        }
    }

    /**
     * @param $optionName
     * @param $optionValue
     *
     * @return boolean
     */
    protected abstract function isValid($optionName, $optionValue);

    protected function getErrorMessageTemplate()
    {
        return 'Invalid value supplied for "%s".';  //>(translatable)<
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