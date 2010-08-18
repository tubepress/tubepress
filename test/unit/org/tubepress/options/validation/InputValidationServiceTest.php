<?php

require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/options/validation/SimpleInputValidationService.class.php';

class org_tubepress_options_validation_InputValidationServiceTest extends TubePressUnitTest {
	
	private $_sut;

	public function setup()
	{
		$this->initFakeIoc();
		$this->_sut = new org_tubepress_options_validation_SimpleInputValidationService();
	}

	public function testThumbHeightOk()
	{
		$this->_sut->validate(org_tubepress_options_category_Display::THUMB_HEIGHT, 90);	
	}

	public function testExecutableFfmpeg()
	{
		$candidate = tempnam("/tmp", "ffmpeg");
		chmod($candidate, 0755);
		$this->_sut->validate(org_tubepress_options_category_Uploads::FFMPEG_BINARY_LOCATION, $candidate);
	}
	
	/**
	 * @expectedException Exception
	 */
	public function testNonExecutableFfmpeg()
	{
		$candidate = tempnam("/tmp", "ffmpeg");
		$this->_sut->validate(org_tubepress_options_category_Uploads::FFMPEG_BINARY_LOCATION, $candidate);
	}
	
	public function testThumbWidthOk()
	{
		$this->_sut->validate(org_tubepress_options_category_Display::THUMB_WIDTH, 120);	
	}
	
	
	public function testResultsPerPageOk()
	{
		$this->_sut->validate(org_tubepress_options_category_Display::RESULTS_PER_PAGE, 50);
	}
	
	/**
	 * @expectedException Exception
	 */
	public function testResultsPerPageTooSmall()
	{
		$this->_sut->validate(org_tubepress_options_category_Display::RESULTS_PER_PAGE, 0);
	}

   	/**
	 * @expectedException Exception
	 */
	public function testDotsInTheme()
	{
		$this->_sut->validate(org_tubepress_options_category_Display::THEME, 'sometheme/../yo');
	}

	public function testNoDotsInTheme()
	{
		$this->_sut->validate(org_tubepress_options_category_Display::THEME, 'sometheme');
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
	public function testDotsInLocalDirectory()
	{
		$this->_sut->validate(org_tubepress_options_category_Gallery::DIRECTORY_VALUE, 'sdfds/../sdfd');
	}

	/**
	 * @expectedException Exception
	 */
	public function testNonBooleanForBooleanOption()
	{
		$this->_sut->validate(org_tubepress_options_category_Meta::TITLE, 'somethingcrazy');
	}

	public function testBooleanForBooleanOption()
	{
		$this->_sut->validate(org_tubepress_options_category_Meta::TITLE, '1');
	}

	/**
	 * @expectedException Exception
	 */
	public function testNonStringForStringOptions()
	{
		$this->_sut->validate(org_tubepress_options_category_Advanced::KEYWORD, array());
	}

	/**
	 * @expectedException Exception
	 */
	public function testNoSuchLocalDirectory()
	{
		$this->_sut->validate(org_tubepress_options_category_Gallery::DIRECTORY_VALUE, 'sdfds');
	}

	/**
	 * @expectedException Exception
	 */
	public function testResultsPerTooBig()
	{
		$this->_sut->validate(org_tubepress_options_category_Display::RESULTS_PER_PAGE, 51);
	}
	
	/**
	 * @expectedException Exception
	 */
	public function testResultsPerPageBelowZero()
	{
		$this->_sut->validate(org_tubepress_options_category_Display::RESULTS_PER_PAGE, -1);
	}
	
	public function testDescLimitZero()
	{
		$this->_sut->validate(org_tubepress_options_category_Display::DESC_LIMIT, 0);
	}
}
?>
