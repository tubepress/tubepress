<?php

require_once(dirname(__FILE__) . '/../../common/TubePressCSS.php');

class CSSTest extends UnitTestCase {
	var $o;
	var $cov;
	var $reporter;
	
	function setUp() {
		$this->o = new TubePressCSS();
		
	}
	
	function testConstructor() {
		$this->assertEqual($this->o->container, "tubepress_container", $this->o->container);
		$this->assertEqual($this->o->mainVid_id, "tubepress_mainvideo", $this->o->container);
		$this->assertEqual($this->o->mainVid_class, "tubepress_mainvideo", $this->o->container);
		$this->assertEqual($this->o->meta_class, "tubepress_meta", $this->o->container);
		$this->assertEqual($this->o->thumb_container_class, "tubepress_video_thumbs", $this->o->container);
		$this->assertEqual($this->o->thumb_class, "tubepress_thumb", $this->o->container);
		$this->assertEqual($this->o->thumbInner, "tubepress_video_thumb_img", $this->o->container);
		$this->assertEqual($this->o->runtime_class, "tubepress_runtime", $this->o->container);
		$this->assertEqual($this->o->title_class, "tubepress_title", $this->o->container);
		$this->assertEqual($this->o->success_class, "updated fade", $this->o->container);
		$this->assertEqual($this->o->meta_group, "tubepress_meta_group", $this->o->container);
	}
}
?>
