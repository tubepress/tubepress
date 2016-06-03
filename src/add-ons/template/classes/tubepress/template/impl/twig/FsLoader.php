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
class tubepress_template_impl_twig_FsLoader extends Twig_Loader_Filesystem
{
    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_shouldLog;

    public function __construct(tubepress_api_log_LoggerInterface $logger, array $paths)
    {
        parent::__construct($paths);

        $this->_logger    = $logger;
        $this->_shouldLog = $logger->isEnabled();
    }

    protected function normalizeName($name)
    {
        if (strpos($name, '::') !== false) {

            $exploded = explode('::', $name);

            if (count($exploded) === 2) {

                $name = $exploded[1];
            }
        }

        return parent::normalizeName($name);
    }

    public function getSource($name)
    {
        $source = file_get_contents($this->findTemplate($name));

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Template source for <code>%s</code> was found on the filesystem at <code>%s</code>',

                $name,
                $this->findTemplate($name)
            ));
        }

        return $source;
    }
}
