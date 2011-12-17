<?php
require_once dirname(__FILE__) . '/../../../../sys/classes/org/tubepress/impl/http/HttpTransferDecoderChain.class.php';
require_once 'AbstractDecoderChainTest.php';

class org_tubepress_impl_http_HttpTransferDecoderChainTest extends org_tubepress_impl_http_AbstractDecoderChainTest {

    protected function buildSut()
    {
        return new org_tubepress_impl_http_HttpTransferDecoderChain();
    }

    protected function getArrayOfCommands()
    {
        return array('org_tubepress_impl_http_transferencoding_ChunkedTransferDecoder');
    }

    protected function getHeaderName()
    {
        return org_tubepress_api_http_HttpResponse::HTTP_HEADER_TRANSFER_ENCODING;
    }

}

