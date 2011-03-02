<?php
require_once 'AbstractHttpStrategyTest.php';
require_once dirname(__FILE__) . '/../../../../../../../../sys/classes/org/tubepress/impl/http/clientimpl/strategies/StreamsStrategy.class.php';

class org_tubepress_impl_http_clientimpl_strategies_StreamsStrategyTest extends org_tubepress_impl_http_clientimpl_strategies_AbstractHttpStrategyTest {

    protected function _getSutInstance()
    {
       return new org_tubepress_impl_http_clientimpl_strategies_StreamsStrategy();
    }
}
?>

