<?php

require_once BASE . '/sys/classes/org/tubepress/impl/exec/MemoryExecutionContext.class.php';
require_once BASE . '/sys/classes/org/tubepress/api/const/options/names/Display.class.php';
require_once BASE . '/sys/classes/org/tubepress/api/const/options/names/Advanced.class.php';

class org_tubepress_impl_exec_MemoryExecutionContextTest extends TubePressUnitTest {

    private $_sut;

    private $_expectedNames;

    public function setup()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_exec_MemoryExecutionContext();
    }

    public function testSetGet()
    {
        $this->_sut->set(org_tubepress_api_const_options_names_Display::THEME, 'crazytheme');
        $this->assertEquals('crazytheme', $this->_sut->get(org_tubepress_api_const_options_names_Display::THEME));
    }

    public function testToShortcode()
    {
        $customOptions = array(
            org_tubepress_api_const_options_names_Display::THEME => 'fakeoptionvalue',
            org_tubepress_api_const_options_names_Display::AJAX_PAGINATION => 'true',
        );

        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $sm  = $ioc->get(org_tubepress_api_options_StorageManager::_);
        $sm->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Advanced::KEYWORD)->andReturn('trigger');

        $this->_sut->setCustomOptions($customOptions);

        $this->assertEquals('[trigger theme="fakeoptionvalue", ajaxPagination="true"]', $this->_sut->toShortcode());
    }

    public function testReset()
    {
        $customOptions = array(org_tubepress_api_const_options_names_Display::THEME => 'fakeoptionvalue');
        $this->_sut->setCustomOptions($customOptions);

        $this->assertEquals($customOptions, $this->_sut->getCustomOptions());

        $this->_sut->reset();

        $this->assertEquals(array(), $this->_sut->getCustomOptions());
    }

    public function testGetSetShortcode()
    {
        $this->_sut->setActualShortcodeUsed("fakeshort");
        $this->assertEquals("fakeshort", $this->_sut->getActualShortcodeUsed());
    }

    public function testGetCustomOption()
    {
        $customOptions = array(org_tubepress_api_const_options_names_Display::THEME => 'fakeoptionvalue');
        $this->_sut->setCustomOptions($customOptions);
        $this->assertEquals('fakeoptionvalue', $this->_sut->get(org_tubepress_api_const_options_names_Display::THEME));
        $this->assertEquals(1, sizeof(array_intersect($customOptions, $this->_sut->getCustomOptions())));
    }

    public function testGetCustomOptionFallback()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $sm  = $ioc->get(org_tubepress_api_options_StorageManager::_);
        $sm->shouldReceive('get')->once()->with('nonexistent');

        $this->_sut->get("nonexistent");
    }
}

