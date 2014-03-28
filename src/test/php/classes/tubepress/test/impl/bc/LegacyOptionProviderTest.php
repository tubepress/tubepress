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
 * @covers tubepress_impl_bc_LegacyOptionProvider<extended>
 */
class tubepress_test_impl_bc_LegacyOptionProviderTest extends tubepress_test_impl_options_AbstractOptionProviderTest
{
    public function testRegex()
    {
        $sut = $this->getSut();

        $sut->setValidValueRegex('option', '/xyz/');

        $this->ensureInvalidValueByRegex('option', 'abc');
    }

    protected function getMapOfOptionNamesToDefaultValues()
    {
        return array('option' => 'xyz');
    }

    protected function getMapOfOptionNamesToUntranslatedLabels()
    {
        return array('option' => 'label');
    }

    protected function getMapOfOptionNamesToUntranslatedDescriptions()
    {
        return array('option' => 'description');
    }

    protected function buildSut()
    {
        return new tubepress_impl_bc_LegacyOptionProvider(
            array('option' => 'label'),
            array('option' => 'description'),
            array('option' => 'xyz')
        );
    }
}