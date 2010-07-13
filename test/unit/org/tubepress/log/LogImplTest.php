<?php

require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/log/Log.class.php';

class org_tubepress_log_LogImplTest extends PHPUnit_Framework_TestCase {

    
    public function testSetEnabled()
    {
        org_tubepress_log_Log::setEnabled(true, array('tubepress_debug' => 'true'));
        org_tubepress_log_Log::log('prefix', 'message');
    }
    
    public function testLog()
    {
        org_tubepress_log_Log::setEnabled(true, array('tubepress_debug' => 'true'));
    }
}
?>