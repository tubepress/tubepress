<?php
require_once BASE . '/sys/classes/org/tubepress/impl/options/WordPressStorageManager.class.php';

class org_tubepress_impl_options_WordPressStorageManagerTest extends TubePressUnitTest {

    private $_sut;

    private $_persist;
    
    function setUp()
    {
        parent::setUp();
        $get_option = new PHPUnit_Extensions_MockFunction('get_option');
        $get_option->expects($this->any())->will($this->returnCallback(array($this, 'callback')));

        $update_option = new PHPUnit_Extensions_MockFunction('update_option');

        $this->setupInit();
        
        $this->_sut = new org_tubepress_impl_options_WordPressStorageManager();
    }

    function setupInit()
    {
        $nopersist = \Mockery::mock(org_tubepress_api_options_OptionDescriptor::_);
        $nopersist->shouldReceive('isMeantToBePersisted')->once()->andReturn(false);
        
        $this->_persist = \Mockery::mock(org_tubepress_api_options_OptionDescriptor::_);
        $this->_persist->shouldReceive('isMeantToBePersisted')->atLeast()->once()->andReturn(true);
        $this->_persist->shouldReceive('getName')->once()->andReturn('optionname');
        $this->_persist->shouldReceive('getDefaultValue')->once()->andReturn('defaultvalue');
        
        $fakeOds = array($nopersist, $this->_persist);
        
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        
        $odr = $ioc->get(org_tubepress_api_options_OptionDescriptorReference::_);
        $odr->shouldReceive('findAll')->once()->andReturn($fakeOds);
    }

    function testSet()
    {
        $ioc               = org_tubepress_impl_ioc_IocContainer::getInstance();
        $validationService = $ioc->get(org_tubepress_api_options_OptionValidator::_);

        $validationService->shouldReceive('validate')->with('optionname', 'optionvalue')->once();

        $odr = $ioc->get(org_tubepress_api_options_OptionDescriptorReference::_);
        $odr->shouldReceive('findOneByName')->once()->with('optionname')->andReturn($this->_persist);
        
        $this->_sut->set('optionname', 'optionvalue');
    }

    function callback($name)
    {
        return $name === 'version' ? 1 : "<value of $name>";
    }
}

