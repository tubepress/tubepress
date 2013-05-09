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
class tubepress_test_impl_bootstrap_BootIntegrationTest extends tubepress_test_TubePressUnitTest
{
    public function onSetup()
    {
        $_GET['tubepress_debug'] = 'true';
    }

    public function onTearDown()
    {
        unset($_GET['tubepress_debug']);
    }

    public function testBoot()
    {$this->markTestSkipped();
    return;
        ob_start();

        require TUBEPRESS_ROOT . '/src/main/php/scripts/boot.php';

        $result = ob_get_contents();

        ob_end_clean();

        $this->assertNotContains('Caught exception while booting', $result, 'Caught exception while booting: ' . $result);
    }
}