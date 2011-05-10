<?php

require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/pagination/DiggStylePaginationService.class.php';

class org_tubepress_impl_pagination_DiggStylePaginationServiceTest extends TubePressUnitTest
{
    private $_prefix = '<div class="pagination"><span class="current">1</span><a rel=';
    
    private static $_currentPage = 1;

    public function setup()
    {
        $this->initFakeIoc();
    }

    public function getMock($className)
    {
        $mock = parent::getMock($className);
        switch ($className) {
            case 'org_tubepress_api_querystring_QueryStringService':
                $mock->expects($this->any())
                     ->method('getFullUrl')
                     ->will($this->returnValue('http://ehough.com'));
                $mock->expects($this->any())
                    ->method('getPageNum')
                    ->will($this->returnValue(self::$_currentPage));
                break;
            case 'org_tubepress_api_plugin_PluginManager':
                $mock->expects($this->once())
                     ->method('runFilters')
                     ->with($this->equalTo(org_tubepress_api_const_plugin_FilterPoint::HTML_PAGINATION), $this->anything())
                     ->will($this->returnCallback(array(new paginationModifier(), 'alter_paginationHtml')));
        }
        return $mock;
    }

    public function testAjax()
    {
        $this->setOptions(array(
            org_tubepress_api_const_options_names_Display::AJAX_PAGINATION => true,
            org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE => 4
        ));

        $this->_tester(<<<EOT
$this->_prefix"page=2">2</a><a rel="page=3">3</a><a rel="page=4">4</a><a rel="page=5">5</a>... <a rel="page=24">24</a><a rel="page=25">25</a><a rel="page=2">next</a></div>

EOT
);
    }
    
    public function testNoAjaxHighPage()
    {
        $this->setOptions(array(
            org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE => 4
        ));

    self::$_currentPage = 25;
        
        $this->_tester(<<<EOT
<div class="pagination"><a rel="nofollow" href="http://ehough.com?tubepress_page=24">prev</a><a rel="nofollow" href="http://ehough.com?tubepress_page=1">1</a><a rel="nofollow" href="http://ehough.com?tubepress_page=2">2</a>... <a rel="nofollow" href="http://ehough.com?tubepress_page=21">21</a> <a rel="nofollow" href="http://ehough.com?tubepress_page=22">22</a> <a rel="nofollow" href="http://ehough.com?tubepress_page=23">23</a> <a rel="nofollow" href="http://ehough.com?tubepress_page=24">24</a><span class="current">25</span><span class="disabled">next</span></div>

EOT
);
    }

    public function testNoAjaxMiddlePage()
    {
        $this->setOptions(array(
            org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE => 4
        ));

    self::$_currentPage = 12;
        
        $this->_tester(<<<EOT
<div class="pagination"><a rel="nofollow" href="http://ehough.com?tubepress_page=11">prev</a><a rel="nofollow" href="http://ehough.com?tubepress_page=1">1</a><a rel="nofollow" href="http://ehough.com?tubepress_page=2">2</a>... <a rel="nofollow" href="http://ehough.com?tubepress_page=11">11</a><span class="current">12</span> <a rel="nofollow" href="http://ehough.com?tubepress_page=13">13</a>... <a rel="nofollow" href="http://ehough.com?tubepress_page=24">24</a> <a rel="nofollow" href="http://ehough.com?tubepress_page=25">25</a><a rel="nofollow" href="http://ehough.com?tubepress_page=13">next</a></div>

EOT
);
    }

    public function testNoAjax()
    {
        $this->setOptions(array(
            org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE => 4
        ));

    self::$_currentPage = 1;
        
        $this->_tester(<<<EOT
$this->_prefix"nofollow" href="http://ehough.com?tubepress_page=2">2</a><a rel="nofollow" href="http://ehough.com?tubepress_page=3">3</a><a rel="nofollow" href="http://ehough.com?tubepress_page=4">4</a><a rel="nofollow" href="http://ehough.com?tubepress_page=5">5</a>... <a rel="nofollow" href="http://ehough.com?tubepress_page=24">24</a><a rel="nofollow" href="http://ehough.com?tubepress_page=25">25</a><a rel="nofollow" href="http://ehough.com?tubepress_page=2">next</a></div>

EOT
);
    }
    
    private function _tester($expected)
    {
        $sut = new org_tubepress_impl_pagination_DiggStylePaginationService();
        $this->assertEquals("$expected", $sut->getHtml(100));
    }
}