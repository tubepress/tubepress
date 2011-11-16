<?php
require_once dirname(__FILE__) . '/../../../../../sys/classes/org/tubepress/impl/http/contentencoding/SimulatedGzipDecompressor.class.php';
require_once 'AbstractDecompressorTest.php';

class org_tubepress_impl_http_contentencoding_SimulatedGzipDecompressorTest extends org_tubepress_impl_http_contentencoding_AbstractDecompressorTest {

    protected function buildSut()
    {
        return new org_tubepress_impl_http_contentencoding_SimulatedGzipDecompressor();
    }

    protected function getHeaderValue()
    {
        return 'gzip';
    }

    protected function getCompressed($data, $level)
    {
        return gzencode($data, $level);
    }

    function testDecompressFile()
    {
        global $data;

        $compressed = file_get_contents(dirname(__FILE__) . '/data.txt.gz');

        $entity   = \Mockery::mock(org_tubepress_api_http_HttpEntity::_);

        $this->getResponse()->shouldReceive('getEntity')->once()->andReturn($entity);
        $this->getResponse()->shouldReceive('getHeaderValue')->once()->with(org_tubepress_api_http_HttpResponse::HTTP_HEADER_CONTENT_ENCODING)->andReturn($this->getHeaderValue());
        $entity->shouldReceive('getContent')->once()->andReturn($compressed);

        $result = $this->getSut()->execute($this->getContext());

        $this->assertTrue($result);

        $decoded = $this->getContext()->decoded;
        $this->assertNotNull($decoded);

        $this->assertEquals($data, $decoded);
    }
}

