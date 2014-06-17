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
abstract class tubepress_core_options_ui_impl_fields_AbstractTemplateBasedOptionsPageField extends tubepress_core_options_ui_impl_fields_AbstractOptionsPageField
{
    /**
     * @var tubepress_core_event_api_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_core_template_api_TemplateFactoryInterface
     */
    private $_templateFactory;

    public function __construct($id,
                                tubepress_core_translation_api_TranslatorInterface   $translator,
                                tubepress_core_options_api_PersistenceInterface      $persistence,
                                tubepress_core_http_api_RequestParametersInterface   $requestParams,
                                tubepress_core_event_api_EventDispatcherInterface    $eventDispatcher,
                                tubepress_core_template_api_TemplateFactoryInterface $templateFactory,
                                $untranslatedDisplayName = null,
                                $untranslatedDescription = null)
    {
        parent::__construct(
            $id,
            $translator,
            $persistence,
            $requestParams,
            $untranslatedDisplayName,
            $untranslatedDescription
        );

        $this->_templateFactory = $templateFactory;
        $this->_eventDispatcher = $eventDispatcher;
    }

    /**
     * @return string The widget HTML for this form element.
     */
    public function getWidgetHTML()
    {
        $template          = $this->_templateFactory->fromFilesystem(array($this->getAbsolutePathToTemplate()));
        $templateVariables = $this->getTemplateVariables();

        foreach ($templateVariables as $name => $value) {

            $template->setVariable($name, $value);
        }

        $templateEvent = $this->_eventDispatcher->newEventInstance($template);
        $templateEvent->setArgument('field', $this);
        $this->_eventDispatcher->dispatch(tubepress_core_options_ui_api_Constants::EVENT_OPTIONS_UI_FIELD_TEMPLATE, $templateEvent);

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