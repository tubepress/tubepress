/*
	ColorBox v1.05 - a full featured, light-weight, customizable lightbox based on jQuery 1.3
	(c) 2009 Jack Moore - www.colorpowered.com - jack@colorpowered.com
	Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
*/

(function(jQuery){

var index, related, loadingElement, modal, modalOverlay, modalLoading, modalContent, modalLoadedContent, modalClose, borderTopLeft, borderTopCenter, borderTopRight, borderMiddleLeft, borderMiddleRight, borderBottomLeft, borderBottomCenter, borderBottomRight;

jQuery(function(){
	//Initialize the modal, preload the interface graphics, and wait until called.
	jQuery("body").append(
		jQuery([
			modalOverlay = jQuery('<div id="modalBackgroundOverlay" />')[0], 
			modal = jQuery('<div id="colorbox" />')[0]
		]).hide()
	);

	jQuery(modal).append(
		jQuery([
			borderTopLeft = jQuery('<div id="borderTopLeft" />')[0],
			borderTopCenter = jQuery('<div id="borderTopCenter" />')[0],
			borderTopRight = jQuery('<div id="borderTopRight" />')[0],
			borderMiddleLeft = jQuery('<div id="borderMiddleLeft" />')[0],
			borderMiddleRight = jQuery('<div id="borderMiddleRight" />')[0],
			borderBottomLeft = jQuery('<div id="borderBottomLeft" />')[0],
			borderBottomCenter = jQuery('<div id="borderBottomCenter" />')[0],
			borderBottomRight = jQuery('<div id="borderBottomRight" />')[0],
			modalContent = jQuery('<div id="modalContent" />')[0]
		])
	);
	
	jQuery(modalContent).append(
		jQuery([
			modalLoadedContent = jQuery('<div id="modalLoadedContent"><a id="contentPrevious" href="#"></a><a id="contentNext" href="#"></a><span id="contentCurrent"></span><br id="modalInfoBr"/><span id="contentTitle"></span><div id="preloadPrevious"></div><div id="preloadNext"></div><div id="preloadClose"></div></div>')[0], 
			modalLoadingOverlay = jQuery('<div id="modalLoadingOverlay" />')[0],
			modalClose = jQuery('<a id="modalClose" href="#"></a>')[0]
		])
	);

	jQuery(modalClose).click(function(){
		closeModal();
		return false;
	});
});

function setModalOverlay(){
	jQuery([modalOverlay]).css({"position":"absolute", width:jQuery(window).width(), height:jQuery(window).height(), top:jQuery(window).scrollTop(), left:jQuery(window).scrollLeft()});
}

function keypressEvents(e){
	if(e.keyCode == 27){
		closeModal();
		return false;
	}
	else if(e.keyCode == 37){
		jQuery("a#contentPrevious").click();
		return false;
	}
	else if(e.keyCode == 39){
		jQuery("a#contentNext").click();
		return false
	}
}

function closeModal(){
	jQuery(modal).removeData("open");
	jQuery([modalOverlay, modal]).fadeOut("fast", function(){
		jQuery(modalLoadedContent).empty();
		jQuery([modalOverlay, modal]).hide();//Seems unnecessary, but sometimes IE6 does not hide the modal.
	});
	if(loadingElement){jQuery(loadingElement).remove()};
	jQuery(document).unbind('keydown', keypressEvents);
	jQuery(window).unbind('resize scroll', setModalOverlay);
}

jQuery.fn.colorbox = function(settings) {

	settings = jQuery.extend({}, jQuery.fn.colorbox.settings, settings);

	//sets the position of the modal on screen.  A transition speed of 0 will result in no animation.
	function modalPosition(modalWidth, modalHeight, transitionSpeed, callback){
		var windowHeight;
		(typeof(window.innerHeight)=='number')?windowHeight=window.innerHeight:windowHeight=document.documentElement.clientHeight;
		var colorboxHeight = modalHeight + jQuery(borderTopLeft).height() + jQuery(borderBottomLeft).height();
		var colorboxWidth = modalWidth + jQuery(borderTopLeft).width() + jQuery(borderBottomLeft).width();
		var posTop = windowHeight/2 - colorboxHeight/2 + jQuery(window).scrollTop();
		var posLeft = jQuery(window).width()/2 - colorboxWidth/2 + jQuery(window).scrollLeft();
		if(colorboxHeight > windowHeight){
			posTop -=(colorboxHeight - windowHeight);
		}
		if(posTop < 0){posTop = 0;} //keeps the box from expanding to an inaccessible area offscreen.
		if(posLeft < 0){posLeft = 0;}
		jQuery(modal).animate({height:colorboxHeight, top:posTop, left:posLeft, width:colorboxWidth}, transitionSpeed);

		//each part is animated seperately to keep them from disappearing during the animation process, which is what would happen if they were positioned relative to a single element being animated.
		jQuery(borderMiddleLeft).animate({top:jQuery(borderTopLeft).height(), left:0, height:modalHeight}, transitionSpeed);
		jQuery(borderMiddleRight).animate({top:jQuery(borderTopRight).height(), left:colorboxWidth-jQuery(borderMiddleRight).width(), height:modalHeight}, transitionSpeed);

		jQuery(borderTopLeft).animate({top:0, left:0}, transitionSpeed);
		jQuery(borderTopCenter).animate({top:0, left:jQuery(borderTopLeft).width(), width:modalWidth}, transitionSpeed);
		jQuery(borderTopRight).animate({top: 0, left: colorboxWidth - jQuery(borderTopRight).width()}, transitionSpeed);

		jQuery(borderBottomLeft).animate({top:colorboxHeight-jQuery(borderBottomLeft).height(), left:0}, transitionSpeed);
		jQuery(borderBottomCenter).animate({top:colorboxHeight-jQuery(borderBottomLeft).height(), left:jQuery(borderBottomLeft).width(), width:modalWidth}, transitionSpeed);
		jQuery(borderBottomRight).animate({top: colorboxHeight - jQuery(borderBottomLeft).height(),	left: colorboxWidth - jQuery(borderBottomRight).width()}, transitionSpeed);
		jQuery(modalContent).animate({height:modalHeight, width:modalWidth, top:jQuery(borderTopLeft).height(), left:jQuery(borderTopLeft).width()}, transitionSpeed, function(){
			if(callback){callback();}
			if(jQuery.browser.msie && jQuery.browser.version < 7){
				setModalOverlay();
			}
		});	
	}
	
	var preloads = [];

	function preload(){
		
	}
	
	function centerModal(contentHtml, contentInfo){
		jQuery(modalLoadedContent).hide().html(contentHtml).append(contentInfo);
		if(settings.contentWidth){jQuery(modalLoadedContent).css({"width":settings.contentWidth})}
		if(settings.contentHeight){jQuery(modalLoadedContent).css({"height":settings.contentHeight})}
		if (settings.transition == "elastic") {
			modalPosition(jQuery(modalLoadedContent).outerWidth(true), jQuery(modalLoadedContent).outerHeight(true), settings.transitionSpeed, function(){
				jQuery(modalLoadedContent).show();
				jQuery(modalLoadingOverlay).hide();
			});
			
		}
		else {
			jQuery(modal).animate({"opacity":0}, settings.transitionSpeed, function(){
				modalPosition(jQuery(modalLoadedContent).outerWidth(true), jQuery(modalLoadedContent).outerHeight(true), 0, function(){
					jQuery(modalLoadedContent).show();
					jQuery(modalLoadingOverlay).hide();
					jQuery(modal).animate({"opacity":1}, settings.transitionSpeed);
				});
			});
		}
		var preloads = preload();
	}
	
	function contentNav(){
		jQuery(modalLoadingOverlay).show();
		if(jQuery(this).attr("id") == "contentPrevious"){
			index > 0 ? index-- : index=related.length-1;
		} else {
			index < related.length-1 ? index++ : index = 0;
		}
		console.info(related);
		buildGallery(related[index]);
		return false;	
	}
	
	function buildGallery(that){
		var contentInfo = "<br id='modalInfoBr'/><span id='contentTitle'></span>";
	
		if (settings.contentInline) {
			centerModal(jQuery(settings.contentInline).html(), contentInfo);
		}
	};
	
	jQuery(this).bind("click.colorbox", function () {
		if (jQuery(modal).data("open") != true) {
			jQuery(modal).data("open", true);
			jQuery(modalLoadedContent).empty().css({
				"height": "auto",
				"width": "auto"
			});
			jQuery(modalClose).html(settings.modalClose);
			jQuery(modalOverlay).css({
				"opacity": settings.bgOpacity
			});
			jQuery([modalOverlay, modal, modalLoadingOverlay]).show();
			jQuery(modalContent).css({
				width: settings.initialWidth,
				height: settings.initialHeight
			});
			modalPosition(jQuery(modalContent).width(), jQuery(modalContent).height(), 0);
			if (this.rel) {
				related = jQuery("a[rel='" + this.rel + "']");
				index = jQuery(related).index(this);
			}
			else {
				related = jQuery(this);
				index = 0;
			}
			console.info(related);
			buildGallery(related[index]);
						jQuery(document).bind('keydown', keypressEvents);
			if (jQuery.browser.msie && jQuery.browser.version < 7) {
				jQuery(window).bind("resize scroll", setModalOverlay);
			}
		}
		return false;
	});

	if(settings.open==true && jQuery(modal).data("open")!=true){
		jQuery(this).triggerHandler('click.colorbox');
	}

	return this.each(function() { 
	});
};

/*
	ColorBox Default Settings.
	
	The colorbox() function takes one argument, an object of key/value pairs, that are used to initialize the modal.
	
	Please do not change these settings here, instead overwrite these settings when attaching the colorbox() event to your anchors.
	Example (Global)	: jQuery.fn.colorbox.settings.transition = "fade"; //changes the transition to fade for all colorBox() events proceeding it's declaration.
	Example (Specific)	: jQuery("a[href='http://www.google.com']").colorbox({contentWidth:"700px", contentHeight:"450px", contentIframe:true});
*/
jQuery.fn.colorbox.settings = {
	transition : "elastic", // "elastic" or "fade". Set transitionSpeed to 0 for no transition.
	transitionSpeed : 350, // Sets the speed of the fade and elastic transition, in milliseconds. Set to 0 for no transition.
	initialWidth : 300, // Set the initial width of the modal, prior to any content being loaded.
	initialHeight : 100, // Set the initial height of the modal, prior to any content being loaded.
	contentWidth : false, // Set a fixed width for div#modalLoadedContent.  Example: "500px"
	contentHeight : false, // Set a fixed height for div#modalLoadedContent.  Example: "500px"
	contentAjax : false, // Set this to the file, or file+selector of content that will be loaded through an external file.  Example "include.html" or "company.inc.php div#ceo_bio"
	contentInline : false, // Set this to the selector, in jQuery selector format, of inline content to be displayed.  Example "#myHiddenDiv".
	contentIframe : false, // If 'true' specifies that content should be displayed in an iFrame.
	bgOpacity : 0.85, // The modalBackgroundOverlay opacity level. Range: 0 to 1.
	preloading : true, // Allows for preloading of 'Next' and 'Previous' content in a shared relation group (same values for the 'rel' attribute), after the current content has finished loading.  Set to 'false' to disable.
	contentCurrent : "{current} of {total}", // the format of the contentCurrent information
	contentPrevious : "previous", // the anchor text for the previous link in a shared relation group (same values for 'rel').
	contentNext : "next", // the anchor text for the next link in a shared relation group (same 'rel' attribute').
	modalClose : "close", // the anchor text for the close link.  Esc will also close the modal.
	open : false //Automatically opens ColorBox. (fires the click.colorbox event without waiting for user input).
}

})(jQuery);

