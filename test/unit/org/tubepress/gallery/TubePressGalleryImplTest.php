<?php

require_once dirname(__FILE__) . '/../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/gallery/TubePressGallery.class.php';

class org_tubepress_gallery_TubePressGalleryImplTest extends TubePressUnitTest
{
    function testGetHtml()
    {
        $ioc = $this->getIoc();
        $tpom = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
        $youtubeUrlBuilder = $ioc->get(org_tubepress_ioc_IocService::URL_BUILDER_YOUTUBE);
        
        $tpom->expects($this->exactly(7))
             ->method('get')
             ->will($this->returnCallback(array('TubePressUnitTest', 'tpomCallback')));
             
        $youtubeUrlBuilder->expects($this->once())
                          ->method('buildGalleryUrl')
                          ->will($this->returnValue('fakeurl'));
             
        $this->assertEquals($this->expected(), 
            org_tubepress_gallery_TubePressGallery::getHtml($ioc, 'FAKEID'));
    }
    
    function expected()
    {
        return 'foo';
    }
}
?>
