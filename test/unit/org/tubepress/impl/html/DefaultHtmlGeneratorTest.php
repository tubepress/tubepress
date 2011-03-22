<?php
require_once dirname(__FILE__) . '/../../../../../../test/unit/TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/html/DefaultHtmlGenerator.class.php';

class org_tubepress_impl_html_DefaultHtmlGeneratorTest extends TubePressUnitTest {

    private $_sut;
    private $_page;
    
    function setup()
    {
        global $tubepress_base_url;
        $tubepress_base_url = 'tubepress_base_url';
        $this->_page = 1;
        $this->initFakeIoc();
        $this->_sut = new org_tubepress_impl_html_DefaultHtmlGenerator();
    }

    function getMock($className)
    {
        $mock = parent::getMock($className);

        if ($className === 'org_tubepress_api_querystring_QueryStringService') {
            $mock->expects($this->once())
                 ->method('getPageNum')
                 ->will($this->returnValue($this->_page));
        }
        if ($className === 'org_tubepress_api_patterns_StrategyManager') {
            $mock->expects($this->once())
                 ->method('executeStrategy')
                 ->with($this->equalTo(array(
                     'org_tubepress_impl_html_strategies_SearchInputStrategy',
                     'org_tubepress_impl_html_strategies_SearchOutputStrategy',
                     'org_tubepress_impl_html_strategies_SingleVideoStrategy',
                     'org_tubepress_impl_html_strategies_SoloPlayerStrategy',
                     'org_tubepress_impl_html_strategies_ThumbGalleryStrategy'
                 )))
                 ->will($this->returnValue('boop'));
        }

        return $mock;
    }

    function testGetHtml()
    {
    $result = $this->_sut->getHtmlForShortcode('');
        $this->assertEquals('boop', $result);
    }

    function testGetHeadMetaStringPageTwo()
    {
        $this->_page = 2;
        $result = $this->_sut->getHeadMetaString();
        $this->assertEquals('<meta name="robots" content="noindex, nofollow" />', $result);
    }

    function testGetHeadMetaStringPageOne()
    {
        $result = $this->_sut->getHeadMetaString();
        $this->assertEquals('', $result);
    }

    function testGetHeadTubePressCssIncludeString()
    {
        $result = $this->_sut->getHeadTubePressCssIncludeString();
        $this->assertEquals('<link rel="stylesheet" href="tubepress_base_url/sys/ui/themes/default/style.css" type="text/css" />', $result);
    }

    function testGetHeadTubePressJsIncludeString()
    {
        $result = $this->_sut->getHeadTubePressJsIncludeString();
        $this->assertEquals('<script type="text/javascript" src="tubepress_base_url/sys/ui/static/js/tubepress.js"></script>', $result);
    }

    function testGetHeadInlineJavaScriptString()
    {
        $result = $this->_sut->getHeadInlineJavaScriptString();
        $this->assertEquals('<script type="text/javascript">function getTubePressBaseUrl(){return "tubepress_base_url";}</script>', $result);
    }

    function testGetHeadJqueryIncludeString()
    {
        $result = $this->_sut->getHeadJqueryIncludeString();
        $this->assertEquals('<script type="text/javascript" src="tubepress_base_url/sys/ui/static/js/jquery-1.5.1.min.js"></script>', $result);
    }
}
?>

