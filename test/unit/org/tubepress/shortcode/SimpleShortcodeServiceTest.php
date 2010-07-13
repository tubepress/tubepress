<?php

require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/shortcode/SimpleShortcodeService.class.php';

class org_tubepress_shortcode_SimpleShortcodeServiceTest extends PHPUnit_Framework_TestCase
{
	private $_sut;
	private $_tpom;
	private $_ioc;
	private $_gallery;
	private $_singleVideo;
	private $_qss;
	
	function setUp()
	{
		$this->_tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
		$this->_ioc = $this->getMock('org_tubepress_ioc_IocService');
		$this->_gallery = $this->getMock('org_tubepress_gallery_TubePressGallery');
		$this->_singleVideo = $this->getMock('org_tubepress_single_Video');
		$this->_qss = $this->getMock('org_tubepress_querystring_QueryStringService');
		$this->_sut = new org_tubepress_shortcode_SimpleShortcodeService();
	    $this->_sut->setInputValidationService($this->getMock('org_tubepress_options_validation_InputValidationService'));
	}

	function testSoloVideoIdSet()
	{
    	$this->_ioc->expects($this->exactly(4))
    	     ->method('get')
    	     ->will($this->returnCallback(array($this, '_iocCallback')));
    	$this->_tpom->expects($this->exactly(2))
    	     ->method('get')
    	     ->will($this->returnCallback(array($this, '_tpomCallbackSoloPlayer')));
    	$this->_singleVideo->expects($this->once())
    	     ->method('getSingleVideoHtml')
    	     ->will($this->returnValue('foofoo'));
    	$this->_qss->expects($this->once())
    	     ->method('getCustomVideo')
    	     ->will($this->returnValue('someid'));
  		$result = $this->_sut->getHtml($this->_ioc);
  		$this->assertEquals('foofoo', $result);
	}

	function testSoloNoVideoIdSet()
	{
    	$this->_ioc->expects($this->exactly(4))
    	     ->method('get')
    	     ->will($this->returnCallback(array($this, '_iocCallback')));
    	$this->_tpom->expects($this->exactly(2))
    	     ->method('get')
    	     ->will($this->returnCallback(array($this, '_tpomCallbackSoloPlayer')));
    	$this->_gallery->expects($this->once())
    	     ->method('getHtml')
    	     ->will($this->returnValue('foofoo'));
  		$result = $this->_sut->getHtml($this->_ioc);
  		$this->assertEquals('foofoo', $result);
	}

	function testGetHtmlSingleVideo()
	{
    	$this->_ioc->expects($this->exactly(3))
    	     ->method('get')
    	     ->will($this->returnCallback(array($this, '_iocCallback')));
    	$this->_tpom->expects($this->exactly(2))
    	     ->method('get')
    	     ->will($this->returnCallback(array($this, '_tpomCallbackSingleVideo')));
    	$this->_singleVideo->expects($this->once())
    	     ->method('getSingleVideoHtml')
    	     ->will($this->returnValue('foofoo'));
  		$result = $this->_sut->getHtml($this->_ioc);
  		$this->assertEquals('foofoo', $result);
	}

	function testGetHtmlNormalGallery()
	{
    	$this->_ioc->expects($this->exactly(4))
    	     ->method('get')
    	     ->will($this->returnCallback(array($this, '_iocCallback')));
    	$this->_tpom->expects($this->exactly(2))
    	     ->method('get')
    	     ->will($this->returnCallback(array($this, '_tpomCallbackNonSoloPlayer')));
    	$this->_gallery->expects($this->once())
    	     ->method('getHtml')
    	     ->will($this->returnValue('foofoo'));
  		$result = $this->_sut->getHtml($this->_ioc);
  		$this->assertEquals('foofoo', $result);
	}

   function testMixedCommasWithAllSortsOfQuotes()
    {
    			$this->_tpom->expects($this->once())
               ->method('get')
               ->with(org_tubepress_options_category_Advanced::KEYWORD)
               ->will($this->returnValue('butters'));
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
               ->method('get')
               ->with(org_tubepress_options_category_Advanced::KEYWORD)
               ->will($this->returnValue('butters'));
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
               ->method('get')
               ->with(org_tubepress_options_category_Advanced::KEYWORD)
               ->will($this->returnValue('butters'));
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
    			$this->_tpom->expects($this->once())
               ->method('get')
               ->with(org_tubepress_options_category_Advanced::KEYWORD)
               ->will($this->returnValue('butters'));
        $this->_tpom->expects($this->never())
                   ->method('setCustomOptions');
        $this->_sut->parse('[butters]', $this->_tpom);
    }   
	
	function testWeirdSingleQuotes()
    {
    			$this->_tpom->expects($this->once())
               ->method('get')
               ->with(org_tubepress_options_category_Advanced::KEYWORD)
               ->will($this->returnValue('butters'));
        $this->_tpom->expects($this->once())
                    ->method('setCustomOptions')
                    ->with(array(org_tubepress_options_category_Gallery::MODE => 'playlist',
                            org_tubepress_options_category_Gallery::PLAYLIST_VALUE => 'foobar'));
        $this->_sut->parse('[butters mode=&#8216playlist&#8217 playlistValue=&#8242;foobar&#8242;]', $this->_tpom);
    }   
    
    function testWeirdDoubleQuotes()
    {
    			$this->_tpom->expects($this->once())
               ->method('get')
               ->with(org_tubepress_options_category_Advanced::KEYWORD)
               ->will($this->returnValue('butters'));
        $this->_tpom->expects($this->once())
                    ->method('setCustomOptions')
                    ->with(array(org_tubepress_options_category_Gallery::MODE => 'playlist',
                            org_tubepress_options_category_Gallery::PLAYLIST_VALUE => 'foobar'));
        $this->_sut->parse('[butters mode=&#34playlist&#8220; playlistValue=&#8221;foobar&#8243;]', $this->_tpom);
    }   

    function testNoQuotes()
    {		$this->_tpom->expects($this->once())
               ->method('get')
               ->with(org_tubepress_options_category_Advanced::KEYWORD)
               ->will($this->returnValue('butters'));
        $this->_tpom->expects($this->once())
                    ->method('setCustomOptions')
                    ->with(array(org_tubepress_options_category_Gallery::MODE => 'playlist'));
        $this->_sut->parse('[butters mode=playlist   ]', $this->_tpom);
    }
    
    function testSingleQuotes()
    {
    			$this->_tpom->expects($this->once())
               ->method('get')
               ->with(org_tubepress_options_category_Advanced::KEYWORD)
               ->will($this->returnValue('butters'));
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
    			$this->_tpom->expects($this->once())
               ->method('get')
               ->with(org_tubepress_options_category_Advanced::KEYWORD)
               ->will($this->returnValue('butters'));
        $this->_tpom->expects($this->never())
                   ->method('setCustomOptions');
        $this->_sut->parse('[butters mode=\'playlist"]', $this->_tpom);
    }	
	
   function testNoClosingBracket()
    {
    			$this->_tpom->expects($this->once())
               ->method('get')
               ->with(org_tubepress_options_category_Advanced::KEYWORD)
               ->will($this->returnValue('butters'));
        $this->_tpom->expects($this->never())
                   ->method('setCustomOptions');
        $this->_sut->parse('[butters mode=\'playlist\'', $this->_tpom);
    }
	
   function testNoOpeningBracket()
    {
    			$this->_tpom->expects($this->once())
               ->method('get')
               ->with(org_tubepress_options_category_Advanced::KEYWORD)
               ->will($this->returnValue('butters'));
        $this->_tpom->expects($this->never())
                   ->method('setCustomOptions');
        $content = "butters mode='playlist']";
        $this->_sut->parse($content, $this->_tpom);
    }
	
   function testSpaceAroundAttributes()
    {
    			$this->_tpom->expects($this->once())
               ->method('get')
               ->with(org_tubepress_options_category_Advanced::KEYWORD)
               ->will($this->returnValue('butters'));
        $this->_tpom->expects($this->once())
                   ->method('setCustomOptions')
                   ->with(array(org_tubepress_options_category_Gallery::MODE => 'playlist'));
        $content = "[butters mode='playlist']";
        $this->_sut->parse($content, $this->_tpom);
    }
	
   function testSpaceAroundShortcode()
    {
    			$this->_tpom->expects($this->once())
               ->method('get')
               ->with(org_tubepress_options_category_Advanced::KEYWORD)
               ->will($this->returnValue('butters'));
        $this->_tpom->expects($this->once())
                   ->method('setCustomOptions')
                   ->with(array(org_tubepress_options_category_Gallery::MODE => 'playlist'));
        $content = "sddf     [butters mode='playlist']   sdsdfsdf";
        $this->_sut->parse($content, $this->_tpom);
    }
	
	function testNoSpaceAroundShortcode()
	{
				$this->_tpom->expects($this->once())
               ->method('get')
               ->with(org_tubepress_options_category_Advanced::KEYWORD)
               ->will($this->returnValue('butters'));
	    $this->_tpom->expects($this->once())
	               ->method('setCustomOptions')
	               ->with(array(org_tubepress_options_category_Gallery::MODE => 'playlist'));
		$content = "sddf[butters mode='playlist']sdsdfsdf";
		$this->_sut->parse($content, $this->_tpom);
	}

	function _tpomCallbackSingleVideo()
	{
	    $args = func_get_args();
	    $vals = array(
	        org_tubepress_options_category_Display::CURRENT_PLAYER_NAME => 'something',
	        org_tubepress_options_category_Advanced::KEYWORD => 'grits',
	        org_tubepress_options_category_Gallery::VIDEO => 'something'
	    );
	    return $vals[$args[0]];
	}

	function _tpomCallbackSoloPlayer()
	{
	    $args = func_get_args();
	    $vals = array(
	        org_tubepress_options_category_Display::CURRENT_PLAYER_NAME => 'solo',
	        org_tubepress_options_category_Advanced::KEYWORD => 'grits',
	        org_tubepress_options_category_Gallery::VIDEO => ''
	    );
	    return $vals[$args[0]];
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
	
	function _iocCallback()
	{
	    $args = func_get_args();
	    $vals = array(
	        org_tubepress_ioc_IocService::OPTIONS_MANAGER => $this->_tpom,
	        org_tubepress_ioc_IocService::GALLERY => $this->_gallery,
	        org_tubepress_ioc_IocService::SINGLE_VIDEO => $this->_singleVideo,
	        org_tubepress_ioc_IocService::QUERY_STRING_SERVICE => $this->_qss
	    );
	    return $vals[$args[0]];
	}
}
?>