<?php

require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/options/validation/InputValidationService.class.php';

class org_tubepress_options_validation_InputValidationServiceTest extends TubePressUnitTest {
	
	public function testThumbHeightOk()
	{
	    org_tubepress_options_validation_InputValidationService::validate(org_tubepress_options_category_Display::THUMB_HEIGHT, 90, $this->getIoc());	
	}

    public function testExecutableFfmpeg()
    {
        $candidate = tempnam("/tmp", "ffmpeg");
        chmod($candidate, 0755);
        org_tubepress_options_validation_InputValidationService::validate(org_tubepress_options_category_Uploads::FFMPEG_BINARY_LOCATION, $candidate, $this->getIoc());
    }
	
	/**
     * @expectedException Exception
     */
    public function testNonExecutableFfmpeg()
    {
        $candidate = tempnam("/tmp", "ffmpeg");
        org_tubepress_options_validation_InputValidationService::validate(org_tubepress_options_category_Uploads::FFMPEG_BINARY_LOCATION, $candidate, $this->getIoc());
    }
	
    public function testThumbWidthOk()
	{
	    org_tubepress_options_validation_InputValidationService::validate(org_tubepress_options_category_Display::THUMB_WIDTH, 120, $this->getIoc());	
	}
	
	
	public function testResultsPerPageOk()
	{
	    org_tubepress_options_validation_InputValidationService::validate(org_tubepress_options_category_Display::RESULTS_PER_PAGE, 50, $this->getIoc());
	}
	
	/**
     * @expectedException Exception
     */
    public function testResultsPerPageTooSmall()
	{
	    org_tubepress_options_validation_InputValidationService::validate(org_tubepress_options_category_Display::RESULTS_PER_PAGE, 0, $this->getIoc());
	}

   /**
     * @expectedException Exception
     */
    public function testDotsInTheme()
    {
        org_tubepress_options_validation_InputValidationService::validate(org_tubepress_options_category_Display::THEME, 'sometheme/../yo', $this->getIoc());
    }
	
   /**
     * @expectedException Exception
     */
    public function testNoSuchOption()
    {
        org_tubepress_options_validation_InputValidationService::validate('no such option', 51, $this->getIoc());
    }
	
	/**
     * @expectedException Exception
     */
    public function testResultsPerTooBig()
	{
	    org_tubepress_options_validation_InputValidationService::validate(org_tubepress_options_category_Display::RESULTS_PER_PAGE, 51, $this->getIoc());
	}
	
	/**
     * @expectedException Exception
     */
    public function testResultsPerPageBelowZero()
	{
	    org_tubepress_options_validation_InputValidationService::validate(org_tubepress_options_category_Display::RESULTS_PER_PAGE, -1, $this->getIoc());
	}
	
	public function testDescLimitZero()
	{
	    org_tubepress_options_validation_InputValidationService::validate(org_tubepress_options_category_Display::DESC_LIMIT, 0, $this->getIoc());
	}
}
?>