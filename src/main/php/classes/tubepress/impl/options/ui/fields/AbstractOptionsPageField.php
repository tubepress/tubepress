<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
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
    private $_description;

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
        if (!isset($this->_description)) {

            return '';
        }

        return $this->_description;
    }

    /**
     * @return string The widget HTML for this form element.
     */
    public function getWidgetHTML()
    {
        $templateBuilder   = tubepress_impl_patterns_sl_ServiceLocator::getTemplateBuilder();
        $eventDispatcher   = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();
        $templatePathEvent = new tubepress_spi_event_EventBase($this->getAbsolutePathToTemplate());

        $eventDispatcher->dispatch(tubepress_api_const_event_EventNames::OPTIONS_PAGE_TEMPLATE_LOAD, $templatePathEvent);

        $template          = $templateBuilder->getNewTemplateInstance($templatePathEvent->getSubject());
        $templateVariables = $this->getTemplateVariables();

        foreach ($templateVariables as $name => $value) {

            $template->setVariable($name, $value);
        }

        return $template->toString();
    }

    public function setUntranslatedDescription($untranslatedDescription)
    {
        $this->_description = $this->translate($untranslatedDescription);
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

        $result = $storageManager->set($name, $value);

        if ($result === true) {

            return null;
        }

        return $result;
    }

    /**
     * @return string The absolute path to the template for this field.
     */
    protected abstract function getAbsolutePathToTemplate();

    /**
     * @return array An associative array of template variables for this field.
     */
    protected abstract function getTemplateVariables();
}