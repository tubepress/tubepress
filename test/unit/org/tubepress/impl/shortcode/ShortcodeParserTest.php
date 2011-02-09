<?php

require_once dirname(__FILE__) . '/../../../../../../test/unit/TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/impl/shortcode/SimpleShortcodeParser.class.php';

class org_tubepress_api_shortcode_ShortcodeParserTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		$this->initFakeIoc();
		$this->_sut = new org_tubepress_impl_shortcode_SimpleShortcodeParser();
		org_tubepress_impl_log_Log::setEnabled(false, array());
	}

	public function getMock($className)
	{
		$mock = parent::getMock($className);
		if ($className == 'org_tubepress_api_options_OptionValidator') {
			$mock->expects($this->any())
				->method('validate')
				->will($this->returnValue(true));
		}
		return $mock;
	}

	function testMixedCommasWithAllSortsOfQuotes()
	{
		$this->setOptions(array(org_tubepress_api_const_options_names_Advanced::KEYWORD => 'butters'));
		$tpom = org_tubepress_impl_ioc_IocContainer::getInstance()->get('org_tubepress_api_options_OptionsManager');
		
		$expectedOptions = array(org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::PLAYLIST,
			org_tubepress_api_const_options_names_Output::PLAYLIST_VALUE => 'foobar',
			org_tubepress_api_const_options_names_Meta::AUTHOR => false,
			org_tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP => 200,
			org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE => 3);
							
		$this->_sut->parse('[butters mode=&#8216playlist&#8217  , playlistValue=&#8242;foobar&#8242; ,author="false", resultCountCap=\'200\' resultsPerPage=3]');

		foreach ($expectedOptions as $optionName => $optionValue) {
			$this->assertEquals($optionValue, $tpom->get($optionName));
		}
	}

	function testNoCommasWithAllSortsOfQuotes()
	{
		$this->setOptions(array(org_tubepress_api_const_options_names_Advanced::KEYWORD => 'butters'));
		$tpom = org_tubepress_impl_ioc_IocContainer::getInstance()->get('org_tubepress_api_options_OptionsManager');
		
		$expectedOptions = array(org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::PLAYLIST,
			org_tubepress_api_const_options_names_Output::PLAYLIST_VALUE => 'foobar',
			org_tubepress_api_const_options_names_Meta::AUTHOR => true,
			org_tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP => 200,
			org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE => 3);
							
		$this->_sut->parse('[butters mode=&#8216playlist&#8217 playlistValue=&#8242;foobar&#8242; author="true" resultCountCap=\'200\' resultsPerPage=3]');
		
		foreach ($expectedOptions as $optionName => $optionValue) {
			$this->assertEquals($optionValue, $tpom->get($optionName));
		}
	}
	
	function testCommasWithAllSortsOfQuotes()
	{
		$this->setOptions(array(org_tubepress_api_const_options_names_Advanced::KEYWORD => 'butters'));
		$tpom = org_tubepress_impl_ioc_IocContainer::getInstance()->get('org_tubepress_api_options_OptionsManager');
		
		$expectedOptions = array(org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::PLAYLIST,
			org_tubepress_api_const_options_names_Output::PLAYLIST_VALUE => 'foobar',
			org_tubepress_api_const_options_names_Meta::AUTHOR => true,
			org_tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP => 200,
			org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE => 3);
							
			$this->_sut->parse('[butters mode=&#8216playlist&#8217, playlistValue=&#8242;foobar&#8242;, author="true", resultCountCap=\'200\', resultsPerPage=3]');
	
		foreach ($expectedOptions as $optionName => $optionValue) {
			$this->assertEquals($optionValue, $tpom->get($optionName));
		}
	}
	
	function testNoCustomOptions()
	{
		$this->setOptions(array(org_tubepress_api_const_options_names_Advanced::KEYWORD => 'butters'));
		$this->_sut->parse('[butters]');
	}	
	
	function testWeirdSingleQuotes()
	{
		$this->setOptions(array(org_tubepress_api_const_options_names_Advanced::KEYWORD => 'butters'));
		$tpom = org_tubepress_impl_ioc_IocContainer::getInstance()->get('org_tubepress_api_options_OptionsManager');
		
		$expectedOptions = array(org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::PLAYLIST,
			org_tubepress_api_const_options_names_Output::PLAYLIST_VALUE => 'foobar');
							
			$this->_sut->parse('[butters mode=&#8216playlist&#8217 playlistValue=&#8242;foobar&#8242;]');
	
		foreach ($expectedOptions as $optionName => $optionValue) {
			$this->assertEquals($optionValue, $tpom->get($optionName));
		}
	}	
	
	function testWeirdDoubleQuotes()
	{
		$this->setOptions(array(org_tubepress_api_const_options_names_Advanced::KEYWORD => 'butters'));
		$tpom = org_tubepress_impl_ioc_IocContainer::getInstance()->get('org_tubepress_api_options_OptionsManager');
		
		$expectedOptions = array(org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::PLAYLIST,
			org_tubepress_api_const_options_names_Output::PLAYLIST_VALUE => 'foobar');
							
	  	$this->_sut->parse('[butters mode=&#34playlist&#8220; playlistValue=&#8221;foobar&#8243;]');
	
		foreach ($expectedOptions as $optionName => $optionValue) {
			$this->assertEquals($optionValue, $tpom->get($optionName));
		}
	}	

	function testNoQuotes()
	{	
		$this->setOptions(array(org_tubepress_api_const_options_names_Advanced::KEYWORD => 'butters'));
		$tpom = org_tubepress_impl_ioc_IocContainer::getInstance()->get('org_tubepress_api_options_OptionsManager');
		
		$expectedOptions = array(org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::PLAYLIST);
					
			$this->_sut->parse('[butters mode=playlist	]');

		foreach ($expectedOptions as $optionName => $optionValue) {
			$this->assertEquals($optionValue, $tpom->get($optionName));
		}
	}
	
	function testSingleQuotes()
	{
		$this->setOptions(array(org_tubepress_api_const_options_names_Advanced::KEYWORD => 'butters'));
		$tpom = org_tubepress_impl_ioc_IocContainer::getInstance()->get('org_tubepress_api_options_OptionsManager');
		
		$expectedOptions = array(org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::PLAYLIST);
					
		$this->_sut->parse('[butters mode=\'playlist\']');

		foreach ($expectedOptions as $optionName => $optionValue) {
			$this->assertEquals($optionValue, $tpom->get($optionName));
		}
	}	
	
	function testDoubleQuotes()
	{
		$this->setOptions(array(org_tubepress_api_const_options_names_Advanced::KEYWORD => 'butters'));
		$tpom = org_tubepress_impl_ioc_IocContainer::getInstance()->get('org_tubepress_api_options_OptionsManager');
		
		$expectedOptions = array(org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::PLAYLIST);
					
		$this->_sut->parse('[butters mode="playlist"]');

		foreach ($expectedOptions as $optionName => $optionValue) {
			$this->assertEquals($optionValue, $tpom->get($optionName));
		}
	}
	
	function testMismatchedStartEndQuotes()
	{
		$this->setOptions(array(org_tubepress_api_const_options_names_Advanced::KEYWORD => 'butters'));
				  
		$this->_sut->parse('[butters mode=\'playlist"]');

	}	
	
	function testNoClosingBracket()
	{
		$this->setOptions(array(org_tubepress_api_const_options_names_Advanced::KEYWORD => 'butters'));
					
		$this->_sut->parse('[butters mode=\'playlist\'');
	}
	
	function testNoOpeningBracket()
	{
		$this->setOptions(array(org_tubepress_api_const_options_names_Advanced::KEYWORD => 'butters'));
		$content = "butters mode='playlist']";
		
		$this->_sut->parse($content);
	}
	
	function testSpaceAroundAttributes()
	{
		$this->setOptions(array(org_tubepress_api_const_options_names_Advanced::KEYWORD => 'butters'));
		$tpom = org_tubepress_impl_ioc_IocContainer::getInstance()->get('org_tubepress_api_options_OptionsManager');
		
		$expectedOptions = array(org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::PLAYLIST);
		$content = "[butters mode='playlist']";
		
		$this->_sut->parse($content);

		foreach ($expectedOptions as $optionName => $optionValue) {
			$this->assertEquals($optionValue, $tpom->get($optionName));
		}
	}
	
	function testSpaceAroundShortcode()
	{
		$this->setOptions(array(org_tubepress_api_const_options_names_Advanced::KEYWORD => 'butters'));
		$tpom = org_tubepress_impl_ioc_IocContainer::getInstance()->get('org_tubepress_api_options_OptionsManager');
		
		$expectedOptions = array(org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::PLAYLIST);
		$content = "sddf	 [butters mode='playlist']	sdsdfsdf";
		
		$this->_sut->parse($content);

		foreach ($expectedOptions as $optionName => $optionValue) {
			$this->assertEquals($optionValue, $tpom->get($optionName));
		}
	}
	
	function testNoSpaceAroundShortcode()
	{
		$this->setOptions(array(org_tubepress_api_const_options_names_Advanced::KEYWORD => 'butters'));
		$tpom = org_tubepress_impl_ioc_IocContainer::getInstance()->get('org_tubepress_api_options_OptionsManager');
		
		$expectedOptions = array(org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::PLAYLIST);
		$content = "sddf[butters mode='playlist']sdsdfsdf";
		
		$this->_sut->parse($content);

		foreach ($expectedOptions as $optionName => $optionValue) {
			$this->assertEquals($optionValue, $tpom->get($optionName));
		}
	}
}
?>
