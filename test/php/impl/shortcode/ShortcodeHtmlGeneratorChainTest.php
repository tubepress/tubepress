<?php
require_once BASE . '/sys/classes/org/tubepress/impl/shortcode/ShortcodeHtmlGeneratorChain.class.php';

class org_tubepress_impl_shortcode_ShortcodeHtmlGeneratorChainTest extends TubePressUnitTest {

    private $_sut;
    private $_page;

    function setup()
    {
        $this->_page = 1;
        parent::setUp();
        $this->_sut = new org_tubepress_impl_shortcode_ShortcodeHtmlGeneratorChain();

        $ioc   = org_tubepress_impl_ioc_IocContainer::getInstance();

        $shortcodeParser = $ioc->get('org_tubepress_api_shortcode_ShortcodeParser');
        $shortcodeParser->shouldReceive('parse')->once()->with('shortcode');
    }

    /**
     * @expectedException Exception
     */
    function testGetHtmlNoCommandHandled()
    {
        $ioc   = org_tubepress_impl_ioc_IocContainer::getInstance();

        $mockChainContext = new stdClass();
        $mockChainContext->returnValue = 'chain-return-value';

        $chain = $ioc->get('org_tubepress_spi_patterns_cor_Chain');
        $chain->shouldReceive('createContextInstance')->once()->andReturn($mockChainContext);
        $chain->shouldReceive('execute')->once()->with($mockChainContext, array(
            	'org_tubepress_impl_shortcode_commands_SearchInputCommand',
                'org_tubepress_impl_shortcode_commands_SearchOutputCommand',
                'org_tubepress_impl_shortcode_commands_SingleVideoCommand',
                'org_tubepress_impl_shortcode_commands_SoloPlayerCommand',
                'org_tubepress_impl_shortcode_commands_ThumbGalleryCommand',
        ))->andReturn(false);

        $this->_sut->getHtmlForShortcode('shortcode');
    }

    function testGetHtml()
    {
        $ioc   = org_tubepress_impl_ioc_IocContainer::getInstance();

        $mockChainContext = new stdClass();
        $mockChainContext->returnValue = 'chain-return-value';

        $chain = $ioc->get('org_tubepress_spi_patterns_cor_Chain');
        $chain->shouldReceive('createContextInstance')->once()->andReturn($mockChainContext);
        $chain->shouldReceive('execute')->once()->with($mockChainContext, array(
        	'org_tubepress_impl_shortcode_commands_SearchInputCommand',
            'org_tubepress_impl_shortcode_commands_SearchOutputCommand',
            'org_tubepress_impl_shortcode_commands_SingleVideoCommand',
            'org_tubepress_impl_shortcode_commands_SoloPlayerCommand',
            'org_tubepress_impl_shortcode_commands_ThumbGalleryCommand',
        ))->andReturn(true);

        $pm    = $ioc->get('org_tubepress_api_plugin_PluginManager');
        $pm->shouldReceive('hasFilters')->once()->with(org_tubepress_api_const_plugin_FilterPoint::HTML_ANY)->andReturn(true);
        $pm->shouldReceive('runFilters')->once()->with(org_tubepress_api_const_plugin_FilterPoint::HTML_ANY, 'chain-return-value')->andReturn('final-value');

        $this->assertEquals('final-value', $this->_sut->getHtmlForShortcode('shortcode'));
    }

}