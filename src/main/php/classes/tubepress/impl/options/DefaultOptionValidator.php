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
 * Performs validation on option values
 */
class tubepress_impl_options_DefaultOptionValidator implements tubepress_spi_options_OptionValidator
{
    /**
     * Validates an option value.
     *
     * @param string $optionName The option name
     * @param mixed  $candidate  The candidate option value
     *
     * @return boolean True if the option name exists and the value supplied is valid. False otherwise.
     */
    public final function isValid($optionName, $candidate)
    {
        return $this->getProblemMessage($optionName, $candidate) === null;
    }

    /**
     * Gets the failure message of a name/value pair that has failed validation.
     *
     * @param string $optionName The option name
     * @param mixed  $candidate  The candidate option value
     *
     * @return mixed Null if the option passes validation, otherwise a string failure message.
     */
    public final function getProblemMessage($optionName, $candidate)
    {
        $optionDescriptorReferenceService = tubepress_impl_patterns_sl_ServiceLocator::getOptionDescriptorReference();
        $messageService                   = tubepress_impl_patterns_sl_ServiceLocator::getMessageService();

        $descriptor = $optionDescriptorReferenceService->findOneByName($optionName);

        if ($descriptor === null) {

            return sprintf('No option with name "%s".', $optionName);                          //>(translatable)<
        }

        if ($descriptor->hasValidValueRegex()) {

            if (preg_match_all($descriptor->getValidValueRegex(), (string) $candidate, $matches) >= 1 && $matches[0][0] === (string) $candidate) {

                return null;
            }

            return sprintf('Invalid value supplied for "%s".', $messageService->_($descriptor->getLabel()));      //>(translatable)<
        }

        if ($descriptor->hasDiscreteAcceptableValues()) {

            $acceptableValues = $descriptor->getAcceptableValues();

            if (tubepress_impl_util_LangUtils::isAssociativeArray($acceptableValues)) {

                $values = array_keys($descriptor->getAcceptableValues());

            } else {

                $values = array_values($acceptableValues);
            }

            if (in_array($candidate, $values)) {

                return null;
            }

            return sprintf('"%s" must be one of "%s". You supplied "%s".',                               //>(translatable)<
                $messageService->_($descriptor->getLabel()), implode(', ', $values), $candidate);
        }

        if ($descriptor->isBoolean()) {

            if (is_bool($candidate)) {

                return null;
            }

            return sprintf('"%s" can only be "true" or "false". You supplied "%s".', $optionName, $candidate);  //>(translatable)<
        }

        return null;
    }
}
