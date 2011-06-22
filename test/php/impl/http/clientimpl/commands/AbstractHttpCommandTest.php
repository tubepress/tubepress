<?php

abstract class org_tubepress_impl_http_clientimpl_commands_AbstractHttpCommandTest extends TubePressUnitTest {

    private $_sut;
    private $_args;

    function setup()
    {
        parent::setUp();
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


    /**
     * @expectedException Exception
     */
    function testGet404()
    {
        $context = new stdClass();
        $context->url  = 'http://last.fm/thisdoesnotexist.txt';
        $context->args = $this->_args;

        $this->_sut->execute($context);
    }

    function testGet()
    {
        $context = new stdClass();
        $context->url  = 'http://last.fm/robots.txt';
        $context->args = $this->_args;

        $this->assertEquals($this->_robots(), $this->_sut->execute($context));
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

