<?php
/**
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_test_integration_BasicsTest extends tubepress_test_integration_IntegrationTest
{
    public function testBootsWithNoErrors()
    {
        $result = $this->request('GET', 'index.php', array(
            'tubepress_clear_system_cache' => 'true'
        ), true)->getContent();

        $this->assertTrue(strpos($result, 'We cannot boot from cache. Will perform a full boot instead.') !== false, $result);
        $this->_assertNoBootErrors($result);

        $result = $this->request('GET', 'index.php', array(), true)->getContent();

        $this->assertTrue(strpos($result, 'We cannot boot from cache. Will perform a full boot instead.') === false, $result);
        $this->assertTrue(strpos($result, 'Done rehydrating cached service container.') !== false, $result);
        $this->_assertNoBootErrors($result);
    }

    public function testCssAndJs()
    {
        $result = $this->request('GET', 'index.php', array())->getContent();

        $this->assertNotEmpty($result);

        $crawler = new \Symfony\Component\DomCrawler\Crawler($result);

        $this->assertCount(1, $crawler->filter('html'));
        $this->assertCount(1, $crawler->filter('html > head > link[rel="stylesheet"]'));
        $this->assertCount(1, $crawler->filter('html > body > script[type="text/javascript"][src="/tubepress/web/js/tubepress.js"]'));
    }

    private function _assertNoBootErrors($html)
    {
        $this->assertTrue(strpos($html, 'ERROR') === false, $html);
        $this->assertRegExp('/Boot completed in [0-9]+\.[0-9]+ milliseconds/', $html);
    }
}