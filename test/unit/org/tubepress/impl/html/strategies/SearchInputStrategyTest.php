<?php

require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/impl/html/strategies/SearchInputStrategy.class.php';
require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/api/const/options/values/OutputValue.class.php';

class org_tubepress_impl_html_strategies_SearchInputStrategyTest extends TubePressUnitTest
{
    private $_sut;

    function setup()
    {
        $this->initFakeIoc();
        $this->_sut = new org_tubepress_impl_html_strategies_SearchInputStrategy();
    }

    function getMock($className)
    {
        $mock = parent::getMock($className);
        
        if ($className === 'org_tubepress_api_querystring_QueryStringService') {
            $mock->expects($this->any())
                 ->method('getFullUrl')
                 ->will($this->returnValue('fullurl'));
        }       

        return $mock;
    }
    
    function testExecute()
    {
        $this->_sut->start();
        $this->assertEquals($this->_expected(), $this->_sut->execute());
    }
    
    function testCanHandleTrue()
    {
        $this->setOptions(array(org_tubepress_api_const_options_names_Output::OUTPUT => org_tubepress_api_const_options_values_OutputValue::SEARCH_INPUT));
        $this->_sut->start();
        $this->assertTrue($this->_sut->canHandle());
        $this->_sut->stop();
    }
    
    function testCanHandleFalse()
    {
        $this->_sut->start();
        $this->assertFalse($this->_sut->canHandle());
    }
   
    function _expected()
    {
        return <<<EOT
<form method="get" action="fullurl">
	<fieldset class="tubepress_search">
		<input type="text" class="tubepress_box"/>
		<button class="tubepress_button" title="Submit Search">Search</button>
	</fieldset>
</form>

EOT;
    }

}
?>
