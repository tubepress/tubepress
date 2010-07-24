<?php

require_once dirname(__FILE__) . '/../../../../../test/unit/TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/shortcode/ShortcodeParser.class.php';

class org_tubepress_shortcode_ShortcodeParserTest extends TubePressUnitTest
{
    function setup()
    {
        org_tubepress_log_Log::setEnabled(false, array());
    }	

   function testMixedCommasWithAllSortsOfQuotes()
    {
        $this->setOptions(array(org_tubepress_options_category_Advanced::KEYWORD => 'butters'));
        $ioc = $this->getIoc();
        $tpom = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
        
        $tpom->expects($this->once())
             ->method('setCustomOptions')
             ->with(array(org_tubepress_options_category_Gallery::MODE => 'playlist',
                            org_tubepress_options_category_Gallery::PLAYLIST_VALUE => 'foobar',
                            org_tubepress_options_category_Meta::AUTHOR => false,
                            org_tubepress_options_category_Feed::RESULT_COUNT_CAP => 200,
                            org_tubepress_options_category_Display::RESULTS_PER_PAGE => 3));
                            
        org_tubepress_shortcode_ShortcodeParser::parse('[butters mode=&#8216playlist&#8217  , playlistValue=&#8242;foobar&#8242; ,author="false", resultCountCap=\'200\' resultsPerPage=3]', $ioc);
    }
	
   function testNoCommasWithAllSortsOfQuotes()
    {
    	$this->setOptions(array(org_tubepress_options_category_Advanced::KEYWORD => 'butters'));
        $ioc = $this->getIoc();
        $tpom = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
        
        $tpom->expects($this->once())
                    ->method('setCustomOptions')
                    ->with(array(org_tubepress_options_category_Gallery::MODE => 'playlist',
                            org_tubepress_options_category_Gallery::PLAYLIST_VALUE => 'foobar',
                            org_tubepress_options_category_Meta::AUTHOR => true,
                            org_tubepress_options_category_Feed::RESULT_COUNT_CAP => 200,
                            org_tubepress_options_category_Display::RESULTS_PER_PAGE => 3));
                            
        org_tubepress_shortcode_ShortcodeParser::parse('[butters mode=&#8216playlist&#8217 playlistValue=&#8242;foobar&#8242; author="true" resultCountCap=\'200\' resultsPerPage=3]', $ioc);
    }
	
   function testCommasWithAllSortsOfQuotes()
    {
    	$this->setOptions(array(org_tubepress_options_category_Advanced::KEYWORD => 'butters'));
        $ioc = $this->getIoc();
        $tpom = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
        
        $tpom->expects($this->once())
                    ->method('setCustomOptions')
                    ->with(array(org_tubepress_options_category_Gallery::MODE => 'playlist',
                            org_tubepress_options_category_Gallery::PLAYLIST_VALUE => 'foobar',
                            org_tubepress_options_category_Meta::AUTHOR => true,
                            org_tubepress_options_category_Feed::RESULT_COUNT_CAP => 200,
                            org_tubepress_options_category_Display::RESULTS_PER_PAGE => 3));
                            
        org_tubepress_shortcode_ShortcodeParser::parse('[butters mode=&#8216playlist&#8217, playlistValue=&#8242;foobar&#8242;, author="true", resultCountCap=\'200\', resultsPerPage=3]', $ioc);
    }
	
	function testNoCustomOptions()
    {
    	$this->setOptions(array(org_tubepress_options_category_Advanced::KEYWORD => 'butters'));
        $ioc = $this->getIoc();
        $tpom = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
        
        $tpom->expects($this->never())
                   ->method('setCustomOptions');
        org_tubepress_shortcode_ShortcodeParser::parse('[butters]', $ioc);
    }   
	
	function testWeirdSingleQuotes()
    {
    	$this->setOptions(array(org_tubepress_options_category_Advanced::KEYWORD => 'butters'));
        $ioc = $this->getIoc();
        $tpom = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
        
        $tpom->expects($this->once())
                    ->method('setCustomOptions')
                    ->with(array(org_tubepress_options_category_Gallery::MODE => 'playlist',
                            org_tubepress_options_category_Gallery::PLAYLIST_VALUE => 'foobar'));
                            
        org_tubepress_shortcode_ShortcodeParser::parse('[butters mode=&#8216playlist&#8217 playlistValue=&#8242;foobar&#8242;]', $ioc);
    }   
    
    function testWeirdDoubleQuotes()
    {
    	$this->setOptions(array(org_tubepress_options_category_Advanced::KEYWORD => 'butters'));
        $ioc = $this->getIoc();
        $tpom = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
        
        $tpom->expects($this->once())
                    ->method('setCustomOptions')
                    ->with(array(org_tubepress_options_category_Gallery::MODE => 'playlist',
                            org_tubepress_options_category_Gallery::PLAYLIST_VALUE => 'foobar'));
                            
        org_tubepress_shortcode_ShortcodeParser::parse('[butters mode=&#34playlist&#8220; playlistValue=&#8221;foobar&#8243;]', $ioc);
    }   

    function testNoQuotes()
    {	
        $this->setOptions(array(org_tubepress_options_category_Advanced::KEYWORD => 'butters'));
        $ioc = $this->getIoc();
        $tpom = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
        
        $tpom->expects($this->once())
                    ->method('setCustomOptions')
                    ->with(array(org_tubepress_options_category_Gallery::MODE => 'playlist'));
                    
        org_tubepress_shortcode_ShortcodeParser::parse('[butters mode=playlist   ]', $ioc);
    }
    
    function testSingleQuotes()
    {
    	$this->setOptions(array(org_tubepress_options_category_Advanced::KEYWORD => 'butters'));
        $ioc = $this->getIoc();
        $tpom = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
        
        $tpom->expects($this->once())
                    ->method('setCustomOptions')
                    ->with(array(org_tubepress_options_category_Gallery::MODE => 'playlist'));
                    
        org_tubepress_shortcode_ShortcodeParser::parse('[butters mode=\'playlist\']', $ioc);
    }	
	
    function testDoubleQuotes()
    {
        $this->setOptions(array(org_tubepress_options_category_Advanced::KEYWORD => 'butters'));
        $ioc = $this->getIoc();
        $tpom = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
        
        $tpom->expects($this->once())
                    ->method('setCustomOptions')
                    ->with(array(org_tubepress_options_category_Gallery::MODE => 'playlist'));
                    
        org_tubepress_shortcode_ShortcodeParser::parse('[butters mode="playlist"]', $ioc);
    }
	
   function testMismatchedStartEndQuotes()
    {
    	$this->setOptions(array(org_tubepress_options_category_Advanced::KEYWORD => 'butters'));
        $ioc = $this->getIoc();
        $tpom = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
        
        $tpom->expects($this->never())
                   ->method('setCustomOptions');
                   
        org_tubepress_shortcode_ShortcodeParser::parse('[butters mode=\'playlist"]', $ioc);
    }	
	
   function testNoClosingBracket()
    {
    	$this->setOptions(array(org_tubepress_options_category_Advanced::KEYWORD => 'butters'));
        $ioc = $this->getIoc();
        $tpom = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
        
        $tpom->expects($this->never())
                   ->method('setCustomOptions');
                   
        org_tubepress_shortcode_ShortcodeParser::parse('[butters mode=\'playlist\'', $ioc);
    }
	
   function testNoOpeningBracket()
    {
    	$this->setOptions(array(org_tubepress_options_category_Advanced::KEYWORD => 'butters'));
        $ioc = $this->getIoc();
        $tpom = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
        
        $tpom->expects($this->never())
                   ->method('setCustomOptions');
        $content = "butters mode='playlist']";
        
        org_tubepress_shortcode_ShortcodeParser::parse($content, $ioc);
    }
	
   function testSpaceAroundAttributes()
    {
    	$this->setOptions(array(org_tubepress_options_category_Advanced::KEYWORD => 'butters'));
        $ioc = $this->getIoc();
        $tpom = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
        
        $tpom->expects($this->once())
                   ->method('setCustomOptions')
                   ->with(array(org_tubepress_options_category_Gallery::MODE => 'playlist'));
        $content = "[butters mode='playlist']";
        
        org_tubepress_shortcode_ShortcodeParser::parse($content, $ioc);
    }
	
   function testSpaceAroundShortcode()
    {
    	$this->setOptions(array(org_tubepress_options_category_Advanced::KEYWORD => 'butters'));
        $ioc = $this->getIoc();
        $tpom = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
        
        $tpom->expects($this->once())
                   ->method('setCustomOptions')
                   ->with(array(org_tubepress_options_category_Gallery::MODE => 'playlist'));
        $content = "sddf     [butters mode='playlist']   sdsdfsdf";
        
        org_tubepress_shortcode_ShortcodeParser::parse($content, $ioc);
    }
	
	function testNoSpaceAroundShortcode()
	{
		$this->setOptions(array(org_tubepress_options_category_Advanced::KEYWORD => 'butters'));
        $ioc = $this->getIoc();
        $tpom = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
        
	    $tpom->expects($this->once())
	               ->method('setCustomOptions')
	               ->with(array(org_tubepress_options_category_Gallery::MODE => 'playlist'));
		$content = "sddf[butters mode='playlist']sdsdfsdf";
		
		org_tubepress_shortcode_ShortcodeParser::parse($content, $ioc);
	}


	function _tpomCallbackNonSoloPlayer()
	{
	    $args = func_get_args();
	    $vals = array(
	        org_tubepress_options_category_Display::CURRENT_PLAYER_NAME => 'something',
	        org_tubepress_options_category_Advanced::KEYWORD => 'grits',
	        org_tubepress_options_category_Gallery::VIDEO => ''
	    );
	    return $vals[$args[0]];
	}
}
?>