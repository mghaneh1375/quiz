if("undefined"==typeofjQuery)thrownewError("Bootstrap'sJavaScriptrequiresjQuery");
+function(a){
"usestrict";
varb=a.fn.jquery.split("")[0].split(".");
if(b[0]<2&&b[1]<9||1==b[0]&&9==b[1]&&b[2]<1||b[0]>3)thrownewError("Bootstrap'sJavaScriptrequiresjQueryversion1.9.1orhigher,butlowerthanversion4")}(jQuery),+function(a){
"usestrict";
functionb(){
vara=document.createElement("bootstrap"),b={
WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"oTransitionEndotransitionend",transition:"transitionend"};
for(varcinb)if(void0!==a.style[c])return{
end:b[c]};
return!1}a.fn.emulateTransitionEnd=function(b){
varc=!1,d=this;
a(this).one("bsTransitionEnd",function(){
c=!0});
vare=function(){
c||a(d).trigger(a.support.transition.end)};
returnsetTimeout(e,b),this},a(function(){
a.support.transition=b(),a.support.transition&&(a.event.special.bsTransitionEnd={
bindType:a.support.transition.end,delegateType:a.support.transition.end,handle:function(b){
if(a(b.target).is(this))returnb.handleObj.handler.apply(this,arguments)}})})}(jQuery),+function(a){
"usestrict";
functionb(b){
returnthis.each(function(){
varc=a(this),e=c.data("bs.alert");
e||c.data("bs.alert",e=newd(this)),"string"==typeofb&&e[b].call(c)})}varc='[data-dismiss="alert"]',d=function(b){
a(b).on("click",c,this.close)};
d.VERSION="3.3.7",d.TRANSITION_DURATION=150,d.prototype.close=function(b){
functionc(){
g.detach().trigger("closed.bs.alert").remove()}vare=a(this),f=e.attr("data-target");
f||(f=e.attr("href"),f=f&&f.replace(/.*(?=#[^\s]*$)/,""));
varg=a("#"===f?[]:f);
b&&b.preventDefault(),g.length||(g=e.closest(".alert")),g.trigger(b=a.Event("close.bs.alert")),b.isDefaultPrevented()||(g.removeClass("in"),a.support.transition&&g.hasClass("fade")?g.one("bsTransitionEnd",c).emulateTransitionEnd(d.TRANSITION_DURATION):c())};
vare=a.fn.alert;
a.fn.alert=b,a.fn.alert.Constructor=d,a.fn.alert.noConflict=function(){
returna.fn.alert=e,this},a(document).on("click.bs.alert.data-api",c,d.prototype.close)}(jQuery),+function(a){
"usestrict";
functionb(b){
returnthis.each(function(){
vard=a(this),e=d.data("bs.button"),f="object"==typeofb&&b;
e||d.data("bs.button",e=newc(this,f)),"toggle"==b?e.toggle():b&&e.setState(b)})}varc=function(b,d){
this.$element=a(b),this.options=a.extend({
},c.DEFAULTS,d),this.isLoading=!1};
c.VERSION="3.3.7",c.DEFAULTS={
loadingText:"loading..."},c.prototype.setState=function(b){
varc="disabled",d=this.$element,e=d.is("input")?"val":"html",f=d.data();
b+="Text",null==f.resetText&&d.data("resetText",d[e]()),setTimeout(a.proxy(function(){
d[e](null==f[b]?this.options[b]:f[b]),"loadingText"==b?(this.isLoading=!0,d.addClass(c).attr(c,c).prop(c,!0)):this.isLoading&&(this.isLoading=!1,d.removeClass(c).removeAttr(c).prop(c,!1))},this),0)},c.prototype.toggle=function(){
vara=!0,b=this.$element.closest('[data-toggle="buttons"]');
if(b.length){
varc=this.$element.find("input");
"radio"==c.prop("type")?(c.prop("checked")&&(a=!1),b.find(".active").removeClass("active"),this.$element.addClass("active")):"checkbox"==c.prop("type")&&(c.prop("checked")!==this.$element.hasClass("active")&&(a=!1),this.$element.toggleClass("active")),c.prop("checked",this.$element.hasClass("active")),a&&c.trigger("change")}elsethis.$element.attr("aria-pressed",!this.$element.hasClass("active")),this.$element.toggleClass("active")};
vard=a.fn.button;
a.fn.button=b,a.fn.button.Constructor=c,a.fn.button.noConflict=function(){
returna.fn.button=d,this},a(document).on("click.bs.button.data-api",'[data-toggle^="button"]',function(c){
vard=a(c.target).closest(".btn");
b.call(d,"toggle"),a(c.target).is('input[type="radio"],input[type="checkbox"]')||(c.preventDefault(),d.is("input,button")?d.trigger("focus"):d.find("input:visible,button:visible").first().trigger("focus"))}).on("focus.bs.button.data-apiblur.bs.button.data-api",'[data-toggle^="button"]',function(b){
a(b.target).closest(".btn").toggleClass("focus",/^focus(in)?$/.test(b.type))})}(jQuery),+function(a){
"usestrict";
functionb(b){
returnthis.each(function(){
vard=a(this),e=d.data("bs.carousel"),f=a.extend({
},c.DEFAULTS,d.data(),"object"==typeofb&&b),g="string"==typeofb?b:f.slide;
e||d.data("bs.carousel",e=newc(this,f)),"number"==typeofb?e.to(b):g?e[g]():f.interval&&e.pause().cycle()})}varc=function(b,c){
this.$element=a(b),this.$indicators=this.$element.find(".carousel-indicators"),this.options=c,this.paused=null,this.sliding=null,this.interval=null,this.$active=null,this.$items=null,this.options.keyboard&&this.$element.on("keydown.bs.carousel",a.proxy(this.keydown,this)),"hover"==this.options.pause&&!("ontouchstart"indocument.documentElement)&&this.$element.on("mouseenter.bs.carousel",a.proxy(this.pause,this)).on("mouseleave.bs.carousel",a.proxy(this.cycle,this))};
c.VERSION="3.3.7",c.TRANSITION_DURATION=600,c.DEFAULTS={
interval:5e3,pause:"hover",wrap:!0,keyboard:!0},c.prototype.keydown=function(a){
if(!/input|textarea/i.test(a.target.tagName)){
switch(a.which){
case37:this.prev();
break;
case39:this.next();
break;
default:return}a.preventDefault()}},c.prototype.cycle=function(b){
returnb||(this.paused=!1),this.interval&&clearInterval(this.interval),this.options.interval&&!this.paused&&(this.interval=setInterval(a.proxy(this.next,this),this.options.interval)),this},c.prototype.getItemIndex=function(a){
returnthis.$items=a.parent().children(".item"),this.$items.index(a||this.$active)},c.prototype.getItemForDirection=function(a,b){
varc=this.getItemIndex(b),d="prev"==a&&0===c||"next"==a&&c==this.$items.length-1;
if(d&&!this.options.wrap)returnb;
vare="prev"==a?-1:1,f=(c+e)%this.$items.length;
returnthis.$items.eq(f)},c.prototype.to=function(a){
varb=this,c=this.getItemIndex(this.$active=this.$element.find(".item.active"));
if(!(a>this.$items.length-1||a<0))returnthis.sliding?this.$element.one("slid.bs.carousel",function(){
b.to(a)}):c==a?this.pause().cycle():this.slide(a>c?"next":"prev",this.$items.eq(a))},c.prototype.pause=function(b){
returnb||(this.paused=!0),this.$element.find(".next,.prev").length&&a.support.transition&&(this.$element.trigger(a.support.transition.end),this.cycle(!0)),this.interval=clearInterval(this.interval),this},c.prototype.next=function(){
if(!this.sliding)returnthis.slide("next")},c.prototype.prev=function(){
if(!this.sliding)returnthis.slide("prev")},c.prototype.slide=function(b,d){
vare=this.$element.find(".item.active"),f=d||this.getItemForDirection(b,e),g=this.interval,h="next"==b?"left":"right",i=this;
if(f.hasClass("active"))returnthis.sliding=!1;
varj=f[0],k=a.Event("slide.bs.carousel",{
relatedTarget:j,direction:h});
if(this.$element.trigger(k),!k.isDefaultPrevented()){
if(this.sliding=!0,g&&this.pause(),this.$indicators.length){
this.$indicators.find(".active").removeClass("active");
varl=a(this.$indicators.children()[this.getItemIndex(f)]);
l&&l.addClass("active")}varm=a.Event("slid.bs.carousel",{
relatedTarget:j,direction:h});
returna.support.transition&&this.$element.hasClass("slide")?(f.addClass(b),f[0].offsetWidth,e.addClass(h),f.addClass(h),e.one("bsTransitionEnd",function(){
f.removeClass([b,h].join("")).addClass("active"),e.removeClass(["active",h].join("")),i.sliding=!1,setTimeout(function(){
i.$element.trigger(m)},0)}).emulateTransitionEnd(c.TRANSITION_DURATION)):(e.removeClass("active"),f.addClass("active"),this.sliding=!1,this.$element.trigger(m)),g&&this.cycle(),this}};
vard=a.fn.carousel;
a.fn.carousel=b,a.fn.carousel.Constructor=c,a.fn.carousel.noConflict=function(){
returna.fn.carousel=d,this};
vare=function(c){
vard,e=a(this),f=a(e.attr("data-target")||(d=e.attr("href"))&&d.replace(/.*(?=#[^\s]+$)/,""));
if(f.hasClass("carousel")){
varg=a.extend({
},f.data(),e.data()),h=e.attr("data-slide-to");
h&&(g.interval=!1),b.call(f,g),h&&f.data("bs.carousel").to(h),c.preventDefault()}};
a(document).on("click.bs.carousel.data-api","[data-slide]",e).on("click.bs.carousel.data-api","[data-slide-to]",e),a(window).on("load",function(){
a('[data-ride="carousel"]').each(function(){
varc=a(this);
b.call(c,c.data())})})}(jQuery),+function(a){
"usestrict";
functionb(b){
varc,d=b.attr("data-target")||(c=b.attr("href"))&&c.replace(/.*(?=#[^\s]+$)/,"");
returna(d)}functionc(b){
returnthis.each(function(){
varc=a(this),e=c.data("bs.collapse"),f=a.extend({
},d.DEFAULTS,c.data(),"object"==typeofb&&b);
!e&&f.toggle&&/show|hide/.test(b)&&(f.toggle=!1),e||c.data("bs.collapse",e=newd(this,f)),"string"==typeofb&&e[b]()})}vard=function(b,c){
this.$element=a(b),this.options=a.extend({
},d.DEFAULTS,c),this.$trigger=a('[data-toggle="collapse"][href="#'+b.id+'"],[data-toggle="collapse"][data-target="#'+b.id+'"]'),this.transitioning=null,this.options.parent?this.$parent=this.getParent():this.addAriaAndCollapsedClass(this.$element,this.$trigger),this.options.toggle&&this.toggle()};
d.VERSION="3.3.7",d.TRANSITION_DURATION=350,d.DEFAULTS={
toggle:!0},d.prototype.dimension=function(){
vara=this.$element.hasClass("width");
returna?"width":"height"},d.prototype.show=function(){
if(!this.transitioning&&!this.$element.hasClass("in")){
varb,e=this.$parent&&this.$parent.children(".panel").children(".in,.collapsing");
if(!(e&&e.length&&(b=e.data("bs.collapse"),b&&b.transitioning))){
varf=a.Event("show.bs.collapse");
if(this.$element.trigger(f),!f.isDefaultPrevented()){
e&&e.length&&(c.call(e,"hide"),b||e.data("bs.collapse",null));
varg=this.dimension();
this.$element.removeClass("collapse").addClass("collapsing")[g](0).attr("aria-expanded",!0),this.$trigger.removeClass("collapsed").attr("aria-expanded",!0),this.transitioning=1;
varh=function(){
this.$element.removeClass("collapsing").addClass("collapsein")[g](""),this.transitioning=0,this.$element.trigger("shown.bs.collapse")};
if(!a.support.transition)returnh.call(this);
vari=a.camelCase(["scroll",g].join("-"));
this.$element.one("bsTransitionEnd",a.proxy(h,this)).emulateTransitionEnd(d.TRANSITION_DURATION)[g](this.$element[0][i])}}}},d.prototype.hide=function(){
if(!this.transitioning&&this.$element.hasClass("in")){
varb=a.Event("hide.bs.collapse");
if(this.$element.trigger(b),!b.isDefaultPrevented()){
varc=this.dimension();
this.$element[c](this.$element[c]())[0].offsetHeight,this.$element.addClass("collapsing").removeClass("collapsein").attr("aria-expanded",!1),this.$trigger.addClass("collapsed").attr("aria-expanded",!1),this.transitioning=1;
vare=function(){
this.transitioning=0,this.$element.removeClass("collapsing").addClass("collapse").trigger("hidden.bs.collapse")};
returna.support.transition?voidthis.$element[c](0).one("bsTransitionEnd",a.proxy(e,this)).emulateTransitionEnd(d.TRANSITION_DURATION):e.call(this)}}},d.prototype.toggle=function(){
this[this.$element.hasClass("in")?"hide":"show"]()},d.prototype.getParent=function(){
returna(this.options.parent).find('[data-toggle="collapse"][data-parent="'+this.options.parent+'"]').each(a.proxy(function(c,d){
vare=a(d);
this.addAriaAndCollapsedClass(b(e),e)},this)).end()},d.prototype.addAriaAndCollapsedClass=function(a,b){
varc=a.hasClass("in");
a.attr("aria-expanded",c),b.toggleClass("collapsed",!c).attr("aria-expanded",c)};
vare=a.fn.collapse;
a.fn.collapse=c,a.fn.collapse.Constructor=d,a.fn.collapse.noConflict=function(){
returna.fn.collapse=e,this},a(document).on("click.bs.collapse.data-api",'[data-toggle="collapse"]',function(d){
vare=a(this);
e.attr("data-target")||d.preventDefault();
varf=b(e),g=f.data("bs.collapse"),h=g?"toggle":e.data();
c.call(f,h)})}(jQuery),+function(a){
"usestrict";
functionb(b){
varc=b.attr("data-target");
c||(c=b.attr("href"),c=c&&/#[A-Za-z]/.test(c)&&c.replace(/.*(?=#[^\s]*$)/,""));
vard=c&&a(c);
returnd&&d.length?d:b.parent()}functionc(c){
c&&3===c.which||(a(e).remove(),a(f).each(function(){
vard=a(this),e=b(d),f={
relatedTarget:this};
e.hasClass("open")&&(c&&"click"==c.type&&/input|textarea/i.test(c.target.tagName)&&a.contains(e[0],c.target)||(e.trigger(c=a.Event("hide.bs.dropdown",f)),c.isDefaultPrevented()||(d.attr("aria-expanded","false"),e.removeClass("open").trigger(a.Event("hidden.bs.dropdown",f)))))}))}functiond(b){
returnthis.each(function(){
varc=a(this),d=c.data("bs.dropdown");
d||c.data("bs.dropdown",d=newg(this)),"string"==typeofb&&d[b].call(c)})}vare=".dropdown-backdrop",f='[data-toggle="dropdown"]',g=function(b){
a(b).on("click.bs.dropdown",this.toggle)};
g.VERSION="3.3.7",g.prototype.toggle=function(d){
vare=a(this);
if(!e.is(".disabled,:disabled")){
varf=b(e),g=f.hasClass("open");
if(c(),!g){
"ontouchstart"indocument.documentElement&&!f.closest(".navbar-nav").length&&a(document.createElement("div")).addClass("dropdown-backdrop").insertAfter(a(this)).on("click",c);
varh={
relatedTarget:this};
if(f.trigger(d=a.Event("show.bs.dropdown",h)),d.isDefaultPrevented())return;
e.trigger("focus").attr("aria-expanded","true"),f.toggleClass("open").trigger(a.Event("shown.bs.dropdown",h))}return!1}},g.prototype.keydown=function(c){
if(/(38|40|27|32)/.test(c.which)&&!/input|textarea/i.test(c.target.tagName)){
vard=a(this);
if(c.preventDefault(),c.stopPropagation(),!d.is(".disabled,:disabled")){
vare=b(d),g=e.hasClass("open");
if(!g&&27!=c.which||g&&27==c.which)return27==c.which&&e.find(f).trigger("focus"),d.trigger("click");
varh="li:not(.disabled):visiblea",i=e.find(".dropdown-menu"+h);
if(i.length){
varj=i.index(c.target);
38==c.which&&j>0&&j--,40==c.which&&j<i.length-1&&j++,~j||(j=0),i.eq(j).trigger("focus")}}}};
varh=a.fn.dropdown;
a.fn.dropdown=d,a.fn.dropdown.Constructor=g,a.fn.dropdown.noConflict=function(){
returna.fn.dropdown=h,this},a(document).on("click.bs.dropdown.data-api",c).on("click.bs.dropdown.data-api",".dropdownform",function(a){
a.stopPropagation()}).on("click.bs.dropdown.data-api",f,g.prototype.toggle).on("keydown.bs.dropdown.data-api",f,g.prototype.keydown).on("keydown.bs.dropdown.data-api",".dropdown-menu",g.prototype.keydown)}(jQuery),+function(a){
"usestrict";
functionb(b,d){
returnthis.each(function(){
vare=a(this),f=e.data("bs.modal"),g=a.extend({
},c.DEFAULTS,e.data(),"object"==typeofb&&b);
f||e.data("bs.modal",f=newc(this,g)),"string"==typeofb?f[b](d):g.show&&f.show(d)})}varc=function(b,c){
this.options=c,this.$body=a(document.body),this.$element=a(b),this.$dialog=this.$element.find(".modal-dialog"),this.$backdrop=null,this.isShown=null,this.originalBodyPad=null,this.scrollbarWidth=0,this.ignoreBackdropClick=!1,this.options.remote&&this.$element.find(".modal-content").load(this.options.remote,a.proxy(function(){
this.$element.trigger("loaded.bs.modal")},this))};
c.VERSION="3.3.7",c.TRANSITION_DURATION=300,c.BACKDROP_TRANSITION_DURATION=150,c.DEFAULTS={
backdrop:!0,keyboard:!0,show:!0},c.prototype.toggle=function(a){
returnthis.isShown?this.hide():this.show(a)},c.prototype.show=function(b){
vard=this,e=a.Event("show.bs.modal",{
relatedTarget:b});
this.$element.trigger(e),this.isShown||e.isDefaultPrevented()||(this.isShown=!0,this.checkScrollbar(),this.setScrollbar(),this.$body.addClass("modal-open"),this.escape(),this.resize(),this.$element.on("click.dismiss.bs.modal",'[data-dismiss="modal"]',a.proxy(this.hide,this)),this.$dialog.on("mousedown.dismiss.bs.modal",function(){
d.$element.one("mouseup.dismiss.bs.modal",function(b){
a(b.target).is(d.$element)&&(d.ignoreBackdropClick=!0)})}),this.backdrop(function(){
vare=a.support.transition&&d.$element.hasClass("fade");
d.$element.parent().length||d.$element.appendTo(d.$body),d.$element.show().scrollTop(0),d.adjustDialog(),e&&d.$element[0].offsetWidth,d.$element.addClass("in"),d.enforceFocus();
varf=a.Event("shown.bs.modal",{
relatedTarget:b});
e?d.$dialog.one("bsTransitionEnd",function(){
d.$element.trigger("focus").trigger(f)}).emulateTransitionEnd(c.TRANSITION_DURATION):d.$element.trigger("focus").trigger(f)}))},c.prototype.hide=function(b){
b&&b.preventDefault(),b=a.Event("hide.bs.modal"),this.$element.trigger(b),this.isShown&&!b.isDefaultPrevented()&&(this.isShown=!1,this.escape(),this.resize(),a(document).off("focusin.bs.modal"),this.$element.removeClass("in").off("click.dismiss.bs.modal").off("mouseup.dismiss.bs.modal"),this.$dialog.off("mousedown.dismiss.bs.modal"),a.support.transition&&this.$element.hasClass("fade")?this.$element.one("bsTransitionEnd",a.proxy(this.hideModal,this)).emulateTransitionEnd(c.TRANSITION_DURATION):this.hideModal())},c.prototype.enforceFocus=function(){
a(document).off("focusin.bs.modal").on("focusin.bs.modal",a.proxy(function(a){
document===a.target||this.$element[0]===a.target||this.$element.has(a.target).length||this.$element.trigger("focus")},this))},c.prototype.escape=function(){
this.isShown&&this.options.keyboard?this.$element.on("keydown.dismiss.bs.modal",a.proxy(function(a){
27==a.which&&this.hide()},this)):this.isShown||this.$element.off("keydown.dismiss.bs.modal")},c.prototype.resize=function(){
this.isShown?a(window).on("resize.bs.modal",a.proxy(this.handleUpdate,this)):a(window).off("resize.bs.modal")},c.prototype.hideModal=function(){
vara=this;
this.$element.hide(),this.backdrop(function(){
a.$body.removeClass("modal-open"),a.resetAdjustments(),a.resetScrollbar(),a.$element.trigger("hidden.bs.modal")})},c.prototype.removeBackdrop=function(){
this.$backdrop&&this.$backdrop.remove(),this.$backdrop=null},c.prototype.backdrop=function(b){
vard=this,e=this.$element.hasClass("fade")?"fade":"";
if(this.isShown&&this.options.backdrop){
varf=a.support.transition&&e;
if(this.$backdrop=a(document.createElement("div")).addClass("modal-backdrop"+e).appendTo(this.$body),this.$element.on("click.dismiss.bs.modal",a.proxy(function(a){
returnthis.ignoreBackdropClick?void(this.ignoreBackdropClick=!1):void(a.target===a.currentTarget&&("static"==this.options.backdrop?this.$element[0].focus():this.hide()))},this)),f&&this.$backdrop[0].offsetWidth,this.$backdrop.addClass("in"),!b)return;
f?this.$backdrop.one("bsTransitionEnd",b).emulateTransitionEnd(c.BACKDROP_TRANSITION_DURATION):b()}elseif(!this.isShown&&this.$backdrop){
this.$backdrop.removeClass("in");
varg=function(){
d.removeBackdrop(),b&&b()};
a.support.transition&&this.$element.hasClass("fade")?this.$backdrop.one("bsTransitionEnd",g).emulateTransitionEnd(c.BACKDROP_TRANSITION_DURATION):g()}elseb&&b()},c.prototype.handleUpdate=function(){
this.adjustDialog()},c.prototype.adjustDialog=function(){
vara=this.$element[0].scrollHeight>document.documentElement.clientHeight;
this.$element.css({
paddingLeft:!this.bodyIsOverflowing&&a?this.scrollbarWidth:"",paddingRight:this.bodyIsOverflowing&&!a?this.scrollbarWidth:""})},c.prototype.resetAdjustments=function(){
this.$element.css({
paddingLeft:"",paddingRight:""})},c.prototype.checkScrollbar=function(){
vara=window.innerWidth;
if(!a){
varb=document.documentElement.getBoundingClientRect();
a=b.right-Math.abs(b.left)}this.bodyIsOverflowing=document.body.clientWidth<a,this.scrollbarWidth=this.measureScrollbar()},c.prototype.setScrollbar=function(){
vara=parseInt(this.$body.css("padding-right")||0,10);
this.originalBodyPad=document.body.style.paddingRight||"",this.bodyIsOverflowing&&this.$body.css("padding-right",a+this.scrollbarWidth)},c.prototype.resetScrollbar=function(){
this.$body.css("padding-right",this.originalBodyPad)},c.prototype.measureScrollbar=function(){
vara=document.createElement("div");
a.className="modal-scrollbar-measure",this.$body.append(a);
varb=a.offsetWidth-a.clientWidth;
returnthis.$body[0].removeChild(a),b};
vard=a.fn.modal;
a.fn.modal=b,a.fn.modal.Constructor=c,a.fn.modal.noConflict=function(){
returna.fn.modal=d,this},a(document).on("click.bs.modal.data-api",'[data-toggle="modal"]',function(c){
vard=a(this),e=d.attr("href"),f=a(d.attr("data-target")||e&&e.replace(/.*(?=#[^\s]+$)/,"")),g=f.data("bs.modal")?"toggle":a.extend({
remote:!/#/.test(e)&&e},f.data(),d.data());
d.is("a")&&c.preventDefault(),f.one("show.bs.modal",function(a){
a.isDefaultPrevented()||f.one("hidden.bs.modal",function(){
d.is(":visible")&&d.trigger("focus")})}),b.call(f,g,this)})}(jQuery),+function(a){
"usestrict";
functionb(b){
returnthis.each(function(){
vard=a(this),e=d.data("bs.tooltip"),f="object"==typeofb&&b;
!e&&/destroy|hide/.test(b)||(e||d.data("bs.tooltip",e=newc(this,f)),"string"==typeofb&&e[b]())})}varc=function(a,b){
this.type=null,this.options=null,this.enabled=null,this.timeout=null,this.hoverState=null,this.$element=null,this.inState=null,this.init("tooltip",a,b)};
c.VERSION="3.3.7",c.TRANSITION_DURATION=150,c.DEFAULTS={
animation:!0,placement:"top",selector:!1,template:'<divclass="tooltip"role="tooltip"><divclass="tooltip-arrow"></div><divclass="tooltip-inner"></div></div>',trigger:"hoverfocus",title:"",delay:0,html:!1,container:!1,viewport:{
selector:"body",padding:0}},c.prototype.init=function(b,c,d){
if(this.enabled=!0,this.type=b,this.$element=a(c),this.options=this.getOptions(d),this.$viewport=this.options.viewport&&a(a.isFunction(this.options.viewport)?this.options.viewport.call(this,this.$element):this.options.viewport.selector||this.options.viewport),this.inState={
click:!1,hover:!1,focus:!1},this.$element[0]instanceofdocument.constructor&&!this.options.selector)thrownewError("`selector`optionmustbespecifiedwheninitializing"+this.type+"onthewindow.documentobject!");
for(vare=this.options.trigger.split(""),f=e.length;
f--;
){
varg=e[f];
if("click"==g)this.$element.on("click."+this.type,this.options.selector,a.proxy(this.toggle,this));
elseif("manual"!=g){
varh="hover"==g?"mouseenter":"focusin",i="hover"==g?"mouseleave":"focusout";
this.$element.on(h+"."+this.type,this.options.selector,a.proxy(this.enter,this)),this.$element.on(i+"."+this.type,this.options.selector,a.proxy(this.leave,this))}}this.options.selector?this._options=a.extend({
},this.options,{
trigger:"manual",selector:""}):this.fixTitle()},c.prototype.getDefaults=function(){
returnc.DEFAULTS},c.prototype.getOptions=function(b){
returnb=a.extend({
},this.getDefaults(),this.$element.data(),b),b.delay&&"number"==typeofb.delay&&(b.delay={
show:b.delay,hide:b.delay}),b},c.prototype.getDelegateOptions=function(){
varb={
},c=this.getDefaults();
returnthis._options&&a.each(this._options,function(a,d){
c[a]!=d&&(b[a]=d)}),b},c.prototype.enter=function(b){
varc=binstanceofthis.constructor?b:a(b.currentTarget).data("bs."+this.type);
returnc||(c=newthis.constructor(b.currentTarget,this.getDelegateOptions()),a(b.currentTarget).data("bs."+this.type,c)),binstanceofa.Event&&(c.inState["focusin"==b.type?"focus":"hover"]=!0),c.tip().hasClass("in")||"in"==c.hoverState?void(c.hoverState="in"):(clearTimeout(c.timeout),c.hoverState="in",c.options.delay&&c.options.delay.show?void(c.timeout=setTimeout(function(){
"in"==c.hoverState&&c.show()},c.options.delay.show)):c.show())},c.prototype.isInStateTrue=function(){
for(varainthis.inState)if(this.inState[a])return!0;
return!1},c.prototype.leave=function(b){
varc=binstanceofthis.constructor?b:a(b.currentTarget).data("bs."+this.type);
if(c||(c=newthis.constructor(b.currentTarget,this.getDelegateOptions()),a(b.currentTarget).data("bs."+this.type,c)),binstanceofa.Event&&(c.inState["focusout"==b.type?"focus":"hover"]=!1),!c.isInStateTrue())returnclearTimeout(c.timeout),c.hoverState="out",c.options.delay&&c.options.delay.hide?void(c.timeout=setTimeout(function(){
"out"==c.hoverState&&c.hide()},c.options.delay.hide)):c.hide()},c.prototype.show=function(){
varb=a.Event("show.bs."+this.type);
if(this.hasContent()&&this.enabled){
this.$element.trigger(b);
vard=a.contains(this.$element[0].ownerDocument.documentElement,this.$element[0]);
if(b.isDefaultPrevented()||!d)return;
vare=this,f=this.tip(),g=this.getUID(this.type);
this.setContent(),f.attr("id",g),this.$element.attr("aria-describedby",g),this.options.animation&&f.addClass("fade");
varh="function"==typeofthis.options.placement?this.options.placement.call(this,f[0],this.$element[0]):this.options.placement,i=/\s?auto?\s?/i,j=i.test(h);
j&&(h=h.replace(i,"")||"top"),f.detach().css({
top:0,left:0,display:"block"}).addClass(h).data("bs."+this.type,this),this.options.container?f.appendTo(this.options.container):f.insertAfter(this.$element),this.$element.trigger("inserted.bs."+this.type);
vark=this.getPosition(),l=f[0].offsetWidth,m=f[0].offsetHeight;
if(j){
varn=h,o=this.getPosition(this.$viewport);
h="bottom"==h&&k.bottom+m>o.bottom?"top":"top"==h&&k.top-m<o.top?"bottom":"right"==h&&k.right+l>o.width?"left":"left"==h&&k.left-l<o.left?"right":h,f.removeClass(n).addClass(h)}varp=this.getCalculatedOffset(h,k,l,m);
this.applyPlacement(p,h);
varq=function(){
vara=e.hoverState;
e.$element.trigger("shown.bs."+e.type),e.hoverState=null,"out"==a&&e.leave(e)};
a.support.transition&&this.$tip.hasClass("fade")?f.one("bsTransitionEnd",q).emulateTransitionEnd(c.TRANSITION_DURATION):q()}},c.prototype.applyPlacement=function(b,c){
vard=this.tip(),e=d[0].offsetWidth,f=d[0].offsetHeight,g=parseInt(d.css("margin-top"),10),h=parseInt(d.css("margin-left"),10);
isNaN(g)&&(g=0),isNaN(h)&&(h=0),b.top+=g,b.left+=h,a.offset.setOffset(d[0],a.extend({
using:function(a){
d.css({
top:Math.round(a.top),left:Math.round(a.left)})}},b),0),d.addClass("in");
vari=d[0].offsetWidth,j=d[0].offsetHeight;
"top"==c&&j!=f&&(b.top=b.top+f-j);
vark=this.getViewportAdjustedDelta(c,b,i,j);
k.left?b.left+=k.left:b.top+=k.top;
varl=/top|bottom/.test(c),m=l?2*k.left-e+i:2*k.top-f+j,n=l?"offsetWidth":"offsetHeight";
d.offset(b),this.replaceArrow(m,d[0][n],l)},c.prototype.replaceArrow=function(a,b,c){
this.arrow().css(c?"left":"top",50*(1-a/b)+"%").css(c?"top":"left","")},c.prototype.setContent=function(){
vara=this.tip(),b=this.getTitle();
a.find(".tooltip-inner")[this.options.html?"html":"text"](b),a.removeClass("fadeintopbottomleftright")},c.prototype.hide=function(b){
functiond(){
"in"!=e.hoverState&&f.detach(),e.$element&&e.$element.removeAttr("aria-describedby").trigger("hidden.bs."+e.type),b&&b()}vare=this,f=a(this.$tip),g=a.Event("hide.bs."+this.type);
if(this.$element.trigger(g),!g.isDefaultPrevented())returnf.removeClass("in"),a.support.transition&&f.hasClass("fade")?f.one("bsTransitionEnd",d).emulateTransitionEnd(c.TRANSITION_DURATION):d(),this.hoverState=null,this},c.prototype.fixTitle=function(){
vara=this.$element;
(a.attr("title")||"string"!=typeofa.attr("data-original-title"))&&a.attr("data-original-title",a.attr("title")||"").attr("title","")},c.prototype.hasContent=function(){
returnthis.getTitle()},c.prototype.getPosition=function(b){
b=b||this.$element;
varc=b[0],d="BODY"==c.tagName,e=c.getBoundingClientRect();
null==e.width&&(e=a.extend({
},e,{
width:e.right-e.left,height:e.bottom-e.top}));
varf=window.SVGElement&&cinstanceofwindow.SVGElement,g=d?{
top:0,left:0}:f?null:b.offset(),h={
scroll:d?document.documentElement.scrollTop||document.body.scrollTop:b.scrollTop()},i=d?{
width:a(window).width(),height:a(window).height()}:null;
returna.extend({
},e,h,i,g)},c.prototype.getCalculatedOffset=function(a,b,c,d){
return"bottom"==a?{
top:b.top+b.height,left:b.left+b.width/2-c/2}:"top"==a?{
top:b.top-d,left:b.left+b.width/2-c/2}:"left"==a?{
top:b.top+b.height/2-d/2,left:b.left-c}:{
top:b.top+b.height/2-d/2,left:b.left+b.width}},c.prototype.getViewportAdjustedDelta=function(a,b,c,d){
vare={
top:0,left:0};
if(!this.$viewport)returne;
varf=this.options.viewport&&this.options.viewport.padding||0,g=this.getPosition(this.$viewport);
if(/right|left/.test(a)){
varh=b.top-f-g.scroll,i=b.top+f-g.scroll+d;
h<g.top?e.top=g.top-h:i>g.top+g.height&&(e.top=g.top+g.height-i)}else{
varj=b.left-f,k=b.left+f+c;
j<g.left?e.left=g.left-j:k>g.right&&(e.left=g.left+g.width-k)}returne},c.prototype.getTitle=function(){
vara,b=this.$element,c=this.options;
returna=b.attr("data-original-title")||("function"==typeofc.title?c.title.call(b[0]):c.title)},c.prototype.getUID=function(a){
doa+=~~(1e6*Math.random());
while(document.getElementById(a));
returna},c.prototype.tip=function(){
if(!this.$tip&&(this.$tip=a(this.options.template),1!=this.$tip.length))thrownewError(this.type+"`template`optionmustconsistofexactly1top-levelelement!");
returnthis.$tip},c.prototype.arrow=function(){
returnthis.$arrow=this.$arrow||this.tip().find(".tooltip-arrow")},c.prototype.enable=function(){
this.enabled=!0},c.prototype.disable=function(){
this.enabled=!1},c.prototype.toggleEnabled=function(){
this.enabled=!this.enabled},c.prototype.toggle=function(b){
varc=this;
b&&(c=a(b.currentTarget).data("bs."+this.type),c||(c=newthis.constructor(b.currentTarget,this.getDelegateOptions()),a(b.currentTarget).data("bs."+this.type,c))),b?(c.inState.click=!c.inState.click,c.isInStateTrue()?c.enter(c):c.leave(c)):c.tip().hasClass("in")?c.leave(c):c.enter(c)},c.prototype.destroy=function(){
vara=this;
clearTimeout(this.timeout),this.hide(function(){
a.$element.off("."+a.type).removeData("bs."+a.type),a.$tip&&a.$tip.detach(),a.$tip=null,a.$arrow=null,a.$viewport=null,a.$element=null})};
vard=a.fn.tooltip;
a.fn.tooltip=b,a.fn.tooltip.Constructor=c,a.fn.tooltip.noConflict=function(){
returna.fn.tooltip=d,this}}(jQuery),+function(a){
"usestrict";
functionb(b){
returnthis.each(function(){
vard=a(this),e=d.data("bs.popover"),f="object"==typeofb&&b;
!e&&/destroy|hide/.test(b)||(e||d.data("bs.popover",e=newc(this,f)),"string"==typeofb&&e[b]())})}varc=function(a,b){
this.init("popover",a,b)};
if(!a.fn.tooltip)thrownewError("Popoverrequirestooltip.js");
c.VERSION="3.3.7",c.DEFAULTS=a.extend({
},a.fn.tooltip.Constructor.DEFAULTS,{
placement:"right",trigger:"click",content:"",template:'<divclass="popover"role="tooltip"><divclass="arrow"></div><h3class="popover-title"></h3><divclass="popover-content"></div></div>'}),c.prototype=a.extend({
},a.fn.tooltip.Constructor.prototype),c.prototype.constructor=c,c.prototype.getDefaults=function(){
returnc.DEFAULTS},c.prototype.setContent=function(){
vara=this.tip(),b=this.getTitle(),c=this.getContent();
a.find(".popover-title")[this.options.html?"html":"text"](b),a.find(".popover-content").children().detach().end()[this.options.html?"string"==typeofc?"html":"append":"text"](c),a.removeClass("fadetopbottomleftrightin"),a.find(".popover-title").html()||a.find(".popover-title").hide()},c.prototype.hasContent=function(){
returnthis.getTitle()||this.getContent()},c.prototype.getContent=function(){
vara=this.$element,b=this.options;
returna.attr("data-content")||("function"==typeofb.content?b.content.call(a[0]):b.content)},c.prototype.arrow=function(){
returnthis.$arrow=this.$arrow||this.tip().find(".arrow")};
vard=a.fn.popover;
a.fn.popover=b,a.fn.popover.Constructor=c,a.fn.popover.noConflict=function(){
returna.fn.popover=d,this}}(jQuery),+function(a){
"usestrict";
functionb(c,d){
this.$body=a(document.body),this.$scrollElement=a(a(c).is(document.body)?window:c),this.options=a.extend({
},b.DEFAULTS,d),this.selector=(this.options.target||"")+".navli>a",this.offsets=[],this.targets=[],this.activeTarget=null,this.scrollHeight=0,this.$scrollElement.on("scroll.bs.scrollspy",a.proxy(this.process,this)),this.refresh(),this.process()}functionc(c){
returnthis.each(function(){
vard=a(this),e=d.data("bs.scrollspy"),f="object"==typeofc&&c;
e||d.data("bs.scrollspy",e=newb(this,f)),"string"==typeofc&&e[c]()})}b.VERSION="3.3.7",b.DEFAULTS={
offset:10},b.prototype.getScrollHeight=function(){
returnthis.$scrollElement[0].scrollHeight||Math.max(this.$body[0].scrollHeight,document.documentElement.scrollHeight)},b.prototype.refresh=function(){
varb=this,c="offset",d=0;
this.offsets=[],this.targets=[],this.scrollHeight=this.getScrollHeight(),a.isWindow(this.$scrollElement[0])||(c="position",d=this.$scrollElement.scrollTop()),this.$body.find(this.selector).map(function(){
varb=a(this),e=b.data("target")||b.attr("href"),f=/^#./.test(e)&&a(e);
returnf&&f.length&&f.is(":visible")&&[[f[c]().top+d,e]]||null}).sort(function(a,b){
returna[0]-b[0]}).each(function(){
b.offsets.push(this[0]),b.targets.push(this[1])})},b.prototype.process=function(){
vara,b=this.$scrollElement.scrollTop()+this.options.offset,c=this.getScrollHeight(),d=this.options.offset+c-this.$scrollElement.height(),e=this.offsets,f=this.targets,g=this.activeTarget;
if(this.scrollHeight!=c&&this.refresh(),b>=d)returng!=(a=f[f.length-1])&&this.activate(a);
if(g&&b<e[0])returnthis.activeTarget=null,this.clear();
for(a=e.length;
a--;
)g!=f[a]&&b>=e[a]&&(void0===e[a+1]||b<e[a+1])&&this.activate(f[a])},b.prototype.activate=function(b){
this.activeTarget=b,this.clear();
varc=this.selector+'[data-target="'+b+'"],'+this.selector+'[href="'+b+'"]',d=a(c).parents("li").addClass("active");
d.parent(".dropdown-menu").length&&(d=d.closest("li.dropdown").addClass("active")),d.trigger("activate.bs.scrollspy")},b.prototype.clear=function(){
a(this.selector).parentsUntil(this.options.target,".active").removeClass("active")};
vard=a.fn.scrollspy;
a.fn.scrollspy=c,a.fn.scrollspy.Constructor=b,a.fn.scrollspy.noConflict=function(){
returna.fn.scrollspy=d,this},a(window).on("load.bs.scrollspy.data-api",function(){
a('[data-spy="scroll"]').each(function(){
varb=a(this);
c.call(b,b.data())})})}(jQuery),+function(a){
"usestrict";
functionb(b){
returnthis.each(function(){
vard=a(this),e=d.data("bs.tab");
e||d.data("bs.tab",e=newc(this)),"string"==typeofb&&e[b]()})}varc=function(b){
this.element=a(b)};
c.VERSION="3.3.7",c.TRANSITION_DURATION=150,c.prototype.show=function(){
varb=this.element,c=b.closest("ul:not(.dropdown-menu)"),d=b.data("target");
if(d||(d=b.attr("href"),d=d&&d.replace(/.*(?=#[^\s]*$)/,"")),!b.parent("li").hasClass("active")){
vare=c.find(".active:lasta"),f=a.Event("hide.bs.tab",{
relatedTarget:b[0]}),g=a.Event("show.bs.tab",{
relatedTarget:e[0]});
if(e.trigger(f),b.trigger(g),!g.isDefaultPrevented()&&!f.isDefaultPrevented()){
varh=a(d);
this.activate(b.closest("li"),c),this.activate(h,h.parent(),function(){
e.trigger({
type:"hidden.bs.tab",relatedTarget:b[0]}),b.trigger({
type:"shown.bs.tab",relatedTarget:e[0]})})}}},c.prototype.activate=function(b,d,e){
functionf(){
g.removeClass("active").find(">.dropdown-menu>.active").removeClass("active").end().find('[data-toggle="tab"]').attr("aria-expanded",!1),b.addClass("active").find('[data-toggle="tab"]').attr("aria-expanded",!0),h?(b[0].offsetWidth,b.addClass("in")):b.removeClass("fade"),b.parent(".dropdown-menu").length&&b.closest("li.dropdown").addClass("active").end().find('[data-toggle="tab"]').attr("aria-expanded",!0),e&&e()}varg=d.find(">.active"),h=e&&a.support.transition&&(g.length&&g.hasClass("fade")||!!d.find(">.fade").length);
g.length&&h?g.one("bsTransitionEnd",f).emulateTransitionEnd(c.TRANSITION_DURATION):f(),g.removeClass("in")};
vard=a.fn.tab;
a.fn.tab=b,a.fn.tab.Constructor=c,a.fn.tab.noConflict=function(){
returna.fn.tab=d,this};
vare=function(c){
c.preventDefault(),b.call(a(this),"show")};
a(document).on("click.bs.tab.data-api",'[data-toggle="tab"]',e).on("click.bs.tab.data-api",'[data-toggle="pill"]',e)}(jQuery),+function(a){
"usestrict";
functionb(b){
returnthis.each(function(){
vard=a(this),e=d.data("bs.affix"),f="object"==typeofb&&b;
e||d.data("bs.affix",e=newc(this,f)),"string"==typeofb&&e[b]()})}varc=function(b,d){
this.options=a.extend({
},c.DEFAULTS,d),this.$target=a(this.options.target).on("scroll.bs.affix.data-api",a.proxy(this.checkPosition,this)).on("click.bs.affix.data-api",a.proxy(this.checkPositionWithEventLoop,this)),this.$element=a(b),this.affixed=null,this.unpin=null,this.pinnedOffset=null,this.checkPosition()};
c.VERSION="3.3.7",c.RESET="affixaffix-topaffix-bottom",c.DEFAULTS={
offset:0,target:window},c.prototype.getState=function(a,b,c,d){
vare=this.$target.scrollTop(),f=this.$element.offset(),g=this.$target.height();
if(null!=c&&"top"==this.affixed)returne<c&&"top";
if("bottom"==this.affixed)returnnull!=c?!(e+this.unpin<=f.top)&&"bottom":!(e+g<=a-d)&&"bottom";
varh=null==this.affixed,i=h?e:f.top,j=h?g:b;
returnnull!=c&&e<=c?"top":null!=d&&i+j>=a-d&&"bottom"},c.prototype.getPinnedOffset=function(){
if(this.pinnedOffset)returnthis.pinnedOffset;
this.$element.removeClass(c.RESET).addClass("affix");
vara=this.$target.scrollTop(),b=this.$element.offset();
returnthis.pinnedOffset=b.top-a},c.prototype.checkPositionWithEventLoop=function(){
setTimeout(a.proxy(this.checkPosition,this),1)},c.prototype.checkPosition=function(){
if(this.$element.is(":visible")){
varb=this.$element.height(),d=this.options.offset,e=d.top,f=d.bottom,g=Math.max(a(document).height(),a(document.body).height());
"object"!=typeofd&&(f=e=d),"function"==typeofe&&(e=d.top(this.$element)),"function"==typeoff&&(f=d.bottom(this.$element));
varh=this.getState(g,b,e,f);
if(this.affixed!=h){
null!=this.unpin&&this.$element.css("top","");
vari="affix"+(h?"-"+h:""),j=a.Event(i+".bs.affix");
if(this.$element.trigger(j),j.isDefaultPrevented())return;
this.affixed=h,this.unpin="bottom"==h?this.getPinnedOffset():null,this.$element.removeClass(c.RESET).addClass(i).trigger(i.replace("affix","affixed")+".bs.affix")}"bottom"==h&&this.$element.offset({
top:g-b-f})}};
vard=a.fn.affix;
a.fn.affix=b,a.fn.affix.Constructor=c,a.fn.affix.noConflict=function(){
returna.fn.affix=d,this},a(window).on("load",function(){
a('[data-spy="affix"]').each(function(){
varc=a(this),d=c.data();
d.offset=d.offset||{
},null!=d.offsetBottom&&(d.offset.bottom=d.offsetBottom),null!=d.offsetTop&&(d.offset.top=d.offsetTop),b.call(c,d)})})}(jQuery);
;
