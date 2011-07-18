<?php

require_once BASE . '/sys/classes/org/tubepress/impl/log/Log.class.php';

class org_tubepress_impl_log_LogImplTest extends TubePressUnitTest {

    public function setup()
    {
        org_tubepress_impl_log_Log::setEnabled(true, array('tubepress_debug' => 'true'));
    }

    public function testLogOneArg()
    {
        ob_start();
        org_tubepress_impl_log_Log::log('prefix', 'message');
        $contents = ob_get_contents();
        ob_end_clean();
        $this->assertTrue(strpos($contents, ' ms (prefix) message (memory: ') !== false);;
    }

    public function testLogTwoArgs()
    {
        ob_start();
        org_tubepress_impl_log_Log::log('prefix', 'message1 %s', 'message2');
        $contents = ob_get_contents();
        ob_end_clean();
        $this->assertTrue(strpos($contents, 'ms (prefix) message1 message2 (memory: ') !== false);
    }
}

