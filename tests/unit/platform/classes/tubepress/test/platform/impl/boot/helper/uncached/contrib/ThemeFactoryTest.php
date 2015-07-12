<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_internal_boot_helper_uncached_contrib_ThemeFactory<extended>
 */
class tubepress_test_platform_impl_boot_helper_uncached_contrib_ThemeFactoryTest extends tubepress_test_platform_impl_boot_helper_uncached_contrib_AbstractFactoryTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironment;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFinderFactory;

    /**
     * @dataProvider getBadJs
     */
    public function testBadJs($candidate, $message)
    {
        $mockUserContentUrl = $this->mock(tubepress_platform_api_url_UrlInterface::_);
        $this->_mockEnvironment->shouldReceive('getUserContentUrl')->andReturn($mockUserContentUrl);

        $data = array(
            'scripts' => $candidate,
        );

        $this->confirmFailures($data, array($message));
    }

    public function getBadJs()
    {
        return array(

            array(new stdClass(), 'Scripts must be a simple array of strings'),
            array(array(''), 'Script 1 is empty'),
        );
    }

    /**
     * @dataProvider getBadParentThemeNames
     */
    public function testBadParentThemeNames($candidate, $message)
    {
        $data = array(
            'parent' => $candidate,
        );

        $this->confirmFailures($data, array($message));
    }

    public function getBadParentThemeNames()
    {
        return array(

            array(new stdClass(), 'Parent theme name must be a string'),
            array(str_repeat('x', 101), 'Parent theme name must be all lowercase, 100 characters or less, and contain only alphanumerics, dots, dashes, underscores, and slashes'),
        );
    }

    public function testGoodTemplatePaths()
    {
        list($handle, $path) = $this->getTemporaryFile();

        fwrite($handle, 'bla bla bla');

        /**
         * @var $theme tubepress_app_impl_theme_FilesystemTheme
         */
        $theme = $this->fromManifest(array());

        $this->assertInstanceOf(tubepress_app_api_theme_ThemeInterface::_, $theme);
    }

    /**
     * @dataProvider getGoodInlineCss
     */
    public function testInlineCss($asJson, $expectedCss)
    {
        /**
         * @var $theme tubepress_app_impl_theme_FilesystemTheme
         */
        $theme = $this->fromManifest(array(
            'inlineCSS' => json_decode($asJson, true)
        ));

        $this->assertEquals($expectedCss, $theme->getInlineCSS());
    }

    public function getGoodInlineCss()
    {
        return array(

            array($this->_getJsonCss1(), $this->_getCssJson1())
        );
    }

    /**
     * @dataProvider getBadInlineCss
     */
    public function testBadInlineCss($candidate, $message)
    {
        $data = array(
            'inlineCSS' => $candidate
        );

        $this->confirmFailures($data, array($message));
    }

    public function getBadInlineCss()
    {
        return array(

            array(new stdClass(), 'Inline CSS contains non-associative arrays'),
            array(33, 'Inline CSS contains non-associative arrays'),
            array(array('foo'), 'Inline CSS contains non-associative arrays'),
        );
    }

    public function testValidConstruction()
    {
        /**
         * @var $theme tubepress_app_impl_theme_FilesystemTheme
         */
        $theme = $this->fromManifest();

        $this->assertNull($theme->getInlineCSS());
        $this->assertFalse($theme->hasTemplateSource('x'));
        $this->assertNull($theme->getParentThemeName());
    }

    /**
     * @return tubepress_internal_boot_helper_uncached_contrib_ThemeFactory
     */
    protected function buildSut(tubepress_platform_api_log_LoggerInterface       $logger,
                                tubepress_platform_api_url_UrlFactoryInterface   $urlFactory,
                                tubepress_platform_api_util_LangUtilsInterface   $langUtils,
                                tubepress_platform_api_util_StringUtilsInterface $stringUtils)
    {
        $this->_mockContext = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $this->_mockEnvironment = $this->mock(tubepress_app_api_environment_EnvironmentInterface::_);
        $this->_mockFinderFactory = $this->mock('ehough_finder_FinderFactory');

        return new tubepress_internal_boot_helper_uncached_contrib_ThemeFactory(

            $this->_mockContext, $urlFactory, $langUtils, $logger, $stringUtils, $this->_mockFinderFactory
        );
    }

    private function _getJsonCss1()
    {
        return <<<EOT
{
  "children": {
    "@media (max-width: 800px)": {
      "children": {
        "#main #comments": {
          "children": {},
          "attributes": {
            "margin": "0px",
            "width": "auto",
            "background": "red"
          }
        },
        "#main #buttons": {
          "children": {},
          "attributes": {
            "padding": "5px 10px",
            "color": "blue"
          }
        }
      },
      "attributes": {
          "something" : ["hi", "there"]
      }
    },
    "#main #content": {
      "children": {},
      "attributes": {
        "margin": "0 7.6%",
        "width": "auto"
      }
    },
    "#nav-below": {
      "children": {},
      "attributes": {
        "border-bottom": "1px solid #ddd",
        "margin-bottom": "1.625em",
        "background-image": "url(http://www.example.com/images/im.jpg)"
      }
    }
  },
  "attributes": {}
}
EOT
        ;
    }

    private function _getCssJson1()
    {
        return <<<EOT
@media (max-width: 800px) {
	something: hi;
	something: there;
	#main #comments {
		margin: 0px;
		width: auto;
		background: red;
	}

	#main #buttons {
		padding: 5px 10px;
		color: blue;
	}
}

#main #content {
	margin: 0 7.6%;
	width: auto;
}

#nav-below {
	border-bottom: 1px solid #ddd;
	margin-bottom: 1.625em;
	background-image: url(http://www.example.com/images/im.jpg);
}

EOT
        ;
    }

    private function _setupMockFinderFactory(array $tplPhpSplFiles, array $twigSplFiles)
    {
        $mockTplPhpIterator = new ArrayIterator($tplPhpSplFiles);

        $mockTwigIterator = new ArrayIterator($twigSplFiles);

        $mockTplPhpFinder = $this->mock('ehough_finder_FinderInterface');
        $mockTplPhpFinder->shouldReceive('files')->once()->andReturn($mockTplPhpFinder);
        $mockTplPhpFinder->shouldReceive('in')->once()->andReturn($mockTplPhpFinder);
        $mockTplPhpFinder->shouldReceive('name')->once()->with('*.tpl.php')->andReturn($mockTplPhpIterator);
        $mockTwigFinder = $this->mock('ehough_finder_FinderInterface');
        $mockTwigFinder->shouldReceive('files')->once()->andReturn($mockTwigFinder);
        $mockTwigFinder->shouldReceive('in')->once()->andReturn($mockTwigFinder);
        $mockTwigFinder->shouldReceive('name')->once()->with('*.html.twig')->andReturn($mockTwigIterator);
        $this->_mockFinderFactory->shouldReceive('createFinder')->twice()->andReturn($mockTplPhpFinder, $mockTwigFinder);
    }
}