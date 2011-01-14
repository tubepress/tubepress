<?php

require_once dirname(__FILE__) . '/../../../../../../test/unit/TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/impl/factory/VimeoVideoFactory.class.php';

class org_tubepress_impl_factory_VimeoVideoFactoryTest extends TubePressUnitTest {
    
    private $_sut;
    private $_fakeFeed;
    
    function setUp() {
    	$this->initFakeIoc();
        $this->_sut = new org_tubepress_impl_factory_VimeoVideoFactory();
        $this->_fakeFeed = file_get_contents(dirname(__FILE__) . '/feeds/vimeo.txt');
    }

   function testGetMultiple()
   {
       $result = $this->_sut->feedToVideoArray($this->_fakeFeed, 100);
       $this->assertTrue(is_array($result));
       $this->assertEquals(8, count($result));
   }
   
   /**
    * @expectedException Exception
    */
   function testGetSingle()
   {
       $this->_sut->convertSingleVideo('bla');
   }
}

?>
