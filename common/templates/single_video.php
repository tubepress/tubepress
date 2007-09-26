<?php
	$video = new TubePressVideo($_GET[TP_PARAM_VID]);
	$id = $video->getId();
?>
<br>
<div id="tubepress_mainvideo">
	<div id="tubepress_inner" style="width: 424px">
		<div id="tubepress_btitle">
			<?= $video->getTitle(); ?>
		</div>
		<object type="application/x-shockwave-flash" style="width:424px;height:336px;" data="http://www.youtube.com/v/<?= $id ?>">
			<param name="movie" value="http://www.youtube.com/v/<?= $id ?>" />
		</object>
	</div><!-- tubepress_inner -->
</div> <!--tubepress_mainvideo-->
<?= $video->getDescription(); ?>
<br />
