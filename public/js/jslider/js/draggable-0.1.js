!function(t){function o(){this._init.apply(this,arguments)}o.prototype.oninit=function(){},o.prototype.events=function(){},o.prototype.onmousedown=function(){this.ptr.css({position:"absolute"})},o.prototype.onmousemove=function(t,o,e){this.ptr.css({left:o,top:e})},o.prototype.onmouseup=function(){},o.prototype.isDefault={drag:!1,clicked:!1,toclick:!0,mouseup:!1},o.prototype._init=function(){if(arguments.length>0){this.ptr=t(arguments[0]),this.outer=t(".draggable-outer"),this.is={},t.extend(this.is,this.isDefault);var o=this.ptr.offset();this.d={left:o.left,top:o.top,width:this.ptr.width(),height:this.ptr.height()},this.oninit.apply(this,arguments),this._events()}},o.prototype._getPageCoords=function(t){return t.targetTouches&&t.targetTouches[0]?{x:t.targetTouches[0].pageX,y:t.targetTouches[0].pageY}:{x:t.pageX,y:t.pageY}},o.prototype._bindEvent=function(t,o,e){this.supportTouches_?t.get(0).addEventListener(this.events_[o],e,!1):t.bind(this.events_[o],e)},o.prototype._events=function(){var o=this;this.supportTouches_="ontouchend"in document,this.events_={click:this.supportTouches_?"touchstart":"click",down:this.supportTouches_?"touchstart":"mousedown",move:this.supportTouches_?"touchmove":"mousemove",up:this.supportTouches_?"touchend":"mouseup"},this._bindEvent(t(document),"move",function(t){o.is.drag&&(t.stopPropagation(),t.preventDefault(),o._mousemove(t))}),this._bindEvent(t(document),"down",function(t){o.is.drag&&(t.stopPropagation(),t.preventDefault())}),this._bindEvent(t(document),"up",function(t){o._mouseup(t)}),this._bindEvent(this.ptr,"down",function(t){return o._mousedown(t),!1}),this._bindEvent(this.ptr,"up",function(t){o._mouseup(t)}),this.ptr.find("a").click(function(){return o.is.clicked=!0,o.is.toclick?void 0:(o.is.toclick=!0,!1)}).mousedown(function(t){return o._mousedown(t),!1}),this.events()},o.prototype._mousedown=function(o){this.is.drag=!0,this.is.clicked=!1,this.is.mouseup=!1;var e=this.ptr.offset(),i=this._getPageCoords(o);this.cx=i.x-e.left,this.cy=i.y-e.top,t.extend(this.d,{left:e.left,top:e.top,width:this.ptr.width(),height:this.ptr.height()}),this.outer&&this.outer.get(0)&&this.outer.css({height:Math.max(this.outer.height(),t(document.body).height()),overflow:"hidden"}),this.onmousedown(o)},o.prototype._mousemove=function(t){this.is.toclick=!1;var o=this._getPageCoords(t);this.onmousemove(t,o.x-this.cx,o.y-this.cy)},o.prototype._mouseup=function(o){this.is.drag&&(this.is.drag=!1,this.outer&&this.outer.get(0)&&(this.outer.css(t.browser.mozilla?{overflow:"hidden"}:{overflow:"visible"}),this.outer.css(t.browser.msie&&"6.0"==t.browser.version?{height:"100%"}:{height:"auto"})),this.onmouseup(o))},window.Draggable=o}(jQuery);