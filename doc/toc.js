window.addEvent('domready', function() {

	//initial vars
	var finders = ['h1','h2','h3','h4','h5','h6'];
	var matches = [];

	//find the h1, which is the article title
	$('article-area').getElements('*').each(function(el,i) {

		//do we want this?
		if(finders.contains(el.get('tag')))
		{
			//create anchor
			var anchor = new Element('a', {
				'class': el.get('tag'),
				'text': el.get('text'),
				'href': 'javascript:;'
			});

			//click event
			anchor.addEvent('click', function() {
				var go = new Fx.Scroll(window).toElement(el);
			});

			//add into our matches array
			matches.include(anchor);
		}

	});

	//should we show the toc?
	if(matches.length)
	{
		//create toc div, inject
		var toc = new Element('div', {
			'id': 'toc',
			'html': '<strong>Table of Contents</strong><br />'
		}).inject('article-area','before');

		//inject the matches
		matches.each(function(el) {
			el.inject(toc);
		});
	}

});

