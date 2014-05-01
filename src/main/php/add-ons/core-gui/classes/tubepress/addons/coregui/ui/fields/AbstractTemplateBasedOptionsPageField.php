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
abstract class tubepress_impl_options_ui_fields_AbstractTemplateBasedOptionsPageField extends tubepress_impl_options_ui_fields_AbstractOptionsPageField
{
    /**
     * @var tubepress_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    public function __construct($id,
                                tubepress_api_translation_TranslatorInterface $translator,
                                tubepress_api_options_PersistenceInterface $persistence,
                                tubepress_api_event_EventDispatcherInterface $eventDispatcher,
                                $untranslatedDisplayName = null,
                                $untranslatedDescription = null)
    {
        parent::__construct(
            $id,
            $translator,
            $persistence,
            $untranslatedDisplayName,
            $untranslatedDescription
        );

        $this->_eventDispatcher = $eventDispatcher;
    }

    /**
     * @return string The widget HTML for this form element.
     */
    public function getWidgetHTML()
    {
        $templateBuilder = tubepress_impl_patterns_sl_ServiceLocator::getTemplateBuilder();
        $template        = $templateBuilder->getNewTemplateInstance($this->getAbsolutePathToTemplate());
        $templateEvent   = new tubepress_spi_event_EventBase($template);
        $templateEvent->setArgument('field', $this);

        $templateVariables = $this->getTemplateVariables();

        foreach ($templateVariables as $name => $value) {

            $template->setVariable($name, $value);
        }

        $this->_eventDispatcher->dispatch(tubepress_api_const_event_EventNames::OPTIONS_PAGE_FIELDTEMPLATE, $templateEvent);

        return $template->toString();
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