<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_app_impl_template_DelegatingEngine extends ehough_templating_DelegatingEngine
{
    /**
     * @var tubepress_platform_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_shouldLog;

    public function __construct(array $engines = array(), tubepress_platform_api_log_LoggerInterface $logger)
    {
        parent::__construct($engines);

        $this->_logger    = $logger;
        $this->_shouldLog = $this->_logger->isEnabled();
    }

    /**
     * Get an engine able to render the given template.
     *
     * @param string|ehough_templating_TemplateReferenceInterface $name A template name or a ehough_templating_TemplateReferenceInterface instance
     *
     * @return ehough_templating_EngineInterface The engine
     *
     * @throws RuntimeException if no engine able to work with the template is found
     *
     * @api
     */
    public function getEngine($name)
    {
        foreach ($this->engines as $engine) {

            if (!$engine->supports($name)) {

                if ($this->_shouldLog) {

                    $this->_logger->debug(sprintf('Template engine %s does not support template %s',

                        get_class($engine),
                        $name
                    ));
                }

                continue;
            }

            if (!$engine->exists($name)) {

                if ($this->_shouldLog) {

                    $this->_logger->debug(sprintf('Template engine %s cannot find template %s',

                        get_class($engine),
                        $name
                    ));
                }

                continue;
            }

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Template engine %s will handle template %s',

                    get_class($engine),
                    $name
                ));
            }

            return $engine;
        }

        throw new RuntimeException(sprintf('Template "%s" not found.', $name));
    }
}