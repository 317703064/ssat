KISSY.Editor.add("music/support",function(){function d(a){d.superclass.constructor.apply(this,arguments);a.cfg.disableObjectResizing||i.on(a.document.body,j.ie?"resizestart":"resize",function(b){(new c.Node(b.target)).hasClass(e)&&b.preventDefault()})}function f(a){return a._4e_name()==="img"&&!!a.hasClass(e)&&a}var c=KISSY,g=c.Editor,i=c.Event,h=g.Flash,j=c.UA,e="ke_music",k=["img."+e];c.extend(d,h,{_config:function(){this._cls=e;this._type="music";this._contextMenu=l;this._flashRules=k},_getFlashUrl:function(){return d.superclass._getFlashUrl.apply(this,
arguments).replace(/^.+niftyplayer\.swf\?file=/,"")}});h.registerBubble("music","\u97f3\u4e50\u7f51\u5740\uff1a ",f);g.MusicInserter=d;var l={"\u97f3\u4e50\u5c5e\u6027":function(a){var b=a.editor.getSelection();(b=(b=b&&b.getStartElement())&&f(b))&&a.show(null,b)}}},{attach:false,requires:["flash/support"]});
