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
 * Generates the "meat" of the options form (in the form of tabs).
 */
class tubepress_impl_options_ui_OptionsPageItem implements tubepress_spi_options_ui_OptionsPageItemInterface
{
    /**
     * @var string The item ID.
     */
    private $_id;

    /**
     * @var string The display name (untranslated).
     */
    private $_untranslatedDisplayName;

    public function __construct($id, $untranslatedDisplayName = null)
    {
        if (!is_string($id)) {

            throw new InvalidArgumentException('Option page item IDs must be of type string');
        }

        if ($untranslatedDisplayName) {

            $this->setUntranslatedDisplayName($untranslatedDisplayName);
        }

        $this->_id = $id;
    }

    /**
     * @return string The name of the item that is displayed to the user.
     */
    public function getTranslatedDisplayName()
    {
        if (!isset($this->_untranslatedDisplayName)) {

            return '';
        }

        return $this->translate($this->_untranslatedDisplayName);
    }

    /**
     * @return string The page-unique identifier for this item.
     */
    public function getId()
    {
        return $this->_id;
    }

    public function setUntranslatedDisplayName($untranslatedDisplayName)
    {
        $this->_untranslatedDisplayName = $untranslatedDisplayName;
    }

    protected function translate($message)
    {
        $messageService = tubepress_impl_patterns_sl_ServiceLocator::getMessageService();

        return $messageService->_($message);
    }
}