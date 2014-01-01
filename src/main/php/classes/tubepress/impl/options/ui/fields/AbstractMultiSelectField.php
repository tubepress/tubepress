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
 * Base class for multi-select fields.
 */
abstract class tubepress_impl_options_ui_fields_AbstractMultiSelectField extends tubepress_impl_options_ui_fields_AbstractTemplateBasedOptionsPageField
{
    /**
     * @return string The absolute path to the template for this field.
     */
    protected function getAbsolutePathToTemplate()
    {
        return TUBEPRESS_ROOT . '/src/main/resources/options-gui/field-templates/multiselect.tpl.php';
    }

    /**
     * @return array An associative array of template variables for this field.
     */
    protected function getTemplateVariables()
    {
        return array(

            'id'                      => $this->getId(),
            'currentlySelectedValues' => $this->getCurrentlySelectedValues(),
            'ungroupedChoices'        => $this->getUngroupedTranslatedChoicesArray(),
            'groupedChoices'          => $this->getGroupedTranslatedChoicesArray(),
            "selectText"              => 'select ...',     //>(translatable)<
        );
    }

    /**
     * Invoked when the element is submitted by the user.
     *
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     */
    public function onSubmit()
    {
        $hrps = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
        $id   = $this->getId();

        if (! $hrps->hasParam($id)) {

            return $this->onSubmitAllMissing();
        }

        $vals = $hrps->getParamValue($id);

        if (! is_array($vals)) {

            /* this should never happen. */
            return null;
        }

        return $this->onSubmitMixed($vals);
    }

    /**
     * @return string[] An array of currently selected values, which may be empty.
     */
    protected abstract function getCurrentlySelectedValues();

    /**
     * @return array An associative array of value => translated display names
     */
    protected abstract function getUngroupedTranslatedChoicesArray();

    /**
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     */
    protected abstract function onSubmitAllMissing();

    /**
     * @param array $values The incoming values for this field.
     *
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     */
    protected abstract function onSubmitMixed(array $values);

    /**
     * @return array An associative array of translated group names to associative array of
     *               value => translated display names
     */
    protected function getGroupedTranslatedChoicesArray()
    {
        //override point
        return array();
    }
}