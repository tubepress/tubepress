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
class tubepress_plugins_embedplus_EmbedPlusTest extends TubePressUnitTest
{
	private $_mockEventDispatcher;

	function setup()
	{
		$this->_mockEventDispatcher = Mockery::mock('ehough_tickertape_api_IEventDispatcher');

        tubepress_impl_patterns_ioc_KernelServiceLocator::setEventDispatcher($this->_mockEventDispatcher);
	}

	function testEmbedPlus()
    {
        $expected = array(

            array(tubepress_api_const_event_CoreEventNames::BOOT =>
            array(new tubepress_plugins_embedplus_impl_listeners_EmbedPlusPlayerRegistrar(), 'onBoot')),
        );

        $eventArray = array();

        foreach ($expected as $expect) {

            $eventName = array_keys($expect);
            $eventName = $eventName[0];

            if (! isset($eventArray[$eventName])) {

                $eventArray[$eventName] = array();
            }

            $eventArray[$eventName][] = $expect[$eventName];
        }

        foreach ($eventArray as $eventName => $callbacks) {

            $this->_mockEventDispatcher->shouldReceive('addListener')->times(count($callbacks))->with(

                $eventName, Mockery::on(function ($arr) use ($callbacks) {

                    foreach ($callbacks as $callback) {

                        if ($arr[0] instanceof $callback[0] && $arr[1] === $callback[1]) {

                            return true;
                        }
                    }

                    return false;
                }));
        }

        require TUBEPRESS_ROOT . '/src/main/php/plugins/addon/embedplus/EmbedPlusPlayer.php';

        $this->assertTrue(true);
    }
}