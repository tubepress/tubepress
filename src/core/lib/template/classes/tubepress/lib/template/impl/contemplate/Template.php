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
 * A template used to render strings.
 *
 * @package TubePress\Template
 */
class tubepress_lib_template_impl_contemplate_Template implements tubepress_lib_template_api_TemplateInterface
{
    /**
     * @var array
     */
    private $_context = array();

    /**
     * @var ehough_contemplate_api_Template
     */
    private $_delegate;

    /**
     * @var tubepress_platform_api_util_LangUtilsInterface
     */
    private $_langUtils;

    public function __construct(ehough_contemplate_api_Template       $delegate,
                                tubepress_platform_api_util_LangUtilsInterface $langUtils)
    {
        $this->_delegate  = $delegate;
        $this->_langUtils = $langUtils;
    }

    /**
     * @return array An associtiave array of template variables. May be empty but never null.
     */
    public function getVariables()
    {
        return $this->_context;
    }

    /**
     * Set the variables for this template.
     *
     * @param array $context An associative array of template variables.
     *
     * @throws InvalidArgumentException If a non-associative array is passed in.
     *
     * @return void
     */
    public function setVariables(array $context)
    {
        if (!$this->_langUtils->isAssociativeArray($context)) {

            throw new InvalidArgumentException('tubepress_lib_template_api_TemplateInterface::setContext() requires an associative array.');
        }

        $this->_context = $context;
    }

    /**
     * @return string The rendered template.
     */
    public function toString()
    {
        $this->_delegate->reset();

        foreach ($this->_context as $key => $value) {

            $this->_delegate->setVariable($key, $value);
        }

        return $this->_delegate->toString();
    }

    /**
     * @param string $name
     *
     * @return mixed The current value for the given variable name. May be null.
     */
    public function getVariable($name)
    {
        if (!isset($this->_context[$name])) {

            return null;
        }

        return $this->_context[$name];
    }

    /**
     * @param string $name
     *
     * @return bool True if the template has something set for the given variable, false otherwise.
     */
    public function hasVariable($name)
    {
        return isset($this->_context[$name]);
    }

    /**
     * @param string $name  The name of the variable to set.
     * @param mixed  $value The value of the variable.
     *
     * @return void
     */
    public function setVariable($name, $value)
    {
        $this->_context[$name] = $value;
    }
}