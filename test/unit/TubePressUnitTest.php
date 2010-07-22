<?php

function_exists('tubepress_load_classes')
    || require dirname(__FILE__) . '/../../classes/tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_options_manager_OptionsManager',
    'org_tubepress_ioc_IocService',
    'org_tubepress_options_reference_OptionsReference',
    'org_tubepress_message_MessageService',
    'org_tubepress_options_storage_StorageManager'));

class TubePressUnitTest extends PHPUnit_Framework_TestCase
{
    private $_needToInit = true;
    
    private $_ioc;
    
    private $_tpom;
    private $_msg;
    private $_tpsm;
    
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
        $this->_ioc  = $this->getMock('org_tubepress_ioc_IocService');
        $this->_tpom = $this->getMock('org_tubepress_options_manager_OptionsManager');
        $this->_msg  = $this->getMock('org_tubepress_message_MessageService');
        $this->_tpsm = $this->getMock('org_tubepress_options_storage_StorageManager');
        
        $this->_ioc->expects($this->any())
                   ->method('get')
                   ->will($this->returnCallback(array($this, 'iocCallback')));
        $this->_tpom->expects($this->any())
                   ->method('get')
                   ->will($this->returnCallback(array($this, 'tpomCallback')));
        $this->_msg->expects($this->any())
                   ->method('_')
                   ->will($this->returnCallback(array($this, 'msgCallback')));
        $this->_tpsm->expects($this->any())
                    ->method('get')
                    ->will($this->returnCallback(array($this, 'msgCallback')));
    }
    
    function setOptions($options)
    {
        $this->options = array();
        foreach ($options as $key => $value) {
            $this->options[$key] = $value;
        }
    }
    
    function msgCallback()
    {
        $args = func_get_args();
        return $args[0];
    }
    
    function iocCallback()
    {
        $args = func_get_args();
        $vals = array(
           org_tubepress_ioc_IocService::OPTIONS_MANAGER => $this->_tpom,
           org_tubepress_ioc_IocService::MESSAGE_SERVICE => $this->_msg,
           org_tubepress_ioc_IocService::OPTIONS_STORAGE_MANAGER => $this->_tpsm
        );
        return $vals[$args[0]];
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