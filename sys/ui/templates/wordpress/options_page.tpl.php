<?php 
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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
?>
<div class="wrap">
  <form method="post">
    <h2><?php echo ${org_tubepress_api_const_template_Variable::OPTIONS_PAGE_TITLE}; ?></h2>
    <div style="margin-bottom: 1em; width: 60%; float: left"><?php echo ${org_tubepress_api_const_template_Variable::OPTIONS_PAGE_INTRO}; ?></div>
    <div style="width: 30%; float: right; background-color: #FFFFFF; padding: .5em 1em .5em 1em" class="ui-corner-all">
	<p style="float: left"><?php echo ${org_tubepress_api_const_template_Variable::OPTIONS_PAGE_OPTIONS_FILTER}; ?></p>
	<div style="float: right; vertical-align: middle; padding: 6px">
		<input type="checkbox" id="youtube-checkbox" /><img src="<?php echo ${org_tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL}; ?>/sys/ui/static/images/youtube.png" style="margin: 0 1em -5px 3px" alt="YouTube"/>
		<input type="checkbox" id="vimeo-checkbox" /><img src="<?php echo ${org_tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL}; ?>/sys/ui/static/images/vimeo.png" style="margin: 0 0 -8px 3px"/ alt="Vimeo">
	</div>
    </div>
    <div id="tubepress_tabs" style="clear: both">
      <ul>
        <?php foreach (${org_tubepress_api_const_template_Variable::OPTIONS_PAGE_CATEGORIES} as $optionCategoryName => $optionCategoryMetaArray): ?>
<li><a href="#<?php echo 'tubepress_' . md5($optionCategoryName); ?>"><span><?php echo $optionCategoryMetaArray[org_tubepress_api_const_template_Variable::OPTIONS_PAGE_CATEGORY_TITLE]; ?></span></a></li>
        <?php endforeach; ?>

      </ul>

      <?php foreach (${org_tubepress_api_const_template_Variable::OPTIONS_PAGE_CATEGORIES} as $optionCategoryName => $optionCategoryMetaArray): ?>

      <div id="<?php echo 'tubepress_' . md5($optionCategoryName); ?>">
        <table class="form-table" style="margin: 0">
        
          <?php if ($optionCategoryName != org_tubepress_api_const_options_CategoryName::META): ?>
        
            <?php foreach ($optionCategoryMetaArray[org_tubepress_api_const_template_Variable::OPTIONS_PAGE_CATEGORY_OPTIONS] as $optionArray): ?>
              
          <tr valign="top" class="<?php $classes = array(); if ($optionArray[org_tubepress_api_const_template_Variable::OPTIONS_PAGE_YOUTUBE_OPTION]) { $classes[] = 'tubepress-youtube-option'; } if ($optionArray[org_tubepress_api_const_template_Variable::OPTIONS_PAGE_VIMEO_OPTION]) { $classes[] = "tubepress-vimeo-option"; } echo join(" ", $classes); ?>">
            <th style="border-bottom-style: none; font-size: 13px}" valign="top"><?php echo $optionArray[org_tubepress_api_const_template_Variable::OPTIONS_PAGE_OPTIONS_TITLE]; ?><?php echo $optionArray[org_tubepress_api_const_template_Variable::OPTIONS_PAGE_OPTIONS_PRO_ONLY]; ?></th>
            <td style="vertical-align: top; border-bottom-style: none"><?php echo $optionArray[org_tubepress_api_const_template_Variable::OPTIONS_PAGE_OPTIONS_WIDGET]; ?><br /><?php echo $optionArray[org_tubepress_api_const_template_Variable::OPTIONS_PAGE_OPTIONS_DESC]; ?></td>
            <td style="padding: 3px 0 0; vertical-align: top">
            <?php if ($optionArray[org_tubepress_api_const_template_Variable::OPTIONS_PAGE_YOUTUBE_OPTION]): ?>
                <img src="<?php echo ${org_tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL}; ?>/sys/ui/static/images/youtube.png" alt="YouTube" class="tubepress-youtube-option"/>
            <?php endif; ?>
            </td>
            <td style="padding: 2px; vertical-align: top">
            <?php if ($optionArray[org_tubepress_api_const_template_Variable::OPTIONS_PAGE_VIMEO_OPTION]): ?>
                <img src="<?php echo ${org_tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL}; ?>/sys/ui/static/images/vimeo.png" alt="Vimeo" class="tubepress-vimeo-option"/>
            <?php endif; ?>
            </td>  
          </tr>
            <?php endforeach; ?>
          
          <?php else: $index = 0; ?>
            <?php foreach ($optionCategoryMetaArray[org_tubepress_api_const_template_Variable::OPTIONS_PAGE_CATEGORY_OPTIONS] as $optionArray): ?>
              <?php if ($index % 2 == 0): ?>
              
          <tr valign="top"><?php endif; ?>
                
            <td style="padding: 2px; vertical-align: middle; width: 45px">
            <?php if ($optionArray[org_tubepress_api_const_template_Variable::OPTIONS_PAGE_YOUTUBE_OPTION]): ?>
                <img class="tubepress-youtube-option" src="<?php echo ${org_tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL}; ?>/sys/ui/static/images/youtube.png" alt="YouTube"/>
            <?php endif; ?>
            </td>
            <td style="padding: 2px; vertical-align: middle; width: 25px">
            <?php if ($optionArray[org_tubepress_api_const_template_Variable::OPTIONS_PAGE_VIMEO_OPTION]): ?>
                <img class="tubepress-vimeo-option" src="<?php echo ${org_tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL}; ?>/sys/ui/static/images/vimeo.png" alt="Vimeo"/>
            <?php endif; ?>
            </td>
            <td style="border-bottom-style: none; font-size: 13px}" valign="top"><span class="<?php $classes = array(); if ($optionArray[org_tubepress_api_const_template_Variable::OPTIONS_PAGE_YOUTUBE_OPTION]) { $classes[] = 'tubepress-youtube-option'; } if ($optionArray[org_tubepress_api_const_template_Variable::OPTIONS_PAGE_VIMEO_OPTION]) { $classes[] = "tubepress-vimeo-option"; } echo join(" ", $classes); ?>"><?php echo $optionArray[org_tubepress_api_const_template_Variable::OPTIONS_PAGE_OPTIONS_WIDGET]; ?> <?php echo $optionArray[org_tubepress_api_const_template_Variable::OPTIONS_PAGE_OPTIONS_TITLE]; ?></span></td>
              <?php if (++$index % 2 == 0): ?>
          </tr><?php endif; ?>
            <?php endforeach; ?>
          <?php endif; ?>
          
        </table>
      </div>

      <?php endforeach; ?>

    </div>
    <br />
    <input type="submit" name="tubepress_save" class="button-primary" value="<?php echo ${org_tubepress_api_const_template_Variable::OPTIONS_PAGE_SAVE}; ?>" />
    <br /><br />
  </form>
</div>
<script type="text/javascript">
	function tubepressOptionsPageProviderSelector() {
		var names = ["youtube", "vimeo"];
		for (var i in names) {
			if (jQuery("#" + names[i] + "-checkbox").attr("checked")) {
				jQuery(".tubepress-" + names[i] + "-option").show();
			} else {
				jQuery(".tubepress-" + names[i] + "-option").each(function () {
					if (jQuery(this).attr("class") == "tubepress-" + names[i] + "-option") {
						jQuery(this).hide();
					}
				});
			}
		}
	}

	jQuery(document).ready(function() {
		jQuery("#youtube-checkbox, #vimeo-checkbox").attr("checked", true);
		jQuery("#tubepress_tabs").tabs();
		jQuery("#youtube-checkbox, #vimeo-checkbox").click(tubepressOptionsPageProviderSelector);
		tubepressOptionsPageProviderSelector();
	});
</script>
