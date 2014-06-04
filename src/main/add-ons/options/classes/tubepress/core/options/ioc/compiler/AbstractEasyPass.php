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
abstract class tubepress_core_options_ioc_compiler_AbstractEasyPass implements tubepress_api_ioc_CompilerPassInterface
{
    /**
     * @var tubepress_api_util_StringUtilsInterface
     */
    private $_stringUtils;

    /**
     * @var tubepress_api_util_LangUtilsInterface
     */
    private $_langUtils;

    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;
    
    /**
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder The primary service container builder.
     *
     * @api
     * @since 4.0.0
     */
    public final function process(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_stringUtils = $containerBuilder->get(tubepress_api_util_StringUtilsInterface::_);
        $this->_langUtils   = $containerBuilder->get(tubepress_api_util_LangUtilsInterface::_);
        $this->_logger      = $containerBuilder->get('tubepress_impl_log_BootLogger');

        $candidateParams = $this->_findCandidateParameters($containerBuilder);

        foreach ($candidateParams as $paramName => $paramValue) {

            if ($this->_isValid($paramName, $paramValue, $containerBuilder)) {

                $this->processParameter($paramName, $paramValue, $containerBuilder);
            }
        }
    }

    /**
     * @return string
     */
    protected abstract function getParameterPrefix();

    /**
     * @return string[]
     */
    protected abstract function getRequiredKeyNames();

    /**
     * @return string[]
     */
    protected abstract function getOptionalKeyNames();
    
    protected abstract function okToProcessParameter($paramName,
                                                     array $param,
                                                     tubepress_api_ioc_ContainerBuilderInterface $containerBuilder);

    protected abstract function processParameter($paramName,
                                                 array $paramValue,
                                                 tubepress_api_ioc_ContainerBuilderInterface $containerBuilder);

    /**
     * @return tubepress_api_util_LangUtilsInterface
     */
    protected function getLangUtils()
    {
        return $this->_langUtils;
    }

    /**
     * @return tubepress_api_log_LoggerInterface
     */
    protected function getLogger()
    {
        return $this->_logger;
    }

    /**
     * @return tubepress_api_util_StringUtilsInterface
     */
    protected function getStringUtils()
    {
        return $this->_stringUtils;
    }

    private function _isValid($paramName, $paramValue, tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        if (!is_array($paramValue)) {

            return $this->_falseWithError(sprintf('%s has a non-array for its value.', $paramName));
        }

        if (!$this->_langUtils->isSimpleArrayOfStrings(array_keys($paramValue))) {

            return $this->_falseWithError(sprintf('%s is not an array with string keys', $paramName));
        }

        $requiredKeyNames = $this->getRequiredKeyNames();
        $optionalKeyNames = $this->getOptionalKeyNames();
        $actualKeyNames   = array_keys($paramValue);

        if (count(array_diff($requiredKeyNames, $actualKeyNames)) !== 0) {

            return $this->_falseWithError(sprintf('%s is missing required attributes', $paramName));
        }

        foreach ($paramValue as $key => $value) {

            if (in_array($key, $requiredKeyNames)) {

                continue;
            }

            if (in_array($key, $optionalKeyNames)) {

                continue;
            }

            return $this->_falseWithError(sprintf('%s has unexpected attribute: %s', $paramName, $key));
        }

        $pass = $this->okToProcessParameter($paramName, $paramValue, $containerBuilder);

        if ($this->_logger->isEnabled()) {

            if ($pass) {

                $this->_logger->debug(sprintf('%s passes validation', $paramName));

            } else {

                $this->_logger->error(sprintf('%s did not pass validation', $paramName));
            }
        }

        return $pass;
    }
    
    private function _findCandidateParameters(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $allParamNames   = $containerBuilder->getParameterNames();
        $prefix          = strtolower($this->getParameterPrefix());
        $candidateParams = array();

        if ($this->_logger->isEnabled()) {

            $this->_logger->debug(sprintf('Looking for parameters prefixed with %s', $prefix));
        }

        foreach ($allParamNames as $paramName) {

            if ($this->_stringUtils->startsWith($paramName, $prefix) && $paramName !== $prefix) {

                $candidateParams[$paramName] = $containerBuilder->getParameter($paramName);
            }
        }

        if ($this->_logger->isEnabled()) {

            $this->_logger->debug(sprintf('Found %d parameters prefixed with %s', count($candidateParams), $prefix));
        }

        return $candidateParams;
    }

    private function _falseWithError($message)
    {
        if ($this->_logger->isEnabled()) {

            $this->_logger->debug($message);
        }

        return false;
    }
}