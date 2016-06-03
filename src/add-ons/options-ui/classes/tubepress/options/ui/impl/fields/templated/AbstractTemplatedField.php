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
 * Base class for HTML fields.
 */
abstract class tubepress_options_ui_impl_fields_templated_AbstractTemplatedField extends tubepress_options_ui_impl_fields_AbstractField
{
    /**
     * @var tubepress_api_template_TemplatingInterface
     */
    private $_templating;

    public function __construct($id,
                                tubepress_api_options_PersistenceInterface    $persistence,
                                tubepress_api_http_RequestParametersInterface $requestParams,
                                tubepress_api_template_TemplatingInterface    $templating,
                                $untranslatedDisplayName = null,
                                $untranslatedDescription = null)
    {
        parent::__construct(
            $id,
            $persistence,
            $requestParams,
            $untranslatedDisplayName,
            $untranslatedDescription
        );

        $this->_templating = $templating;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetHTML()
    {
        $templateName = $this->getTemplateName();
        $templateVars = $this->getTemplateVariables();

        return $this->_templating->renderTemplate($templateName, $templateVars);
    }

    /**
     * @return tubepress_api_template_TemplatingInterface
     */
    protected function getTemplating()
    {
        return $this->_templating;
    }

    /**
     * @return string The absolute path to the template for this field.
     */
    protected abstract function getTemplateName();

    /**
     * @return array An associative array of template variables for this field.
     */
    protected abstract function getTemplateVariables();
}
