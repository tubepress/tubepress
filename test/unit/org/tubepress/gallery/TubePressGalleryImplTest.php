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
             
        $youtubeUrlBuilder->expects($this->once())
                          ->method('buildGalleryUrl')
                          ->will($this->returnValue('fakeurl'));

        $insp = $ioc->get(org_tubepress_ioc_IocService::FEED_INSPECTION_YOUTUBE);
        $insp->expects($this->once())
                             ->method('getTotalResultCount')
                             ->with('xml')
                             ->will($this->returnValue(2));
        $ret = $ioc->get(org_tubepress_ioc_IocService::FEED_RETRIEVAL_SERVICE);
        $insp->expects($this->once())
                             ->method('getQueryResultCount')
                             ->with('xml')
                             ->will($this->returnValue(1));
        $ret = $ioc->get(org_tubepress_ioc_IocService::FEED_RETRIEVAL_SERVICE);
        $ret->expects($this->once())
                           ->method('fetch')
                           ->will($this->returnValue('xml'));
                          
        $factory = $ioc->get(org_tubepress_ioc_IocService::VIDEO_FACTORY_YOUTUBE);
        $factory->expects($this->once())
                               ->method('feedToVideoArray')
                               ->will($this->returnValue($this->fakeVideos()));
                               
        $pag = $ioc->get(org_tubepress_ioc_IocService::PAGINATION_SERVICE);
        $pag->expects($this->once())
            ->method('getHtml')
            ->will($this->returnValue('pag'));

        global $_GET;
        $_GET['tubepress_galleryId'] = 'FAKEID';
        
        $this->assertEquals($this->expected(), 
            org_tubepress_gallery_TubePressGallery::getHtml($ioc));
    }
    
    function expected()
    {
        return <<<EOT

<div class="tubepress_container" id="tubepress_gallery_FAKEID">
  <div class="tubepress_normal_embedded_wrapper" style="width: 425px">
    <div id="tubepress_embedded_title_FAKEID" class="tubepress_embedded_title">
    </div>
    <div id="tubepress_embedded_object_FAKEID">
    </div>
  </div>  
  <div id="tubepress_gallery_FAKEID_thumbnail_area" class="tubepress_thumbnail_area">
    pag
    <div class="tubepress_thumbs">
      <div class="tubepress_thumb">
        <a id="tubepress_image__FAKEID" rel="tubepress_youtube_normal_FAKEID"> 
          <img alt="" src="" width="120" height="90" />
        </a>
        <dl class="tubepress_meta_group" style="width: 120px">
          <dt class="tubepress_meta tubepress_meta_title">video-title</dt><dd class="tubepress_meta tubepress_meta_title"><a id="tubepress_title__FAKEID" rel="tubepress_youtube_normal_FAKEID"></a></dd>
          <dt class="tubepress_meta tubepress_meta_runtime">video-length</dt><dd class="tubepress_meta tubepress_meta_runtime"></dd>
          <dt class="tubepress_meta tubepress_meta_views">video-views</dt><dd class="tubepress_meta tubepress_meta_views"></dd>
        </dl>
      </div>
    </div>
    pag  </div>
  <script type="text/javascript">
    jQuery(document).ready(function(){
        TubePress.centerThumbs("#tubepress_gallery_FAKEID");
    });
  </script>
</div>

EOT;
    }
}
?>
