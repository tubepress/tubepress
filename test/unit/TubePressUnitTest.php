<?php

function_exists('tubepress_load_classes')
    || require dirname(__FILE__) . '/../../classes/tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_options_manager_OptionsManager',
    'org_tubepress_ioc_IocService',
    'org_tubepress_options_reference_OptionsReference'));

class TubePressUnitTest extends PHPUnit_Framework_TestCase
{
    private $_needToInit = true;
    
    private $_ioc;
    private $_tpom;
    
    private $options = array();

    function tearDown()
    {
        $this->_needToInit = true;
    }
    
    protected function getIoc()
    {
        if ($this->_needToInit) {
            $this->_init();
        }
        return $this->_ioc;
    }
    
    private function _init()
    {
        $this->_ioc               = $this->getMock('org_tubepress_ioc_IocService');
        $this->_tpom              = $this->getMock('org_tubepress_options_manager_OptionsManager');
        
        $this->_ioc->expects($this->any())
                   ->method('get')
                   ->will($this->returnCallback(array($this, 'iocCallback')));
        $this->_tpom->expects($this->any())
                   ->method('get')
                   ->will($this->returnCallback(array($this, 'tpomCallback')));
    }
    
    function iocCallback()
    {
        $args = func_get_args();
        $vals = array(
           org_tubepress_ioc_IocService::OPTIONS_MANAGER => $this->_tpom
        );
        return $vals[$args[0]];
    }
    
    function setOptions($options)
    {
        $this->options = array();
        foreach ($options as $key => $value) {
            $this->options[$key] = $value;
        }
    }
    
    function tpomCallback() {
        $args = func_get_args();
        
        if (array_key_exists($args[0], $this->options)) {
            return $this->options[$args[0]];
        }
        
        return org_tubepress_options_reference_OptionsReference::getDefaultValue($args[0]);
    }
}
?>