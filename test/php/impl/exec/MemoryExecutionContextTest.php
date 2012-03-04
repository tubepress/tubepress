<?php

require_once BASE . '/sys/classes/org/tubepress/impl/exec/MemoryExecutionContext.class.php';
require_once BASE . '/sys/classes/org/tubepress/api/const/plugin/FilterPoint.class.php';
require_once BASE . '/sys/classes/org/tubepress/api/const/options/names/Thumbs.class.php';
require_once BASE . '/sys/classes/org/tubepress/api/const/options/names/Advanced.class.php';
require_once BASE . '/sys/classes/org/tubepress/api/plugin/PluginManager.class.php';

class org_tubepress_impl_exec_MemoryExecutionContextTest__fakeFilter
{

}

class org_tubepress_impl_exec_MemoryExecutionContextTest extends TubePressUnitTest {

    private $_sut;

    private $_expectedNames;

    private $_pluginManager;

    public function setup()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_exec_MemoryExecutionContext();

        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $this->_pluginManager = $ioc->get(org_tubepress_api_plugin_PluginManager::_);
    }

    public function testSetGet()
    {
        $this->_setupPluginManagerForSet(org_tubepress_api_const_options_names_Thumbs::THEME, 'crazytheme');

        $this->_sut->set(org_tubepress_api_const_options_names_Thumbs::THEME, 'crazytheme');
        $this->assertEquals('XX crazytheme XX', $this->_sut->get(org_tubepress_api_const_options_names_Thumbs::THEME));
    }

    public function testToShortcode()
    {
        $customOptions = array(
            org_tubepress_api_const_options_names_Thumbs::THEME => 'fakeoptionvalue',
            org_tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION => 'true',
        );

        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $sm  = $ioc->get(org_tubepress_api_options_StorageManager::_);
        $sm->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Advanced::KEYWORD)->andReturn('trigger');

        $this->_setupPluginManagerForSet(org_tubepress_api_const_options_names_Thumbs::THEME, 'fakeoptionvalue');
        $this->_setupPluginManagerForSet(org_tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION, 'true');

        $this->_sut->setCustomOptions($customOptions);

        $this->assertEquals('[trigger theme="XX fakeoptionvalue XX", ajaxPagination="XX true XX"]', $this->_sut->toShortcode());
    }

    public function testReset()
    {
        $this->_setupPluginManagerForSet(org_tubepress_api_const_options_names_Thumbs::THEME, 'fakeoptionvalue');

        $customOptions = array(org_tubepress_api_const_options_names_Thumbs::THEME => 'fakeoptionvalue');
        $this->_sut->setCustomOptions($customOptions);

        $this->assertEquals(array('theme' => 'XX fakeoptionvalue XX'), $this->_sut->getCustomOptions());

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
        $this->_setupPluginManagerForSet(org_tubepress_api_const_options_names_Thumbs::THEME, 'fakeoptionvalue');

        $customOptions = array(org_tubepress_api_const_options_names_Thumbs::THEME => 'fakeoptionvalue');
        $this->_sut->setCustomOptions($customOptions);
        $this->assertEquals('XX fakeoptionvalue XX', $this->_sut->get(org_tubepress_api_const_options_names_Thumbs::THEME));
        $this->assertEquals(1, sizeof(array_intersect(array('theme' => 'XX fakeoptionvalue XX'), $this->_sut->getCustomOptions())));
    }

    public function testGetCustomOptionFallback()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $sm  = $ioc->get(org_tubepress_api_options_StorageManager::_);
        $sm->shouldReceive('get')->once()->with('nonexistent');

        $this->_sut->get("nonexistent");
    }

    private function _setupPluginManagerForSet($name, $value)
    {
        $this->_pluginManager->shouldReceive('runFilters')->once()->with(org_tubepress_api_const_plugin_FilterPoint::EXEC_CONTEXT_SET_VALUE_ . $name, $value)->andReturnUsing(function ($name, $value) {

            return "XX $value XX";
        });
    }
}

