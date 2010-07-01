<?php
require_once dirname(__FILE__) . '/../../../../../classes/net/sourceforge/phpvideotoolkit/PhpVideoToolkit.class.php';

class net_sourceforge_phpvideotoolkit_PhpVideoToolkitTest extends PHPUnit_Framework_TestCase {

	private $_sut;

	function setUp()
	{
		$this->_sut = new net_sourceforge_phpvideotoolkit_PhpVideoToolkit();
	}

	function testGetFileInfo()
	{
		$result = $this->_sut->getFileInfo(dirname(__FILE__) . '/CIMG0873.AVI');	
		$this->assertTrue(is_array($result));
		$this->assertTrue(is_array($result['duration']));
		$this->assertEquals(1519, $result['bitrate']);
	}

	function testHasVhookSupport()
	{
		$this->assertFalse($this->_sut->hasVHookSupport());	
	}

	function testHasFfmpegPHPSupport()
	{
		$this->assertEquals('emulated', $this->_sut->hasFFmpegPHPSupport());	
	}

	function testReset()
	{
		$this->_sut->reset();
	}
	
	function testMicrotimeFloat()
	{
		net_sourceforge_phpvideotoolkit_PhpVideoToolkit::microtimeFloat();
	}
	
	function testGetFFmpegInfo()
	{
		$this->assertTrue(is_array($this->_sut->getFFmpegInfo()));
	}
	
	function testHasCodec()
	{
		$this->assertTrue($this->_sut->hasCodecSupport('3gp'));	
	}
	
	function testDoesntHaveCodec()
	{
		$this->assertFalse($this->_sut->hasCodecSupport('fakecodec'));	
	}
}
?>
