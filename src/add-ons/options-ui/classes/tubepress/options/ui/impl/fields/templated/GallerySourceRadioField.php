<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Displays a single radio input.
 */
class tubepress_options_ui_impl_fields_templated_GallerySourceRadioField extends tubepress_options_ui_impl_fields_templated_AbstractTemplatedField implements tubepress_api_options_ui_MultiSourceFieldInterface
{
    /**
     * @var tubepress_api_options_ui_FieldInterface
     */
    private $_additionalField;

    /**
     * @var string
     */
    private $_multiSourcePrefix = '';

    /**
     * @var string
     */
    private $_modeName;

    public function __construct($modeName,
                                tubepress_api_options_PersistenceInterface    $persistence,
                                tubepress_api_http_RequestParametersInterface $requestParams,
                                tubepress_api_template_TemplatingInterface    $templating,
                                tubepress_api_options_ui_FieldInterface       $additionalField = null)
    {
        parent::__construct(

            $modeName,
            $persistence,
            $requestParams,
            $templating
        );

        $this->_additionalField = $additionalField;
        $this->_modeName        = $modeName;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->_multiSourcePrefix . parent::getId();
    }

    /**
     * {@inheritdoc}
     */
    protected function getTemplateName()
    {
        return 'options-ui/fields/gallery-source-radio';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTemplateVariables()
    {
        $currentMode = $this->getOptionPersistence()->fetch(tubepress_api_options_Names::GALLERY_SOURCE);

        return array(

            'id'                        => $this->getId(),
            'modeName'                  => $this->_modeName,
            'currentMode'               => $currentMode,
            'additionalFieldWidgetHtml' => isset($this->_additionalField) ? $this->_additionalField->getWidgetHTML() : '',
            'prefix'                    => $this->_multiSourcePrefix,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function onSubmit()
    {
        if (!isset($this->_additionalField)) {

            return null;
        }

        return $this->_additionalField->onSubmit();
    }

    /**
     * {@inheritdoc}
     */
    public function getUntranslatedDescription()
    {
        if ($this->_additionalField) {

            return $this->_additionalField->getUntranslatedDescription();
        }

        return parent::getUntranslatedDescription();
    }

    /**
     * {@inheritdoc}
     */
    public function getUntranslatedDisplayName()
    {
        if ($this->_additionalField) {

            return $this->_additionalField->getUntranslatedDisplayName();
        }

        return parent::getUntranslatedDisplayName();
    }

    /**
     * {@inheritdoc}
     */
    public function isProOnly()
    {
        return false;
    }

    public function setMultiSourcePrefix($prefix)
    {
        $this->_multiSourcePrefix = $prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function cloneForMultiSource($prefix, tubepress_api_options_PersistenceInterface $persistence)
    {
        $httpRequestParams = $this->getHttpRequestParameters();
        $templating        = $this->getTemplating();
        $additionalField   = null;

        if ($this->_additionalField && $this->_additionalField instanceof tubepress_api_options_ui_MultiSourceFieldInterface) {

            /*
             * @var tubepress_api_options_ui_MultiSourceFieldInterface
             */
            $temp = $this->_additionalField;

            $additionalField = $temp->cloneForMultiSource($prefix, $persistence);
        }

        $toReturn = new self($this->getId(), $persistence, $httpRequestParams, $templating, $additionalField);

        $toReturn->setMultiSourcePrefix($prefix);

        return $toReturn;
    }
}
