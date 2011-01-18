<?php

require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/impl/single/SimpleSingleVideo.class.php';

class org_tubepress_single_VideoTest extends TubePressUnitTest
{
	private $_sut;
	private $_video;

	function setup()
	{
		$this->initFakeIoc();
		$this->_sut = new org_tubepress_impl_single_SimpleSingleVideo();
	    org_tubepress_impl_log_Log::setEnabled(false, array());
	    $this->_video =  new org_tubepress_api_video_Video();
	    $this->_video->setTitle('fake title');
	}
    
	public function getMock($className)
	{
		$mock = parent::getMock($className);

		switch ($className) {
			case 'org_tubepress_api_provider_Provider':
				$mock->expects($this->any())
					->method('getSingleVideo')
					->will($this->returnValue($this->_video));
			    break;
			case 'org_tubepress_api_patterns_FilterManager':
			    $mock->expects($this->exactly(2))
			         ->method('runFilters')
			         ->will($this->returnCallback(array($this, 'callback')));
		}

		return $mock;
	}

	function testGetHtml()
	{
		$result = $this->_sut->getSingleVideoHtml('someid');
		
		$this->assertEquals($this->expected(), $result);
	}
	
	function expected()
	{
	    return <<<EOT

<div class="tubepress_single_video">
    <dl class="tubepress_meta_group" style="width: px">
</dl>
</div>

EOT;
	}
	
	function callback()
	{
	    $args = func_get_args();
	    return $args[1];
	}
}
?>
