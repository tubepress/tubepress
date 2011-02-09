<?php

require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/impl/filters/html/AjaxPagination.class.php';

class org_tubepress_impl_filters_html_AjaxPaginationTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		$this->initFakeIoc();
		$this->_sut = new org_tubepress_impl_filters_html_AjaxPagination();
	}

	function getMock($className)
	{
	    $mock = parent::getMock($className);
	    
	   
	    
	    return $mock;
	}

	function testAjaxPagination()
	{
	    $this->setOptions(array(org_tubepress_api_const_options_names_Display::AJAX_PAGINATION => true));
	    $result = $this->_sut->filter('hello', 'galleryid');
	    $this->assertEquals($this->expected(), $result);
	}
	
	function testNonAjaxPagination()
	{
	    $this->assertEquals('hello', $this->_sut->filter('hello', 2));
	}
	
	function testFilterNonString()
	{
	    $result = $this->_sut->filter(array(), 3);
	    $this->assertTrue(is_array($result));
	    $this->assertEquals(0, count($result));
	}
	
	function expected()
	{
	    return <<<EOT
hello<script type="text/javascript">
    function getUrlEncodedShortcodeForTubePressGallerygalleryid() {
        return "";
    }
    jQuery(document).ready(function(){
        TubePressAjaxPagination.init(galleryid);
    });
</script>

EOT;
	}

}
?>
