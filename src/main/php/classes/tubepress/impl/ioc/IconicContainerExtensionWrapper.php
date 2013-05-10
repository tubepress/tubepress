<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_impl_ioc_IconicContainerExtensionWrapper implements ehough_iconic_extension_ExtensionInterface
{
    /**
     * @var tubepress_api_ioc_ContainerExtensionInterface
     */
    private $_delegate;

    public function __construct(tubepress_api_ioc_ContainerExtensionInterface $extension)
    {
        $this->_delegate = $extension;
    }

    /**
     * Loads a specific configuration.
     *
     * @param array                          $config    An array of configuration values
     * @param ehough_iconic_ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws ehough_iconic_exception_InvalidArgumentException When provided tag is not defined in this extension
     *
     * @throws RuntimeException If not instance of ehough_iconic_ContainerInterface
     *
     * @api
     */
    public function load(array $config, ehough_iconic_ContainerBuilder $container)
    {
        if (!($container instanceof tubepress_api_ioc_ContainerInterface)) {

            throw new RuntimeException('Container extension expected instance of tubepress_api_ioc_ContainerInterface');
        }

        $this->_delegate->load($container);
    }

    /**
     * Returns the namespace to be used for this extension (XML namespace).
     *
     * @return string The XML namespace
     *
     * @api
     */
    public function getNamespace()
    {
        return null;
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     *
     * @api
     */
    public function getXsdValidationBasePath()
    {
        return null;
    }

    /**
     * Returns the recommended alias to use in XML.
     *
     * This alias is also the mandatory prefix to use when using YAML.
     *
     * @return string The alias
     *
     * @api
     */
    public function getAlias()
    {
        return strtolower(get_class($this->_delegate));
    }
}