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
class tubepress_dailymotion_impl_listeners_options_TransformListener
{
    /**
     * @var mixed
     */
    private $_transformer;

    /**
     * @var string
     */
    private $_errorMessage;

    /**
     * @var bool
     */
    private $_allowEmpty;

    public function __construct($transformer, $errorMessage, $allowEmpty)
    {
        $this->_transformer  = $transformer;
        $this->_errorMessage = (string) $errorMessage;
        $this->_allowEmpty   = (bool) $allowEmpty;
    }

    public function onOption(tubepress_api_event_EventInterface $event)
    {
        $incoming = (string) $event->getArgument('optionValue');
        $final    = $this->_transformer->transform($incoming);

        if (!$final && !$this->_allowEmpty) {

            $errors   = $event->getSubject();
            $errors[] = $this->_errorMessage;
            $event->setSubject($errors);

        } else {

            if ($incoming !== $final) {

                $event->setArgument('optionValue', $final);
            }
        }
    }
}
