<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
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
