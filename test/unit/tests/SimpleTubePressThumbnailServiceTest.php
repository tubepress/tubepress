<?php
class SimpleTubePressThumbnailServiceTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	private $_tpom;
	private $_messageService;
	private $_player;
	
	function setUp()
	{
		$this->_tpom = $this->getMock("TubePressOptionsManager");
		$this->_messageService = $this->getMock("TubePressMessageService");
		$this->_player = $this->getMock("TubePressPlayer");
		$this->_sut = new SimpleTubePressThumbnailService();
	}
	
	function testGetHtml()
	{
		
		$this->_player->expects($this->exactly(2))
					  ->method("getPlayLink")
					  ->will($this->returnValue("play link"));
		
		$this->_tpom->expects($this->any())
					->method("get")
					->will($this->returnCallback("stptsCallback"));			  
					  
		$this->_sut->setMessageService($this->_messageService);
		$this->_sut->setOptionsManager($this->_tpom);
		$vid = new TubePressVideo();
		$vid->setId("fakeid");
		$vid->setTitle("Fake title");
		$vid->setLength("1:50");
		$vid->setAuthor("3hough");
		$vid->setDescription("Fake description.");
		$vid->setTags(array("foo", "bar"));
		$vid->setYouTubeUrl("youtube url");
		$vid->setRating("4.5");
		$vid->setRatings("1000");
		$this->assertEquals(<<<EOT
<div class="tubepress_thumb">
	<div class="tubepress_video_thumb_inner" style="width: 40px">
		<a play link> <img alt="Fake title" src="http://img.youtube.com/vi/fakeid/default.jpg" width="40" height="100" /></a>
		
		<div class="tubepress_meta_group">
	
			
			<div class="tubepress_stitle"><a play link>Fake title</a><br /></div>
			

			
			<span class="tubepress_runtime"> 1:50 </span><br />
			
			
			
			<span class="tubepress_meta">: </span>
			<a  href="http://www.youtube.com/profile?user=3hough">3hough</a><br />
			 
			
			 

			
			<span class="tubepress_meta">: </span>
			<a  href="http://youtube.com/results?search_query=foo%20bar&amp;search=Search">foo bar</a><br />
			 
			
			 
			<a  href="youtube url"></a><br />
			 
			
			
			<span class="tubepress_meta">: </span>fakeid<br />
			
			<span class="tubepress_meta">: </span>4.5<br />
			
			<span class="tubepress_meta">: </span>1000<br />
			
			<span class="tubepress_meta">: </span>12, 31<br />
			 
			
			 
			Fake description. 
			

		</div><!-- tubepress_meta_group -->
	</div><!-- tubepress_video_thumb_inner -->
</div><!-- tubepress_thumb -->

EOT
		, $this->_sut->getHtml("thumbnail.tpl.html", $vid, $this->_player));
	}
}

function stptsCallback()
{
	$args = func_get_args();
   	$vals = array(
		TubePressAdvancedOptions::RANDOM_THUMBS => false,
		TubePressDisplayOptions::THUMB_WIDTH => 40,
		TubePressDisplayOptions::THUMB_HEIGHT => 100,
		TubePressDisplayOptions::DESC_LIMIT => 20,
		TubePressEmbeddedOptions::EMBEDDED_HEIGHT => 500,
		TubePressEmbeddedOptions::EMBEDDED_WIDTH => 600,
		TubePressAdvancedOptions::NOFOLLOW_LINKS => false,
		TubePressMetaOptions::TITLE => true,
		TubePressMetaOptions::LENGTH => true,
		TubePressMetaOptions::DESCRIPTION => true,
		TubePressMetaOptions::AUTHOR => true,
		TubePressMetaOptions::TAGS => true,
		TubePressMetaOptions::URL => true,
		TubePressMetaOptions::VIEWS => true,
		TubePressMetaOptions::ID => true,
		TubePressMetaOptions::RATING => true,
		TubePressMetaOptions::RATINGS => true,
		TubePressMetaOptions::UPLOADED => true,
		TubePressDisplayOptions::RELATIVE_DATES => false,
		TubePressAdvancedOptions::DATEFORMAT => "m, d",
		TubePressMetaOptions::CATEGORY => true
	);
	return $vals[$args[0]];
}
?>