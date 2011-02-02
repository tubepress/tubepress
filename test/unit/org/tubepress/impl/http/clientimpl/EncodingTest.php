<?php
require_once dirname(__FILE__) . '/../../../../../../../test/unit/TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/impl/http/clientimpl/Encoding.class.php';

class org_tubepress_impl_http_clientimpl_EncodingTest extends TubePressUnitTest {

    private $_sut;
    
    function setup()
    {
        $this->initFakeIoc();
        $this->_sut = new org_tubepress_impl_http_clientimpl_Encoding();
    }

    function testSimulatedDecompress()
    {
        $this->assertEquals('', org_tubepress_impl_http_clientimpl_Encoding::simulatedGzInflate("\x1f\x8b\x08" . org_tubepress_impl_http_clientimpl_Encoding::compress('something')));
    }
    
    function testDecompress()
    {
        $this->assertEquals('something', org_tubepress_impl_http_clientimpl_Encoding::decompress(org_tubepress_impl_http_clientimpl_Encoding::compress('something')));
    }
    
    function testDecompressEmpty()
    {
        $this->assertTrue(org_tubepress_impl_http_clientimpl_Encoding::decompress(null) === null);
    }
    
    function testGetAcceptEncodingHeaderString()
    {
        $this->assertEquals('deflate;q=1.0, compress;q=0.5', org_tubepress_impl_http_clientimpl_Encoding::getAcceptEncodingString());
    }
    
    function testGetContentEncodingString()
    {
        $this->assertEquals('deflate', org_tubepress_impl_http_clientimpl_Encoding::getContentEncodingString());
    }
}
?>

