<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/api/provider/ProviderResult.class.php';

class org_tubepress_api_provider_ProviderResultTest extends TubePressUnitTest {
    
    private $_sut;
    
    function setUp()
    {
        $this->_sut = new org_tubepress_api_provider_ProviderResult();
    }

    /**
     * @expectedException Exception
     */
    function testSetNonArrayForVideos()
    {
        $this->_sut->setVideoArray('poo');
    }
    
    function testSetNonIntegralTotal()
    {
        $this->_sut->setEffectiveTotalResultCount('50.1');
        $this->assertEquals(50, $this->_sut->getEffectiveTotalResultCount());
    }
    
    /**
     * @expectedException Exception
     */
    function testSetNonNumericTotal()
    {
        $this->_sut->setEffectiveTotalResultCount('something bad');
    }
    
    /**
     * @expectedException Exception
     */
    function testSetNegativeTotal()
    {
        $this->_sut->setEffectiveTotalResultCount(-1501);
    }

    function testSetGetVideos()
    {
        $vids = array('hello');
        $this->_sut->setVideoArray($vids);
        $this->assertEquals($vids, $this->_sut->getVideoArray());
    }
    
    function testSetGetTotal()
    {
        $this->_sut->setEffectiveTotalResultCount(501);
        $this->assertEquals(501, $this->_sut->getEffectiveTotalResultCount());
    }
}

