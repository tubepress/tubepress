<?php
abstract class TubePressGallery extends TPAbstractHasValue implements TubePressValue {
	
	/* All valid modes here */
	const favorites = 	"favorites";
	const tag = 		"tag";
    const related= 		"related";
    const user= 		"user";
    const playlist = 	"playlist";
    const featured = 	"featured";
    const popular = 	"popular";
    const category = 	"category";
    const top_rated = 	"top_rated";
    const mobile = 		"mobile";
	
    public abstract function getVideos(int $start, int $perPage);
}
?>