<?php
require_once BASE . '/sys/classes/org/tubepress/impl/plugin/PluginManagerImpl.class.php';

class FakePlugin
{
    public function alter_galleryHtml($one, $two)
    {
        return $one . $two;
    }

    public function on_boot() {}
}

class org_tubepress_impl_plugin_PluginManagerImplTest extends TubePressUnitTest
{

    private $_sut;

    function setUp()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_plugin_PluginManagerImpl();
    }

    function testRunFilters()
    {
        $this->_sut->registerFilter('galleryHtml', new FakePlugin());
        $result = $this->_sut->runFilters('galleryHtml', 'some value', 56);
        $this->assertEquals('some value56', $result);
    }

    function testNotifyListenersNoneRegistered()
    {
        $this->_sut->notifyListeners('boot');
    }

    function testNotifyListeners()
    {
        $this->_sut->registerListener('boot', new FakePlugin());
        $this->_sut->notifyListeners('boot');
    }

    function testHasListenersBadEventName()
    {
        $this->assertFalse($this->_sut->hasListeners('booter'));
    }

    function testHasListeners()
    {
        $this->assertFalse($this->_sut->hasListeners('boot'));
        $this->_sut->registerListener('boot', new FakePlugin());
        $this->assertTrue($this->_sut->hasListeners('boot'));
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
        $this->_sut->registerFilter('html', new FakePlugin());
    }

    function testRegisterNonObject()
    {
        $this->_sut->registerFilter('html', 'hello');
    }

    function testRegisterBadExecutionPoint()
    {
        $this->_sut->registerFilter(1,  new FakePlugin());
    }
}

