<?php

require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/options/validation/SimpleInputValidationService.class.php';

class org_tubepress_options_validation_SimpleInputValidationServiceTest extends PHPUnit_Framework_TestCase {
	
	private $_sut;
	
	public function setUp()
	{
		$this->_sut = new org_tubepress_options_validation_SimpleInputValidationService();
		$this->_sut->setMessageService($this->getMock("org_tubepress_message_MessageService"));
		$this->_sut->setOptionsReference($this->getMock('org_tubepress_options_reference_OptionsReference'));
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