<?php

require_once(dirname(__FILE__) . '/../../../common/class/TubePressVideo.php');
require_once(dirname(__FILE__) . '/../../../common/defines.php');


require_once("PEAR.php");

class TubePressVideoTest extends PHPUnit_Framework_TestCase {
    var $v;
    
    function testRealVideo() {
        $this->v['author'] = "3hough";
        $vid = new TubePressVideo($this->v);
        $this->assertTrue($vid->isValid());
    }
    
    function testFakeVideo() {
        $vid = new TubePressVideo("bbla");
        $this->assertFalse($vid->isValid());
    }
    
    function testAlmostVid() {
        $vid = new TubePressVideo($this->v);
        $this->assertFalse($vid->isValid());
    }
    
    function testInvalidValues() {
        $this->v['upload_time'] = "1177894154";
        $this->v['author'] = "3hough";
        $vid = new TubePressVideo($this->v);
        $this->assertTrue($vid->isValid());
        $this->v['upload_time'] = "blabla";
        $vid = new TubePressVideo($this->v);
        $this->assertFalse($vid->isValid());
        $this->v['upload_time'] = "1177894154";
        $vid = new TubePressVideo($this->v);
        $this->assertTrue($vid->isValid());
        
        $this->v['length_seconds'] = "blabla";
        $vid = new TubePressVideo($this->v);
        $this->assertTrue($vid->isValid());
        
        $this->v['length_seconds'] = "111";
        $vid = new TubePressVideo($this->v);
        $this->assertTrue($vid->isValid());
    }
    
    function setUp() {
        $this->v = array();
        $this->v['id'] = "oU-qqkWOKJk";
        $this->v['title'] = "Scuba in the Bahamas";
        $this->v['length_seconds'] = "03";
        $this->v['rating_avg'] = "0.00";
        $this->v['rating_count'] = "0";
        $this->v['description'] = "Katie filmed me jumpin in";
        $this->v['view_count'] = "31";
        $this->v['upload_time'] = "1177894154";
        $this->v['comment_count'] = "0";
        $this->v['tags'] = "scuba bahamas";
        $this->v['url'] = "http://www.youtube.com/?v=oU-qqkWOKJk";
        $this->v['thumbnail_url'] = "http://img.youtube.com/vi/oU-qqkWOKJk/2.jpg";
        $this->v['embed_status'] = "ok";
    }
}
?>
