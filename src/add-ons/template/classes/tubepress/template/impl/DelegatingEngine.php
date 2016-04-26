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
class tubepress_template_impl_DelegatingEngine extends \Symfony\Component\Templating\DelegatingEngine
{
    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_shouldLog;

    public function __construct(array $engines = array(), tubepress_api_log_LoggerInterface $logger)
    {
        parent::__construct($engines);

        $this->_logger    = $logger;
        $this->_shouldLog = $this->_logger->isEnabled();
    }

    /**
     * {@inheritdoc}
     */
    public function render($name, array $parameters = array())
    {
        $this->_logDebug(sprintf('Start render of template <code>%s</code>', $name));

        $toReturn = parent::render($name, $parameters);

        $this->_logDebug(sprintf('Finished rendering template <code>%s</code>', $name));

        return $toReturn;
    }

    /**
     * {@inheritdoc}
     */
    public function getEngine($name)
    {
        foreach ($this->engines as $engine) {

            if (!$engine->supports($name)) {

                if ($this->_shouldLog) {

                    $this->_logDebug(sprintf('Template engine <code>%s</code> does not support template <code>%s</code>',

                        get_class($engine),
                        $name
                    ));
                }

                continue;
            }

            if (!$engine->exists($name)) {

                if ($this->_shouldLog) {

                    $this->_logDebug(sprintf('Template engine <code>%s</code> cannot find template <code>%s</code>',

                        get_class($engine),
                        $name
                    ));
                }

                continue;
            }

            if ($this->_shouldLog) {

                $this->_logDebug(sprintf('Template engine <code>%s</code> will handle template <code>%s</code>',

                    get_class($engine),
                    $name
                ));
            }

            return $engine;
        }

        throw new RuntimeException(sprintf('Template <code>%s</code> not found.', $name));
    }

    private function _logDebug($msg)
    {
        $this->_logger->debug(sprintf('(Delegating Template Engine) %s', $msg));
    }
}
