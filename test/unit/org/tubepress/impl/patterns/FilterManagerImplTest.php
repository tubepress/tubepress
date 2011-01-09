<?php
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/impl/patterns/FilterManagerImpl.class.php';
require_once dirname(__FILE__) . '/../../../../../../test/unit/TubePressUnitTest.php';

class org_tubepress_impl_patterns_FilterManagerImplTest extends TubePressUnitTest {

	private $_sut;
	private $_adderOneInvocationCount = 0;
	private $_adderTwoInvocationCount = 0;

	function setUp()
	{
	    $this->_adderOneInvocationCount = 0;
	    $this->_adderTwoInvocationCount = 0;
		$this->_sut = new org_tubepress_impl_patterns_FilterManagerImpl();
	}
	
	function testNoFiltersRegistered()
	{
	    $this->assertEquals('hi', $this->_sut->runFilters('fake', 'hi'));
	}

   function testFilterBails()
    {
        $this->_sut->registerFilter('fakepoint', array($this, 'bailer'));
        $this->_sut->registerFilter('fakepoint', array($this, 'adderTwo'));
        
        $result = $this->_sut->runFilters('fakepoint', 1);
        
        $this->assertEquals(2, $result);
        $this->assertEquals(0, $this->_adderOneInvocationCount);
        $this->assertEquals(1, $this->_adderTwoInvocationCount);
    }
	
    function testDoubleFilter()
    {
        $this->_sut->registerFilter('fakepoint', array($this, 'adderOne'));
        $this->_sut->registerFilter('fakepoint', array($this, 'adderTwo'));
        
        $result = $this->_sut->runFilters('fakepoint', 1);
        
        $this->assertEquals(3, $result);
        $this->assertEquals(1, $this->_adderOneInvocationCount);
        $this->assertEquals(1, $this->_adderTwoInvocationCount);
    }
    
	function testSingleFilter()
	{
	    $this->_sut->registerFilter('fakepoint', array($this, 'adderOne'));
	    $this->_sut->registerFilter('fakepoint2', array($this, 'adderTwo'));
	    
	    $result = $this->_sut->runFilters('fakepoint', 1);
	    
	    $this->assertEquals(2, $result);
	    $this->assertEquals(1, $this->_adderOneInvocationCount);
	    $this->assertEquals(0, $this->_adderTwoInvocationCount);
	}
	
    /**
     * @expectedException Exception
     */
    function testRegisterBadExecutionPoint()
    {
        $this->_sut->registerFilter(1, array($this, 'setUp'));
    }
	
	/**
     * @expectedException Exception
     */
	function testRegisterNonCallback()
	{
	    $this->_sut->registerFilter('fake', 'nothing');
	}
	
	function bailer()
	{
	    throw new Exception('bla');
	}
	
	function adderOne($value)
	{
	    $this->_adderOneInvocationCount++;
	    return $value + 1;
	}
	
	function adderTwo($value)
	{
	    $this->_adderTwoInvocationCount++;
	    return $value + 1;
	}
	
}
?>
