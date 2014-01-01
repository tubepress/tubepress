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
 * Base class for HTML fields.
 */
abstract class tubepress_impl_options_ui_fields_AbstractOptionsPageField extends tubepress_impl_options_ui_OptionsPageItem implements tubepress_spi_options_ui_OptionsPageFieldInterface
{
    /**
     * @var string Translated description.
     */
    private $_untranslatedDescription;

    public function __construct($id, $untranslatedDisplayName = null, $untranslatedDescription = null)
    {
        parent::__construct($id, $untranslatedDisplayName);

        if ($untranslatedDescription) {

            $this->setUntranslatedDescription($untranslatedDescription);
        }
    }

    /**
     * @return string The optional description of this element that is displayed to the user. May be empty or null.
     */
    public function getTranslatedDescription()
    {
        if (!isset($this->_untranslatedDescription)) {

            return '';
        }

        return $this->getModifiedDescription($this->translate($this->_untranslatedDescription));
    }

    public function setUntranslatedDescription($untranslatedDescription)
    {
        $this->_untranslatedDescription = $untranslatedDescription;
    }

    /**
     * @param string $name  The option name.
     * @param string $value The option value.
     *
     * @return string|null Null if stored successfully, otherwise a string error message.
     */
    protected function sendToStorage($name, $value)
    {
        $storageManager = tubepress_impl_patterns_sl_ServiceLocator::getOptionStorageManager();

        return $storageManager->queueForSave($name, $value);
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
}