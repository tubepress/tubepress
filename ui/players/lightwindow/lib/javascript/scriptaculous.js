// script.aculo.us scriptaculous.js v1.7.1_beta2, Sat Apr 28 15:20:12 CEST 2007

// Copyright (c) 2005-2007 Thomas Fuchs (http://script.aculo.us, http://mir.aculo.us)
// 
// Permission is hereby granted, free of charge, to any person obtaining
// a copy of this software and associated documentation files (the
// "Software"), to deal in the Software without restriction, including
// without limitation the rights to use, copy, modify, merge, publish,
// distribute, sublicense, and/or sell copies of the Software, and to
// permit persons to whom the Software is furnished to do so, subject to
// the following conditions:
// 
// The above copyright notice and this permission notice shall be
// included in all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
// EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
// NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
// LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
// OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
// WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
//
// For details, see the script.aculo.us web site: http://script.aculo.us/
var Scriptaculous={Version:"1.7.1_beta2",require:function(a){document.write('<script type="text/javascript" src="'+a+'"><\/script>');},REQUIRED_PROTOTYPE:"1.5.1",load:function(){function a(b){var c=b.split(".");return parseInt(c[0])*100000+parseInt(c[1])*1000+parseInt(c[2]);}if((typeof Prototype=="undefined")||(typeof Element=="undefined")||(typeof Element.Methods=="undefined")||(a(Prototype.Version)<a(Scriptaculous.REQUIRED_PROTOTYPE))){throw ("script.aculo.us requires the Prototype JavaScript framework >= "+Scriptaculous.REQUIRED_PROTOTYPE);
}$A(document.getElementsByTagName("script")).findAll(function(b){return(b.src&&b.src.match(/scriptaculous\.js(\?.*)?$/));}).each(function(c){var d=c.src.replace(/scriptaculous\.js(\?.*)?$/,"");var b=c.src.match(/\?.*load=([a-z,]*)/);(b?b[1]:"effects").split(",").each(function(e){Scriptaculous.require(d+e+".js");
});});}};Scriptaculous.load();