<?php
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/impl/patterns/FilterManagerImpl.class.php';
require_once dirname(__FILE__) . '/../../../../../../test/unit/TubePressUnitTest.php';

class FilterManagerImplTestCallback
{
    public $adderOneInvocationCount = 0;
    public $adderTwoInvocationCount = 0;
    public $adderThreeValue = 0;
    
    function bailer()
    {
        throw new Exception('bla');
    }
    
    function adderOne($value)
    {
        $this->adderOneInvocationCount++;
        return $value + 1;
    }
    
    function adderTwo($value)
    {
        $this->adderTwoInvocationCount++;
        return $value + 1;
    }
    
    function adderThree($value1, $value2)
    {
        $this->adderThreeValue += $value2;
        return $value1;
    }
}

class org_tubepress_impl_patterns_FilterManagerImplTest extends TubePressUnitTest {

	private $_sut;
	private $_callback;

	function setUp()
	{
	    $this->initFakeIoc();
	    $this->_callback = new FilterManagerImplTestCallback();
		$this->_sut = new org_tubepress_impl_patterns_FilterManagerImpl();
	}
	
	function getMock($className)
	{
	    if ($className === 'FilterManagerImplTestCallback') {
            return $this->_callback;
        }
        
	    $mock = parent::getMock($className);
	
	    return $mock;
	}
	
	function testNoFiltersRegistered()
	{
	    $this->assertEquals('hi', $this->_sut->runFilters('fake', 'hi'));
	}

	function testMultipleArguments()
	{
	    $this->_sut->registerFilter('fakepoint', 'FilterManagerImplTestCallback', 'adderThree');
	    $result = $this->_sut->runFilters('fakepoint', 1, 500);
	    $result += $this->_sut->runFilters('fakepoint', 1, 800);
	    
	    $this->assertEquals(2, $result);
	    $this->assertEquals(1300, $this->_callback->adderThreeValue);
	}
	
    function testFilterBails()
    {
        $this->_sut->registerFilter('fakepoint', 'FilterManagerImplTestCallback', 'bailer');
        $this->_sut->registerFilter('fakepoint', 'FilterManagerImplTestCallback', 'adderTwo');
        
        $result = $this->_sut->runFilters('fakepoint', 1);
        
        $this->assertEquals(2, $result);
        $this->assertEquals(0, $this->_callback->adderOneInvocationCount);
        $this->assertEquals(1, $this->_callback->adderTwoInvocationCount);
    }
	
    function testDoubleFilter()
    {
        $this->_sut->registerFilter('fakepoint', 'FilterManagerImplTestCallback', 'adderOne');
        $this->_sut->registerFilter('fakepoint', 'FilterManagerImplTestCallback', 'adderTwo');
        
        $result = $this->_sut->runFilters('fakepoint', 1);
        
        $this->assertEquals(3, $result);
        $this->assertEquals(1, $this->_callback->adderOneInvocationCount);
        $this->assertEquals(1, $this->_callback->adderTwoInvocationCount);
    }
    
	function testSingleFilter()
	{
	    $this->_sut->registerFilter('fakepoint', 'FilterManagerImplTestCallback', 'adderOne');
	    $this->_sut->registerFilter('fakepoint2', 'FilterManagerImplTestCallback', 'adderTwo');
	    
	    $result = $this->_sut->runFilters('fakepoint', 1);
	    
	    $this->assertEquals(2, $result);
	    $this->assertEquals(1, $this->_callback->adderOneInvocationCount);
	    $this->assertEquals(0, $this->_callback->adderTwoInvocationCount);
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
	

	
}
?>
