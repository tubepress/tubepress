<?php
require_once dirname(__FILE__) . '/../../../../../../../../test/unit/TubePressUnitTest.php';

abstract class org_tubepress_impl_http_clientimpl_strategies_AbstractHttpStrategyTest extends TubePressUnitTest {

    private $_sut;
    private $_args;
    
    function setup()
    {
        $this->initFakeIoc();
        $this->_sut = $this->_getSutInstance();
        $this->_args = array(
            'method'       => 'GET',
            'timeout'      => 5,
            'httpversion' => '1.0',
            'user-agent'   => 'TubePress; http://tubepress.org',
            'headers'      => array(),
            'cookies'      => array(),
            'body'         => null,
            'compress'     => false,
            'decompress'   => true,
            'sslverify'   => true
        );
    }

    function testCanHandle()
    {
        $this->assertTrue($this->_sut->canHandle('http://last.fm/thisdoesnotexist.txt', $this->_args));
    }    

    /**
     * @expectedException Exception
     */
    function testGet404()
    {
        $this->_sut->start();
        $this->_sut->execute('http://last.fm/thisdoesnotexist.txt', $this->_args);
    }

    function testGet()
    {
        $this->_sut->start();

        $result = $this->_sut->execute('http://last.fm/robots.txt', $this->_args);

        $this->assertEquals($this->_robots(), $result);
    }

    function _robots()
    {
        return <<<EOT
User-Agent: *
Disallow: /music?
Disallow: /widgets/radio?
Disallow: /show_ads.php

Disallow: /affiliate/
Disallow: /affiliate_redirect.php
Disallow: /affiliate_sendto.php
Disallow: /affiliatelink.php
Disallow: /campaignlink.php
Disallow: /delivery.php

Disallow: /music/+noredirect/

Disallow: /harming/humans
Disallow: /ignoring/human/orders
Disallow: /harm/to/self

Allow: /

EOT;

    }

    protected abstract function _getSutInstance();
}

