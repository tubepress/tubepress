<?php

require_once BASE . '/sys/classes/org/tubepress/api/http/Url.class.php';

class org_tubepress_api_http_UrlTest extends TubePressUnitTest {

	private $_sut;

	function setup()
	{
	    $this->_sut = new org_tubepress_api_http_Url('http://user@tubepress.org:994/something/index.php?one=two+four&three=four#fragment/one/three?poo');
	}

	function testSetQueryVariables()
	{
	    $arr = array('orange' => null, 'bear' => 'claws are good');
	    $this->_sut->setQueryVariables($arr);
	    $this->assertEquals($arr, $this->_sut->getQueryVariables());
	    $this->assertEquals('orange&bear=claws+are+good', $this->_sut->getQuery());
	    $this->assertEquals('http://user@tubepress.org:994/something/index.php?orange&bear=claws+are+good#fragment/one/three?poo', $this->_sut->toString());
	}

	/**
	* @expectedException Exception
	*/
	function testSetQueryVariablesNonArray()
	{
	    $this->_sut->setQueryVariables('something');
	}

	function testUnsetQueryVariable()
	{
	    $this->_sut->unsetQueryVariable('three');
	    $this->assertEquals(array('one' => 'two four'), $this->_sut->getQueryVariables());
	    $this->assertEquals('http://user@tubepress.org:994/something/index.php?one=two+four#fragment/one/three?poo', $this->_sut->toString());
	}

	function testGetQueryVariablesSimple()
	{
	    $this->_sut = new org_tubepress_api_http_Url('http://user@tubepress.org:994/something/index.php?one&three=four#fragment/one/three?poo');
	    $this->assertEquals(array('one' => null, 'three' => 'four'), $this->_sut->getQueryVariables());
	}

	function testGetQueryVariablesNormal()
	{
	    $this->assertEquals(array('one' => 'two four', 'three' => 'four'), $this->_sut->getQueryVariables());
	}

	function testSetFragment()
	{
	    $this->_sut->setFragment('hello/fool');
	    $this->assertEquals('hello/fool', $this->_sut->getFragment());
	    $this->assertEquals('http://user@tubepress.org:994/something/index.php?one=two+four&three=four#hello/fool', $this->_sut->toString());
	}

	/**
	 * @expectedException Exception
	 */
	function testSetInvalidFragment()
	{
	    $this->_sut->setFragment('(*&#$///sdfsfdsf//#@)(*@#');
	}

	/**
	 * @expectedException Exception
	 */
	function testSetNonStringFragment()
	{
	    $this->_sut->setFragment(4);
	}

	function testSetQuery()
	{
	    $this->_sut->setQuery('something=awesome&other=stuff');
	    $this->assertEquals('something=awesome&other=stuff', $this->_sut->getQuery());
	    $this->assertEquals('http://user@tubepress.org:994/something/index.php?something=awesome&other=stuff#fragment/one/three?poo', $this->_sut->toString());
	}

	/**
	 * @expectedException Exception
	 */
	function testSetInvalidQuery()
	{
	    $this->_sut->setQuery('(*&#$///sdfsfdsf//#@)(*@#');
	}

	/**
	 * @expectedException Exception
	 */
	function testSetNonStringQuery()
	{
	    $this->_sut->setQuery(4);
	}

	function testSetPath()
	{
	    $this->_sut->setPath('/hello/dolly');
	    $this->assertEquals('/hello/dolly', $this->_sut->getPath());
	    $this->assertEquals('http://user@tubepress.org:994/hello/dolly?one=two+four&three=four#fragment/one/three?poo', $this->_sut->toString());
	}

	/**
	* @expectedException Exception
	*/
	function testSetInvalidPath()
	{
	    $this->_sut->setPath('(*&#$///sdfsfdsf//#@)(*@#');
	}

	/**
	* @expectedException Exception
	*/
	function testSetNonStringPath()
	{
	    $this->_sut->setPath(4);
	}

	function testSetPort()
	{
	    $this->_sut->setPort(44);
	    $this->assertEquals(44, $this->_sut->getPort());
	    $this->assertEquals('http://user@tubepress.org:44/something/index.php?one=two+four&three=four#fragment/one/three?poo', $this->_sut->toString());
	}

	/**
	* @expectedException Exception
	*/
	function testSetNegativePort()
	{
	    $this->_sut->setPort(-1);
	}

	/**
	* @expectedException Exception
	*/
	function testSetZeroPort()
	{
	    $this->_sut->setPort(0);
	}

	/**
	* @expectedException Exception
	*/
	function testSetNonNumericPort()
	{
	    $this->_sut->setPort('two');
	}

	function testSetUser()
	{
	    $this->_sut->setUser('billybob');
	    $this->assertEquals('billybob', $this->_sut->getUser());
	    $this->assertEquals('http://billybob@tubepress.org:994/something/index.php?one=two+four&three=four#fragment/one/three?poo', $this->_sut->toString());
	}

	/**
	* @expectedException Exception
	*/
	function testSetInvalidUser()
	{
	    $this->_sut->setUser('#@$)(*$%');
	}

	function testSetHostname()
	{
	    $this->_sut->setHostName('ehough.com');
	    $this->assertEquals('ehough.com', $this->_sut->getHost());
	    $this->assertEquals('http://user@ehough.com:994/something/index.php?one=two+four&three=four#fragment/one/three?poo', $this->_sut->toString());
	}

	/**
	* @expectedException Exception
	*/
	function testSetInvalidHostname()
	{
	    $this->_sut->setHostName('&&^$');
	}

	function testSetGetScheme()
	{
	    $scheme = 'ftp+.-hTtp';
	    $this->_sut->setScheme($scheme);
	    $this->assertEquals(strtolower($scheme), $this->_sut->getScheme());
	    $this->assertEquals('ftp+.-http://user@tubepress.org:994/something/index.php?one=two+four&three=four#fragment/one/three?poo', $this->_sut->toString());
	}

	/**
	* @expectedException Exception
	*/
	function testSetInvalidScheme()
	{
	    $this->_sut->setScheme('#foobar?yello');
	}

	/**
	 * @expectedException Exception
	 */
	function testSetNonStringScheme()
	{
	    $this->_sut->setScheme(4);
	}

	function testIpv6Constructors()
	{
	    $ipv6 = $this->_getEasyIpv6Cases();

	    foreach ($ipv6 as $case) {

	        if ($case[1] === true) {

	            $this->_testConstructValidIpv6($case[0]);

	        } else {

	            $this->_testConstructInvalidIpv6($case[0]);
	        }
	    }
	}

	function testIpv6Setter()
	{
	    $url = new org_tubepress_api_http_Url("http://[123::]/foo/bar?something#nine/two");
	    $this->assertEquals('123::', $url->getHost());

	    $ipv6 = $this->_getDifficultIpv6Cases();

	    foreach ($ipv6 as $case) {

	        if ($case[1] === true) {

	            $this->_testSetValidIpv6($url, $case[0]);

	        } else {

	            $this->_testSetInvalidIpv6($url, $case[0]);
	        }
	    }
	}

	private function _testSetValidIpv6($url, $ip)
	{
        $url->setHostIp($ip);
        $this->assertTrue($url->getHost() === strtolower(trim($ip)), "Expected '$ip' but got '" . $url->getHost() . "'");
	}

	private function _testSetInvalidIpv6($url, $ip)
	{
	    $bad = false;

	    try {

	        $url->setHostIp($ip);

	    } catch (Exception $e) {

            $bad = true;
	    }

	    $this->assertTrue($bad, "$ip should not have validated");
	}

	private function _testConstructValidIpv6($ip)
	{
        $url = new org_tubepress_api_http_Url("http://[$ip]:89/foo/bar?fickle#niner/eight");

        $this->assertEquals(strtolower($ip), $url->getHost());
	}

	private function _testConstructInvalidIpv6($ip)
	{
	    $bad = false;

	    try {

	        $url = new org_tubepress_api_http_Url("http://[$ip]:89/foo/bar?fickle#niner/eight");

	    } catch (Exception $e) {

	        $bad = true;
	    }

	    $this->assertTrue($bad, "$ip should not have passed validation");
	}

	function testGetFragment()
	{
	    $this->assertEquals('fragment/one/three?poo', $this->_sut->getFragment());
	}

    function testSet()
    {
        $this->_sut->setQueryVariable('three', 'five');
        $this->_sut->setQueryVariable('nine', 'ten');

        $this->assertEquals('http://user@tubepress.org:994/something/index.php?one=two+four&three=five&nine=ten#fragment/one/three?poo', $this->_sut->toString());
    }

//     function testUnset()
//     {
//         $this->_sut->unsetQueryVariable('three');

//         $this->assertEquals('http://user@tubepress.org:994/something/index.php?one=two+four&nine=ten#fragment/one/three?poo', $this->_sut->toString());
//     }

    private function _getEasyIpv6Cases()
    {
        //https://github.com/strattg/ipv6-address-test/blob/master/Tests/Ipv6TestCase.php

        return array(

            array('', false),
            array('2001:0000:1234:0000:0000:C1C0:ABCD:0876', true),
            array('3ffe:0b00:0000:0000:0001:0000:0000:000a', true),
            array('FF02:0000:0000:0000:0000:0000:0000:0001', true),
            array('0000:0000:0000:0000:0000:0000:0000:0001', true),
            array('0000:0000:0000:0000:0000:0000:0000:0000', true),
            array('02001:0000:1234:0000:0000:C1C0:ABCD:0876', false), // extra 0 not allowed!
            array('2001:0000:1234:0000:00001:C1C0:ABCD:0876', false), // extra 0 not allowed!
            array('2001:0000:1234:0000:0000:C1C0:ABCD:0876', true), // trailing space
            array('2001:0000:1234: 0000:0000:C1C0:ABCD:0876', false), // internal space
            array('::ffff:192x168.1.26', false),
            array('FF02:0000:0000:0000:0000:0000:0000:0000:0001', false), // nine segments
            array('::1111:2222:3333:4444:5555:6666::', false), // double '::'
            array('2::10', true),
            array('ff02::1', true),
            array('fe80::', true),
            array('2002::', true),
            array('2001:db8::', true),
            array('2001:0db8:1234::', true),
            array('::ffff:0:0', true),
            array('::1', true),
            array('1:2:3:4:5:6:7:8', true),
            array('1:2:3:4:5:6::8', true),
            array('1:2:3:4:5::8', true),
            array('1:2:3:4::8', true),
            array('1:2:3::8', true),
            array('1:2::8', true),
            array('1::8', true),
            array('1::2:3:4:5:6:7', true),
            array('1::2:3:4:5:6', true),
            array('1::2:3:4:5', true),
            array('1::2:3:4', true),
            array('1::2:3', true),
            array('1::8', true),
            array('::2:3:4:5:6:7:8', true),
            array('::2:3:4:5:6:7', true),
            array('::2:3:4:5:6', true),
            array('::2:3:4:5', true),
            array('::2:3:4', true),
            array('::2:3', true),
            array('::8', true),
            array('1:2:3:4:5:6::', true),
            array('1:2:3:4:5::', true),
            array('1:2:3:4::', true),
            array('1:2:3::', true),
            array('1:2::', true),
            array('1::', true),
            array('1:2:3:4:5::7:8', true),
            array('1:2:3::4:5::7:8', false), // Double '::'
            array('12345::6:7:8', false),
            array('1:2:3:4::7:8', true),
            array('1:2:3::7:8', true),
            array('1:2::7:8', true),
            array('1::7:8', true),
            array('fe80::217:f2ff:fe07:ed62', true),
            array('2001:DB8:0:0:8:800:200C:417A', true), // unicast, full
            array('FF01:0:0:0:0:0:0:101', true), // multicast, full
            array('0:0:0:0:0:0:0:1', true), // loopback, full
            array('0:0:0:0:0:0:0:0', true), // unspecified, full
            array('2001:DB8::8:800:200C:417A', true), // unicast, compressed
            array('FF01::101', true), // multicast, compressed
            array('::1', true), // loopback, compressed, non-routable
            array('::', true), // unspecified, compressed, non-routable
            array('2001:DB8:0:0:8:800:200C:417A:221', false), // unicast, full
            array('FF01::101::2', false), // multicast, compressed
            array('', false), // nothing
            array('fe80:0000:0000:0000:0204:61ff:fe9d:f156', true),
            array('fe80:0:0:0:204:61ff:fe9d:f156', true),
            array('fe80::204:61ff:fe9d:f156', true),
            array('fe80:0000:0000:0000:0204:61ff:254.157.241.086', false),
            array('::1', true),
            array('fe80::', true),
            array('fe80::1', true),
            array(':', false),
            array('1111:2222:3333:4444::5555:', false),
            array('1111:2222:3333::5555:', false),
            array('1111:2222::5555:', false),
            array('1111::5555:', false),
            array('::5555:', false),
            array(':::', false),
            array('1111:', false),
            array(':', false),
            array(':1111:2222:3333:4444::5555', false),
            array(':1111:2222:3333::5555', false),
            array(':1111:2222::5555', false),
            array(':1111::5555', false),
            array(':::5555', false),
            array(':::', false),
            array('1.2.3.4:1111:2222:3333:4444::5555', false),
            array('1.2.3.4:1111:2222:3333::5555', false),
            array('1.2.3.4:1111:2222::5555', false),
            array('1.2.3.4:1111::5555', false),
            array('1.2.3.4::5555', false),
            array('1.2.3.4::', false),
            array('2001:0db8:85a3:0000:0000:8a2e:0370:7334', true),
            array('2001:db8:85a3:0:0:8a2e:370:7334', true),
            array('2001:db8:85a3::8a2e:370:7334', true),
            array('2001:0db8:0000:0000:0000:0000:1428:57ab', true),
            array('2001:0db8:0000:0000:0000::1428:57ab', true),
            array('2001:0db8:0:0:0:0:1428:57ab', true),
            array('2001:0db8:0:0::1428:57ab', true),
            array('2001:0db8::1428:57ab', true),
            array('2001:db8::1428:57ab', true),
            array('0000:0000:0000:0000:0000:0000:0000:0001', true),
            array('::1', true),
            array('::ffff:0c22:384e', true),
            array('2001:0db8:1234:0000:0000:0000:0000:0000', true),
            array('2001:0db8:1234:ffff:ffff:ffff:ffff:ffff', true),
            array('2001:db8:a::123', true),
            array('fe80::', true),
            array('::ffff:c000:280', true),
            array('123', false),
            array('2001:db8:85a3::8a2e:37023:7334', false),
            array('2001:db8:85a3::8a2e:370k:7334', false),
            array('1:2:3:4:5:6:7:8:9', false),
            array('1::2::3', false),
            array('1:::3:4:5', false),
            array('1:2:3::4:5:6:7:8:9', false),
            array('::ffff:2.3.4', false),
            array('::ffff:257.1.2.3', false),
            array('1.2.3.4', false),
            array('1111:2222:3333:4444:5555:6666:7777:8888', true),
            array('1111:2222:3333:4444:5555:6666:7777::', true),
            array('1111:2222:3333:4444:5555:6666::', true),
            array('1111:2222:3333:4444:5555::', true),
            array('1111:2222:3333:4444::', true),
            array('1111:2222:3333::', true),
            array('1111:2222::', true),
            array('1111::', true),
            array('::', true),
            array('1111:2222:3333:4444:5555:6666::8888', true),
            array('1111:2222:3333:4444:5555::8888', true),
            array('1111:2222:3333:4444::8888', true),
            array('1111:2222:3333::8888', true),
            array('1111:2222::8888', true),
            array('1111::8888', true),
            array('::8888', true),
            array('1111:2222:3333:4444:5555::7777:8888', true),
            array('1111:2222:3333:4444::7777:8888', true),
            array('1111:2222:3333::7777:8888', true),
            array('1111:2222::7777:8888', true),
            array('1111::7777:8888', true),
            array('::7777:8888', true),
            array('1111:2222:3333:4444::6666:7777:8888', true),
            array('1111:2222:3333::6666:7777:8888', true),
            array('1111:2222::6666:7777:8888', true),
            array('1111::6666:7777:8888', true),
            array('::6666:7777:8888', true),
            array('1111:2222:3333::5555:6666:7777:8888', true),
            array('1111:2222::5555:6666:7777:8888', true),
            array('1111::5555:6666:7777:8888', true),
            array('::5555:6666:7777:8888', true),
            array('1111:2222::4444:5555:6666:7777:8888', true),
            array('1111::4444:5555:6666:7777:8888', true),
            array('::4444:5555:6666:7777:8888', true),
            array('1111::3333:4444:5555:6666:7777:8888', true),
            array('::3333:4444:5555:6666:7777:8888', true),
            array('::2222:3333:4444:5555:6666:7777:8888', true),
            array('1111:2222:3333:4444:5555:6666:7777:8888:9999', false),
            array('1111:2222:3333:4444:5555:6666:7777:8888::', false),
            array('::2222:3333:4444:5555:6666:7777:8888:9999', false),
            array('1111:2222:3333:4444:5555:6666:7777', false),
            array('1111:2222:3333:4444:5555:6666', false),
            array('1111:2222:3333:4444:5555', false),
            array('1111:2222:3333:4444', false),
            array('1111:2222:3333', false),
            array('1111:2222', false),
            array('1111', false),
            array('11112222:3333:4444:5555:6666:7777:8888', false),
            array('1111:22223333:4444:5555:6666:7777:8888', false),
            array('1111:2222:33334444:5555:6666:7777:8888', false),
            array('1111:2222:3333:44445555:6666:7777:8888', false),
            array('1111:2222:3333:4444:55556666:7777:8888', false),
            array('1111:2222:3333:4444:5555:66667777:8888', false),
            array('1111:2222:3333:4444:5555:6666:77778888', false),
            array('1111:2222:3333:4444:5555:6666:7777:8888:', false),
            array('1111:2222:3333:4444:5555:6666:7777:', false),
            array('1111:2222:3333:4444:5555:6666:', false),
            array('1111:2222:3333:4444:5555:', false),
            array('1111:2222:3333:4444:', false),
            array('1111:2222:3333:', false),
            array('1111:2222:', false),
            array('1111:', false),
            array(':', false),
            array(':8888', false),
            array(':7777:8888', false),
            array(':6666:7777:8888', false),
            array(':5555:6666:7777:8888', false),
            array(':4444:5555:6666:7777:8888', false),
            array(':3333:4444:5555:6666:7777:8888', false),
            array(':2222:3333:4444:5555:6666:7777:8888', false),
            array(':1111:2222:3333:4444:5555:6666:7777:8888', false),
            array(':::2222:3333:4444:5555:6666:7777:8888', false),
            array('1111:::3333:4444:5555:6666:7777:8888', false),
            array('1111:2222:::4444:5555:6666:7777:8888', false),
            array('1111:2222:3333:::5555:6666:7777:8888', false),
            array('1111:2222:3333:4444:::6666:7777:8888', false),
            array('1111:2222:3333:4444:5555:::7777:8888', false),
            array('1111:2222:3333:4444:5555:6666:::8888', false),
            array('1111:2222:3333:4444:5555:6666:7777:::', false),
            array('::2222::4444:5555:6666:7777:8888', false),
            array('::2222:3333::5555:6666:7777:8888', false),
            array('::2222:3333:4444::6666:7777:8888', false),
            array('::2222:3333:4444:5555::7777:8888', false),
            array('::2222:3333:4444:5555:7777::8888', false),
            array('::2222:3333:4444:5555:7777:8888::', false),
            array('1111::3333::5555:6666:7777:8888', false),
            array('1111::3333:4444::6666:7777:8888', false),
            array('1111::3333:4444:5555::7777:8888', false),
            array('1111::3333:4444:5555:6666::8888', false),
            array('1111::3333:4444:5555:6666:7777::', false),
            array('1111:2222::4444::6666:7777:8888', false),
            array('1111:2222::4444:5555::7777:8888', false),
            array('1111:2222::4444:5555:6666::8888', false),
            array('1111:2222::4444:5555:6666:7777::', false),
            array('1111:2222:3333::5555::7777:8888', false),
            array('1111:2222:3333::5555:6666::8888', false),
            array('1111:2222:3333::5555:6666:7777::', false),
            array('1111:2222:3333:4444::6666::8888', false),
            array('1111:2222:3333:4444::6666:7777::', false),
            array('1111:2222:3333:4444:5555::7777::', false),
            array('XXXX:XXXX:XXXX:XXXX:XXXX:XXXX:1.2.3.4', false),
            array('1111:2222:3333:4444:5555:6666:00.00.00.00', false),
            array('1111:2222:3333:4444:5555:6666:000.000.000.000', false),
            array('1111:2222:3333:4444:5555:6666:256.256.256.256', false),
            array('1111:2222:3333:4444:5555:6666:7777:8888:1.2.3.4', false),
            array('1111:2222:3333:4444:5555:6666:7777:1.2.3.4', false),
            array('1111:2222:3333:4444:5555:6666::1.2.3.4', false),
            array('::2222:3333:4444:5555:6666:7777:1.2.3.4', false),
            array('1111:2222:3333:4444:5555:6666:1.2.3.4.5', false),
            array('1111:2222:3333:4444:5555:1.2.3.4', false),
            array('1111:2222:3333:4444:1.2.3.4', false),
            array('1111:2222:3333:1.2.3.4', false),
            array('1111:2222:1.2.3.4', false),
            array('1111:1.2.3.4', false),
            array('1.2.3.4', false),
            array('11112222:3333:4444:5555:6666:1.2.3.4', false),
            array('1111:22223333:4444:5555:6666:1.2.3.4', false),
            array('1111:2222:33334444:5555:6666:1.2.3.4', false),
            array('1111:2222:3333:44445555:6666:1.2.3.4', false),
            array('1111:2222:3333:4444:55556666:1.2.3.4', false),
            array('1111:2222:3333:4444:5555:66661.2.3.4', false),
            array('1111:2222:3333:4444:5555:6666:255255.255.255', false),
            array('1111:2222:3333:4444:5555:6666:255.255255.255', false),
            array('1111:2222:3333:4444:5555:6666:255.255.255255', false),
            array(':1.2.3.4', false),
            array(':6666:1.2.3.4', false),
            array(':5555:6666:1.2.3.4', false),
            array(':4444:5555:6666:1.2.3.4', false),
            array(':3333:4444:5555:6666:1.2.3.4', false),
            array(':2222:3333:4444:5555:6666:1.2.3.4', false),
            array(':1111:2222:3333:4444:5555:6666:1.2.3.4', false),
            array(':::2222:3333:4444:5555:6666:1.2.3.4', false),
            array('1111:::3333:4444:5555:6666:1.2.3.4', false),
            array('1111:2222:::4444:5555:6666:1.2.3.4', false),
            array('1111:2222:3333:::5555:6666:1.2.3.4', false),
            array('1111:2222:3333:4444:::6666:1.2.3.4', false),
            array('1111:2222:3333:4444:5555:::1.2.3.4', false),
            array('::2222::4444:5555:6666:1.2.3.4', false),
            array('::2222:3333::5555:6666:1.2.3.4', false),
            array('::2222:3333:4444::6666:1.2.3.4', false),
            array('::2222:3333:4444:5555::1.2.3.4', false),
            array('1111::3333::5555:6666:1.2.3.4', false),
            array('1111::3333:4444::6666:1.2.3.4', false),
            array('1111::3333:4444:5555::1.2.3.4', false),
            array('1111:2222::4444::6666:1.2.3.4', false),
            array('1111:2222::4444:5555::1.2.3.4', false),
            array('1111:2222:3333::5555::1.2.3.4', false),
            array('::.', false),
            array('::..', false),
            array('::...', false),
            array('::1...', false),
            array('::1.2..', false),
            array('::1.2.3.', false),
            array('::.2..', false),
            array('::.2.3.', false),
            array('::.2.3.4', false),
            array('::..3.', false),
            array('::..3.4', false),
            array('::...4', false),
            array(':1111:2222:3333:4444:5555:6666:7777::', false),
            array(':1111:2222:3333:4444:5555:6666::', false),
            array(':1111:2222:3333:4444:5555::', false),
            array(':1111:2222:3333:4444::', false),
            array(':1111:2222:3333::', false),
            array(':1111:2222::', false),
            array(':1111::', false),
            array(':::', false),
            array(':1111:2222:3333:4444:5555:6666::8888', false),
            array(':1111:2222:3333:4444:5555::8888', false),
            array(':1111:2222:3333:4444::8888', false),
            array(':1111:2222:3333::8888', false),
            array(':1111:2222::8888', false),
            array(':1111::8888', false),
            array(':::8888', false),
            array(':1111:2222:3333:4444:5555::7777:8888', false),
            array(':1111:2222:3333:4444::7777:8888', false),
            array(':1111:2222:3333::7777:8888', false),
            array(':1111:2222::7777:8888', false),
            array(':1111::7777:8888', false),
            array(':::7777:8888', false),
            array(':1111:2222:3333:4444::6666:7777:8888', false),
            array(':1111:2222:3333::6666:7777:8888', false),
            array(':1111:2222::6666:7777:8888', false),
            array(':1111::6666:7777:8888', false),
            array(':::6666:7777:8888', false),
            array(':1111:2222:3333::5555:6666:7777:8888', false),
            array(':1111:2222::5555:6666:7777:8888', false),
            array(':1111::5555:6666:7777:8888', false),
            array(':::5555:6666:7777:8888', false),
            array(':1111:2222::4444:5555:6666:7777:8888', false),
            array(':1111::4444:5555:6666:7777:8888', false),
            array(':::4444:5555:6666:7777:8888', false),
            array(':1111::3333:4444:5555:6666:7777:8888', false),
            array(':::3333:4444:5555:6666:7777:8888', false),
            array(':::2222:3333:4444:5555:6666:7777:8888', false),
            array(':1111:2222:3333:4444:5555:6666:1.2.3.4', false),
            array(':1111:2222:3333:4444:5555::1.2.3.4', false),
            array(':1111:2222:3333:4444::1.2.3.4', false),
            array(':1111:2222:3333::1.2.3.4', false),
            array(':1111:2222::1.2.3.4', false),
            array(':1111::1.2.3.4', false),
            array(':::1.2.3.4', false),
            array(':1111:2222:3333:4444::6666:1.2.3.4', false),
            array(':1111:2222:3333::6666:1.2.3.4', false),
            array(':1111:2222::6666:1.2.3.4', false),
            array(':1111::6666:1.2.3.4', false),
            array(':::6666:1.2.3.4', false),
            array(':1111:2222:3333::5555:6666:1.2.3.4', false),
            array(':1111:2222::5555:6666:1.2.3.4', false),
            array(':1111::5555:6666:1.2.3.4', false),
            array(':::5555:6666:1.2.3.4', false),
            array(':1111:2222::4444:5555:6666:1.2.3.4', false),
            array(':1111::4444:5555:6666:1.2.3.4', false),
            array(':::4444:5555:6666:1.2.3.4', false),
            array(':1111::3333:4444:5555:6666:1.2.3.4', false),
            array(':::2222:3333:4444:5555:6666:1.2.3.4', false),
            array('1111:2222:3333:4444:5555:6666:7777:::', false),
            array('1111:2222:3333:4444:5555:6666:::', false),
            array('1111:2222:3333:4444:5555:::', false),
            array('1111:2222:3333:4444:::', false),
            array('1111:2222:3333:::', false),
            array('1111:2222:::', false),
            array('1111:::', false),
            array(':::', false),
            array('1111:2222:3333:4444:5555:6666::8888:', false),
            array('1111:2222:3333:4444:5555::8888:', false),
            array('1111:2222:3333:4444::8888:', false),
            array('1111:2222:3333::8888:', false),
            array('1111:2222::8888:', false),
            array('1111::8888:', false),
            array('::8888:', false),
            array('1111:2222:3333:4444:5555::7777:8888:', false),
            array('1111:2222:3333:4444::7777:8888:', false),
            array('1111:2222:3333::7777:8888:', false),
            array('1111:2222::7777:8888:', false),
            array('1111::7777:8888:', false),
            array('::7777:8888:', false),
            array('1111:2222:3333:4444::6666:7777:8888:', false),
            array('1111:2222:3333::6666:7777:8888:', false),
            array('1111:2222::6666:7777:8888:', false),
            array('1111::6666:7777:8888:', false),
            array('::6666:7777:8888:', false),
            array('1111:2222:3333::5555:6666:7777:8888:', false),
            array('1111:2222::5555:6666:7777:8888:', false),
            array('1111::5555:6666:7777:8888:', false),
            array('::5555:6666:7777:8888:', false),
            array('1111:2222::4444:5555:6666:7777:8888:', false),
            array('1111::4444:5555:6666:7777:8888:', false),
            array('::4444:5555:6666:7777:8888:', false),
            array('1111::3333:4444:5555:6666:7777:8888:', false),
            array('::3333:4444:5555:6666:7777:8888:', false),
            array('::2222:3333:4444:5555:6666:7777:8888:', false)
        );
    }

    private function _getDifficultIpv6Cases()
    {
        return array(

            array('::ffff:192.168.1.26', true),
            array(' 2001:0000:1234:0000:0000:C1C0:ABCD:0876', true), // leading space
            array(' 2001:0000:1234:0000:0000:C1C0:ABCD:0876  ', true), // leading and trailing space
            array(' 2001:0000:1234:0000:0000:C1C0:ABCD:0876  0', false), // junk after valid address
            array('2001:1:1:1:1:1:255Z255X255Y255', false), // garbage instead of '.' in IPv4
            array('3ffe:0b00:0000:0001:0000:0000:000a', false), // seven segments
            array('3ffe:b00::1::a', false), // double '::'
            array('::ffff:192.168.1.1', true),
            array('1:2:3:4:5:6:1.2.3.4', true),
            array('1:2:3:4:5::1.2.3.4', true),
            array('1:2:3:4::1.2.3.4', true),
            array('1:2:3::1.2.3.4', true),
            array('1:2::1.2.3.4', true),
            array('1::1.2.3.4', true),
            array('1:2:3:4::5:1.2.3.4', true),
            array('1:2:3::5:1.2.3.4', true),
            array('1:2::5:1.2.3.4', true),
            array('1::5:1.2.3.4', true),
            array('1::5:11.22.33.44', true),
            array('1::5:400.2.3.4', false),
            array('1::5:260.2.3.4', false),
            array('1::5:256.2.3.4', false),
            array('1::5:1.256.3.4', false),
            array('1::5:1.2.256.4', false),
            array('1::5:1.2.3.256', false),
            array('1::5:300.2.3.4', false),
            array('1::5:1.300.3.4', false),
            array('1::5:1.2.300.4', false),
            array('1::5:1.2.3.300', false),
            array('1::5:900.2.3.4', false),
            array('1::5:1.900.3.4', false),
            array('1::5:1.2.900.4', false),
            array('1::5:1.2.3.900', false),
            array('1::5:300.300.300.300', false),
            array('1::5:3000.30.30.30', false),
            array('1::400.2.3.4', false),
            array('1::260.2.3.4', false),
            array('1::256.2.3.4', false),
            array('1::1.256.3.4', false),
            array('1::1.2.256.4', false),
            array('1::1.2.3.256', false),
            array('1::300.2.3.4', false),
            array('1::1.300.3.4', false),
            array('1::1.2.300.4', false),
            array('1::1.2.3.300', false),
            array('1::900.2.3.4', false),
            array('1::1.900.3.4', false),
            array('1::1.2.900.4', false),
            array('1::1.2.3.900', false),
            array('1::300.300.300.300', false),
            array('1::3000.30.30.30', false),
            array('::400.2.3.4', false),
            array('::260.2.3.4', false),
            array('::256.2.3.4', false),
            array('::1.256.3.4', false),
            array('::1.2.256.4', false),
            array('::1.2.3.256', false),
            array('::300.2.3.4', false),
            array('::1.300.3.4', false),
            array('::1.2.300.4', false),
            array('::1.2.3.300', false),
            array('::900.2.3.4', false),
            array('::1.900.3.4', false),
            array('::1.2.900.4', false),
            array('::1.2.3.900', false),
            array('::300.300.300.300', false),
            array('::3000.30.30.30', false),
            array('fe80::217:f2ff:254.7.237.98', true),
            array('0:0:0:0:0:0:13.1.68.3', true), // IPv4-compatible IPv6 address, full, deprecated
            array('0:0:0:0:0:FFFF:129.144.52.38', true), // IPv4-mapped IPv6 address, full
            array('::13.1.68.3', true), // IPv4-compatible IPv6 address, compressed, deprecated
            array('::FFFF:129.144.52.38', true), // IPv4-mapped IPv6 address, compressed
            array('fe80:0:0:0:204:61ff:254.157.241.86', true),
            array('fe80::204:61ff:254.157.241.86', true),
            array('::ffff:12.34.56.78', true),
            array('::ffff:192.0.2.128', true),
            array('ldkfj', false),
            array('2001::FFD3::57ab', false),
            array('1111:2222:3333:4444:5555:6666:123.123.123.123', true),
            array('1111:2222:3333:4444:5555::123.123.123.123', true),
            array('1111:2222:3333:4444::123.123.123.123', true),
            array('1111:2222:3333::123.123.123.123', true),
            array('1111:2222::123.123.123.123', true),
            array('1111::123.123.123.123', true),
            array('::123.123.123.123', true),
            array('1111:2222:3333:4444::6666:123.123.123.123', true),
            array('1111:2222:3333::6666:123.123.123.123', true),
            array('1111:2222::6666:123.123.123.123', true),
            array('1111::6666:123.123.123.123', true),
            array('::6666:123.123.123.123', true),
            array('1111:2222:3333::5555:6666:123.123.123.123', true),
            array('1111:2222::5555:6666:123.123.123.123', true),
            array('1111::5555:6666:123.123.123.123', true),
            array('::5555:6666:123.123.123.123', true),
            array('1111:2222::4444:5555:6666:123.123.123.123', true),
            array('1111::4444:5555:6666:123.123.123.123', true),
            array('::4444:5555:6666:123.123.123.123', true),
            array('1111::3333:4444:5555:6666:123.123.123.123', true),
            array('::2222:3333:4444:5555:6666:123.123.123.123', true),
            array('XXXX:XXXX:XXXX:XXXX:XXXX:XXXX:XXXX:XXXX', false),
        );
    }
}