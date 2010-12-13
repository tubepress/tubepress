<?php

require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/impl/log/Log.class.php';
require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';

class org_tubepress_impl_log_LogImplTest extends TubePressUnitTest {

    public function setup()
    {
        org_tubepress_impl_log_Log::setEnabled(true, array('tubepress_debug' => 'true'));        
    }
    
    public function testLogOneArg()
    {
        org_tubepress_impl_log_Log::log('prefix', 'message');
    }
    
    public function testLogTwoArgs()
    {
        org_tubepress_impl_log_Log::log('prefix', 'message1 %s', 'message2');
    }
}
?>
