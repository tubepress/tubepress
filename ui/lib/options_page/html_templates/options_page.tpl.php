<?php 
/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
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
    <h2><?php echo ${org_tubepress_template_Template::OPTIONS_PAGE_TITLE}; ?></h2>
    <div style="margin-bottom: 1em; width: 60%; float: left"><?php echo ${org_tubepress_template_Template::OPTIONS_PAGE_INTRO}; ?></div>
    <div style="width: 30%; float: right; background-color: #FFFFFF; padding: .5em 1em .5em 1em" class="ui-corner-all">
	<p style="float: left">Only show options applicable to...</p>
	<div style="float: right; vertical-align: middle; padding: 6px">
		<input type="checkbox" id="youtube-checkbox" /><img src="<?php echo ${org_tubepress_template_Template::TUBEPRESS_BASE_URL}; ?>/ui/options_page/images/youtube.png" style="margin: 0 1em -5px 3px" alt="YouTube"/>
		<input type="checkbox" id="vimeo-checkbox" /><img src="<?php echo ${org_tubepress_template_Template::TUBEPRESS_BASE_URL}; ?>/ui/options_page/images/vimeo.png" style="margin: 0 0 -8px 3px"/ alt="Vimeo">
	</div>
    </div>
    <div id="tubepress_tabs" style="clear: both">
      <ul>
        <?php foreach (${org_tubepress_template_Template::OPTIONS_PAGE_CATEGORIES} as $optionCategoryName => $optionCategoryMetaArray): ?>
<li><a href="#<?php echo 'tubepress_' . md5($optionCategoryName); ?>"><span><?php echo $optionCategoryMetaArray[org_tubepress_template_Template::OPTIONS_PAGE_CATEGORY_TITLE]; ?></span></a></li>
        <?php endforeach; ?>

      </ul>

      <?php foreach (${org_tubepress_template_Template::OPTIONS_PAGE_CATEGORIES} as $optionCategoryName => $optionCategoryMetaArray): ?>

      <div id="<?php echo 'tubepress_' . md5($optionCategoryName); ?>">
        <table class="form-table" style="margin: 0">
        
          <?php if ($optionCategoryName != org_tubepress_options_Category::META): ?>
        
            <?php foreach ($optionCategoryMetaArray[org_tubepress_template_Template::OPTIONS_PAGE_CATEGORY_OPTIONS] as $optionArray): ?>
              
          <tr valign="top" class="<?php $classes = array(); if ($optionArray[org_tubepress_template_Template::OPTIONS_PAGE_YOUTUBE_OPTION]) { $classes[] = 'tubepress-youtube-option'; } if ($optionArray[org_tubepress_template_Template::OPTIONS_PAGE_VIMEO_OPTION]) { $classes[] = "tubepress-vimeo-option"; } echo join(" ", $classes); ?>">
            <th style="border-bottom-style: none; font-size: 13px}" valign="top"><?php echo $optionArray[org_tubepress_template_Template::OPTIONS_PAGE_OPTIONS_TITLE]; ?><?php echo $optionArray[org_tubepress_template_Template::OPTIONS_PAGE_OPTIONS_PRO_ONLY]; ?></th>
            <td style="vertical-align: top; border-bottom-style: none"><?php echo $optionArray[org_tubepress_template_Template::OPTIONS_PAGE_OPTIONS_WIDGET]; ?><br /><?php echo $optionArray[org_tubepress_template_Template::OPTIONS_PAGE_OPTIONS_DESC]; ?></td>
            <td style="padding: 3px 0 0; vertical-align: top">
            <?php if ($optionArray[org_tubepress_template_Template::OPTIONS_PAGE_YOUTUBE_OPTION]): ?>
                <img src="<?php echo ${org_tubepress_template_Template::TUBEPRESS_BASE_URL}; ?>/ui/options_page/images/youtube.png" alt="YouTube" class="tubepress-youtube-option"/>
            <?php endif; ?>
            </td>
            <td style="padding: 2px; vertical-align: top">
            <?php if ($optionArray[org_tubepress_template_Template::OPTIONS_PAGE_VIMEO_OPTION]): ?>
                <img src="<?php echo ${org_tubepress_template_Template::TUBEPRESS_BASE_URL}; ?>/ui/options_page/images/vimeo.png" alt="Vimeo" class="tubepress-vimeo-option"/>
            <?php endif; ?>
            </td>  
          </tr>
            <?php endforeach; ?>
          
          <?php else: $index = 0; ?>
            <?php foreach ($optionCategoryMetaArray[org_tubepress_template_Template::OPTIONS_PAGE_CATEGORY_OPTIONS] as $optionArray): ?>
              <?php if ($index % 2 == 0): ?>
              
          <tr valign="top"><?php endif; ?>
                
            <td style="padding: 2px; vertical-align: middle; width: 45px">
            <?php if ($optionArray[org_tubepress_template_Template::OPTIONS_PAGE_YOUTUBE_OPTION]): ?>
                <img class="tubepress-youtube-option" src="<?php echo ${org_tubepress_template_Template::TUBEPRESS_BASE_URL}; ?>/ui/options_page/images/youtube.png" alt="YouTube"/>
            <?php endif; ?>
            </td>
            <td style="padding: 2px; vertical-align: middle; width: 25px">
            <?php if ($optionArray[org_tubepress_template_Template::OPTIONS_PAGE_VIMEO_OPTION]): ?>
                <img class="tubepress-vimeo-option" src="<?php echo ${org_tubepress_template_Template::TUBEPRESS_BASE_URL}; ?>/ui/options_page/images/vimeo.png" alt="Vimeo"/>
            <?php endif; ?>
            </td>
            <td style="border-bottom-style: none; font-size: 13px}" valign="top"><span class="<?php $classes = array(); if ($optionArray[org_tubepress_template_Template::OPTIONS_PAGE_YOUTUBE_OPTION]) { $classes[] = 'tubepress-youtube-option'; } if ($optionArray[org_tubepress_template_Template::OPTIONS_PAGE_VIMEO_OPTION]) { $classes[] = "tubepress-vimeo-option"; } echo join(" ", $classes); ?>"><?php echo $optionArray[org_tubepress_template_Template::OPTIONS_PAGE_OPTIONS_WIDGET]; ?> <?php echo $optionArray[org_tubepress_template_Template::OPTIONS_PAGE_OPTIONS_TITLE]; ?></span></td>
              <?php if (++$index % 2 == 0): ?>
          </tr><?php endif; ?>
            <?php endforeach; ?>
          <?php endif; ?>
          
        </table>
      </div>

      <?php endforeach; ?>

    </div>
    <br />
    <input type="submit" name="tubepress_save" class="button-primary" value="<?php echo ${org_tubepress_template_Template::OPTIONS_PAGE_SAVE}; ?>" />
  </form><br />
  <?php echo ${org_tubepress_template_Template::OPTIONS_PAGE_DONATION}; ?>
  <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_s-xclick" />
    <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!" />
    <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
    <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----" />
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
