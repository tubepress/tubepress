<?php

require_once dirname(__FILE__) . '/../../../../sys/classes/org/tubepress/impl/html/DefaultHeadHtmlGenerator.class.php';

class org_tubepress_impl_html_DefaultHeadHtmlGeneratorTest extends TubePressUnitTest
{
    private $_sut;

    function setUp()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_html_DefaultHeadHtmlGenerator();
    }

    function testJqueryInclude()
    {
        $this->assertEquals('<script type="text/javascript" src="<tubepress_base_url>/sys/ui/static/js/jquery-1.6.min.js"></script>', $this->_sut->getHeadJqueryInclusion());
    }

    function testJsInclude()
    {
        $this->assertEquals('<script type="text/javascript" src="<tubepress_base_url>/sys/ui/static/js/tubepress.js"></script>', $this->_sut->getHeadJsIncludeString());
    }

    function testInlineJs()
    {
        $this->assertEquals('<script type="text/javascript">function getTubePressBaseUrl(){return "<tubepress_base_url>";}</script>', $this->_sut->getHeadInlineJs());
    }

    function testCss()
    {
        $this->assertEquals('<link rel="stylesheet" href="<tubepress_base_url>/sys/ui/themes/default/style.css" type="text/css" />', $this->_sut->getHeadCssIncludeString());
    }

	function testHeadMetaPageOne()
	{
	    $ioc  = org_tubepress_impl_ioc_IocContainer::getInstance();

	    $qss  = $ioc->get('org_tubepress_api_querystring_QueryStringService');
        $qss->shouldReceive('getPageNum')->once()->andReturn(1);

	    $this->assertEquals('', $this->_sut->getHeadHtmlMeta());
	}

    function testHeadMetaPageTwo()
	{
	    $ioc  = org_tubepress_impl_ioc_IocContainer::getInstance();

	    $qss  = $ioc->get('org_tubepress_api_querystring_QueryStringService');
        $qss->shouldReceive('getPageNum')->once()->andReturn(2);

	    $this->assertEquals('<meta name="robots" content="noindex, nofollow" />', $this->_sut->getHeadHtmlMeta());
	}
}
