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
 * Base class for HTML fields.
 */
abstract class tubepress_impl_options_ui_fields_AbstractPluggableOptionsPageField implements tubepress_spi_options_ui_Field
{
    const TEMPLATE_VAR_NAME  = 'tubepress_impl_options_ui_fields_AbstractField__name';

    /**
     * Gets the title of this field, usually consumed by humans.
     *
     * @return string The title of this field. May be empty or null.
     */
    public final function getTitle()
    {
        return $this->_getMessage($this->getRawTitle());
    }

    /**
     * Gets the description of this field, usually consumed by humans.
     *
     * @return string The description of this field. May be empty or null.
     */
    public final function getDescription()
    {
        $originalDescription = $this->_getMessage($this->getRawDescription());

        return $this->getModifiedDescription($originalDescription);
    }

    /**
     * Override point.
     *
     * Allows subclasses to further modify the description for this field.
     *
     * @param $originalDescription string The original description as calculated by AbstractField.php.
     *
     * @return string The (possibly) modified description for this field.
     */
    protected function getModifiedDescription($originalDescription)
    {
        //override point
        return $originalDescription;
    }

    /**
     * Get the untranslated title of this field.
     *
     * @return string The untranslated title of this field.
     */
    protected abstract function getRawTitle();

    /**
     * Get the untranslated description of this field.
     *
     * @return string The untranslated description of this field.
     */
    protected abstract function getRawDescription();

    private function _getMessage($raw)
    {
        if ($raw == '') {

            return '';
        }

        $messageService = tubepress_impl_patterns_sl_ServiceLocator::getMessageService();

        return $messageService->_($raw);
    }
}