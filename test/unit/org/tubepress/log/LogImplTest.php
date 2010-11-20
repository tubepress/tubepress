<?php

require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/util/Log.class.php';
require_once dirname(__FILE__) . '/../../../../../test/unit/TubePressUnitTest.php';

class org_tubepress_util_LogImplTest extends TubePressUnitTest {

    public function setup()
    {
        org_tubepress_util_Log::setEnabled(true, array('tubepress_debug' => 'true'));        
    }
    
    public function testLogOneArg()
    {
        org_tubepress_util_Log::log('prefix', 'message');
    }
    
    public function testLogTwoArgs()
    {
        org_tubepress_util_Log::log('prefix', 'message1 %s', 'message2');
    }
}
?>
