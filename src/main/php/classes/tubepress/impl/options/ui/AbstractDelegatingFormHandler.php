<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Handles nested submit handlers.
 */
abstract class tubepress_impl_options_ui_AbstractDelegatingFormHandler implements tubepress_spi_options_ui_FormHandler
{
    /**
     * Updates options from a keyed array.
     *
     * @throws InvalidArgumentException If the subclass returns non-tubepress_spi_options_ui_FormHandler instances.
     *
     * @return mixed Null if there was no problem handling the submission, otherwise an array
     *               of string failure messages.
     */
    public final function onSubmit()
    {
        $formHandlerInstances = $this->getDelegateFormHandlers();

        if (! is_array($formHandlerInstances)) {

            throw new InvalidArgumentException('Must pass an array of form handler instances');
        }

        $failures = array();

        foreach ($formHandlerInstances as $formHandlerInstance) {

            /** @noinspection PhpUndefinedMethodInspection */
            $result = $formHandlerInstance->onSubmit();

            if (is_array($result) && ! empty($result)) {

                $failures = array_merge($failures, $result);
            }
        }

        if (empty($failures)) {

            return null;
        }

        return $failures;
    }

    /**
     * Get the delegate form handlers.
     *
     * @return array An array of tubepress_spi_options_ui_FormHandler.
     */
    protected abstract function getDelegateFormHandlers();
}
