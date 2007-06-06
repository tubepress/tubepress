<?php

require_once 'PHPUnit/Framework.php';
require_once(dirname(__FILE__) . '/../../common/class/TubePressCSS.php');

class CSSTest extends PHPUnit_Framework_TestCase {
	var $o;
	var $cov;
	var $reporter;
	
	function setUp() {
		$this->o = new TubePressCSS();
	}
	
	function testConstructor() {
		$this->assertEquals($this->o->container, "tubepress_container");
		$this->assertEquals($this->o->mainVid_id, "tubepress_mainvideo");
		$this->assertEquals($this->o->mainVid_class, "tubepress_mainvideo");
		$this->assertEquals($this->o->meta_class, "tubepress_meta");
		$this->assertEquals($this->o->thumb_container_class, "tubepress_video_thumbs");
		$this->assertEquals($this->o->thumb_class, "tubepress_thumb");
		$this->assertEquals($this->o->thumbInner, "tubepress_video_thumb_img");
		$this->assertEquals($this->o->runtime_class, "tubepress_runtime");
		$this->assertEquals($this->o->title_class, "tubepress_title");
		$this->assertEquals($this->o->success_class, "updated fade");
		$this->assertEquals($this->o->meta_group, "tubepress_meta_group");
	}
}
?>
