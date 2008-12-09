<?php
class TubePressPaginationService_DiggStyleTest extends PHPUnit_Framework_TestCase {
    
	public function testGetHtml100Videos()
	{
		$queryStringService = $this->getMock("TubePressQueryStringService");
		
		$queryStringService->expects($this->once())
			 ->method("getPageNum")
			 ->will($this->returnValue(1));
		
		$queryStringService->expects($this->once())
			 ->method("getFullUrl")
			 ->will($this->returnValue("http://ehough.com"));
		
		$tpom = $this->getMock("TubePressOptionsManager");
		
		$tpom->expects($this->once())
			->method("get")
			->with($this->equalTo(TubePressDisplayOptions::RESULTS_PER_PAGE))
			->will($this->returnValue(4));
		
		$msgService = $this->getMock("TubePressMessageService");
		$msgService->expects($this->any())
				   ->method("_")
				   ->will($this->returnCallback("msgCallback"));	
			
		$sut = new TubePressPaginationService_DiggStyle();
		$sut->setOptionsManager($tpom);
		$sut->setQueryStringService($queryStringService);
		$sut->setMessageService($msgService);
		
		$expected = <<<EOT
<div class="pagination"><span class="disabled">prev</span><span class="current">1</span><a href="http://ehough.com?tubepress_page=2">2</a><a href="http://ehough.com?tubepress_page=3">3</a><a href="http://ehough.com?tubepress_page=4">4</a><a href="http://ehough.com?tubepress_page=5">5</a>... <a href="http://ehough.com?tubepress_page=24">24</a><a href="http://ehough.com?tubepress_page=25">25</a><a href="http://ehough.com?tubepress_page=2">next</a></div>

EOT;
		
		$this->assertEquals($expected, $sut->getHtml(100));
	}
}

function msgCallback()
{
	$args = func_get_args();
	return $args[0];
}
?>