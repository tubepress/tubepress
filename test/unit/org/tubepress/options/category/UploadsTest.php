<?php
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/options/category/Uploads.class.php';
require_once 'AbstractOptionsCategoryTest.php';

class org_tubepress_options_category_UploadsTest extends org_tubepress_options_category_AbstractOptionsCategoryTest
{
    protected function getClassName()
    {
        return 'org_tubepress_options_category_Uploads';
    }
    
    protected function getExpectedNames()
    {
        return array('ffmpegBinary', 'thumbsPerVideo');
    }
}
?>