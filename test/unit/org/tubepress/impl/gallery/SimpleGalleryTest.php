<?php

require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/impl/gallery/SimpleGallery.class.php';

class org_tubepress_impl_gallery_SimpleGalleryTest extends TubePressUnitTest
{
	private $_sut;
	private $_shouldParse;

	function setup()
	{
		$this->initFakeIoc();
		$this->_sut = new org_tubepress_impl_gallery_SimpleGallery();
	}

	public function getMock($className)
	{
		$mock = parent::getMock($className);

		switch ($className) {
			case 'org_tubepress_api_patterns_StrategyManager':
				$mock->expects($this->once())
					->method('executeStrategy')
					->with($this->equalTo(array(
            'org_tubepress_impl_gallery_strategies_SingleVideoStrategy',
            'org_tubepress_impl_gallery_strategies_SoloPlayerStrategy',
            'org_tubepress_impl_gallery_strategies_ThumbGalleryStrategy'
        )))
					->will($this->returnValue('boop'));
				break;
		}

		return $mock;
	}

	function callback()
	{
	    
	}
	
    function testGetHtml()
    {
	    $result = $this->_sut->getHtml();
        $this->assertEquals('boop', $result);
    }

}
?>
