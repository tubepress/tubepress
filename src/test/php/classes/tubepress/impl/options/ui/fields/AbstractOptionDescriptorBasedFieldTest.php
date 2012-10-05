<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
abstract class tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedFieldTest extends tubepress_impl_options_ui_fields_AbstractFieldTest
{
	private $_sut;

	private $_mockOptionDescriptor;

	private $_mockHttpRequestParameterService;

    private $_mockOptionsValidator;

    private $_mockStorageManager;

    private $_mockOptionDescriptorReference;

    private $_mockMessageService;

    private $_mockEnvironmentDetector;

    private $_mockTemplateBuilder;

	public function setup()
	{
		$this->_mockHttpRequestParameterService = Mockery::mock(tubepress_spi_http_HttpRequestParameterService::_);
		$this->_mockOptionDescriptor            = new tubepress_api_model_options_OptionDescriptor('name');

		$this->_mockOptionDescriptorReference = Mockery::mock(tubepress_spi_options_OptionDescriptorReference::_);
		$this->_mockOptionDescriptorReference->shouldReceive('findOneByName')->once()->with('name')->andReturn($this->_mockOptionDescriptor);

        $this->_mockStorageManager      = Mockery::mock(tubepress_spi_options_StorageManager::_);
        $this->_mockEnvironmentDetector = Mockery::mock(tubepress_spi_environment_EnvironmentDetector::_);
        $this->_mockOptionsValidator    = Mockery::mock(tubepress_spi_options_OptionValidator::_);
        $this->_mockTemplateBuilder     = Mockery::mock('ehough_contemplate_api_TemplateBuilder');
        $this->_mockMessageService      = Mockery::mock(tubepress_spi_message_MessageService::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setHttpRequestParameterService($this->_mockHttpRequestParameterService);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionStorageManager($this->_mockStorageManager);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionDescriptorReference($this->_mockOptionDescriptorReference);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionValidator($this->_mockOptionsValidator);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setMessageService($this->_mockMessageService);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setTemplateBuilder($this->_mockTemplateBuilder);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setEnvironmentDetector($this->_mockEnvironmentDetector);

        parent::doSetup($this->_mockMessageService);

		$this->_sut = $this->_buildSut('name');
	}

	public function testSubmitSimpleInvalid()
	{
	    $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('name')->andReturn(true);
	    $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('name')->andReturn('some-value');

	    $this->_mockOptionsValidator->shouldReceive('isValid')->once()->with('name', 'some-value')->andReturn(false);
        $this->_mockOptionsValidator->shouldReceive('getProblemMessage')->once()->with('name', 'some-value')->andReturn('you suck');

	    $this->assertEquals(array('you suck'), $this->_sut->onSubmit());
	}

	public function testSubmitNoExist()
	{
	    $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('name')->andReturn(false);

	    $this->assertNull($this->_sut->onSubmit());
	}

	public function testSubmitBoolean()
	{
	    $this->_mockOptionDescriptor->setBoolean();

	    $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('name')->andReturn(true);

	    $this->_mockStorageManager->shouldReceive('set')->once()->with('name', true)->andReturn(true);

	    $this->assertNull($this->_sut->onSubmit());
	}

	public function testSubmitSimple()
	{

	    $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('name')->andReturn(true);
	    $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('name')->andReturn('some-value');

	    $this->_mockOptionsValidator->shouldReceive('isValid')->once()->with('name', 'some-value')->andReturn(true);
	    $this->_mockStorageManager->shouldReceive('set')->once()->with('name', 'some-value')->andReturn(true);

	    $this->assertNull($this->_sut->onSubmit());
	}

	protected function getSut()
	{
	    return $this->_sut;
	}

	protected function getMockOptionDescriptor()
	{
	    return $this->_mockOptionDescriptor;
	}

    protected function getMockEnvironmentDetector()
    {
        return $this->_mockEnvironmentDetector;
    }

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testBadOptionName()
	{
		$this->_mockOptionDescriptorReference->shouldReceive('findOneByName')->once()->with('name')->andReturn(null);

		$this->_sut = new tubepress_impl_options_ui_fields_TextField('name');
	}

	public function testGetInputHtml()
	{
	    $template = \Mockery::mock('ehough_contemplate_api_Template');
	    $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedField::TEMPLATE_VAR_NAME, 'name');
	    $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedField::TEMPLATE_VAR_VALUE, '<<currentvalue>>');
        $template->shouldReceive('toString')->once()->andReturn('boogity');

	    $this->_mockEnvironmentDetector->shouldReceive('getTubePressBaseInstallationPath')->once()->andReturn('<<basepath>>');

	    $this->_mockTemplateBuilder->shouldReceive('getNewTemplateInstance')->once()->with('<<basepath>>/' . $this->getTemplatePath())->andReturn($template);

	    $this->_mockStorageManager->shouldReceive('get')->once()->with('name')->andReturn('<<currentvalue>>');

	    $this->_performAdditionToStringTestSetup($template);

	    $this->assertEquals('boogity', $this->_sut->getHtml());
	}

	protected function _performAdditionToStringTestSetup($template)
	{
	    //override point
	}

	protected function _performAdditionGetDescriptionSetup()
	{
	    //override point
	}

	public function testProviders()
	{
        $this->assertTrue($this->_sut->getArrayOfApplicableProviderNames() === array('vimeo', 'youtube'));
	}

	public function testGetProOnlyNo()
	{

	    $this->assertTrue($this->_sut->isProOnly() === false);
	}

	public function testGetProOnlyYes()
	{
	    $this->_mockOptionDescriptor->setProOnly();

	    $this->assertTrue($this->_sut->isProOnly() === true);
	}

	public function testGetDescription()
	{
	    $this->_mockOptionDescriptor->setDescription('some-desc');

	    $this->_performAdditionGetDescriptionSetup();

	    $this->assertTrue($this->_sut->getDescription() === '<<message: some-desc>>');
	}

	public function testGetTitle()
	{
	    $this->_mockOptionDescriptor->setLabel('some-label');

	    $this->assertTrue($this->_sut->getTitle() === '<<message: some-label>>');
	}

	protected abstract function getTemplatePath();

	protected abstract function _buildSut($name);
}

