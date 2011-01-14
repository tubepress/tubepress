<?php

require_once dirname(__FILE__) . '/../../../../../../test/unit/TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/impl/factory/DelegatingVideoFactory.class.php';

class org_tubepress_impl_factory_DelegatingVideoFactoryTest extends TubePressUnitTest {
    
    private $_sut;
    
    function setUp() {
    	$this->initFakeIoc();
        $this->_sut = new org_tubepress_impl_factory_DelegatingVideoFactory();
    }

   function testGetMultiple()
   {
       $this->_sut->feedToVideoArray('bla', 1);
   }
   
   function testGetSingle()
   {
       $this->_sut->convertSingleVideo('bla');
   }
}

?>
