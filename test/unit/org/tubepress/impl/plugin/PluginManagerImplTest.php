<?php
require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/plugin/PluginManagerImpl.class.php';

class FilterManagerImplTestCallback
{
    function alter_galleryHtml($galleryHtml, $galleryId)
    {
        return 'altered gallery html ' . $galleryHtml . ' gallery id ' . $galleryId;
    }
}

class org_tubepress_impl_plugin_PluginManagerImplTest extends TubePressUnitTest {

    private $_sut;
    private $_plugin;

    function setUp()
    {
        parent::setUp();
        $this->_plugin = new FilterManagerImplTestCallback();
        $this->_sut = new org_tubepress_impl_plugin_PluginManagerImpl();
    }
    
    function getMock($className)
    {
        if ($className === 'FilterManagerImplTestCallback') {
            return $this->_plugin;
        }
        
        $mock = parent::getMock($className);
    
        return $mock;
    }

    function testRunFilters()
    {
        $this->_sut->registerFilter('galleryHtml', $this->_plugin);
        $result = $this->_sut->runFilters('galleryHtml', 'some value', 56);
        $this->assertEquals('altered gallery html some value gallery id 56', $result);
    }  

    function testRunFiltersNoFiltersRegistered()
    {
        $result = $this->_sut->runFilters('galleryTemplate', 'some value');
        $this->assertEquals('some value', $result);
    }  

    function testRunFiltersNonFilterPoint()
    {
        $this->_sut->runFilters('fake', 'some value');
    }   

    function testRegisterFilterMissingCallbackMethod()
    {
        $this->_sut->registerFilter('html', $this->_plugin);
    }   
    
    function testRegisterNonObject()
    {
        $this->_sut->registerFilter('html', 'hello');
    }   
    
    function testRegisterBadExecutionPoint()
    {
        $this->_sut->registerFilter(1, $this->_plugin);
    }
}

