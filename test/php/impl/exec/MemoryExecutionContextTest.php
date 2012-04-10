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

    private $_validationService;

    public function setup()
    {
        parent::setUp();

        $this->_sut = new org_tubepress_impl_exec_MemoryExecutionContext();

        $ioc                      = org_tubepress_impl_ioc_IocContainer::getInstance();
        $this->_pluginManager     = $ioc->get(org_tubepress_api_plugin_PluginManager::_);
        $this->_validationService = $ioc->get(org_tubepress_api_options_OptionValidator::_);
    }

    public function testSetGet()
    {
        $this->_setupPluginManagerForSet(org_tubepress_api_const_options_names_Thumbs::THEME, 'crazytheme');
        $this->_setupValidationServiceToPass(org_tubepress_api_const_options_names_Thumbs::THEME, 'XX crazytheme XX');

        $result = $this->_sut->set(org_tubepress_api_const_options_names_Thumbs::THEME, 'crazytheme');

        $this->assertTrue($result === true);
        $this->assertEquals('XX crazytheme XX', $this->_sut->get(org_tubepress_api_const_options_names_Thumbs::THEME));
    }

    public function testSetWithInvalidValue()
    {
        $this->_setupPluginManagerForSet(org_tubepress_api_const_options_names_Thumbs::THEME, 'crazytheme');
        $this->_setupValidationServiceToFail(org_tubepress_api_const_options_names_Thumbs::THEME, 'XX crazytheme XX');

        $result = $this->_sut->set(org_tubepress_api_const_options_names_Thumbs::THEME, 'crazytheme');

        $this->assertTrue($result === 'XX crazytheme XX was a bad value', var_export($result, true));
    }

    public function testToShortcode()
    {
        $customOptions = array(
            org_tubepress_api_const_options_names_Thumbs::THEME => 'some "option" with double quotes',
            org_tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION => 'true',
        );

        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $sm  = $ioc->get(org_tubepress_api_options_StorageManager::_);
        $sm->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Advanced::KEYWORD)->andReturn('trigger');

        $this->_setupPluginManagerForSet(org_tubepress_api_const_options_names_Thumbs::THEME, 'some "option" with double quotes');
        $this->_setupPluginManagerForSet(org_tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION, 'true');
        $this->_setupValidationServiceToPass(org_tubepress_api_const_options_names_Thumbs::THEME, 'XX some "option" with double quotes XX');
        $this->_setupValidationServiceToPass(org_tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION, 'XX true XX');

        $this->_sut->setCustomOptions($customOptions);

        $this->assertEquals('[trigger theme="XX some \"option\" with double quotes XX", ajaxPagination="XX true XX"]', $this->_sut->toShortcode());
    }

    public function testReset()
    {
        $this->_setupPluginManagerForSet(org_tubepress_api_const_options_names_Thumbs::THEME, 'fakeoptionvalue');
        $this->_setupValidationServiceToPass(org_tubepress_api_const_options_names_Thumbs::THEME, 'XX fakeoptionvalue XX');

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

    public function testSetCustomOptionsNonArray()
    {
        $this->_sut->setCustomOptions('hello');
    }

    public function testGetCustomOption()
    {
        $this->_setupPluginManagerForSet(org_tubepress_api_const_options_names_Thumbs::THEME, 'fakeoptionvalue');
        $this->_setupValidationServiceToPass(org_tubepress_api_const_options_names_Thumbs::THEME, 'XX fakeoptionvalue XX');

        $customOptions = array(org_tubepress_api_const_options_names_Thumbs::THEME => 'fakeoptionvalue');

        $result = $this->_sut->setCustomOptions($customOptions);

        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) === 0);
        $this->assertEquals('XX fakeoptionvalue XX', $this->_sut->get(org_tubepress_api_const_options_names_Thumbs::THEME));
        $this->assertEquals(1, sizeof(array_intersect(array('theme' => 'XX fakeoptionvalue XX'), $this->_sut->getCustomOptions())));
    }

    public function testGetCustomOptionWithBadValue()
    {
        $this->_setupPluginManagerForSet(org_tubepress_api_const_options_names_Thumbs::THEME, 'fakeoptionvalue');
        $this->_setupValidationServiceToFail(org_tubepress_api_const_options_names_Thumbs::THEME, 'XX fakeoptionvalue XX');

        $customOptions = array(org_tubepress_api_const_options_names_Thumbs::THEME => 'fakeoptionvalue');

        $result = $this->_sut->setCustomOptions($customOptions);

        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) === 1);
        $this->assertTrue($result[0] === 'XX fakeoptionvalue XX was a bad value');
    }

    public function testGetCustomOptionFallback()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $sm  = $ioc->get(org_tubepress_api_options_StorageManager::_);
        $sm->shouldReceive('get')->once()->with('nonexistent')->andReturn('something');

        $result = $this->_sut->get("nonexistent");

        $this->assertTrue($result === 'something');
    }

    private function _setupValidationServiceToFail($name, $value)
    {
        $this->_validationService->shouldReceive('isValid')->once()->with($name, $value)->andReturn(false);

        $this->_validationService->shouldReceive('getProblemMessage')->once()->with($name, $value)->andReturnUsing(function ($n, $v) {

            return "$v was a bad value";
        });
    }

    private function _setupValidationServiceToPass($name, $value)
    {
        $this->_validationService->shouldReceive('isValid')->once()->with($name, $value)->andReturn(true);
    }

    private function _setupPluginManagerForSet($name, $value)
    {
        $this->_pluginManager->shouldReceive('runFilters')->once()->with(org_tubepress_api_const_plugin_FilterPoint::OPTION_VALUE_SET_IN_EXEC_CONTEXT, $name, $value)->andReturnUsing(function ($filter, $name, $value) {

            return "XX $value XX";
        });
    }
}

