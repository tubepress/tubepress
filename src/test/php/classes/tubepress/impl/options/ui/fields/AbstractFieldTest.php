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
abstract class tubepress_impl_options_ui_fields_AbstractFieldTest extends TubePressUnitTest
{
    private $_messageService;

    protected function doSetup(tubepress_spi_message_MessageService $messageService)
    {
        $this->_messageService = $messageService;

        $this->_messageService->shouldReceive('_')->andReturnUsing( function ($key) {
            return "<<message: $key>>";
        });
    }
}

