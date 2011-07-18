<?php
require_once BASE . '/sys/classes/org/tubepress/impl/ioc/IocContainer.class.php';
require_once BASE . '/test/includes/TubePressUnitTest.php';


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
