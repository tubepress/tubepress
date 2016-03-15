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

class tubepress_wordpress_impl_options_ui_fields_WpMultiSelectField extends tubepress_options_ui_impl_fields_templated_multi_AbstractMultiSelectField
{
    /**
     * @var tubepress_wordpress_impl_wp_WpFunctions
     */
    private $_wpFunctions;

    /**
     * @var tubepress_api_options_AcceptableValuesInterface
     */
    private $_acceptableValues;

    public function __construct($id, $untranslatedDisplayName, $untranslatedDescription,
                                tubepress_api_options_PersistenceInterface      $persistence,
                                tubepress_api_http_RequestParametersInterface   $requestParams,
                                tubepress_api_template_TemplatingInterface      $templating,
                                tubepress_wordpress_impl_wp_WpFunctions         $wpFunctions,
                                tubepress_api_options_AcceptableValuesInterface $acceptableValues)
    {
        parent::__construct(
            $id,
            $persistence,
            $requestParams,
            $templating,
            $untranslatedDisplayName,
            $untranslatedDescription
        );

        $this->_wpFunctions      = $wpFunctions;
        $this->_acceptableValues = $acceptableValues;
    }

    /**
     * @return string[] An array of currently selected values, which may be empty.
     */
    protected function getCurrentlySelectedValues()
    {
        return explode(',', $this->getOptionPersistence()->fetch($this->getId()));
    }

    /**
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     */
    protected function onSubmitAllMissing()
    {
        $this->getOptionPersistence()->queueForSave(
            $this->getId(),
            null
        );
    }

    /**
     * @return array An associative array of value => untranslated display names
     */
    protected function getUngroupedChoicesArray()
    {
        return $this->_acceptableValues->getAcceptableValues($this->getId());
    }

    /**
     * @param array $values The incoming values for this field.
     *
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     */
    protected function onSubmitMixed(array $values)
    {
        $toSave = implode(',', $values);

        $this->getOptionPersistence()->queueForSave($this->getId(), $toSave);
    }

    /**
     * Gets whether or not this field is TubePress Pro only.
     *
     * @return boolean True if this field is TubePress Pro only. False otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function isProOnly()
    {
        return true;
    }
}