/*
Copyright (c) 2012 jqWidgets.
http://jqwidgets.com/license/
*/

(function(a){a.jqx.jqxWidget("jqxDropDownList","",{});a.extend(a.jqx._jqxDropDownList.prototype,{defineInstance:function(){this.disabled=false;this.width=null;this.height=null;this.items=new Array();this.selectedIndex=-1;this.source=null;this.scrollBarSize=15;this.arrowSize=19;this.enableHover=true;this.enableSelection=true;this.visualItems=new Array();this.groups=new Array();this.equalItemsWidth=true;this.itemHeight=-1;this.visibleItems=new Array();this.emptyGroupText="Group";if(this.showDelay==undefined){this.showDelay=350}if(this.hideDelay==undefined){this.hideDelay=400}this.animationType="default";this.dropDownWidth="auto";this.dropDownHeight="200px";this.autoDropDownHeight=false;this.enableBrowserBoundsDetection=false;this.events=["open","close","select","unselect","change"]},createInstance:function(h){this._setSize();var d=a("<div style='background-color: transparent; -webkit-appearance: none; outline: none; width:100%; height: 100%; padding: 0px; margin: 0px; border: 0px; position: relative;'><div id='dropdownlistWrapper' style='outline: none; background-color: transparent; border: none; float: left; width:100%; height: 100%; position: relative;'><div id='dropdownlistContent' style='outline: none; background-color: transparent; border: none; float: left; position: relative;'/><div id='dropdownlistArrow' style='background-color: transparent; border: none; float: right; position: relative;'/></div></div>");if(a.jqx._jqxListBox==null||a.jqx._jqxListBox==undefined){alert("jqxListBox is not loaded.")}try{var j="listBox"+this.element.id;var f=a(a.find("#"+j));if(f.length>0){f.remove()}var b=a("<div style='overflow: hidden; background-color: transparent; border: none; position: absolute;' id='listBox"+this.element.id+"'><div id='innerListBox"+this.element.id+"'></div></div>");b.appendTo(document.body);this.container=b;this.listBoxContainer=a(a.find("#innerListBox"+this.element.id));var c=this.width;if(this.dropDownWidth!="auto"){c=this.dropDownWidth}if(this.dropDownHeight==null){this.dropDownHeight=200}this.listBoxContainer.jqxListBox({width:c,height:this.dropDownHeight,autoHeight:this.autoDropDownHeight,scrollBarSize:this.scrollBarSize,source:this.source,theme:this.theme});this.container.width(parseInt(c)+25);this.container.height(parseInt(this.dropDownHeight)+25);this.listBoxContainer.css({position:"absolute",zIndex:9999999999999,top:0,left:0});this.listBox=a.data(this.listBoxContainer[0],"jqxListBox").instance;this.listBox.enableSelection=this.enableSelection;this.listBox.enableHover=this.enableHover;this.listBox.equalItemsWidth=this.equalItemsWidth;this.listBox.selectIndex(this.selectedIndex);this.listBox._arrange();var i=this;this.addHandler(this.listBoxContainer,"select",function(e){i._raiseEvent("2",{index:e.args.index,type:e.args.type})});this.addHandler(this.listBoxContainer,"unselect",function(e){i._raiseEvent("3",{index:e.args.index,type:e.args.type})});this.addHandler(this.listBoxContainer,"change",function(e){i._raiseEvent("4",{index:e.args.index,type:e.args.type})});if(this.animationType=="none"){this.container.css("display","none")}else{this.container.hide()}}catch(g){}this.touch=a.jqx.mobile.isTouchDevice();this.host.append(d);this.dropdownlistWrapper=this.host.find("#dropdownlistWrapper");this.dropdownlistArrow=this.host.find("#dropdownlistArrow");this.dropdownlistContent=this.host.find("#dropdownlistContent");this.dropdownlistContent.addClass(this.toThemeProperty("jqx-dropdownlist-content"));this.dropdownlistWrapper.addClass(this.toThemeProperty("jqx-disableselect"));this.addHandler(this.dropdownlistWrapper,"selectstart",function(){return false});var k=this;this.propertyChangeMap.disabled=function(e,m,l,n){if(k.disabled){k.host.addClass(k.toThemeProperty("jqx-dropdownlist-state-disabled"));k.dropdownlistContent.addClass(k.toThemeProperty("jqx-dropdownlist-content-disabled"))}else{k.host.removeClass(k.toThemeProperty("jqx-dropdownlist-state-disabled"));k.dropdownlistContent.removeClass(k.toThemeProperty("jqx-dropdownlist-content-disabled"))}};if(this.disabled){this.host.addClass(this.toThemeProperty("jqx-dropdownlist-state-disabled"));this.dropdownlistContent.addClass(this.toThemeProperty("jqx-dropdownlist-content-disabled"))}this.host.addClass(this.toThemeProperty("jqx-dropdownlist-state-normal"));this.host.addClass(this.toThemeProperty("jqx-rc-all"));this.dropdownlistArrow.addClass(this.toThemeProperty("icon-arrow-down"));this.render()},_setSize:function(){if(this.width!=null&&this.width.toString().indexOf("px")!=-1){this.host.width(this.width)}else{if(this.width!=undefined&&!isNaN(this.width)){this.host.width(this.width)}}if(this.height!=null&&this.height.toString().indexOf("px")!=-1){this.host.height(this.height)}else{if(this.height!=undefined&&!isNaN(this.height)){this.host.height(this.height)}}},isOpened:function(){var c=this;var b=a.data(document.body,"openedJQXListBox");if(b!=null&&b==c.listBoxContainer){return true}return false},render:function(){var b=this;var c=false;if(!this.touch){this.host.hover(function(){if(!b.disabled&&b.enableHover){c=true;b.host.addClass(b.toThemeProperty("jqx-dropdownlist-state-hover"));b.dropdownlistArrow.addClass(b.toThemeProperty("icon-arrow-down-hover"))}},function(){if(!b.disabled&&b.enableHover){b.host.removeClass(b.toThemeProperty("jqx-dropdownlist-state-hover"));b.dropdownlistArrow.removeClass(b.toThemeProperty("icon-arrow-down-hover"));c=false}})}this.addHandler(this.dropdownlistWrapper,"mousedown",function(e){if(!b.disabled){var d=b.container.css("display")=="block";if(d){b.hideListBox()}else{b.showListBox()}}});this.addHandler(a(document),"mousedown."+this.element.id,b.closeOpenedListBox,{me:this,listbox:this.listBox,id:this.element.id});this.addHandler(this.host,"keydown",function(e){var d=b.container.css("display")=="block";if(e.keyCode=="13"){b.hideListBox()}if(d&&!b.disabled){return b.listBox._handleKeyDown(e)}});this.addHandler(this.listBoxContainer,"select",function(d){if(!b.disabled){b.renderSelection();if(d.args.type=="mouse"){b.hideListBox()}}});if(this.listBox){if(this.listBox.content){this.addHandler(this.listBox.content,"click",function(d){if(!b.disabled){b.renderSelection("mouse");b.hideListBox()}})}}},removeHandlers:function(){var b=this;this.removeHandler(this.dropdownlistWrapper,"mousedown");this.removeHandler(this.host,"keydown");this.host.unbind("hover")},getItem:function(b){var c=this.listBox.getItem(b);return c},renderSelection:function(){if(this.listBox==null){return}var i=this.listBox.getItem(this.listBox.selectedIndex);if(i==null){return}this.selectedIndex=this.listBox.selectedIndex;var d=a('<span style="color: inherit; border: none; background-color: transparent;"></span>');d.appendTo(a(document.body));d.addClass(this.toThemeProperty("jqx-listitem-state-normal"));if(i.html){d.html(i.html)}else{if(i.label){d.html(i.label)}else{if(i.value){d.html(i.value)}}}var f=this.dropdownlistContent.css("font-size");var e=this.dropdownlistContent.css("font-family");var c=this.dropdownlistContent.css("padding-top");var j=this.dropdownlistContent.css("padding-bottom");d.css("font-size",f);d.css("font-family",e);d.css("padding-top",c);d.css("padding-bottom",j);var b=d.outerHeight();d.remove();d.removeClass();if(this.selectionRenderer){this.dropdownlistContent.html(this.selectionRenderer(d))}else{this.dropdownlistContent.html(d)}var h=this.host.height();if(this.height!=null&&this.height!=undefined){h=parseInt(this.height)}var g=(parseInt(h)-parseInt(b))/2;if(g>0){this.dropdownlistContent.css("margin-top",g+"px");this.dropdownlistContent.css("margin-bottom",g+"px")}},clearSelection:function(b){this.listBox.clearSelection()},unselectIndex:function(b,c){if(isNaN(b)){return}this.listBox.unselectIndex(b,c);this.renderSelection()},selectIndex:function(b,d,e,c){this.listBox.selectIndex(b,d,e,c);this.renderSelection()},insertAt:function(c,b){if(c==null){return}this.listBox.insertAt(c,b)},removeAt:function(b){this.listBox.removeAt(b)},ensureVisible:function(b){this.listBox.ensureVisible(b)},disableAt:function(b){this.listBox.disableAt(b)},enableAt:function(b){this.listBox.enableAt(b)},_findPos:function(c){while(c&&(c.type=="hidden"||c.nodeType!=1||a.expr.filters.hidden(c))){c=c.nextSibling}var b=a(c).offset();return[b.left,b.top]},testOffset:function(d,h,f){var g=d.outerWidth();var c=d.outerHeight();var e=document.documentElement.clientWidth+a(window).scrollLeft();var b=document.documentElement.clientHeight+a(window).scrollTop();h.left-=0;h.left-=0;h.top-=0;h.left-=Math.min(h.left,(h.left+g>e&&e>g)?Math.abs(h.left+g-e):0);h.top-=Math.min(h.top,(h.top+c>b&&b>c)?Math.abs(c+f):0);return h},showListBox:function(){var l=this;var b=this.listBoxContainer;var g=this.listBox;var i=a(window).scrollTop();var e=a(window).scrollLeft();var h=parseInt(this._findPos(this.host[0])[1])+parseInt(this.host.outerHeight())-1+"px";var d=parseInt(this.host.offset().left)+"px";var k=a.jqx.mobile.isSafariMobileBrowser();if(this.autoDropDownHeight){this.container.height(this.listBoxContainer.height()+25)}if(k!=null&&k){h=parseInt(h)-i+"px";d=parseInt(d)-e+"px"}b.stop();this.host.addClass(this.toThemeProperty("jqx-dropdownlist-state-selected"));this.dropdownlistArrow.addClass(this.toThemeProperty("icon-arrow-down-selected"));this.container.css("left",d);this.container.css("top",h);g._arrange();var c=true;if(this.enableBrowserBoundsDetection){var f=this.testOffset(b,{left:parseInt(this.container.css("left")),top:parseInt(h)},parseInt(this.host.outerHeight()));this.container.css("top",f.top)}if(this.animationType=="none"){this.container.css("display","block");a.data(document.body,"openedJQXListBoxParent",l);a.data(document.body,"openedJQXListBox",b)}else{this.container.css("display","block");var j=b.outerHeight();b.css("margin-top",-j);b.animate({"margin-top":0},this.showDelay,function(){a.data(document.body,"openedJQXListBoxParent",l);a.data(document.body,"openedJQXListBox",b)})}this._raiseEvent("0",g)},hideListBox:function(){var d=this.listBoxContainer;var e=this.listBox;var c=this.container;a.data(document.body,"openedJQXListBox",null);d.stop();if(this.animationType=="none"){this.container.css("display","none")}else{var b=d.outerHeight();d.css("margin-top",0);d.animate({"margin-top":-b},this.hideDelay,function(){c.css("display","none")})}this.host.removeClass(this.toThemeProperty("jqx-dropdownlist-state-selected"));this.dropdownlistArrow.removeClass(this.toThemeProperty("icon-arrow-down-selected"));this._raiseEvent("1",e)},closeOpenedListBox:function(e){var d=e.data.me;var b=a(e.target);var c=e.data.listbox;if(c==null){return true}if(a(e.target).ischildof(e.data.me.host)){return}var f=d;var g=false;a.each(b.parents(),function(){if(this.className!=undefined){if(this.className.indexOf("jqx-listbox")!=-1){g=true;return false}}});if(c!=null&&!g){d.hideListBox()}return true},loadFromSelect:function(b){this.listBox.loadFromSelect(b)},refresh:function(){this._arrange();this.renderSelection()},_arrange:function(){var f=parseInt(this.host.width());var b=parseInt(this.host.height());var e=this.arrowSize;var d=this.arrowSize;var g=3;var c=f-d-2*g;if(c>0){this.dropdownlistContent.width(c+"px")}this.dropdownlistContent.height(b);this.dropdownlistContent.css("left",0);this.dropdownlistContent.css("top",0);this.dropdownlistArrow.width(d);this.dropdownlistArrow.height(b)},destroy:function(){this.removeHandler(this.listBoxContainer,"select");this.removeHandler(this.listBoxContainer,"unselect");this.removeHandler(this.listBoxContainer,"change");this.removeHandler(this.dropdownlistWrapper,"selectstart");this.removeHandler(this.dropdownlistWrapper,"mousedown");this.removeHandler(this.host,"keydown");this.removeHandler(this.listBoxContainer,"select");this.removeHandler(this.listBox.content,"click");this.listBoxContainer.jqxListBox("destroy");this.listBoxContainer.remove();this.host.removeClass();this.removeHandler(a(document),"mousedown."+this.element.id,self.closeOpenedListBox);this.host.remove()},_raiseEvent:function(f,c){if(c==undefined){c={owner:null}}var d=this.events[f];args=c;args.owner=this;var e=new jQuery.Event(d);e.owner=this;if(f==2||f==3||f==4){e.args=c}var b=this.host.trigger(e);return b},propertyChangedHandler:function(b,c,f,e){if(this.isInitialized==undefined||this.isInitialized==false){return}if(c=="source"){this.listBoxContainer.jqxListBox({source:this.source});this.renderSelection()}if(c=="theme"&&e!=null){this.listBoxContainer.jqxListBox({theme:e})}if(c=="autoDropDownHeight"){this.listBoxContainer.jqxListBox({autoHeight:this.autoDropDownHeight});if(this.autoDropDownHeight){this.container.height(this.listBoxContainer.height()+25)}else{this.listBoxContainer.jqxListBox({height:this.dropDownHeight});this.container.height(parseInt(this.dropDownHeight)+25)}}if(c=="dropDownHeight"){if(!this.autoDropDownHeight){this.listBoxContainer.jqxListBox({height:this.dropDownHeight});this.container.height(parseInt(this.dropDownHeight)+25)}}if(c=="dropDownWidth"||c=="scrollBarSize"){var d=this.width;if(this.dropDownWidth!="auto"){d=this.dropDownWidth}this.listBoxContainer.jqxListBox({width:d,scrollBarSize:this.scrollBarSize});this.container.width(parseInt(d)+25)}if(c=="width"||c=="height"){this._setSize()}if(c=="selectedIndex"){this.listBox.selectIndex(e);this.renderSelection()}}})})(jQuery);