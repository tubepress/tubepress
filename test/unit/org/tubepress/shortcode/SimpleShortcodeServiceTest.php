<?php

require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/shortcode/SimpleShortcodeService.class.php';

class org_tubepress_shortcode_SimpleShortcodeServiceTest extends PHPUnit_Framework_TestCase
{
	private $_sut;
	private $_tpom;
	
	function setUp()
	{
		$this->_tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
		$this->_sut = new org_tubepress_shortcode_SimpleShortcodeService();
		$this->_sut->setLog($this->getMock('org_tubepress_log_Log'));
	    $this->_sut->setInputValidationService($this->getMock('org_tubepress_options_validation_InputValidationService'));
		$this->_tpom->expects($this->once())
               ->method('get')
               ->with(org_tubepress_options_category_Advanced::KEYWORD)
               ->will($this->returnValue('butters'));
	}

   function testMixedCommasWithAllSortsOfQuotes()
    {
        $this->_tpom->expects($this->once())
                    ->method('setCustomOptions')
                    ->with(array(org_tubepress_options_category_Gallery::MODE => 'playlist',
                            org_tubepress_options_category_Gallery::PLAYLIST_VALUE => 'foobar',
                            org_tubepress_options_category_Meta::AUTHOR => false,
                            org_tubepress_options_category_Feed::RESULT_COUNT_CAP => 200,
                            org_tubepress_options_category_Display::RESULTS_PER_PAGE => 3));
        $this->_sut->parse('[butters mode=&#8216playlist&#8217  , playlistValue=&#8242;foobar&#8242; ,author="false", resultCountCap=\'200\' resultsPerPage=3]', $this->_tpom);
    }
	
   function testNoCommasWithAllSortsOfQuotes()
    {
        $this->_tpom->expects($this->once())
                    ->method('setCustomOptions')
                    ->with(array(org_tubepress_options_category_Gallery::MODE => 'playlist',
                            org_tubepress_options_category_Gallery::PLAYLIST_VALUE => 'foobar',
                            org_tubepress_options_category_Meta::AUTHOR => true,
                            org_tubepress_options_category_Feed::RESULT_COUNT_CAP => 200,
                            org_tubepress_options_category_Display::RESULTS_PER_PAGE => 3));
        $this->_sut->parse('[butters mode=&#8216playlist&#8217 playlistValue=&#8242;foobar&#8242; author="true" resultCountCap=\'200\' resultsPerPage=3]', $this->_tpom);
    }
	
   function testCommasWithAllSortsOfQuotes()
    {
        $this->_tpom->expects($this->once())
                    ->method('setCustomOptions')
                    ->with(array(org_tubepress_options_category_Gallery::MODE => 'playlist',
                            org_tubepress_options_category_Gallery::PLAYLIST_VALUE => 'foobar',
                            org_tubepress_options_category_Meta::AUTHOR => true,
                            org_tubepress_options_category_Feed::RESULT_COUNT_CAP => 200,
                            org_tubepress_options_category_Display::RESULTS_PER_PAGE => 3));
        $this->_sut->parse('[butters mode=&#8216playlist&#8217, playlistValue=&#8242;foobar&#8242;, author="true", resultCountCap=\'200\', resultsPerPage=3]', $this->_tpom);
    }
	
	function testNoCustomOptions()
    {
        $this->_tpom->expects($this->never())
                   ->method('setCustomOptions');
        $this->_sut->parse('[butters]', $this->_tpom);
    }   
	
	function testWeirdSingleQuotes()
    {
        $this->_tpom->expects($this->once())
                    ->method('setCustomOptions')
                    ->with(array(org_tubepress_options_category_Gallery::MODE => 'playlist',
                            org_tubepress_options_category_Gallery::PLAYLIST_VALUE => 'foobar'));
        $this->_sut->parse('[butters mode=&#8216playlist&#8217 playlistValue=&#8242;foobar&#8242;]', $this->_tpom);
    }   
    
    function testWeirdDoubleQuotes()
    {
        $this->_tpom->expects($this->once())
                    ->method('setCustomOptions')
                    ->with(array(org_tubepress_options_category_Gallery::MODE => 'playlist',
                            org_tubepress_options_category_Gallery::PLAYLIST_VALUE => 'foobar'));
        $this->_sut->parse('[butters mode=&#34playlist&#8220; playlistValue=&#8221;foobar&#8243;]', $this->_tpom);
    }   

    function testNoQuotes()
    {
        $this->_tpom->expects($this->once())
                    ->method('setCustomOptions')
                    ->with(array(org_tubepress_options_category_Gallery::MODE => 'playlist'));
        $this->_sut->parse('[butters mode=playlist   ]', $this->_tpom);
    }
    
    function testSingleQuotes()
    {
        $this->_tpom->expects($this->once())
                    ->method('setCustomOptions')
                    ->with(array(org_tubepress_options_category_Gallery::MODE => 'playlist'));
        $this->_sut->parse('[butters mode=\'playlist\']', $this->_tpom);
    }	
	
    function testDoubleQuotes()
    {
        $this->_tpom->expects($this->once())
                    ->method('setCustomOptions')
                    ->with(array(org_tubepress_options_category_Gallery::MODE => 'playlist'));
        $this->_sut->parse('[butters mode="playlist"]', $this->_tpom);
    }
	
   function testMismatchedStartEndQuotes()
    {
        $this->_tpom->expects($this->never())
                   ->method('setCustomOptions');
        $this->_sut->parse('[butters mode=\'playlist"]', $this->_tpom);
    }	
	
   function testNoClosingBracket()
    {
        $this->_tpom->expects($this->never())
                   ->method('setCustomOptions');
        $this->_sut->parse('[butters mode=\'playlist\'', $this->_tpom);
    }
	
   function testNoOpeningBracket()
    {
        $this->_tpom->expects($this->never())
                   ->method('setCustomOptions');
        $content = "butters mode='playlist']";
        $this->_sut->parse($content, $this->_tpom);
    }
	
   function testSpaceAroundAttributes()
    {
        $this->_tpom->expects($this->once())
                   ->method('setCustomOptions')
                   ->with(array(org_tubepress_options_category_Gallery::MODE => 'playlist'));
        $content = "[butters mode='playlist']";
        $this->_sut->parse($content, $this->_tpom);
    }
	
   function testSpaceAroundShortcode()
    {
        $this->_tpom->expects($this->once())
                   ->method('setCustomOptions')
                   ->with(array(org_tubepress_options_category_Gallery::MODE => 'playlist'));
        $content = "sddf     [butters mode='playlist']   sdsdfsdf";
        $this->_sut->parse($content, $this->_tpom);
    }
	
	function testNoSpaceAroundShortcode()
	{
	    $this->_tpom->expects($this->once())
	               ->method('setCustomOptions')
	               ->with(array(org_tubepress_options_category_Gallery::MODE => 'playlist'));
		$content = "sddf[butters mode='playlist']sdsdfsdf";
		$this->_sut->parse($content, $this->_tpom);
	}
}
?>