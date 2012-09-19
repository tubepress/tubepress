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
class tubepress_impl_options_ui_tabs_ThemeTabTest extends tubepress_impl_options_ui_tabs_AbstractTabTest
{
	protected function _getFieldArray()
	{
	    return array(

            tubepress_api_const_options_names_Thumbs::THEME => tubepress_impl_options_ui_fields_ThemeField::__

        );
	}

	protected function _getRawTitle()
	{
	    return 'Theme';
	}

	protected function _buildSut()
	{
	    return new tubepress_impl_options_ui_tabs_ThemeTab();
	}
}