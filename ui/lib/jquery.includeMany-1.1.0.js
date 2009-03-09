/*
 * includeMany 1.1.0
 *
 * Copyright (c) 2009 Arash Karimzadeh (arashkarimzadeh.com)
 * Licensed under the MIT (MIT-LICENSE.txt)
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Date: Feb 28 2009
 */
(function(a){a.include=function(e,b){var f=a.include.luid++;var d=function(h,g){if(a.isFunction(h)){h(g);}if(--a.include.counter[f]==0&&a.isFunction(b)){b();}};if(typeof e=="object"&&typeof e.length=="undefined"){a.include.counter[f]=0;for(var c in e){a.include.counter[f]++;}return a.each(e,function(g,h){a.include.load(g,d,h);
});}e=a.makeArray(e);a.include.counter[f]=e.length;a.each(e,function(){a.include.load(this,d,null);});};a.extend(a.include,{luid:0,counter:[],load:function(b,c,d){if(a.include.exist(b)){return c(d);}if(/.css$/.test(b)){a.include.loadCSS(b,c,d);}else{if(/.js$/.test(b)){a.include.loadJS(b,c,d);}else{a.get(b,function(e){c(d,e);
});}}},loadCSS:function(b,d,e){var c=document.createElement("link");c.setAttribute("type","text/css");c.setAttribute("rel","stylesheet");c.setAttribute("href",b);a("head").get(0).appendChild(c);a.browser.msie?a.include.IEonload(c,d,e):d(e);},loadJS:function(b,d,e){var c=document.createElement("script");
c.setAttribute("type","text/javascript");c.setAttribute("src",b);a.browser.msie?a.include.IEonload(c,d,e):c.onload=function(){d(e);};a("head").get(0).appendChild(c);},IEonload:function(d,b,c){d.onreadystatechange=function(){if(this.readyState=="loaded"||this.readyState=="complete"){b(c);}};},exist:function(c){var b=false;
a("head script").each(function(){if(/.css$/.test(c)&&this.href==c){return b=true;}else{if(/.js$/.test(c)&&this.src==c){return b=true;}}});return b;}});})(jQuery);