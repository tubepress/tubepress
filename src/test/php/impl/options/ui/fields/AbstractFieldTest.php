<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
abstract class tubepress_impl_options_ui_fields_AbstractFieldTest extends PHPUnit_Framework_TestCase
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

