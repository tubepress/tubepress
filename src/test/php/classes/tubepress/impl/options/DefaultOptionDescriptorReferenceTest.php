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
class org_tubepress_impl_options_DefaultOptionDescriptorReferenceTest extends TubePressUnitTest
{
	private $_sut;

	public function setup()
	{
		$this->_sut = new tubepress_impl_options_DefaultOptionDescriptorReference();
	}

	public function testFindAll()
	{
        $this->setupMocks();

	    $all = $this->_sut->findAll();

	    $this->assertTrue(count($all) === 97, "Expected 97 options but got " . count($all));
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testRegisterDuplicate()
	{
	    $od = new tubepress_api_model_options_OptionDescriptor('name');

	    $this->_sut->registerOptionDescriptor($od);
	    $this->_sut->registerOptionDescriptor($od);
	}

	public function testOptions()
	{
        $this->setupMocks();

	    $option = $this->_sut->findOneByName(tubepress_api_const_options_names_Advanced::DEBUG_ON);
	    $this->assertTrue($option->getDefaultValue() === true, $option->getName());
	    $this->assertTrue($option->getLabel() === 'Enable debugging', $option->getName());
	    $this->assertTrue($option->getDescription() === 'If checked, anyone will be able to view your debugging information. This is a rather small privacy risk. If you\'re not having problems with TubePress, or you\'re worried about revealing any details of your TubePress pages, feel free to disable the feature.', $option->getName());
	    $this->assertTrue($option->isBoolean(), $option->getName());
	    $this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Cache::CACHE_CLEAN_FACTOR);
    	$this->assertTrue($option->getDefaultValue() === 20, $option->getName() . ' should have default value of 20');
    	$this->assertTrue($option->getLabel() === 'Cache cleaning factor', $option->getName());
    	$this->assertTrue($option->getDescription() === 'If you enter X, the entire cache will be cleaned every 1/X cache writes. Enter 0 to disable cache cleaning.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName() . ' should not be pro only');

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Cache::CACHE_DIR);
    	$this->assertTrue($option->getLabel() === 'Cache directory', $option->getName());
    	$this->assertTrue($option->getDescription() === 'Leave blank to attempt to use your system\'s temp directory. Otherwise enter the absolute path of a writeable directory.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Cache::CACHE_LIFETIME_SECONDS);
    	$this->assertTrue($option->getDefaultValue() === 3600, $option->getName());
    	$this->assertTrue($option->getLabel() === 'Cache expiration time (seconds)', $option->getName());
    	$this->assertTrue($option->getDescription() === 'Cache entries will be considered stale after the specified number of seconds. Default is 3600 (one hour).', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Meta::DATEFORMAT);
    	$this->assertTrue($option->getDefaultValue() === 'M j, Y', $option->getName());
    	$this->assertTrue($option->getLabel() === 'Date format', $option->getName());
    	$this->assertTrue($option->getDescription() === 'Set the textual formatting of date information for videos. See <a href="http://us.php.net/date">date</a> for examples.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Advanced::DISABLE_HTTP_CURL);
    	$this->assertTrue($option->getLabel() === 'Disable <a href="http://php.net/manual/en/function.curl-exec.php">cURL</a> HTTP transport', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Do not attempt to use cURL to fetch remote feeds. Leave enabled unless you know what you are doing.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Advanced::DISABLE_HTTP_EXTHTTP);
    	$this->assertTrue($option->getLabel() === 'Disable <a href="http://php.net/http_request">HTTP extension</a> transport', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Do not attempt to use the PHP HTTP extension to fetch remote feeds. Leave enabled unless you know what you are doing.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FOPEN);
    	$this->assertTrue($option->getLabel() === 'Disable <a href="http://php.net/manual/en/function.fopen.php">fopen</a> HTTP transport', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Do not attempt to use fopen to fetch remote feeds. Leave enabled unless you know what you are doing.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FSOCKOPEN);
    	$this->assertTrue($option->getLabel() === 'Disable <a href="http://php.net/fsockopen">fsockopen</a> HTTP transport', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Do not attempt to use fsockopen to fetch remote feeds. Leave enabled unless you know what you are doing.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Advanced::DISABLE_HTTP_STREAMS);
    	$this->assertTrue($option->getLabel() === 'Disable <a href="http://php.net/manual/en/intro.stream.php">PHP streams</a> HTTP transport', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Do not attempt to use PHP streams to fetch remote feeds. Leave enabled unless you know what you are doing.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Advanced::GALLERY_ID);
    	$this->assertTrue($option->getValidValueRegex() === '/\w+/', $option->getName());
    	$this->assertTrue($option->isMeantToBePersisted() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Advanced::HTTPS);
    	$this->assertTrue($option->isBoolean() === true, $option->getName());
    	$this->assertTrue($option->isProOnly() === true, $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
        $this->assertTrue($option->getLabel() === 'Enable HTTPS', $option->getName());
        $this->assertTrue($option->getDescription() === 'Serve thumbnails and embedded video player over a secure connection.', $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Advanced::KEYWORD);
    	$this->assertTrue($option->getLabel() === 'Shortcode keyword', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === 'tubepress', $option->getName());
    	$this->assertTrue($option->getDescription() === 'The word you insert (in plaintext, between square brackets) into your posts/pages to display a gallery.', $option->getName());
    	$this->assertTrue($option->isAbleToBeSetViaShortcode() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Feed::VIDEO_BLACKLIST);
    	$this->assertTrue($option->getLabel() === 'Video blacklist', $option->getName());
    	$this->assertTrue($option->getDescription() === 'A list of video IDs that should never be displayed.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION);
    	$this->assertTrue($option->getLabel() === '<a href="http://wikipedia.org/wiki/Ajax_(programming)">Ajax</a>-enabled pagination', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === true, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION);
    	$this->assertTrue($option->getLabel() === 'Play each video', $option->getName() . ' has incorrect label');
    	$this->assertTrue($option->getDefaultValue() === tubepress_api_const_options_values_PlayerLocationValue::NORMAL, $option->getName() . ' has incorrect default value');
    	$this->assertTrue($option->getAcceptableValues() === array(
        	tubepress_api_const_options_values_PlayerLocationValue::NORMAL    => 'normally (at the top of your gallery)',
        	tubepress_api_const_options_values_PlayerLocationValue::POPUP     => 'in a popup window',
        	tubepress_api_const_options_values_PlayerLocationValue::YOUTUBE   => 'from the video\'s original YouTube page',
        	tubepress_api_const_options_values_PlayerLocationValue::VIMEO     => 'from the video\'s original Vimeo page',
        	tubepress_api_const_options_values_PlayerLocationValue::SHADOWBOX => 'with Shadowbox',
        	tubepress_api_const_options_values_PlayerLocationValue::JQMODAL   => 'with jqModal',
        	tubepress_api_const_options_values_PlayerLocationValue::TINYBOX   => 'with TinyBox',
        	tubepress_api_const_options_values_PlayerLocationValue::FANCYBOX  => 'with FancyBox',
        	tubepress_api_const_options_values_PlayerLocationValue::STATICC   => 'statically (page refreshes on each thumbnail click)',
        	tubepress_api_const_options_values_PlayerLocationValue::SOLO      => 'in a new window on its own',
        	tubepress_api_const_options_values_PlayerLocationValue::DETACHED  => 'in a "detached" location (see the documentation)'
    	), $option->getName() . ' has incorrect value map');
    	$this->assertTrue($option->isProOnly() === false, $option->getName() . ' should not be pro only');

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Meta::DESC_LIMIT);
    	$this->assertTrue($option->getLabel() === 'Maximum description length', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === 80, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Maximum number of characters to display in video descriptions. Set to 0 for no limit.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Thumbs::FLUID_THUMBS);
    	$this->assertTrue($option->getLabel() === 'Use "fluid" thumbnails', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === true, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Dynamically set thumbnail spacing based on the width of their container.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Thumbs::HQ_THUMBS);
    	$this->assertTrue($option->getLabel() === 'Use high-quality thumbnails', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Note: this option cannot be used with the "randomize thumbnails" feature.', $option->getName());
    	$this->assertTrue($option->isProOnly() === true, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Feed::PER_PAGE_SORT);
    	$this->assertTrue($option->getLabel() === 'Per-page sort order', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === tubepress_api_const_options_values_PerPageSortValue::NONE, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Additional sort order applied to each individual page of a gallery', $option->getName());
    	$this->assertTrue($option->getAcceptableValues() === array(
                tubepress_api_const_options_values_PerPageSortValue::COMMENT_COUNT  => 'comment count',
                tubepress_api_const_options_values_PerPageSortValue::NEWEST         => 'date published (newest first)',
                tubepress_api_const_options_values_PerPageSortValue::OLDEST         => 'date published (oldest first)',
                tubepress_api_const_options_values_PerPageSortValue::DURATION       => 'length',
                tubepress_api_const_options_values_PerPageSortValue::NONE           => 'none',
                tubepress_api_const_options_values_PerPageSortValue::RANDOM         => 'random',
                tubepress_api_const_options_values_PerPageSortValue::RATING         => 'rating',
                tubepress_api_const_options_values_PerPageSortValue::TITLE          => 'title',
                tubepress_api_const_options_values_PerPageSortValue::VIEW_COUNT     => 'view count',
    	), $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Feed::ORDER_BY);
    	$this->assertTrue($option->getLabel() === 'Order videos by', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === tubepress_api_const_options_values_OrderByValue::VIEW_COUNT, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Not all sort orders can be applied to all gallery types. See the <a href="http://tubepress.org/documentation">documentation</a> for more info.', $option->getName());
    	$this->assertTrue($option->getAcceptableValues() === array(
        	tubepress_api_const_options_values_OrderByValue::COMMENT_COUNT  => 'comment count',
        	tubepress_api_const_options_values_OrderByValue::NEWEST         => 'date published (newest first)',
        	tubepress_api_const_options_values_OrderByValue::OLDEST         => 'date published (oldest first)',
        	tubepress_api_const_options_values_OrderByValue::DURATION       => 'length',
        	tubepress_api_const_options_values_OrderByValue::POSITION       => 'position in a playlist',
        	tubepress_api_const_options_values_OrderByValue::RANDOM         => 'randomly',
        	tubepress_api_const_options_values_OrderByValue::RATING         => 'rating',
        	tubepress_api_const_options_values_OrderByValue::RELEVANCE      => 'relevance',
        	tubepress_api_const_options_values_OrderByValue::TITLE          => 'title',
        	tubepress_api_const_options_values_OrderByValue::VIEW_COUNT     => 'view count',
    	), $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Thumbs::PAGINATE_ABOVE);
    	$this->assertTrue($option->getLabel() === 'Show pagination above thumbnails', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === true, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Only applies to galleries that span multiple pages.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW);
    	$this->assertTrue($option->getLabel() === 'Show pagination below thumbnails', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === true, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Only applies to galleries that span multiple pages.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS);
    	$this->assertTrue($option->getLabel() === 'Randomize thumbnail images', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === true, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Most videos come with several thumbnails. By selecting this option, each time someone views your gallery they will see the same videos with each video\'s thumbnail randomized. Note: this option cannot be used with the "high quality thumbnails" feature.', $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName() . ' should not be applicable to vimeo');
    	$this->assertTrue($option->isProOnly() === false, $option->getName() . ' should be set to pro only');

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Meta::RELATIVE_DATES);
    	$this->assertTrue($option->getLabel() === 'Use relative dates', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->getDescription() === 'e.g. "yesterday" instead of "November 3, 1980".', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE);
    	$this->assertTrue($option->getLabel() === 'Thumbnails per page', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === 20, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Default is 20. Maximum is 50.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Thumbs::THEME);
    	$this->assertTrue($option->getLabel() === 'Theme', $option->getName());
    	$this->assertTrue($option->getDescription() === 'The TubePress theme to use for this gallery. Your themes can be found at <tt>%s</tt>, and default themes can be found at <tt>%s</tt>.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Thumbs::THUMB_HEIGHT);
    	$this->assertTrue($option->getLabel() === 'Height (px) of thumbs', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === 90, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Default is 90.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Thumbs::THUMB_WIDTH);
    	$this->assertTrue($option->getLabel() === 'Width (px) of thumbs', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === 120, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Default is 120.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Embedded::AUTONEXT);
    	$this->assertTrue($option->getLabel() === 'Play videos sequentially without user intervention', $option->getName());
    	$this->assertTrue($option->getDescription() === 'When a video finishes, this will start playing the next video in the gallery.', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === true, $option->getName());
    	$this->assertTrue($option->isBoolean() === true, $option->getName());
    	$this->assertTrue($option->isProOnly() === true, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Embedded::AUTOPLAY);
    	$this->assertTrue($option->getLabel() === 'Auto-play all videos', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Embedded::AUTOHIDE);
    	$this->assertTrue($option->getLabel() === 'Auto-hide video controls', $option->getName());
    	$this->assertTrue($option->getDescription() === 'A few seconds after playback begins, fade out the video controls.', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isBoolean() === true, $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Embedded::MODEST_BRANDING);
    	$this->assertTrue($option->getLabel() === '"Modest" branding', $option->getName());
    	$this->assertTrue($option->getDescription() === 'Hide the YouTube logo from the control area.', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === true, $option->getName());
    	$this->assertTrue($option->isBoolean() === true, $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT);
    	$this->assertTrue($option->getLabel() === 'Max height (px)', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === 350, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Default is 350.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH);
    	$this->assertTrue($option->getLabel() === 'Max width (px)', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === 425, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Default is 425.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Embedded::FULLSCREEN);
    	$this->assertTrue($option->getLabel() === 'Allow fullscreen playback.', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === true, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Embedded::HIGH_QUALITY);
    	$this->assertTrue($option->getLabel() === 'Play videos in high definition by default', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Embedded::LAZYPLAY);
    	$this->assertTrue($option->getLabel() === '"Lazy" play videos', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === true, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Auto-play each video after thumbnail click.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Embedded::LOOP);
    	$this->assertTrue($option->getLabel() === 'Loop', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Continue playing the video until the user stops it.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Embedded::PLAYER_COLOR);
    	$this->assertTrue($option->getLabel() === 'Main color', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === '999999', $option->getName());
    	$this->assertTrue($option->getDescription() === 'Default is 999999.', $option->getName());
    	$this->assertTrue($option->getValidValueRegex() === '/^([0-9a-f]{1,2}){3}$/i', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Embedded::PLAYER_HIGHLIGHT);
    	$this->assertTrue($option->getLabel() === 'Highlight color', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === 'FFFFFF', $option->getName());
    	$this->assertTrue($option->getDescription() === 'Default is FFFFFF.', $option->getName());
    	$this->assertTrue($option->getValidValueRegex() === '/^([0-9a-f]{1,2}){3}$/i', $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Embedded::PLAYER_IMPL);
    	$this->assertTrue($option->getLabel() === 'Implementation', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED, $option->getName());
    	$this->assertTrue($option->getDescription() === 'The brand of the embedded player. Default is the provider\'s player (YouTube, Vimeo, etc).', $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->getAcceptableValues() === array(
    	tubepress_api_const_options_values_PlayerImplementationValue::EMBEDPLUS      => 'EmbedPlus',
    	tubepress_api_const_options_values_PlayerImplementationValue::LONGTAIL       => 'JW FLV Media Player (by Longtail Video)',
    	tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED => 'Provider default',
    	), $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Embedded::SHOW_INFO);
    	$this->assertTrue($option->getLabel() === 'Show title and rating before video starts', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Embedded::ENABLE_JS_API);
    	$this->assertTrue($option->getDefaultValue() === true, $option->getName());
    	$this->assertTrue($option->getLabel() === 'Enable JavaScript API', $option->getName());
    	$this->assertTrue($option->getDescription() === 'Allow TubePress to communicate with the embedded video player via JavaScript. This incurs a very small performance overhead, but is required for some features.', $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Embedded::SEQUENCE);
    	$this->assertTrue($option->getDefaultValue() === null, $option->getName());
    	$this->assertTrue($option->isMeantToBePersisted() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Embedded::SHOW_RELATED);
    	$this->assertTrue($option->getLabel() === 'Show related videos', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === true, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Toggles the display of related videos after a video finishes.', $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Cache::CACHE_ENABLED);
    	$this->assertTrue($option->getLabel() === 'Enable API cache', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Store API responses in a cache file to significantly reduce load times for your galleries at the slight expense of freshness.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Feed::DEV_KEY);
    	$this->assertTrue($option->getLabel() === 'YouTube API Developer Key', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === 'AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg', $option->getName());
    	$this->assertTrue($option->getDescription() === 'YouTube will use this developer key for logging and debugging purposes if you experience a service problem on their end. You can register a new client ID and developer key <a href="http://code.google.com/apis/youtube/dashboard/">here</a>. Don\'t change this unless you know what you\'re doing.', $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Feed::EMBEDDABLE_ONLY);
    	$this->assertTrue($option->getLabel() === 'Only retrieve embeddable videos', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === true, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Some videos have embedding disabled. Checking this option will exclude these videos from your galleries.', $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Feed::FILTER);
    	$this->assertTrue($option->getLabel() === 'Filter "racy" content', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === tubepress_api_const_options_values_SafeSearchValue::GALLERY_SOURCERATE, $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->getAcceptableValues() === array(
        	tubepress_api_const_options_values_SafeSearchValue::NONE     => 'none',
        	tubepress_api_const_options_values_SafeSearchValue::GALLERY_SOURCERATE => 'moderate',
        	tubepress_api_const_options_values_SafeSearchValue::STRICT   => 'strict',
    	), $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP);
    	$this->assertTrue($option->getLabel() === 'Maximum total videos to retrieve', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === 300, $option->getName());
    	$this->assertTrue($option->getDescription() === 'This can help to reduce the number of pages in your gallery. Set to "0" to remove any limit.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER);
    	$this->assertTrue($option->getLabel() === 'Restrict search results to videos from author', $option->getName());
    	$this->assertTrue($option->getDescription() === 'A YouTube or Vimeo user name. Only applies to search-based galleries.', $option->getName());
    	$this->assertTrue($option->getValidValueRegex() === '/\w*/', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Feed::VIMEO_KEY);
    	$this->assertTrue($option->getLabel() === 'Vimeo API "Consumer Key"', $option->getName());
    	$this->assertTrue($option->getDescription() === '<a href="http://vimeo.com/api/applications/new">Click here</a> to register for a consumer key and secret.', $option->getName());
    	$this->assertTrue($option->isApplicableToYouTube() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Feed::VIMEO_SECRET);
    	$this->assertTrue($option->getLabel() === 'Vimeo API "Consumer Secret"', $option->getName());
    	$this->assertTrue($option->getDescription() === '<a href="http://vimeo.com/api/applications/new">Click here</a> to register for a consumer key and secret.', $option->getName());
    	$this->assertTrue($option->isApplicableToYouTube() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Meta::AUTHOR);
    	$this->assertTrue($option->getLabel() === 'Author', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Meta::CATEGORY);
    	$this->assertTrue($option->getLabel() === 'Category', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Meta::DESCRIPTION);
    	$this->assertTrue($option->getLabel() === 'Description', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Meta::ID);
    	$this->assertTrue($option->getLabel() === 'ID', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Meta::LENGTH);
    	$this->assertTrue($option->getLabel() === 'Runtime', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === true, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Meta::LIKES);
    	$this->assertTrue($option->getLabel() === 'Number of "likes"', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isApplicableToYouTube() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Meta::RATING);
    	$this->assertTrue($option->getLabel() === 'Average rating', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Meta::RATINGS);
    	$this->assertTrue($option->getLabel() === 'Number of ratings', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Meta::KEYWORDS);
    	$this->assertTrue($option->getLabel() === 'Keywords', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Meta::TITLE);
    	$this->assertTrue($option->getLabel() === 'Title', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === true, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Meta::UPLOADED);
    	$this->assertTrue($option->getLabel() === 'Date posted', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Meta::URL);
    	$this->assertTrue($option->getLabel() === 'URL', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Meta::VIEWS);
    	$this->assertTrue($option->getLabel() === 'View count', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === true, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_GallerySource::YOUTUBE_FAVORITES_VALUE);
    	$this->assertTrue($option->getDefaultValue() === 'mrdeathgod', $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Output::GALLERY_SOURCE);
    	$this->assertTrue($option->getDefaultValue() === tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED, $option->getName());
    	$this->assertTrue($option->getAcceptableValues() === array(
            tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES,
            tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
            tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_DISCUSSED,
            tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RECENT,
            tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RESPONDED,
            tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_VIEWED,
            tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
            tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_FAVORITES,
            tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_RATED,
            tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_USER,
            tubepress_api_const_options_values_GallerySourceValue::VIMEO_ALBUM,
            tubepress_api_const_options_values_GallerySourceValue::VIMEO_APPEARS_IN,
            tubepress_api_const_options_values_GallerySourceValue::VIMEO_CHANNEL,
            tubepress_api_const_options_values_GallerySourceValue::VIMEO_CREDITED,
            tubepress_api_const_options_values_GallerySourceValue::VIMEO_GROUP,
            tubepress_api_const_options_values_GallerySourceValue::VIMEO_LIKES,
            tubepress_api_const_options_values_GallerySourceValue::VIMEO_SEARCH,
            tubepress_api_const_options_values_GallerySourceValue::VIMEO_UPLOADEDBY,
    	), $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_GallerySource::YOUTUBE_MOST_VIEWED_VALUE);
    	$this->assertTrue($option->getDefaultValue() === tubepress_api_const_options_values_TimeFrameValue::TODAY, $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Output::OUTPUT);
    	$this->assertTrue($option->isMeantToBePersisted() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_Output::VIDEO);
    	$this->assertTrue($option->isMeantToBePersisted() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE);
    	$this->assertTrue($option->getDefaultValue() === 'D2B04665B213AE35', $option->getName());
    	$this->assertTrue($option->getDescription() === 'Limited to 200 videos per playlist. Will usually look something like this: D2B04665B213AE35. Copy the playlist id from the end of the URL in your browser\'s address bar (while looking at a YouTube playlist). It comes right after the "p=". For instance: http://youtube.com/my_playlists?p=D2B04665B213AE35', $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_InteractiveSearch::SEARCH_PROVIDER);
    	$this->assertTrue($option->isProOnly() === false, $option->getName());
    	$this->assertTrue($option->getAcceptableValues() === array(
            tubepress_spi_provider_Provider::YOUTUBE,
            tubepress_spi_provider_Provider::VIMEO,
        ));

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_DOM_ID);
    	$this->assertTrue($option->isProOnly() === true, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_ONLY);
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_URL);
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE);
    	$this->assertTrue($option->getDefaultValue() === 'pittsburgh steelers', $option->getName());
    	$this->assertTrue($option->getDescription() === 'YouTube limits this to 1,000 results.', $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_GallerySource::YOUTUBE_TOP_RATED_VALUE);
    	$this->assertTrue($option->getDefaultValue() === tubepress_api_const_options_values_TimeFrameValue::TODAY, $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_GallerySource::YOUTUBE_TOP_FAVORITES_VALUE);
    	$this->assertTrue($option->getDefaultValue() === tubepress_api_const_options_values_TimeFrameValue::TODAY, $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_GallerySource::YOUTUBE_USER_VALUE);
    	$this->assertTrue($option->getDefaultValue() === '3hough', $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_GallerySource::VIMEO_UPLOADEDBY_VALUE);
    	$this->assertTrue($option->getDefaultValue() === 'mattkaar', $option->getName());
    	$this->assertTrue($option->isApplicableToYouTube() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_GallerySource::VIMEO_LIKES_VALUE);
    	$this->assertTrue($option->getDefaultValue() === 'coiffier', $option->getName());
    	$this->assertTrue($option->isApplicableToYouTube() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_GallerySource::VIMEO_APPEARS_IN_VALUE);
    	$this->assertTrue($option->getDefaultValue() === 'royksopp', $option->getName());
    	$this->assertTrue($option->isApplicableToYouTube() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE);
    	$this->assertTrue($option->getDefaultValue() === 'cats playing piano', $option->getName());
    	$this->assertTrue($option->isApplicableToYouTube() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_GallerySource::VIMEO_CREDITED_VALUE);
    	$this->assertTrue($option->getDefaultValue() === 'patricklawler', $option->getName());
    	$this->assertTrue($option->isApplicableToYouTube() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_GallerySource::VIMEO_CHANNEL_VALUE);
    	$this->assertTrue($option->getDefaultValue() === 'splitscreenstuff', $option->getName());
    	$this->assertTrue($option->isApplicableToYouTube() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_GallerySource::VIMEO_GROUP_VALUE);
    	$this->assertTrue($option->getDefaultValue() === 'hdxs', $option->getName());
    	$this->assertTrue($option->isApplicableToYouTube() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_GallerySource::VIMEO_ALBUM_VALUE);
    	$this->assertTrue($option->getDefaultValue() === '140484', $option->getName());
    	$this->assertTrue($option->isApplicableToYouTube() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_plugins_wordpresscore_lib_api_const_options_names_WordPress::WIDGET_TITLE);
    	$this->assertTrue($option->getDefaultValue() === 'TubePress', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_plugins_wordpresscore_lib_api_const_options_names_WordPress::WIDGET_SHORTCODE);
    	$this->assertTrue($option->getDefaultValue() === '[tubepress thumbHeight=\'105\' thumbWidth=\'135\']', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_GallerySource::YOUTUBE_MOST_DISCUSSED_VALUE);


    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_GallerySource::YOUTUBE_MOST_RECENT_VALUE);


    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_GallerySource::YOUTUBE_MOST_RESPONDED_VALUE);

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_OptionsUi::SHOW_VIMEO_OPTIONS);

    	$option = $this->_sut->findOneByName(tubepress_api_const_options_names_OptionsUi::SHOW_YOUTUBE_OPTIONS);
	}

    private function setupMocks()
    {
        $environmentDetector = \Mockery::mock(tubepress_spi_environment_EnvironmentDetector::_);
        $filesystem          = \Mockery::mock('ehough_fimble_api_Filesystem');
        $finderFactory       = \Mockery::mock('ehough_fimble_api_FinderFactory');
        $finder              = \Mockery::mock('ehough_fimble_api_Finder');
        $fakeThemeDir        = \Mockery::mock();

        $environmentDetector->shouldReceive('getTubePressBaseInstallationPath')->andReturn('base-install-path');
        $environmentDetector->shouldReceive('getUserContentDirectory')->once()->andReturn('user-content-dir');

        $filesystem->shouldReceive('exists')->once()->with('base-install-path/src/main/resources/default-themes')->andReturn(false);
        $filesystem->shouldReceive('exists')->once()->with('user-content-dir/themes')->andReturn(true);

        $finderFactory->shouldReceive('createFinder')->once()->andReturn($finder);
        $finder->shouldReceive('directories')->once()->andReturn($finder);
        $finder->shouldReceive('in')->once()->with(array('user-content-dir/themes'))->andReturn($finder);
        $finder->shouldReceive('depth')->once()->with(0);

        $finder->shouldReceive('getIterator')->andReturn(new ArrayIterator(array($fakeThemeDir)));

        $fakeThemeDir->shouldReceive('getBasename')->once()->andReturn('xyz');

        tubepress_impl_patterns_ioc_KernelServiceLocator::setEnvironmentDetector($environmentDetector);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setFileSystem($filesystem);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setFileSystemFinderFactory($finderFactory);
    }
}

