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
interface tubepress_spi_options_OptionValidator
{
    const _ = 'tubepress_spi_options_OptionValidator';

    /**
     * Validates an option value.
     *
     * @param string $optionName The option name
     * @param mixed  $candidate  The candidate option value
     *
     * @return boolean True if the option name exists and the value supplied is valid. False otherwise.
    */
    function isValid($optionName, $candidate);

   /**
    * Gets the failure message of a name/value pair that has failed validation.
    *
    * @param string $optionName The option name
    * @param mixed  $candidate  The candidate option value
    *
    * @return mixed Null if the option passes validation, otherwise a string failure message.
    */
    function getProblemMessage($optionName, $candidate);
}
