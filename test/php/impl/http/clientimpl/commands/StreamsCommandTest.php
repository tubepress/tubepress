<?php
require_once 'AbstractHttpCommandTest.php';
require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/http/clientimpl/commands/StreamsCommand.class.php';

class org_tubepress_impl_http_clientimpl_commands_StreamsCommandTest extends org_tubepress_impl_http_clientimpl_commands_AbstractHttpCommandTest {

    protected function _getSutInstance()
    {
       return new org_tubepress_impl_http_clientimpl_commands_StreamsCommand();
    }
}


