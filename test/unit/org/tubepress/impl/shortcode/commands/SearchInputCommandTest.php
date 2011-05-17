<?php

require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/impl/shortcode/commands/SearchInputCommand.class.php';
require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/api/const/options/values/OutputValue.class.php';

class org_tubepress_impl_shortcode_commands_SearchInputCommandTest extends TubePressUnitTest
{
    private $_sut;

    function setup()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_shortcode_commands_SearchInputCommand();
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
        $this->setOptions(array(org_tubepress_api_const_options_names_Output::OUTPUT => org_tubepress_api_const_options_values_OutputValue::SEARCH_INPUT));
        $this->_sut->execute(new org_tubepress_impl_shortcode_ShortcodeHtmlGenerationChainContext());

    }

   
    function _expected()
    {
        return <<<EOT
<form method="get" action="fullurl">
	<fieldset class="tubepress_search">
		<input type="text" id="tubepress_search" name="tubepress_search" class="tubepress_text_input" value=""/>
		<button class="tubepress_button" title="Submit Search">search-input-button</button>
	</fieldset>
</form>

EOT;
    }

}

