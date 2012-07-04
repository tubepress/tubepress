<?php
require_once dirname(__FILE__) . '/../../../../sys/classes/org/tubepress/impl/http/HttpContentDecoderChain.class.php';
require_once 'AbstractDecoderChainTest.php';

class org_tubepress_impl_http_HttpContentDecoderChainTest extends org_tubepress_impl_http_AbstractDecoderChainTest {

    protected function buildSut()
    {
        return new org_tubepress_impl_http_HttpContentDecoderChain();
    }

    protected function getArrayOfCommands()
    {
        return array(
            'org_tubepress_impl_http_contentencoding_NativeGzipDecompressor',
            'org_tubepress_impl_http_contentencoding_SimulatedGzipDecompressor',
            'org_tubepress_impl_http_contentencoding_NativeDeflateRfc1950Decompressor',
            'org_tubepress_impl_http_contentencoding_NativeRfc1951Decompressor',
        );
    }

    protected function getHeaderName()
    {
        return org_tubepress_api_http_HttpResponse::HTTP_HEADER_CONTENT_ENCODING;
    }

    protected function getHeaderValue()
    {
        return 'chuNkEd';
    }

    function testGetAcceptEncodingHeader()
    {
        $this->assertEquals('gzip;q=1.0, deflate;q=0.5', $this->getSut()->getAcceptEncodingHeaderValue());
    }

}

