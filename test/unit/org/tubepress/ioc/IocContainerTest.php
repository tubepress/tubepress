<?php
require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/ioc/IocContainer.class.php';

class FakeIocService implements org_tubepress_ioc_IocService
{
    public function get($className)
    {
        return 'foo';
    }            
}

class org_tubepress_ioc_IocContainerTest extends PHPUnit_Framework_TestCase {

    function testDefaultContainer()
    {
        $result = org_tubepress_ioc_IocContainer::getInstance();
        $this->assertNotNull($result);
        $this->assertTrue(is_a($result, 'org_tubepress_ioc_impl_FreeWordPressPluginIocService'));
    }
    
    function testCustomContainer()
    {
        
        org_tubepress_ioc_IocContainer::setInstance(new FakeIocService());
        $result = org_tubepress_ioc_IocContainer::getInstance();
        $this->assertTrue(is_a($result, 'FakeIocService'));
    }
}
?>
