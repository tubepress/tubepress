<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTestSuite.php';
require_once 'HttpTransferDecoderChainTest.php';
require_once 'HttpContentDecoderChainTest.php';
require_once 'HttpClientChainTest.php';
require_once 'DefaultHttpMessageParserTest.php';
require_once 'transferencoding/ChunkedTransferDecoderTest.php';
require_once 'contentencoding/SimulatedGzipDecompressorTest.php';
require_once 'contentencoding/NativeDeflateRfc1951DecompressorTest.php';
require_once 'contentencoding/NativeDeflateRfc1950DecompressorTest.php';
require_once 'transports/FakeTransportTest.php';
require_once 'transports/CurlTransportTest.php';
require_once 'transports/ExtHttpTransportTest.php';
require_once 'transports/FopenTransportTest.php';
require_once 'transports/FsockOpenTransportTest.php';
require_once 'transports/StreamsTransportTest.php';
require_once 'HttpResponseHandlerChainTest.php';
require_once 'responsehandling/YouTubeHttpErrorResponseHandlerTest.php';

class org_tubepress_impl_http_HttpTestSuite
{
    public static function suite()
    {
        return new TubePressUnitTestSuite(array(
            'org_tubepress_impl_http_responsehandling_YouTubeHttpErrorResponseHandlerTest',
            'org_tubepress_impl_http_HttpTransferDecoderChainTest',
            'org_tubepress_impl_http_HttpContentDecoderChainTest',
            'org_tubepress_impl_http_HttpClientChainTest',
            'org_tubepress_impl_http_DefaultHttpMessageParserTest',
            'org_tubepress_impl_http_transferencoding_ChunkedTransferDecoderTest',
            'org_tubepress_impl_http_contentencoding_SimulatedGzipDecompressorTest',
            'org_tubepress_impl_http_contentencoding_NativeDeflateRfc1951DecompressorTest',
            'org_tubepress_impl_http_contentencoding_NativeDeflateRfc1950DecompressorTest',
            'org_tubepress_impl_http_transports_FakeTransportTest',
            'org_tubepress_impl_http_transports_CurlTransportTest',
	        'org_tubepress_impl_http_transports_ExtHttpTransportTest',
	        'org_tubepress_impl_http_transports_FopenTransportTest',
	        'org_tubepress_impl_http_transports_FsockOpenTransportTest',
            'org_tubepress_impl_http_transports_StreamsTransportTest',
            'org_tubepress_impl_http_HttpResponseHandlerChainTest',
        ));
    }
}

