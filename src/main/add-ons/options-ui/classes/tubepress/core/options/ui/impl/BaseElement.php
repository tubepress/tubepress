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
 *
 */
class tubepress_core_options_ui_impl_BaseElement implements tubepress_core_options_ui_api_ElementInterface
{
    /**
     * @var string The item ID.
     */
    private $_id;

    /**
     * @var string The display name (untranslated).
     */
    private $_untranslatedDisplayName;

    /**
     * @var tubepress_core_translation_api_TranslatorInterface
     */
    private $_translator;

    public function __construct($id,
                                tubepress_core_translation_api_TranslatorInterface $translator,
                                $untranslatedDisplayName = null)
    {
        if (!is_string($id)) {

            throw new InvalidArgumentException('Option page item IDs must be of type string');
        }

        if ($untranslatedDisplayName) {

            $this->setUntranslatedDisplayName($untranslatedDisplayName);
        }

        $this->_id         = $id;
        $this->_translator = $translator;
    }

    /**
     * @return string The name of the item that is displayed to the user.
     *
     * @api
     * @since 4.0.0
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
     *
     * @api
     * @since 4.0.0
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
        return $this->_translator->_($message);
    }
}