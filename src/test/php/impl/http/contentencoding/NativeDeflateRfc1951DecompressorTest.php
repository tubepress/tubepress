<?php
require_once dirname(__FILE__) . '/../../../../../sys/classes/org/tubepress/impl/http/contentencoding/NativeDeflateRfc1951Decompressor.class.php';
require_once 'AbstractDecompressorTest.php';

class org_tubepress_impl_http_contentencoding_NativeDeflateRfc1951DecompressorTest extends org_tubepress_impl_http_contentencoding_AbstractDecompressorTest {

    protected function buildSut()
    {
        return new org_tubepress_impl_http_contentencoding_NativeDeflateRfc1951Decompressor();
    }

    protected function getHeaderValue()
    {
        return 'deflate';
    }

    protected function getCompressed($data, $level)
    {
        return gzdeflate($data, $level);
    }
}

