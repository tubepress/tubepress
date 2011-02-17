<?php

require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/impl/options/SimpleOptionValidator.class.php';

class org_tubepress_impl_options_SimpleOptionValidatorTest extends TubePressUnitTest {
	
	private $_sut;

	public function setup()
	{
		$this->initFakeIoc();
		$this->_sut = new org_tubepress_impl_options_SimpleOptionValidator();
	}

	public function testThumbHeightOk()
	{
		$this->_sut->validate(org_tubepress_api_const_options_names_Display::THUMB_HEIGHT, 90);	
	}

	
	public function testThumbWidthOk()
	{
		$this->_sut->validate(org_tubepress_api_const_options_names_Display::THUMB_WIDTH, 120);	
	}
	
	
	public function testResultsPerPageOk()
	{
		$this->_sut->validate(org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE, 50);
	}
	
	/**
	 * @expectedException Exception
	 */
	public function testResultsPerPageTooSmall()
	{
		$this->_sut->validate(org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE, 0);
	}

   	/**
	 * @expectedException Exception
	 */
	public function testDotsInTheme()
	{
		$this->_sut->validate(org_tubepress_api_const_options_names_Display::THEME, 'sometheme/../yo');
	}

	public function testNoDotsInTheme()
	{
		$this->_sut->validate(org_tubepress_api_const_options_names_Display::THEME, 'sometheme');
	}
	
   	/**
	 * @expectedException Exception
	 */
	public function testNoSuchOption()
	{
		$this->_sut->validate('no such option', 51);
	}

	/**
	 * @expectedException Exception
	 */
	public function testNonBooleanForBooleanOption()
	{
		$this->_sut->validate(org_tubepress_api_const_options_names_Meta::TITLE, 'somethingcrazy');
	}

	public function testBooleanForBooleanOption()
	{
		$this->_sut->validate(org_tubepress_api_const_options_names_Meta::TITLE, '1');
	}

	/**
	 * @expectedException Exception
	 */
	public function testNonStringForStringOptions()
	{
		$this->_sut->validate(org_tubepress_api_const_options_names_Advanced::KEYWORD, array());
	}

	/**
	 * @expectedException Exception
	 */
	public function testResultsPerTooBig()
	{
		$this->_sut->validate(org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE, 51);
	}
	
	/**
	 * @expectedException Exception
	 */
	public function testResultsPerPageBelowZero()
	{
		$this->_sut->validate(org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE, -1);
	}
	
	public function testDescLimitZero()
	{
		$this->_sut->validate(org_tubepress_api_const_options_names_Display::DESC_LIMIT, 0);
	}
}
?>
