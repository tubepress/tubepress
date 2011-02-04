<?php
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/impl/ioc/IocContainer.class.php';
require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';

if (!function_exists('get_option')) {
    function get_option() {};
}
if (!function_exists('update_option')) {
    function update_option() {};
}

class FakeIocService implements org_tubepress_api_ioc_IocService
{
    public function get($className)
    {
        return 'foo';
    }            
}

class org_tubepress_impl_ioc_IocContainerTest extends TubePressUnitTest
{

    function testDefaultContainer()
    {
        $result = org_tubepress_impl_ioc_IocContainer::getInstance();
        $correct = is_a($result, 'org_tubepress_api_ioc_IocService');
        $this->assertTrue($correct);
    }
    
    function testCustomContainer()
    {
        org_tubepress_impl_ioc_IocContainer::setInstance(new FakeIocService());
        $result = org_tubepress_impl_ioc_IocContainer::getInstance();
        $this->assertTrue(is_a($result, 'FakeIocService'));
    }
}
