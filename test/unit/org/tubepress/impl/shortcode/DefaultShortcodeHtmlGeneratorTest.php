<?php
require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/shortcode/ShortcodeHtmlGeneratorChain.class.php';

class org_tubepress_impl_shortcode_ShortcodeHtmlGeneratorChainTest extends TubePressUnitTest {

    private $_sut;
    private $_page;
    private $_result;
    
    function setup()
    {
        $this->_page = 1;
        parent::setUp();
        $this->_sut = new org_tubepress_impl_shortcode_ShortcodeHtmlGeneratorChain();
    }

    function getMock($className)
    {
        $mock = parent::getMock($className);

        if ($className === 'org_tubepress_api_querystring_QueryStringService') {
            $mock->expects($this->once())
                 ->method('getPageNum')
                 ->will($this->returnValue($this->_page));
        }
        if ($className === 'org_tubepress_api_patterns_cor_Chain') {
            $mock->expects($this->once())
                 ->method('execute')
                 ->with($this->anything(), $this->equalTo(array(
                     'org_tubepress_impl_shortcode_commands_SearchInputCommand',
                     'org_tubepress_impl_shortcode_commands_SearchOutputCommand',
                     'org_tubepress_impl_shortcode_commands_SingleVideoCommand',
                     'org_tubepress_impl_shortcode_commands_SoloPlayerCommand',
                     'org_tubepress_impl_shortcode_commands_ThumbGalleryCommand'
                 )))
                 ->will($this->returnCallback(array($this, 'fake')));
        }

        return $mock;
    }

    function testGetHtml()
    {
        $this->_sut->getHtmlForShortcode('');
        $this->assertEquals('boop', $this->_result);
    }

    function fake()
    {
        $this->_result = 'boop';
        return true;
    }
}


