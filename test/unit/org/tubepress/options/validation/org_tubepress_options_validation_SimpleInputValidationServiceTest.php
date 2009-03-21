<?php
class org_tubepress_options_validation_SimpleInputValidationServiceTest extends PHPUnit_Framework_TestCase {
	
	private $_sut;
	
	public function setUp()
	{
		$this->_sut = new org_tubepress_options_validation_SimpleInputValidationService();
		$this->_sut->setMessageService($this->getMock("org_tubepress_message_MessageService"));
	}
	
	public function testThumbHeightOk()
	{
	    $this->_sut->validate(org_tubepress_options_category_Display::THUMB_HEIGHT, 90);	
	}
	
	/**
     * @expectedException Exception
     */
	public function testThumbHeightTooSmall()
	{
	    $this->_sut->validate(org_tubepress_options_category_Display::THUMB_HEIGHT, 0);
	}
	
    /**
     * @expectedException Exception
     */
	public function testThumbHeightBelowZero()
	{
	    $this->_sut->validate(org_tubepress_options_category_Display::THUMB_HEIGHT, -100);
	}
	
    /**
     * @expectedException Exception
     */
	public function testThumbHeightTooBig()
	{
	    $this->_sut->validate(org_tubepress_options_category_Display::THUMB_HEIGHT, 91);
	}
	
    public function testThumbWidthOk()
	{
	    $this->_sut->validate(org_tubepress_options_category_Display::THUMB_WIDTH, 120);	
	}
	
	/**
     * @expectedException Exception
     */
	public function testThumbWidthTooSmall()
	{
	    $this->_sut->validate(org_tubepress_options_category_Display::THUMB_WIDTH, 0);
	}
	
    /**
     * @expectedException Exception
     */
	public function testThumbWidthBelowZero()
	{
	    $this->_sut->validate(org_tubepress_options_category_Display::THUMB_WIDTH, -100);
	}
	
    /**
     * @expectedException Exception
     */
	public function testThumbWidthTooBig()
	{
	    $this->_sut->validate(org_tubepress_options_category_Display::THUMB_WIDTH, 121);
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
}
?>