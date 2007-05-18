// lightWindow.js v1.1
//
// Copyright (c) 2007 Einstein Industries
// Author: Kevin P Miller | http://www.stickmanlabs.com
// 
// LightWindow is freely distributable under the terms of an MIT-style license.
//
// I don't care what you think about the file size...
//   Be a pro: 
//	    http://www.thinkvitamin.com/features/webapps/serving-javascript-fast
//      http://rakaz.nl/item/make_your_pages_load_faster_by_combining_and_compressing_javascript_and_css_files
//

/*-----------------------------------------------------------------------------------------------*/

if(typeof Effect == 'undefined')
  throw("lightWindow.js requires including script.aculo.us' effects.js library!");

var lightWindow = Class.create();	
lightWindow.prototype = {
	//
	//	Setup Variables
	//
	element : null,
	contentToFetch : null,
	boxOverFlow : 'hidden',
	retroIE : null,
	windowType : null,
	animating : false,
	scrollX : null,
	scrollY : null,
	imageArray : [],
	preloadImage : null,
	activeGallery : null,
	activeImage : 0,
	galleryDirection : null,
	showDataToggle : false,
	galleryToggle : false,
	showTitleToggle : false,
	//
	//	Initialize the lightWindow.
	//
	initialize : function(options) {
		this.options = Object.extend({
			resizeSpeed : 9,
			cushion : 10,
			dimensions : {
				image : {height : 250, width : 250},
				page : {height : 250, width : 500},
				inline : {height : 250, width : 500},
				media : {height : 250, width : 250},
				external : {height : 250, width : 250},
				dataHeight : 40,
				titleHeight : 25
			},
			classNames : {	
				standard : 'lWOn',
				action : 'lWAction'
			},
			fileTypes : {
				page : ['htm', 'html', 'rhtml', 'phtml', 'txt', 'php', 'shtml', 'php4', 'php3', 'php', 'php5', 'asp', 'aspx', 'vbs', 'pl', 'cgi', 'rb'],
				media : ['mpg', 'mp4', 'avi', 'mov', 'mp3', 'aif', 'wmv', 'wav', 'mpeg', 'flv', 'aiff', 'aac', 'ac3', 'asf', 'divx', 'qt', 'rm', 'ram', 'swf', 'wma', 'moov'],
				image : ['png', 'jpg', 'gif', 'bmp', 'tiff']
			},
			loadingDialog : {
				message : 'Loading',
				image :  'wp-content/plugins/tubepress/lib/lightWindow/images/ajax-loading.gif',
				options : '<a onclick="javascript: mylightWindow.deactivate();">Cancel</a>',
				delay : 3.0
			},
			authorLead : 'by ',
			galleryTab : {
				name : 'Galleries',
				height : 20,
				visible : true
			},
			overlay : {
				color : '#000000',
				opacity : 70,
				image : 'wp-content/plugins/tubepress/lib/lightWindow/images/black-70.png'
			},
			formMethod : 'get',
			hideFlash : true,
			showTitleBar : true
		}, options || {})
		this.duration = ((11-this.options.resizeSpeed)*0.15);
		this.setupLinks();
		this.addLightWindowMarkup(false);
		this.setupDimensions(true);
	},
	// 
	//  Set Links Up
	//
	setupLinks : function () {
		var links = $$('.'+this.options.classNames.standard);
		links.each(function(link) {
			if (this.fileType(link.href) == 'image') {
				if (gallery = this.getGalleryInfo(link.rel)) {
					if (!this.imageArray[gallery[0]]) this.imageArray[gallery[0]] = new Array();
					if (!this.imageArray[gallery[0]][gallery[1]]) this.imageArray[gallery[0]][gallery[1]] = new Array();
					this.imageArray[gallery[0]][gallery[1]].push(new Array(link.href, link.getAttribute('title'), link.getAttribute('caption'), link.getAttribute('author'), link.getAttribute('rel'), link.getAttribute('params')));
				}
			}
			var url = link.getAttribute('href');
			if (link.href.indexOf('?') > -1) url = url.substring(0, url.indexOf('?'));
			container = url.substring(url.indexOf('#')+1);
			if($(container)) $(container).style.display = 'none';
			Event.observe(link, 'click', this.activate.bindAsEventListener(this, link));
			link.onclick = function() {return false;};
		}.bind(this));	
	},
	//
	//  Initialize specific window
	//
	initializeWindow : function (id) {
		var link = $(id);
		if (this.fileType(link.href) == 'image') {
			if (gallery = this.getGalleryInfo(link.rel)) {
				if (!this.imageArray[gallery[0]]) this.imageArray[gallery[0]] = new Array();
				if (!this.imageArray[gallery[0]][gallery[1]]) this.imageArray[gallery[0]][gallery[1]] = new Array();
				this.imageArray[gallery[0]][gallery[1]].push(new Array(link.href, link.getAttribute('title'), link.getAttribute('caption'), link.getAttribute('author'), link.getAttribute('rel'), link.getAttribute('params')));
			}
		}
		var url = link.getAttribute('href');
		if (link.href.indexOf('?') > -1) url = url.substring(0, url.indexOf('?'));
		container = url.substring(url.indexOf('#')+1);
		if($(container)) $(container).style.display = 'none';
		Event.observe(link, 'click', this.activate.bindAsEventListener(this, link));
		link.onclick = function() {return false;};
	},
	//
	//	Add the markup to the page.
	//
	addLightWindowMarkup : function(rebuild) {
	    if (!rebuild) {
			var overlay = document.createElement('div');
			overlay.setAttribute('id', 'overlay');
			if (this.checkBrowser('mac') && this.checkBrowser('firefox')) {
		    	overlay.style.backgroundImage = 'url('+this.options.overlay.image+')';
			    overlay.style.backgroundRepeat = 'repeat';
			} else {
				overlay.style.backgroundColor = this.options.overlay.color;
			    overlay.style.MozOpacity = '.'+this.options.overlay.opacity;
			    overlay.style.opacity = '.'+this.options.overlay.opacity;
			    overlay.style.filter = 'alpha(opacity='+this.options.overlay.opacity+')';
			}
			var lw = document.createElement('div');
			lw.setAttribute('id', 'lightWindow');
		} else {
			var lw = $('lightWindow');
		}

		if (this.options.showTitleBar) lw = this.addTitleBarMarkup(lw);
				
		var lwc = document.createElement('div');
		lwc.setAttribute('id', 'lightWindow-contents');
		
		var lwcc = document.createElement('div');
		lwcc.setAttribute('id', 'lightWindow-contents-container');
		lwc.appendChild(lwcc);						
						
		var lwl = document.createElement('div');
		lwl.setAttribute('id', 'lightWindow-loading');

		var lwi = document.createElement('img');
		lwi.setAttribute('src', this.options.loadingDialog.image);
		lwl.appendChild(lwi);

		var lwld = document.createElement('span');
		lwld.setAttribute('id', 'lightWindow-loading-message');
		lwld.innerHTML += this.options.loadingDialog.message;
		lwl.appendChild(lwld);
		
		var lwlo = document.createElement('span');
		lwlo.setAttribute('id', 'lightWindow-loading-options');
		lwlo.setAttribute('style', 'display:none;');
		lwlo.innerHTML += this.options.loadingDialog.options;
		lwl.appendChild(lwlo);
		
		lwc.appendChild(lwl);
		
		lw.appendChild(lwc);
		
		if (!rebuild) {
			var body = document.getElementsByTagName('body')[0];
			body.appendChild(overlay);
			body.appendChild(lw);	
			Event.observe(overlay, 'click', this.deactivate.bindAsEventListener(this), false);
			overlay.onclick = function() {return false;};
		}
		this.addDataWindowMarkup();
		this.actions('#lightWindow-loading-options');
	},
	//
	//	Add the Title Bar Markup
	//
	addTitleBarMarkup : function(lw) {
	
		var lwdt = document.createElement('div');
		lwdt.setAttribute('id', 'lightWindow-title-bar');
		lwdt.style.display = 'none';

		var lwdtt = document.createElement('div');
		lwdtt.setAttribute('id', 'lightWindow-title-bar-title');
		lwdt.appendChild(lwdtt);
				
		var lwdtc = document.createElement('div');
		lwdtc.setAttribute('id', 'lightWindow-title-bar-close');


		var lwdtca = document.createElement('a');
		lwdtca.setAttribute('id', 'lightWindow-title-bar-close-link');
		lwdtca.innerHTML = 'close';
		Event.observe(lwdtca, 'click', this.deactivate.bindAsEventListener(this));
		lwdtca.onclick = function() {return false;};
		lwdtc.appendChild(lwdtca);
		lwdt.appendChild(lwdtc);
		
		lw.appendChild(lwdt);
		return lw;
		
	},
	//
	//	Add the Data Window Markup
	//
	addDataWindowMarkup : function() {
		var lw = $('lightWindow');
		
		var lwd = document.createElement('div');
		lwd.setAttribute('id', 'lightWindow-data');	
		lwd.style.display = 'none';

		// This container needs to be here to get the data slide to slide as a group
		var lwds = document.createElement('div');
		lwds.setAttribute('id', 'lightWindow-data-slide');
		
		if (!this.options.showTitleBar) {
			var lwdt = document.createElement('div');
			lwdt.setAttribute('id', 'lightWindow-data-title');	
			lwds.appendChild(lwdt);
		}
		
		var lwdc = document.createElement('div');
		lwdc.setAttribute('id', 'lightWindow-data-caption');	
		lwds.appendChild(lwdc);
				
		var lwda = document.createElement('div');
		lwda.setAttribute('id', 'lightWindow-data-author');	
		lwds.appendChild(lwda);
				
		var lwdi = document.createElement('div');
		lwdi.setAttribute('id', 'lightWindow-data-image');	
		lwds.appendChild(lwdi);

		lwd.appendChild(lwds);
		lw.appendChild(lwd);
	},
	//
	//	Add Photo Window Markup
	//
	addPhotoWindowMarkup : function() {
		var lwc = $('lightWindow-contents');
		
		var lwpc = document.createElement('div');
		lwpc.setAttribute('id', 'lightWindow-photo-container');
		lwpc.style.display = 'none';
		
		if (images = parseInt(this.getParam('lWShowImages'))) {
			for (var x = 0; x < images; x++) {
				lwp = document.createElement('img');
	    		lwp.setAttribute('id', 'lightWindow-photo-'+x);
	    		lwpc.appendChild(lwp);
			}
		} else {
			lwp = document.createElement('img');
    		lwp.setAttribute('id', 'lightWindow-photo-0');
    		lwpc.appendChild(lwp);
		}
		
		// You ask why I do this?  I ask why you insist on using a browser worse than IE? ...Safari!
		lwps = document.createElement('img');
		lwps.setAttribute('id', 'lightWindow-photo-sizer');
		lwps.style.display = 'none';
		lwps.style.height = '1px';	
		lwpc.appendChild(lwps);

    	lwc.appendChild(lwpc);
	},
	//
	//	Add Gallery Window Markup
	//
	addGalleryWindowMarkup : function() {
		var lwpc = $('lightWindow-photo-container');

		var lwpg = document.createElement('div');
		lwpg.setAttribute('id', 'lightWindow-photo-galleries');
		lwpg.style.display = 'none';
		if (!this.options.galleryTab.visible) lwpg.style.visibility = 'hidden';
		
		var lwptc = document.createElement('div');
		lwptc.setAttribute('id', 'lightWindow-photo-tab-container');
		
		var lwpgt = document.createElement('a');
		lwpgt.setAttribute('id', 'lightWindow-photo-galleries-tab');
		lwpgt.className = 'up';
		lwpgt.innerHTML = this.options.galleryTab.name;
		Event.observe(lwpgt, 'click', this.getGallery.bindAsEventListener(this));
		lwpgt.onclick = function() {return false;};
		
		lwptc.appendChild(lwpgt);
		lwpg.appendChild(lwptc);
						
		var lwpgl = document.createElement('div');
		lwpgl.setAttribute('id', 'lightWindow-photo-galleries-list');	
		lwpg.appendChild(lwpgl);

    	lwpc.appendChild(lwpg);
	},
	//
	//	Activate the lightWindow.
	//
	activate : function(e, link){
		link.blur();
		this.element = link;
		this.element.title = link.getAttribute('title');
		this.element.author = link.getAttribute('author');
		this.element.caption = link.getAttribute('caption');
		this.element.rel = link.getAttribute('rel');
		this.element.params = this.element.getAttribute('params');
		this.windowType = this.fileType(this.contentToFetch = link.href);
		if (this.element.caption || this.element.author) this.showDataToggle = true;
		if (this.options.showTitleBar && this.element.title) this.showTitleToggle = true;
		else if (!this.options.showTitleBar && this.element.title) this.showDataToggle = true;
		if (this.getGalleryInfo(this.element.rel)) this.galleryToggle = true;
		this.prepareIE(true);
		this.toggleTroubleElements('hidden', false);
		this.displayLightWindow(true);
		this.setupDimensions(true);
		this.monitorKeyboard(true);	
		this.loadInfo();	
	},
	//
	//	Turn off the window
	//
	deactivate : function(){
		var queue = Effect.Queues.get('lightWindowAnimation').each(function(e) {e.cancel();});
		queue = Effect.Queues.get('lightWindowAnimation-loading').each(function(e) {e.cancel();});
		if ($('lightWindow-iframe')) Element.remove($('lightWindow-iframe'));
		Element.remove($('lightWindow-contents'));
		if ($('lightWindow-data')) Element.remove($('lightWindow-data'));
		if ($('lightWindow-title-bar')) Element.remove($('lightWindow-title-bar'));
		this.displayLightWindow(false);	
		this.boxOverFlow = 'hidden';
		this.prepareIE(false);
		this.setStatus(false);
		this.showDataToggle = this.galleryToggle = this.showTitleToggle = false;
		this.addLightWindowMarkup(true);
		this.setupDimensions(true);
		this.monitorKeyboard(false);
		this.toggleTroubleElements('visible', false);		
	},
	//
	//	Setup our actions
	//
	actions : function(prefix) {
		if (prefix) links = $$(prefix+' .'+this.options.classNames.action);
		else links = $$('.'+this.options.classNames.action);
		links.each(function(link) {
			Event.observe(link, 'click', this[link.rel].bindAsEventListener(this, link), false);
			link.onclick = function() {return false;};
		}.bind(this));
	},
	//
	//	Set the staus of our animation to keep things from getting clunky
	//
	setStatus : function(status) {
		this.animating = status;
		// We have to put this here to avoid a flicker in FF Mac
		if (this.showTitleToggle && !status && $('lightWindow-title-bar')) {
			$('lightWindow-title-bar').setStyle({ 
				display : 'block' 
			});
		}
	},
	//
	//	Setup Dimensions of lightWindow.
	//
	setupDataDimensions : function() {
		if ($('lightWindow-contents') && $('lightWindow-data') && this.showDataToggle) {
			$('lightWindow-data').setStyle({
				height : this.options.dimensions.dataHeight+'px',
		  		width : (parseFloat($('lightWindow-contents').style.width)+this.options.cushion*2)+'px'
			});
			$('lightWindow-data-slide').setStyle({
				height : this.options.dimensions.dataHeight+'px',
		  		overflow : 'hidden' // Because of IE
			});
		}	
		if (this.showTitleToggle && $('lightWindow-title-bar')) {
			$('lightWindow-title-bar').setStyle({
				height : this.options.dimensions.titleHeight+'px',
	  			width : (parseFloat($('lightWindow-contents').style.width)+this.options.cushion*2)+'px'
			});
		}
	},
	//
	//	Setup Dimensions of lightWindow.
	//
	setupDimensions : function(reset) {
		if (this.showDataToggle || (this.galleryToggle && this.options.galleryTab.visible)) var adjust = this.options.dimensions.dataHeight;
		else var adjust = 0;

		var originalHeight, originalWidth, titleHeight;
		switch (this.windowType) {
			case 'page' :
				originalHeight = this.options.dimensions.page.height;
				originalWidth = this.options.dimensions.page.width;
				break;

			case 'image' :
				originalHeight = this.options.dimensions.image.height;
				originalWidth = this.options.dimensions.image.width;
				break;
				
			case 'media' :
				originalHeight = this.options.dimensions.media.height;
				originalWidth = this.options.dimensions.media.width;
				break;
			
			case 'external' : 
				originalHeight = this.options.dimensions.external.height;
				originalWidth = this.options.dimensions.external.width;
				break;
				
			case 'inline' :
				originalHeight = this.options.dimensions.inline.height;
				originalWidth = this.options.dimensions.inline.width;
				break;
				
			default :
				originalHeight = this.options.dimensions.page.height;
				originalWidth = this.options.dimensions.page.width;
				break;
				
		}
		if (this.showTitleToggle) titleHeight = this.options.dimensions.titleHeight-1; // We subtract one to smooth out the hiccup when the title bar is added
		else titleHeight = 0;
		
		if (reset) {
			if (parseFloat($('lightWindow-contents').style.height) != originalHeight) {
				$('lightWindow-contents').setStyle({
					top : titleHeight+'px',
				  	width : (originalWidth+this.options.cushion)+'px',
				  	height : (originalHeight+this.options.cushion)+'px'
				});
			} else {
				$('lightWindow-contents').setStyle({
				  	width : (originalWidth+this.options.cushion)+'px',
				  	height : (originalHeight+this.options.cushion)+'px'
				});
			}
			$('lightWindow').setStyle({
				padding : '0 0 0 0',
			  	width : '0px',
			  	height : '0px',		
				margin : (-(((originalHeight+this.options.cushion*3)/2)+(adjust/2)+(titleHeight/2)))+'px 0 0 '+(-((originalWidth+this.options.cushion*3)/2))+'px'
			});
		} else {
			$('lightWindow').setStyle({
				padding : parseFloat($('lightWindow-contents').style.height)+2*this.options.cushion+titleHeight+'px 0 0 0',
	  			width : '0px',
	  			height : '0px',
				margin : (-(((parseFloat($('lightWindow-contents').style.height)+this.options.cushion*2)/2)+(adjust/2)+(titleHeight/2)))+'px 0 0 '+(-((parseFloat($('lightWindow-contents').style.width)+this.options.cushion*2)/2))+'px'
			});
			if (parseFloat($('lightWindow-contents').style.height) != originalHeight) {
				$('lightWindow-contents').setStyle({
					top : titleHeight+'px',
					left : '0px'
				});
			}
		}
	}, 
	// 
	// Setup the Overlay (Special Thanks to quirksmode.com and huddletogether.com)
	//
	setupOverlay : function() {

		var xScroll, yScroll;

		if (window.innerHeight && window.scrollMaxY) {	
			xScroll = document.body.scrollWidth;
			yScroll = window.innerHeight + window.scrollMaxY;
		} else if (document.body.scrollHeight > document.body.offsetHeight){ 
			xScroll = document.body.scrollWidth;
			yScroll = document.body.scrollHeight;
		} else { 
			xScroll = document.body.offsetWidth;
			yScroll = document.body.offsetHeight;
		}

		var windowWidth, windowHeight;
		if (self.innerHeight) {	
			windowWidth = self.innerWidth;
			windowHeight = self.innerHeight;
		} else if (document.documentElement && document.documentElement.clientHeight) { 
			windowWidth = document.documentElement.clientWidth;
			windowHeight = document.documentElement.clientHeight;
		} else if (document.body) { 
			windowWidth = document.body.clientWidth;
			windowHeight = document.body.clientHeight;
		}	

		if(yScroll < windowHeight){
			pageHeight = windowHeight;
		} else { 
			pageHeight = yScroll;
		}

		if(xScroll < windowWidth){	
			pageWidth = windowWidth;
		} else {
			pageWidth = xScroll;
		}
		
		$('overlay').style.height = pageHeight;
		$('overlay').style.width = pageWidth;
	},  
	//
	//	Display the lightWindow.
	//
	displayLightWindow : function(display) {
		if (display) {
			$('overlay').style.display = $('lightWindow').style.display = $('lightWindow-contents').style.display = 'block';	
		} else {
			$('overlay').style.display = $('lightWindow').style.display = 'none'; 	
		}
	},  
	//
	//	Is this IE?
	//
	checkBrowser : function(type) {
		var detect = navigator.userAgent.toLowerCase();
		var version = parseInt(navigator.appVersion);
		var place = detect.indexOf(type)+1;
		return place;
	},
	//
	//	Prepare the window for IE.
	//
	prepareIE : function(setup) {
		if (this.checkBrowser('msie')) {
			var height, overflowX, overflowY;
			if (setup) { 
				this.getScroll();
				this.setScroll(0, 0);
				var height = '100%';
			} else {
				var height = 'auto';
			}
			var body = document.getElementsByTagName('body')[0];
			var html = document.getElementsByTagName('html')[0];
			html.style.height = body.style.height = height;
			html.style.margin = body.style.margin = '0';
			this.setupOverlay();
			if (!setup) this.setScroll(this.scrollX, this.scrollY);				
		}
	},
	//
	//	Hide Selects from the page because of IE.
	//     We could use iframe shims instead here but why add all the extra markup for one browser when this is much easier and cleaner
	//
	toggleTroubleElements : function(visibility, content){
		if (content) var selects = $('lightWindow-contents').getElementsByTagName('select');
		else var selects = document.getElementsByTagName('select');
		for(var i = 0; i < selects.length; i++) {
			selects[i].style.visibility = visibility;
		}
		if (!content) {
			if (this.options.hideFlash){
				var objects = document.getElementsByTagName('object');
				for (i = 0; i != objects.length; i++) {
					objects[i].style.visibility = visibility;
				}
				var embeds = document.getElementsByTagName('embed');
				for (i = 0; i != embeds.length; i++) {
					embeds[i].style.visibility = visibility;
				}
			}
			var iframes = document.getElementsByTagName('iframe');
			for (i = 0; i != iframes.length; i++) {
				iframes[i].style.visibility = visibility;
			}
		}
	},
	//
	//	Get the scroll for the page.
	//
	getScroll : function(){
      	if(typeof(window.pageYOffset) == 'number') {
        	this.scrollY = window.pageYOffset;
        	this.scrollX = window.pageXOffset;
      	} else if(document.body && (document.body.scrollLeft || document.body.scrollTop)) {
        	this.scrollY = document.body.scrollTop;
        	this.scrollX = document.body.scrollLeft;
      	} else if(document.documentElement && (document.documentElement.scrollLeft || document.documentElement.scrollTop)) {
        	this.scrollY = document.documentElement.scrollTop;
        	this.scrollX = document.documentElement.scrollLeft;
      	}
	},
	//
	//	Reset the scroll.
	//
	setScroll : function(x, y) {
		document.documentElement.scrollLeft = x; 
		document.documentElement.scrollTop = y; 
	},
	//
	//	Get the value from the params attribute string.
	//
	getParam : function(strParamName, strParams) {
        if (!strParams) {
			if (this.element.params) strParams = this.element.params;
			else return;
		}
		var strReturn;
        var aQueryString = strParams.split(',');
        var cmpstring = strParamName+'=';
        var cmplen = cmpstring.length;
        for (var iParam = 0; iParam < aQueryString.length; iParam++) {
        	if (aQueryString[iParam].substr(0, cmplen) == cmpstring) {
            	var aParam = aQueryString[iParam].split('=');
                strReturn = aParam[1];
                break;
            }
        }
		if (!strReturn) return false;
        else return unescape(strReturn);
    },
	//
	//	Get the domain from a string.
	//
	getDomain : function(url) {    
        var leadSlashes = url.indexOf('//');
        var domainStart = leadSlashes+2;
        var withoutResource = url.substring(domainStart, url.length);
        var nextSlash = withoutResource.indexOf('/');
        var domain = withoutResource.substring(0, nextSlash);
		if (domain.indexOf(':') > -1){
			var portColon = domain.indexOf(':');
			domain = domain.substring(0, portColon);
       	}
		return domain;
    },
	//
	//	Get the type of file.
	//
	fileType : function(url) {

		var image = new RegExp("[^\.]\.("+this.options.fileTypes.image.join('|')+")\s*$", "i");
		if (image.test(url)) return 'image';
				
		if (url.indexOf('#') > -1 && (document.domain == this.getDomain(url))) return 'inline';		
		if (url.indexOf('?') > -1) url = url.substring(0, url.indexOf('?'));

		var type = 'unknown';
		var page = new RegExp("[^\.]\.("+this.options.fileTypes.page.join('|')+")\s*$", "i");
		var media = new RegExp("[^\.]\.("+this.options.fileTypes.media.join('|')+")\s*$", "i");
		
		if (document.domain != this.getDomain(url)) type = 'external';
	  	if (media.test(url)) type = 'media';
	
		if (type == 'external' || type == 'media') return type;
			
	  	if (page.test(url) || url.substr((url.length-1), url.length) == '/') type = 'page';

		return type;
	},
	//
	//	Monitor the keyboard while this lightWindow is up
	//
	monitorKeyboard : function(status) {
		if (status) document.onkeydown = this.eventKeypress.bind(this); 
		else document.onkeydown = '';
	},
	//
	//  Perform keyboard actions
	//
	eventKeypress : function(e) {

		if (e == null) var keycode = event.keyCode;
		else var keycode = e.which;
		
		switch (keycode) { 
			case 27: 
				this.deactivate(); 
				break;
			
			case 13:
				return;
				
			default:
				break;
		}
	
		// Gotta stop those quick fingers
		if (this.animating || !this.galleryToggle) return;

		switch (String.fromCharCode(keycode).toLowerCase()) {
			case 'p':
				this.galleryDirection = -1;
				this.changeImage();
				break;
				
			case 'n':
				this.galleryDirection = 1;
				this.changeImage();
				break;
				
			default:
				break;
		}
	},  
	//
	//	Make the Data Box for the Window
	//
	showData : function() {
		if (this.galleryToggle) $('lightWindow-photo-galleries').style.display = 'block';
		this.setupDataDimensions();
		this.setupDimensions(false);		
		if (this.showDataToggle) {
			var showDatabox = new Effect.Parallel(
				[new Effect.SlideDown( 'lightWindow-data', {sync: true, duration: this.duration+1.0, from: 0.0, to: 1.0}), 
			 	new Effect.Appear('lightWindow-data', {sync: true, duration: 1.0}) ], 
				{duration: 0.65, afterFinish: this.setStatus.bind(this, false), queue: {position: 'end', scope: 'lightWindowAnimation'} } 
			);
		} else {
			 this.setStatus(false);
		}
	},
	//
	//	Insert Data into Window
	//
	insertData : function() {
		if (this.element.title) {
			if (this.showTitleToggle) $('lightWindow-title-bar-title').innerHTML = this.element.title; 
			else $('lightWindow-data-title').innerHTML = this.element.title;
		}
		if (this.element.caption) $('lightWindow-data-caption').innerHTML = this.element.caption;
		if (this.element.author) $('lightWindow-data-author').innerHTML = this.options.authorLead+this.element.author;	
	},
	//
	//	Reset the scroll.
	//
	getGalleryInfo : function(rel) {
		if (rel.indexOf('[') > -1) {
			return new Array(escape(rel.substring(0, rel.indexOf('['))), escape(rel.substring(rel.indexOf('[')+1, rel.indexOf(']'))));
		} else {
			return false;
		}
	},
	// 
	//	Choose a gallery/category
	//
	getGallery : function() {
		var isBadBrowser = this.checkBrowser('msie 6');
		if (!$('lightWindow-photo-galleries').style.height || parseInt($('lightWindow-photo-galleries').style.height) == this.options.galleryTab.height) {
			if (isBadBrowser) {
				var gallerySize = 100;
			} else {
				var gallerySize = ((parseInt($('lightWindow-contents').style.height)*0.95)/this.options.galleryTab.height)*100;
			}
			
			$('lightWindow-photo-galleries-list').setStyle({
				height : (parseInt($('lightWindow-contents').style.height)*0.95)-this.options.galleryTab.height+'px'
			});

			// Get out Galleries from the imageArray
			$('lightWindow-photo-galleries-list').innerHTML = '';
			var output = '';
			for (i in this.imageArray) {
				if (typeof this.imageArray[i] == 'object') {
					output += '<div class="lightWindow-photo-gallery-listing"><h1>'+unescape(i)+'</h1><ul>';
					for (j in this.imageArray[i]) {
						if (typeof this.imageArray[i][j] == 'object') {
							if (this.imageArray[i][j][0][5]) showImages = ',lWShowImages='+this.getParam('lWShowImages', this.imageArray[i][j][0][5]);
							else showImages = '';
							output += '<li><a href="#" params="lWGallery='+escape(i)+',lWCategory='+escape(j)+''+showImages+'" class="'+this.options.classNames.action+'" rel="reloadGallery" >'+unescape(j)+'</a></li>';
						}
					}
					output += '</ul></div>';
				}
			}
			new Insertion.Top('lightWindow-photo-galleries-list', output);
			this.actions('.lightWindow-photo-gallery-listing');

			// IE CSS support sucks and I cannot scale from the bottom....
			if (isBadBrowser) {
				$('lightWindow-photo-galleries').setStyle({
					height : (parseInt($('lightWindow-contents').style.height)*0.95)+'px',
					bottom : '0px'
				});				
				$('lightWindow-photo-galleries-tab').className = 'down';
			} else {
				var showGalleries = new Effect.CushionScale('lightWindow-photo-galleries', gallerySize, {duration: this.duration, afterFinish: function(){$('lightWindow-photo-galleries-list').style.overflow = 'auto'; $('lightWindow-photo-galleries-tab').className = 'down';}, scaleX: false, scaleY: true, scaleContent: false, scaleFromCenter: false, queue: {position: 'end', scope: 'lightWindowAnimation'}});		
			}
		} else {
			if (isBadBrowser) {
				var bottom = -(parseInt($('lightWindow-contents').style.height)*0.95)+this.options.galleryTab.height;			
			} else {
				var bottom = 0;
			}
			
			$('lightWindow-photo-galleries').setStyle({
				height : this.options.galleryTab.height+'px',
				bottom : bottom+'px',
				top : ''
			});
			$('lightWindow-photo-galleries-list').setStyle({
				overflow : 'hidden'
			});
			$('lightWindow-photo-galleries-tab').className = 'up';
		}
	},
	//
	//	Set the gallery up.
	//
	setupGallery : function(gallery, start) 
	{
		var lwc = $('lightWindow-photo-container');

		if (!(images = parseInt(this.getParam('lWShowImages')))) images = 1;		
		
		for (var x = 0; x < this.imageArray[gallery[0]][gallery[1]].length; x++) {
			if (this.imageArray[gallery[0]][gallery[1]][x][0] == this.contentToFetch) break;
		}

		this.activeImage = x;
		this.activeGallery = gallery;
		
		var lwn = document.createElement("div");
		lwn.setAttribute('id','lightWindow-navigation');
		lwc.appendChild(lwn);
				
		if (x != 0 && this.imageArray[gallery[0]][gallery[1]][x-images]) {
			var lwnp = document.createElement("a");
			lwnp.setAttribute('id','lightWindow-previous');
			lwnp.setAttribute('href','#');
			lwn.appendChild(lwnp);
			Event.observe(lwnp, 'click', this.changeImage.bindAsEventListener(this, this.imageArray[gallery[0]][gallery[1]][x-images][0], this.imageArray[gallery[0]][gallery[1]][x-images][1], this.imageArray[gallery[0]][gallery[1]][x-images][2], this.imageArray[gallery[0]][gallery[1]][x-images][3], this.imageArray[gallery[0]][gallery[1]][x-images][4]));
			lwnp.onclick = function(){return false;};
		}
		if ((x+1) < this.imageArray[gallery[0]][gallery[1]].length && this.imageArray[gallery[0]][gallery[1]][x+images]) {
			var lwnn = document.createElement("a");
			lwnn.setAttribute('id','lightWindow-next');
			lwnn.setAttribute('href','#');
			lwn.appendChild(lwnn);
			Event.observe(lwnn, 'click', this.changeImage.bindAsEventListener(this, this.imageArray[gallery[0]][gallery[1]][x+images][0], this.imageArray[gallery[0]][gallery[1]][x+images][1], this.imageArray[gallery[0]][gallery[1]][x+images][2], this.imageArray[gallery[0]][gallery[1]][x+images][3], this.imageArray[gallery[0]][gallery[1]][x+images][4]));
			lwnn.onclick = function(){return false;};
		}	
		if (images == 1) $('lightWindow-data-image').innerHTML = 'Image '+(x+1)+' of '+this.imageArray[gallery[0]][gallery[1]].length;
		this.addGalleryWindowMarkup();
	},
	//
	//	Get the contents for the window
	//
	loadInfo : function() {	
		var showLoadingOptions = new Effect.Appear('lightWindow-loading-options', {delay: this.options.loadingDialog.delay, duration: this.duration, queue: {position: 'front', scope: 'lightWindowAnimation-loading'}});
		switch (this.windowType) {
			case 'image' :
				this.preloadImage = new Array();
				if (!$('lightWindow-photo-container')) {
					this.addPhotoWindowMarkup();
					this.addDataWindowMarkup();
					this.addGalleryWindowMarkup();
				}
				var totalWidth = 0;
				var totalHeight = 0;
				var gallery = this.getGalleryInfo(this.element.rel);
				if (images = parseInt(this.getParam('lWShowImages'))) {
					for (var z = 0; z < this.imageArray[gallery[0]][gallery[1]].length; z++) {
						if (this.imageArray[gallery[0]][gallery[1]][z][0] == this.contentToFetch) break;
					}
					$('lightWindow-photo-container').style.display = 'none';
					this.loading = images-1;
					for (var x = 0; x < images; x++) {
						if (this.imageArray[gallery[0]][gallery[1]][x+z]) {
							this.preloadImage[x] = new Image();
							this.preloadImage[x].onload=function(){
								if ($('lightWindow-photo-container').style.display != 'block') {
									for (var t = 0; t <= x; t++) {
										if (this.preloadImage[t] && (this.preloadImage[t].width != 0 && this.preloadImage[t].height != 0)) {
											totalWidth = totalWidth+this.preloadImage[t].width;
											totalHeight = this.preloadImage[t].height;
											this.preloadImage.splice(t, 1);
											this.loading--;	
										}
									}
									if (this.loading < 0) {
										$('lightWindow-photo-container').setStyle({
											display : 'block'
										});
										$('lightWindow-photo-sizer').setStyle({
											width : totalWidth+'px',
											height : totalHeight+'px'
										});
										this.processInfo();
									}
								}
							}.bind(this, x);
							this.preloadImage[x].src = $('lightWindow-photo-'+x).src = this.imageArray[gallery[0]][gallery[1]][x+z][0];
						}
					}
					this.activeImage = this.activeImage+x-1;
					if (this.galleryToggle) this.setupGallery(this.getGalleryInfo(this.element.rel));
				} else {
					this.preloadImage[0] = new Image();
					this.preloadImage[0].onload=function(){
						totalWidth = this.preloadImage[0].width;
						totalHeight = this.preloadImage[0].height;
						$('lightWindow-photo-container').setStyle({
							display : 'block'
						});
						$('lightWindow-photo-sizer').setStyle({
							width : totalWidth+'px',
							height : totalHeight+'px'
						});
						this.processInfo();
					}.bind(this);
					this.preloadImage[0].src = $('lightWindow-photo-0').src = this.contentToFetch;
					if (this.galleryToggle) this.setupGallery(this.getGalleryInfo(this.element.rel));
				}
				break;
			
			case 'media' :	
				// Don't question it.... damn safari
				var lwi = '<iframe id="lightWindow-iframe" name="lightWindow-iframe" height="100%" width="100%" frameborder="0" scrolling="no"></iframe>';
				new Insertion.Top($('lightWindow-contents'), lwi);
				parent.$('lightWindow-iframe').style.visibility = 'hidden';	
				this.processInfo();
				break;

			case 'external' :	
				// Don't question it.... damn safari
				var lwi = '<iframe id="lightWindow-iframe" name="lightWindow-iframe" height="100%" width="100%" frameborder="0" scrolling="auto"></iframe>';
				new Insertion.Top($('lightWindow-contents'), lwi);
				parent.$('lightWindow-iframe').style.visibility = 'hidden';
				this.processInfo();
				break;
					
			case 'page' :
				var newAJAX = new Ajax.Request(
        			this.contentToFetch,
        			{method: 'get', parameters: '', onComplete: this.processInfo.bind(this)}
				);
				break;
				
			case 'inline' : 
				var content = this.contentToFetch;
				if (content.indexOf('?') > -1) {
					content = content.substring(0, content.indexOf('?'));
				}
				content = content.substring(content.indexOf('#')+1);
				new Insertion.Top($('lightWindow-contents-container'), $(content).innerHTML);
				this.toggleTroubleElements('hidden', true); 
				this.processInfo();
				break;
				
			default : 
				throw('Page Type could not be determined, please amend this lightWindow URL '+this.contentToFetch);
				break;
			}
	},
	//
	//	Finish the loading process and clean up.
	//
	loadFinish : function() {
		this.actions();	
		this.insertData(false);
		switch (this.windowType) {
			case 'page' :
				var hideLoading = new Effect.Fade('lightWindow-loading', {duration: this.duration, afterFinish: this.windowAdjust.bind(this), queue: {position: 'end', scope: 'lightWindowAnimation'}});
				break;

			case 'image' :
				var hideLoading = new Effect.Fade('lightWindow-loading', {duration: this.duration, afterFinish: this.windowAdjust.bind(this), queue: {position: 'end', scope: 'lightWindowAnimation'}});
				break;
				
			case 'media' :
				parent.$('lightWindow-iframe').src = this.contentToFetch;
				var hideLoading = new Effect.Fade('lightWindow-loading', {duration: this.duration, afterFinish: this.windowAdjust.bind(this), queue: {position: 'end', scope: 'lightWindowAnimation'}});
				break;
			
			case 'external' : 
				parent.$('lightWindow-iframe').src = this.contentToFetch;
				var hideLoading = new Effect.Fade('lightWindow-loading', {duration: this.duration, afterFinish: this.windowAdjust.bind(this), queue: {position: 'end', scope: 'lightWindowAnimation'}});
				break;
				
			case 'inline' :
				var hideLoading = new Effect.Fade('lightWindow-loading', {duration: this.duration, afterFinish: this.windowAdjust.bind(this), queue: {position: 'end', scope: 'lightWindowAnimation'}});
				break;
				
			default :
				break;
		}
	},	
	// 
	//  Adjust the Window and add the data box if it needs it
	//
	windowAdjust : function() {
		if (this.windowType == 'external' || this.windowType == 'media') {
			// No I don't like this but it works with a small flicker, FF for the Mac is a little more buggy than I would have thought
			// Of Note this is really for the quicktime samples as far as I can tell....
			if (this.checkBrowser('mac') && this.checkBrowser('firefox')) {
				if ($('overlay').style.height == '100%' || !$('overlay').style.height) $('overlay').style.height = '101%';
				else $('overlay').style.height = '100%';
			}
			parent.$('lightWindow-iframe').style.visibility = 'visible';
		}
		$('lightWindow-contents').style.overflow = this.boxOverFlow;
		this.toggleTroubleElements('visible', true);
		if (this.showDataToggle || this.showTitleToggle) {
			this.showData();
		}
	},
	//
	//	Get the content into the window and show it off.
	//
	processInfo : function(response) {	
		if(this.checkBrowser('msie')) {
            var windowHeight = document.documentElement.clientHeight;
            var windowWidth = document.documentElement.clientWidth;   
        } else {
            var windowHeight = window.innerHeight;
            var windowWidth = window.innerWidth;
        }

		// What if the window size is ridiculously small? If so we need some overrides to make it fit and make it usable (even on set dimensions)
		if (this.showDataToggle) var dataWindow = this.options.dimensions.dataHeight;
		else var dataWindow = 0;
		if (this.options.showTitleBar) titleHeight = this.options.dimensions.titleHeight;
		else titleHeight = 0;
		var lWcWidth = parseInt($('lightWindow-contents').style.width);
		var lWcHeight = parseInt($('lightWindow-contents').style.height);
		var availableHeight = windowHeight-dataWindow-2*this.options.cushion-titleHeight;
		var availableWidth = windowWidth-2*this.options.cushion;
      	var boxWidth, boxScrollWidth, boxHeight, boxScrollHeight, scaleX, scaleY;	
		var totalHeight = 0;
		var totalWidth = 0;	
		switch (this.windowType) {
			case 'image' :
				if (!(images = parseInt(this.getParam('lWShowImages')))) images = 1;
				boxWidth = $('lightWindow-contents').offsetWidth;
				boxHeight = $('lightWindow-contents').offsetHeight;	
				if ($('lightWindow-photo-0').height > availableHeight) {
					var totalWidth = 0;
					for (var x = 0; x < images; x++) {
						$('lightWindow-photo-'+x).height = availableHeight;
						totalWidth = totalWidth+$('lightWindow-photo-'+x).width;
					}
					if (images > 1) totalWidth++; // This is needed for putting images side by side when we resize the iamge only
					boxScrollHeight = availableHeight;
					boxScrollWidth = totalWidth;
					$('lightWindow-photo-sizer').style.height = availableHeight+'px';
					$('lightWindow-photo-sizer').style.width = totalWidth+'px';
				} else {
					boxScrollHeight = parseInt($('lightWindow-photo-sizer').style.height);		
					boxScrollWidth = parseInt($('lightWindow-photo-sizer').style.width);
				}
				break;
				
			case 'external' :				
		    	boxWidth = $('lightWindow-contents').offsetWidth;
				boxHeight = $('lightWindow-contents').offsetHeight;			
				break;
			
			case 'media' :				
			    boxWidth = $('lightWindow-contents').offsetWidth;
				boxHeight = $('lightWindow-contents').offsetHeight;			
				break;
					
			case 'page' :
				new Insertion.Top($('lightWindow-contents-container'), response.responseText);
				this.toggleTroubleElements('hidden', true); 
				boxWidth = $('lightWindow-contents').offsetWidth;
				boxScrollWidth = $('lightWindow-contents').scrollWidth;
				boxHeight = $('lightWindow-contents').offsetHeight;
				boxScrollHeight = $('lightWindow-contents').scrollHeight;
				break;
			
			case 'inline' :
				boxWidth = $('lightWindow-contents').offsetWidth;
				boxScrollWidth = $('lightWindow-contents').scrollWidth;
				boxHeight = $('lightWindow-contents').offsetHeight;
				boxScrollHeight = $('lightWindow-contents').scrollHeight+3;
				break;
					
			default : 
				break;
				
		}

		// Were dimensions set?
		// This also resizes to fit the window, for things like flash!
		var ignorelWHeight = false;
      	if (lWWidth = this.getParam('lWWidth')) {
			boxScrollWidth = parseFloat(lWWidth);
			if (boxScrollWidth > (windowWidth*.95)) {
				tmp = boxScrollWidth;
				boxScrollWidth = 0.90*windowWidth;
				lWHeight = this.getParam('lWHeight'); // For this case I require a height to be set, why would you set width and not set height?
				boxScrollHeight = parseFloat(lWHeight);
				boxScrollHeight = boxScrollHeight * (boxScrollWidth/tmp)
				ignorelWHeight = true;
			}
		}

		if (lWHeight = this.getParam('lWHeight')) {
			if (!ignorelWHeight) {
				boxScrollHeight = parseFloat(lWHeight);
				if (boxScrollHeight > (windowHeight*.8)) {
					boxScrollHeight = 0.8*windowHeight;
				}
			}
		}
		
		if (lWOverflow = this.getParam('lWOverflow')) this.boxOverFlow = lWOverflow;

		if ((boxScrollHeight < (windowHeight*.8)) && this.windowType != 'external' && this.windowType != 'image') {
			scaleY = parseFloat((boxScrollHeight/boxHeight)*100);
		} else if (this.windowType == 'external' && !lWHeight) {
			scaleY = parseFloat((windowHeight/(1.2*boxHeight))*100);
		} else if (this.windowType == 'external' && lWHeight) {
			scaleY = parseFloat((boxScrollHeight/(boxHeight))*100);
		} else if (this.windowType == 'image' || this.windowType == 'media') {
			scaleY = parseFloat(((boxScrollHeight)/boxHeight)*100);
		} else {
			if (this.windowType != 'media') this.boxOverFlow = 'auto';
			$('lightWindow-contents-container').marginRight = '16px';
			scaleY = parseFloat((windowHeight/(1.2*boxHeight))*100);
		}
		if ((boxScrollWidth < (windowWidth*.8)) && this.windowType != 'external' && this.windowType != 'image' && this.windowType != 'media') {
			scaleX = parseFloat(((boxScrollWidth)/boxWidth)*100);
		} else if (this.windowType == 'external' && !lWWidth) {
			scaleX = parseFloat((windowWidth/(1.1*boxWidth))*100);
		} else if (this.windowType == 'external' && lWWidth) {
			scaleX = parseFloat((boxScrollWidth/(boxWidth))*100);
		} else if (this.windowType == 'image' || this.windowType == 'media') {
			scaleX = parseFloat(((boxScrollWidth)/boxWidth)*100);
		} else {
			if (this.windowType != 'media') this.boxOverFlow = 'auto';
			$('lightWindow-contents-container').marginRight = '16px';
			scaleX = parseFloat((windowWidth/(1.1*boxWidth))*100);
		}
		
		this.setStatus(true);
		var doDelay = 0;
		if (scaleX != 100 && lWcWidth != boxScrollWidth) {
			if (scaleY == 100) var doX = new Effect.CushionScale('lightWindow-contents', scaleX, {duration: this.duration, scaleX: true, scaleY: false, scaleCushion: {top: this.options.cushion, left: this.options.cushion}, afterFinish: this.loadFinish.bind(this), scaleFromCenter: true, scaleContent: false, queue: {position: 'front', scope: 'lightWindowAnimation'}});	
			else var doX = new Effect.CushionScale('lightWindow-contents', scaleX, {duration: this.duration, scaleX: true, scaleY: false, scaleCushion: {top: this.options.cushion, left: this.options.cushion}, scaleContent: false, scaleFromCenter: true, queue: {position: 'front', scope: 'lightWindowAnimation'}});	
			doDelay = this.duration/2;
		}
		if (scaleY != 100 && lWcHeight != boxScrollHeight) {
			var doY = new Effect.CushionScale('lightWindow-contents', scaleY, {duration: this.duration, delay: doDelay, scaleX: false, scaleY: true, scaleCushion: {top: this.options.cushion, left: this.options.cushion}, afterFinish: this.loadFinish.bind(this), scaleContent: false, scaleFromCenter: true, queue: {position: 'end', scope: 'lightWindowAnimation'}});
		}
		if ((!doX && !doY) || (doX && scaleY != 100 && !doY)) this.loadFinish();	
	},	
	//
	//	Reload the window with another location
	//
	reloadWindow : function(element) {
		Element.remove($('lightWindow-contents'));
		if ($('lightWindow-data')) Element.remove($('lightWindow-data'));
		this.element = element;
		this.contentToFetch = this.element.href;
		this.addLightWindowMarkup(true);
		this.setupDimensions(true);
		this.displayLightWindow(true);
		this.loadInfo();
	},
	//
	//  Reload the Gallery
	//
	reloadGallery : function(e, link) {
		this.element.params = link.getAttribute('params');
		var gallery = this.getParam('lWGallery', this.element.params);
		var category = this.getParam('lWCategory', this.element.paramse);
		this.element.rel = this.imageArray[gallery][category][0][4];
		this.element.title = this.imageArray[gallery][category][0][1];
		this.element.caption = this.imageArray[gallery][category][0][2];
		this.element.author = this.imageArray[gallery][category][0][3];
		this.contentToFetch = this.imageArray[gallery][category][0][0];
		Element.remove($('lightWindow-photo-container'));
		if ($('lightWindow-data')) Element.remove($('lightWindow-data'));
		if ($('lightWindow-title-bar')) $('lightWindow-title-bar').style.display = 'none';
		this.galleryToggle = true;
		this.activeGallery[0] = gallery
		this.activeGallery[1] = category;
		this.activeImage = 0;
		// Becuase of IE we have to use either Appear or setOpacity/show
		var showLoading = Effect.Appear('lightWindow-loading', {duration: 0, afterFinish: this.loadInfo.bind(this)});
	},
	//
	//	Change the Image
	//
	changeImage : function(e) {
	  	var data = $A(arguments);
	  	data.shift();
		if (data != '') {
			this.contentToFetch = data[0];
			this.element.title = data[1];
			this.element.caption = data[2];
			this.element.author = data[3];
			this.element.rel = data[4];
		} else {
			if (!(images = parseInt(this.getParam('lWShowImages')))) images = 1;
			if ((this.galleryDirection < 0 && (this.activeImage-1*images) < 0) || (this.galleryDirection > 0 && (this.activeImage+1*images) >= this.imageArray[this.activeGallery[0]][this.activeGallery[1]].length)) return false;
			this.element.title = this.imageArray[this.activeGallery[0]][this.activeGallery[1]][this.activeImage+this.galleryDirection*images][1];
			this.element.caption = this.imageArray[this.activeGallery[0]][this.activeGallery[1]][this.activeImage+this.galleryDirection*images][2];
			this.element.author = this.imageArray[this.activeGallery[0]][this.activeGallery[1]][this.activeImage+this.galleryDirection*images][3];	
			this.element.params = this.imageArray[this.activeGallery[0]][this.activeGallery[1]][0][5];				
			this.element.rel = unescape(this.activeGallery[0]+'['+this.activeGallery[1]+']');			
			this.contentToFetch = this.imageArray[this.activeGallery[0]][this.activeGallery[1]][this.activeImage+this.galleryDirection*images][0];
			this.activeImage = this.activeImage+this.galleryDirection*images;	
		}
		// Preload the previous and next images
		if ((this.activeImage-1) >= 0) {
			var preloadNextImage = new Image();
			preloadNextImage.src = this.imageArray[this.activeGallery[0]][this.activeGallery[1]][this.activeImage-1][0];
		}
		if ((this.activeImage+1) < this.imageArray[this.activeGallery[0]][this.activeGallery[1]].length) {
			var preloadPrevImage = new Image();
			preloadPrevImage.src = this.imageArray[this.activeGallery[0]][this.activeGallery[1]][this.activeImage+1][0];
		}
		Element.remove($('lightWindow-photo-container'));
		if ($('lightWindow-data')) Element.remove($('lightWindow-data'));
		if ($('lightWindow-title-bar')) $('lightWindow-title-bar').style.display = 'none';
		this.galleryToggle = true;
		$('lightWindow-loading-options').style.display = 'none';
		// Becuase of IE we have to use either Appear or setOpacity/show
		var showLoading = Effect.Appear('lightWindow-loading', {duration: 0, afterFinish: this.loadInfo.bind(this)});
	},
	//
	//	Submit a form to another lightWindow
	//
	insertForm : function(e) {
		var element = Event.element(e).parentNode;
		var parameterString = Form.serialize(this.getParam('lWForm', element.getAttribute('params')));
		if (this.options.formMethod == 'post') {
			var newAJAX = new Ajax.Request(
			    element.href,
			    {method: 'post', postBody: parameterString, onComplete: this.reloadWindow.bind(this, element)}
			);
		} else if (this.options.formMethod == 'get') {
			var newAJAX = new Ajax.Request(
			    element.href,
			    {method: 'get', parameters: parameterString, onComplete: this.reloadWindow.bind(this, element)}
			);			
		}
	}
}

/*-----------------------------------------------------------------------------------------------*/

Event.observe(window, 'load', lightWindowInit, false);

//
//	Set up all of our links
//
var mylightWindow = null;
function lightWindowInit() {
	mylightWindow = new lightWindow();
}


/*-----------------------------------------------------------------------------------------------
	Problem:
		This effect does not take into account padding or a border on an element, especially an
		absolutely position element. 
	
	Added: 
		Options:
			scaleCushion: 0	// or {top, left} with provided values
			
			Example:
				scaleCushion: {top: 10, left: 10}
		
		Code:
			To setDimensions: function(height, width)
			
				Original:
		    		if(this.options.scaleY) d.top = this.originalTop-topd + 'px';
	        		if(this.options.scaleX) d.left = this.originalLeft-leftd + 'px';					
				
				New:
			    	if(this.options.scaleCushion == 'none') {
		        		if(this.options.scaleY) d.top = this.originalTop-topd + 'px';
		        		if(this.options.scaleX) d.left = this.originalLeft-leftd + 'px';
					} else {
			        	if(this.options.scaleY) d.top = (this.originalTop-topd-this.options.scaleCushion.top-this.options.scaleCushion.bottom) + 'px';
			        	if(this.options.scaleX) d.left = (this.originalLeft-leftd-this.options.scaleCushion.right-this.options.scaleCushion.left) + 'px';		
					}
		
	Credit: Kevin P Miller http://www.stickmanlabs.com
-----------------------------------------------------------------------------------------------*/

Effect.CushionScale = Class.create();
Object.extend(Object.extend(Effect.CushionScale.prototype, Effect.Base.prototype), {
  initialize: function(element, percent) {
    this.element = $(element);
    if(!this.element) throw(Effect._elementDoesNotExistError);
    var options = Object.extend({
      scaleX: true,
      scaleY: true,
      scaleContent: true,
      scaleFromCenter: false,
      scaleMode: 'box',        // 'box' or 'contents' or {} with provided values
      scaleFrom: 100.0,
      scaleTo: percent,
	  scaleCushion: 'none'	   // 'none' or {} with provided values
    }, arguments[2] || {});
    this.start(options);
  },
  setup: function() {
    this.restoreAfterFinish = this.options.restoreAfterFinish || false;
    this.elementPositioning = this.element.getStyle('position');
    
    this.originalStyle = {};
    ['top','left','width','height','fontSize'].each( function(k) {
      this.originalStyle[k] = this.element.style[k];
    }.bind(this));
      
    this.originalTop  = this.element.offsetTop;
    this.originalLeft = this.element.offsetLeft;
    
    var fontSize = this.element.getStyle('font-size') || '100%';
    ['em','px','%','pt'].each( function(fontSizeType) {
      if(fontSize.indexOf(fontSizeType)>0) {
        this.fontSize     = parseFloat(fontSize);
        this.fontSizeType = fontSizeType;
      }
    }.bind(this));
    
    this.factor = (this.options.scaleTo - this.options.scaleFrom)/100;
    
    this.dims = null;
    if(this.options.scaleMode=='box')
      this.dims = [this.element.offsetHeight, this.element.offsetWidth];
    if(/^content/.test(this.options.scaleMode))
      this.dims = [this.element.scrollHeight, this.element.scrollWidth];
    if(!this.dims)
      this.dims = [this.options.scaleMode.originalHeight,
                   this.options.scaleMode.originalWidth];
  },
  update: function(position) {
    var currentScale = (this.options.scaleFrom/100.0) + (this.factor * position);
    if(this.options.scaleContent && this.fontSize)
      this.element.setStyle({fontSize: this.fontSize * currentScale + this.fontSizeType });
    this.setDimensions(this.dims[0] * currentScale, this.dims[1] * currentScale);
  },
  finish: function(position) {
    if(this.restoreAfterFinish) this.element.setStyle(this.originalStyle);
  },
  setDimensions: function(height, width) {
    var d = {};
	if(this.options.scaleX) d.width = width + 'px';
	if(this.options.scaleY) d.height = height + 'px';
	if(this.options.scaleFromCenter) {
      var topd  = (height - this.dims[0])/2;
      var leftd = (width  - this.dims[1])/2;
      if(this.elementPositioning == 'absolute') {
	    if(this.options.scaleCushion == 'none') {
        	if(this.options.scaleY) d.top = this.originalTop-topd + 'px';
        	if(this.options.scaleX) d.left = this.originalLeft-leftd + 'px';
		} else {
        	if(this.options.scaleY) d.top = (this.originalTop-topd-this.options.scaleCushion.top) + 'px';
        	if(this.options.scaleX) d.left = (this.originalLeft-leftd-this.options.scaleCushion.left) + 'px';			
		}
      } else {
        if(this.options.scaleY) d.top = -topd + 'px';
        if(this.options.scaleX) d.left = -leftd + 'px';
      }
    }
    this.element.setStyle(d);
  }
});
