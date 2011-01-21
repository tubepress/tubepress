<?php

require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/impl/gallery/filters/ThemeCss.class.php';

class org_tubepress_impl_gallery_filters_ThemeCssTest extends TubePressUnitTest
{
	private $_sut;
	private $_themeName;

	function setup()
	{
		$this->initFakeIoc();
		$this->_themeName = 'default';
		$this->_sut = new org_tubepress_impl_gallery_filters_ThemeCss();
	}

	function getMock($className)
	{
	    $mock = parent::getMock($className);
	    
	    if ($className === 'org_tubepress_api_theme_ThemeHandler') {
	        $mock->expects($this->once())
	             ->method('calculateCurrentThemeName')
	             ->will($this->returnValue($this->_themeName));
	    }
	    
	    return $mock;
	}

    function testSidebarTheme()
    {
        global $tubepress_base_url;
        $tubepress_base_url = 'foobee';
        $this->_themeName = 'sidebar';
        $result = $this->_sut->filter('hello', 'galleryid');
        $this->assertEquals($this->expected(), $result);
    }
	
   function testDefaultTheme()
    {
        $this->_themeName = 'default';
        $result = $this->_sut->filter('hello', 'galleryid');
        $this->assertEquals('hello', $result);
    }
	
	function testThemeCssNotFound()
	{
	    $result = $this->_sut->filter('hello', 'galleryid');
	    $this->assertEquals('hello', $result);
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
  jQuery(document).ready(function(){
    TubePressJS.loadCss("foobee/ui/themes/sidebar/style.css");
  });
</script>

EOT;
	}

}
?>
