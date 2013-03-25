/*
Copyright (c) 2012 jqWidgets.
http://jqwidgets.com/license/
*/

(function(a){a.jqx.cssroundedcorners=function(b){var c={all:"jqx-rc-all",top:"jqx-rc-t",bottom:"jqx-rc-b",left:"jqx-rc-l",right:"jqx-rc-r","top-right":"jqx-rc-tr","top-left":"jqx-rc-tl","bottom-right":"jqx-rc-br","bottom-left":"jqx-rc-br"};for(prop in c){if(!c.hasOwnProperty(prop)){continue}if(b==prop){return c[prop]}}};a.jqx.jqxWidget("jqxButton","",{});a.extend(a.jqx._jqxButton.prototype,{defineInstance:function(){this.cursor="arrow";this.roundedCorners="all";this.disabled=false;this.height=null;this.width=null;this.overrideTheme=false},createInstance:function(c){var b=this;if(this.width!=null&&this.width.toString().indexOf("px")!=-1){this.host.width(this.width)}else{if(this.width!=undefined&&!isNaN(this.width)){this.host.width(this.width)}}if(this.height!=null&&this.height.toString().indexOf("px")!=-1){this.host.height(this.height)}else{if(this.height!=undefined&&!isNaN(this.height)){this.host.height(this.height)}}if(!this.overrideTheme){this.host.addClass(this.toThemeProperty(a.jqx.cssroundedcorners(this.roundedCorners)))}this.host.onselectstart=function(){};this.isTouchDevice=a.jqx.mobile.isTouchDevice();this.host.addClass(this.toThemeProperty("jqx-button"));this.host.css({cursor:this.cursor});if(!this.isTouchDevice){this.addHandler(this.host,"mouseenter",function(d){if(!b.disabled){b.isMouseOver=true;b.refresh()}});this.addHandler(this.host,"mouseleave",function(d){if(!b.disabled){b.isMouseOver=false;b.refresh()}})}this.addHandler(this.host,"mousedown",function(d){if(!b.disabled){b.isPressed=true;b.refresh()}});this.addHandler(a(document),"mouseup",function(d){if(!b.disabled){b.isPressed=false;b.refresh()}});this.propertyChangeMap.roundedCorners=function(d,f,e,g){d.refresh()};this.propertyChangeMap.disabled=function(d,f,e,g){b.host[0].disabled=g;d.refresh()}},_removeHandlers:function(){this.removeHandler(this.host,"mouseenter");this.removeHandler(this.host,"mouseleave");this.removeHandler(this.host,"mousedown");this.removeHandler(a(document),"mouseup")},destroy:function(){this._removeHandlers();this.host.removeClass();this.host.remove()},refresh:function(){if(this.overrideTheme){return}var g=this.toThemeProperty("jqx-fill-state-disabled");var b=this.toThemeProperty("jqx-fill-state-normal");var f=this.toThemeProperty("jqx-fill-state-hover");var d=this.toThemeProperty("jqx-fill-state-pressed");var e=this.toThemeProperty("jqx-fill-state-pressedhover");var c="";this.host[0].disabled=this.disabled;if(this.disabled){c=g}else{if(this.isMouseOver&&!this.isTouchDevice){if(this.isPressed){c=e}else{c=f}}else{if(this.isPressed){c=d}else{c=b}}}if(this.host.hasClass(g)&&g!=c){this.host.removeClass(g)}if(this.host.hasClass(b)&&b!=c){this.host.removeClass(b)}if(this.host.hasClass(f)&&f!=c){this.host.removeClass(f)}if(this.host.hasClass(d)&&d!=c){this.host.removeClass(d)}if(this.host.hasClass(e)&&e!=c){this.host.removeClass(e)}if(!this.host.hasClass(c)){this.host.addClass(c)}}});a.jqx.jqxWidget("jqxLinkButton","",{});a.extend(a.jqx._jqxLinkButton.prototype,{createInstance:function(d){var c=this;this.host.onselectstart=function(){return false};var b=this.height||this.host.height();var f=this.width||this.host.width();this.href=this.host.attr("href");this.target=this.host.attr("target");this.content=this.host.html();this.host.wrapInner("<table id='jqxLinkButtonInnerWrapper'><tr valign='middle'><td align='center'></td></tr></table>");this.host.wrap("<table id='jqxLinkButtonOuterWrap'><tr><td></td></tr></table>");var e=this.host.find("#jqxLinkButtonOuterWrap");var g=this.host.find("#jqxLinkButtonInnerWrapper");g.addClass(this.toThemeProperty("jqx-reset"));e.addClass(this.toThemeProperty("jqx-reset"));this.host.find("tr").addClass(this.toThemeProperty("jqx-reset"));this.host.find("td").addClass(this.toThemeProperty("jqx-reset"));this.host.find("tbody").addClass(this.toThemeProperty("jqx-reset"));this.host.css("color","inherit");this.host.addClass(this.toThemeProperty("jqx-link"));g.css({width:f});g.css({height:b});var h=d==undefined?{}:d[0]||{};g.jqxButton(h);if(this.disabled){this.host[0].disabled=true}this.propertyChangeMap.disabled=function(i,k,j,l){c.host[0].disabled=l};this.addHandler(g,"click",function(i){if(!this.disabled){c.onclick(i)}return false})},onclick:function(b){if(this.target!=null){window.open(this.href,this.target)}else{window.location=this.href}}});a.jqx.jqxWidget("jqxRepeatButton","jqxButton",{});a.extend(a.jqx._jqxRepeatButton.prototype,{createInstance:function(d){var b=this;this.delay=this.delay||50;var c=a.jqx.mobile.isTouchDevice();this.addHandler(a(document),"mouseup",function(e){if(!c){if(b.timeout!=null){clearTimeout(b.timeout);b.timeout=null}clearInterval(b.timer);b.timer=null}});this.addHandler(this.base.host,"mousedown",function(e){if(!c){if(b.timer!=null){clearInterval(b.timer)}b.timeout=setTimeout(function(){clearInterval(b.timer);b.timer=setInterval(function(f){b.ontimer(f)},b.delay)},150)}});this.addHandler(this.base.host,"mousemove",function(e){if(!c){if(e.which==0){if(b.timer!=null){clearInterval(b.timer);b.timer=null}}}})},stop:function(){clearInterval(this.timer);this.timer=null},ontimer:function(b){var b=new jQuery.Event("click");if(this.base!=null&&this.base.host!=null){this.base.host.trigger(b)}}});a.jqx.jqxWidget("jqxToggleButton","jqxButton",{});a.extend(a.jqx._jqxToggleButton.prototype,{defineInstance:function(){this.toggled=false},createInstance:function(c){var b=this;this.base.overrideTheme=true;this.isTouchDevice=a.jqx.mobile.isTouchDevice();this.addHandler(this.base.host,"click",function(d){b.toggle()});if(!this.isTouchDevice){this.addHandler(this.base.host,"mouseenter",function(d){if(!b.base.disabled){b.refresh()}});this.addHandler(this.base.host,"mouseleave",function(d){if(!b.base.disabled){b.refresh()}})}this.addHandler(this.base.host,"mousedown",function(d){if(!b.base.disabled){b.refresh()}});this.addHandler(a(document),"mouseup",function(d){if(!b.base.disabled){b.refresh()}})},_removeHandlers:function(){this.removeHandler(this.base.host,"click");this.removeHandler(this.base.host,"mouseenter");this.removeHandler(this.base.host,"mouseleave");this.removeHandler(this.base.host,"mousedown");this.removeHandler(a(document),"mouseup")},toggle:function(){this.toggled=!this.toggled;this.refresh()},refresh:function(){var g=this.base.toThemeProperty("jqx-fill-state-disabled");var b=this.base.toThemeProperty("jqx-fill-state-normal");var f=this.base.toThemeProperty("jqx-fill-state-hover");var d=this.base.toThemeProperty("jqx-fill-state-pressed");var e=this.base.toThemeProperty("jqx-fill-state-pressedhover");var c="";this.base.host[0].disabled=this.base.disabled;if(this.base.disabled){c=g}else{if(this.base.isMouseOver&&!this.isTouchDevice){if(this.base.isPressed||this.toggled){c=e}else{c=f}}else{if(this.base.isPressed||this.toggled){c=d}else{c=b}}}if(this.base.host.hasClass(g)&&g!=c){this.base.host.removeClass(g)}if(this.base.host.hasClass(b)&&b!=c){this.base.host.removeClass(b)}if(this.base.host.hasClass(f)&&f!=c){this.base.host.removeClass(f)}if(this.base.host.hasClass(d)&&d!=c){this.base.host.removeClass(d)}if(this.base.host.hasClass(e)&&e!=c){this.base.host.removeClass(e)}if(!this.base.host.hasClass(c)){this.base.host.addClass(c)}}})})(jQuery);