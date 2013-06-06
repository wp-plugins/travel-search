/*	@note	this object will be used to open up the popups from each form
	@date	2013 JUN 06
	@author	Tibi	*/
TGSB_WindowOpener	= function(opts) {
			/*	try to reach the top window if this is not it;
				will work only from the same domain;
				needed to adjust new windows' sizes to top window;	*/
			if(top!=self) {
				try {
					if(top.document.location.toString()) {
						this._top	= top;
					}
				} catch(err) {}
			}
			/*	options moved here to be unique for each object	*/
			this.opts	= {
				/*	open windows as popUnder or popUps	*/
				popUnder:	false,
				/*	max. screen resolution to use, for restrict sizes and fallback case	*/
				maxScreenWidth:	1280,
				maxScreenHeight:1024,
				/*	min. win height and width; customizable	*/
				minWinWidth:	0,
				minWinHeight:	0,
				/*	for the cascading model	*/
				winWidth:	600,
				winHeight:	700,
				/*	open mode: tileWindows (how Kayak does) vs left+top offset (LowFares); tab|side|cascade|fix	*/
				style:		'cascade',
				heightRatio:	null,
				/*	sending top, left, width, height to the newly opened window;
					needed for Popup-Blocker get-around solution,
					because WIN1 can NOT resize/move WIN2 after WIN2 was opened,
					especially if they are from different domains
				*/
				sendPosition:	false,
				/*	position: used for style:'fix' only; needed for blank windows	*/
				position:	{left:0, top:0, width:100, height:100},
				/*	enable/disable chrome popup-blocker trick	*/
				chromePPBmode:	false,
				blankPageHtml: "<h4 style='text-align:center;'>Please wait...</h4>",
				/*	@note	if hover is enabled a CSS class will be added to the wrapped button in chrome when the mouse goes over the iFrame
					@date	2013 JUN 06
					@author	Tibi	*/
				useHover:	false
			};
			/*	object holding opened windows;	*/
			this.windowCache	= {};
			/*	object holding all previously opened windows' names; needed for popup-blocker mode	*/
			this.allWindowNames	= {};
			/*	current window names; to avoid the reuse of already loaded window; i.e. Expedia_hotel => Expedia_hotel3	*/
			// this.windowNames	= {};
			/*	passing options from constr. call	*/
			for(var i in opts)
				this.opts[i]	= opts[i];
};
/*	the prototype of the class	*/
TGSB_WindowOpener.prototype	= {
	/*	_top common for all instances from the same page	*/
	_top: self,
	/**	WINDOW OPENING FUNCTIONS	*/
	/*	opener fn. returning boolean value;	*/
	open: function(url, wName, wIndex, totalWinCount, isSingle) {
		if(!url)
			return true;
		/*	check if it is already opened	*/
		var alreadyOpenedWin	= this._isWindowOpen(wName);
		/*	gen. formatted name if needed	*/
		var formattedWinName	= '';
		if(!alreadyOpenedWin) {
			/*	format window's name: punctuations are not supported in IE	*/
			formattedWinName	= this._normalizeWindowName(wName);
			/*	generate a unique, not yet used window name; needed for popup-blocker mdoe	*/
			formattedWinName	= this._getUniqueWinName(formattedWinName);
		}
		/*	decide the target: WIN object or WIN name/string	*/
		var wnd	= this._openWindow(url, alreadyOpenedWin || formattedWinName, wIndex, totalWinCount, isSingle);
		/*	check if the window has been opened	*/
		if(!wnd)
			return false;
		/*	if there's a win name present => add the opened win or remove it	*/
		if(wName) {
			/*	if window was pre-opened and now loaded => remove it	*/
			if(alreadyOpenedWin) {
				delete this.windowCache[wName];
			} else {
				this.windowCache[wName]	= wnd;
			}
		}
		if(!this.opts.popUnder) {
			/*	HANDLE FOCUS WHEN POPUPS ARE OPENED, USING BLANK WINDOWS!	*/
			this._focusWindow(wnd);
			return wnd;
		}
		/*	popunders: handle focus/blur in each browser	*/
		this._blurWindow(wnd);
		this._extendPopUpWindow(wnd);
		try{
			wnd.sendFocus2Parent(wnd);
		} catch(h) {};
		return wnd;
	},
	/*	"Fly.com213523"	=>	"Flycom213523"	*/
	_normalizeWindowName: function(wName) {
		return wName && wName.replace(/[^a-zA-Z0-9_]+/, '');
	},
	/**
		@note	changes the location of the window specified by windowObj, only for blank windows;
		@date	2013-Apr-08;
		@author	Lorand;
	*/
	_changeBlankWindowLocation: function(windowObj, url, t, l, w, h) {
		if(!windowObj || typeof(windowObj) != 'object')
			return false;
		try {windowObj.resizeTo(w,h);} catch(err){}
		try {windowObj.moveTo(l,t);} catch(err){}
		if (this.opts.sendPosition)
			url		= this._addPos2Url(url, t, l, w, h);
		windowObj.location	= url;
		return true;
	},
	/**
		@note	changes the location of the window specified by wnd; JS-based redirect for Chrome pop-up blocker trick;
		@date	2013-Mar-21;
		@author	Tibor; Ciprian; Lorand;
	*/
	_changeWindowLocation: function(wnd, url) {
		if(!wnd || typeof(wnd) != 'object')
			return false;
		wnd.document.open();
		wnd.document.write("<html><head>");
		wnd.document.write('<script type="text/javascript">\n');
		wnd.document.write('window.location="' + url + '";');
		wnd.document.write("<\/script>\n");
		wnd.document.write("</head><body></body></html>");
		wnd.document.close();
		return true;
	},
	
	_populateBlankPage: function(wnd){
		if(!wnd || typeof(wnd) != 'object')
			return false;
		wnd.document.open();
		wnd.document.write("<html><head>");
		wnd.document.write("<title>Please wait</title>");
		wnd.document.write("</head><body style='padding:0;margin:0;'>");
		if (this.opts.blankPageHtml && typeof(this.opts.blankPageHtml)=='string')
			wnd.document.write(this.opts.blankPageHtml);
		wnd.document.write("</body></html>");
		wnd.document.close();
	},
	/*	actual open call in a separate fn; thus, dynamically calculated coordinates can be added later;
		ATTENTION/UPDATE:	wName can be both a window object or a window name
	*/
	_openWindow: function(url, wNameOrObj, wIndex, totalWinCount, isSingle) {
		var t	= this._newWinTop(wIndex, totalWinCount);
		var l	= this._newWinLeft(wIndex, totalWinCount);
		var w	= this._newWinWidth(wIndex, totalWinCount);
		var h	= this._newWinHeight(wIndex, totalWinCount);
		// console.log('' + wIndex + '|' + t + '|' + l + '|' + w + '|' + h);
		/*	handling window object passed as param., for IE only, when blank windows are opened	*/
		if(wNameOrObj && typeof wNameOrObj == 'object') {
			this._changeBlankWindowLocation(wNameOrObj, url, t, l, w, h);
			return wNameOrObj;
		}
		var params	= this.opts.style == 'tab' ? '' :
						'toolbar=0,scrollbars=1,location=1,statusbar=1,menubar=0,resizable=1'+
						',top='+t+',left='+l+',width='+w+',height='+h;
		try {
			/*	if Chrome popup-blocker trick is not enabled OR isSingle == simple text ads => common win.open() as before	*/
			if(!this.opts.chromePPBmode || isSingle) {
				var w	= this._top.window.open(url, wNameOrObj, params);
				if (url=='about:blank')
					this._populateBlankPage(w);
				return w;
			};
			// popups are working in chrome only if focus is given back to current window like this
			/*	changed by Tibi on 2013.05.20 | We want the windows to remain on the screen because they are popups	*/
			//this._top.window.open("javascript:window.focus()", "_self", "");
			this._top.window.open("javascript:window.blur()", "_top", "");

			/*	Chrome popup-blocker trick enabled => we open a new blank window and change the location it via JS	*/
	        	var w	= this._top.window.open('about:blank', wNameOrObj, params);
			/*	write the URL into the blank window via JS	*/
			if (url=='about:blank')
				this._populateBlankPage(w);
			else
				this._changeWindowLocation(w, url);
			return w;
		} catch(a) {
			return false;
		}
	},
	/**	END OF WINDOW OPENING FUNCTIONS	*/
	/**	WINDOW POSITIONING FUNCTIONS	*/
	_newWinTop: function(wIndex, totalWinCount) {
		/*	'fix' mode should use top from this.opts.position.top	*/
		if(this.opts.style == 'fix')
			return this.opts.position.top;
		if(this.opts.heightRatio!==null) {
			return Math.round( this._maxScreenHeight() * (1-this.opts.heightRatio) );
		}
		/*	side-by-side opening model	*/
		if(this.opts.style == 'side') {
			return 0;
		}
		/*	cascading opening model	*/
		if(this.opts.style == 'cascade') {
			return wIndex * Math.floor((this._maxScreenHeight()-this.opts.winHeight)/totalWinCount);
		}
		if(typeof this._top.window.screenTop != 'undefined') {
			return this._top.window.screenTop;
		}
		return this._top.window.screenY;
	},
	_newWinLeft: function(wIndex, totalWinCount) {
		/*	'fix' mode should use left from this.opts.position.left	*/
		if(this.opts.style == 'fix')
			return this.opts.position.left;
		/*	side-by-side opening model	*/
		if(this.opts.style == 'side') {
			return Math.floor(this._newWinWidth(wIndex, totalWinCount) * wIndex);
		}
		/*	cascading opening model	*/
		if(this.opts.style == 'cascade') {
			return wIndex * Math.floor((this._maxScreenWidth()-this.opts.winWidth)/totalWinCount);
		}
		/*	if none set the size to top window	*/
		if(typeof this._top.window.screenLeft != 'undefined') {
			return this._top.window.screenLeft;
		}
		return this._top.window.screenX;
	},
	_newWinWidth: function(wIndex, totalWinCount) {
		/*	'fix' mode should use width from this.opts.position.width	*/
		if(this.opts.style == 'fix')
			return this.opts.position.width;
		/*	side-by-side opening model	*/
		if(this.opts.style == 'side') {
			/*	attention, no window can exceed max. sizes defined in options	*/
			return Math.min(this.opts.maxScreenWidth, Math.floor(this._maxScreenWidth()/totalWinCount-10));
		}
		if(this.opts.style == 'cascade') {
			return this.opts.winWidth;
		}
		var topWinWidth	= this._topWinWidth();
		if(this.opts.minWinWidth) {
			/*	if top window is smaller than minWinWidth	*/
			return Math.max(this.opts.minWinWidth, topWinWidth);
		}
		return topWinWidth;
	},
	_newWinHeight: function() {
		/*	'fix' mode should use height from this.opts.position.height	*/
		if(this.opts.style == 'fix')
			return this.opts.position.height;
		/*	check if a height ratio is set	*/
		if(this.opts.heightRatio!==null) {
			return Math.round(this._maxScreenHeight() * this.opts.heightRatio);
		}
		/*	side-by-side windows: use max screen height	*/
		if(this.opts.style == 'side') {
			return this._maxScreenHeight();
		}
		if(this.opts.style == 'cascade') {
			return this.opts.winHeight;
		}
		var topWinHeight	= this._topWinHeight();
		if(this.opts.minWinHeight) {
			return Math.max(this.opts.minWinHeight, topWinHeight);
		}
		return topWinHeight;
	},
	/*	gets Top Window's current Height	*/
	_topWinHeight: function() {
		if(typeof(this._top.window.innerHeight) == 'number') {
			return this._top.window.innerHeight;
		}
		if(this._top.document.documentElement && this._top.document.documentElement.clientHeight) {
			return this._top.document.documentElement.clientHeight;
		}
		if(this._top.document.body && this._top.document.body.clientHeight) {
			return this._top.document.body.clientHeight;
		}
		return 0;
	},
	/*	gets Top Window's current Width		*/
	_topWinWidth: function() {
		if(typeof(this._top.window.innerWidth) == 'number') {
			return this._top.window.innerWidth;
		}
		if(this._top.document.documentElement && this._top.document.documentElement.clientWidth) {
			return this._top.document.documentElement.clientWidth;
		}
		if(this._top.document.body && this._top.document.body.clientWidth) {
			return this._top.document.body.clientWidth;
		}
		return 0;
	},
	/*	max available width of the screen	*/
	_maxScreenWidth:function() {
		/*	if it's available from the browser/DOM => use it	*/
		if(window.screen.availWidth) {
			return window.screen.availWidth < this.opts.maxScreenWidth ?
					window.screen.availWidth :
					this.opts.maxScreenWidth;
		}
		/*	if it's not available from the browser/DOM => use from options as fallback case	*/
		return this.opts.maxScreenWidth;
	},
	/*	max available height of the screen	*/
	_maxScreenHeight:function() {
		if(window.screen.availHeight) {
			return window.screen.availHeight < this.opts.maxScreenHeight ?
					window.screen.availHeight :
					this.opts.maxScreenHeight;
		}
		return this.opts.maxScreenHeight;
	},
	/*	sends new win positions to be set from the opened window; needed for pp-blocker get-around solution, IE;	*/
	_addPos2Url:function(url, t, l, w, h) {
		/*	basic input validation	*/
		if(!url)
			return url;
		/*	decide which separator to use, "?" or "&"	*/
		var separator	= url.indexOf('?') == '-1' ? '?' : '&';
		return url	+= separator +
					'resizeTo[t]='+t+
					'&resizeTo[l]='+l+
					'&resizeTo[w]='+w+
					'&resizeTo[h]='+h;
	},
	/**	END OF WINDOW POSITIONING FUNCTIONS	*/
	/**	FOCUS-RELATED FUNCTIONS	*/
	/*	blur opened win; for popunder mode;	*/
	_blurWindow: function(wnd) {
		/*	window blur for each browser	*/
		wnd.blur();
		/*	AppleWebKit-based browsers fix (Chrome, Safari)	*/
		if(navigator.userAgent.toLowerCase().indexOf("applewebkit")>-1) {
			this._top.window.blur();
			this._top.window.focus();
		}
	},
	/*	focuses the opened win; for popup mode when blank windows are also used;	*/
	_focusWindow: function(wnd) {
		wnd.focus();
	},

	/*	extends newly opened window	*/
	_extendPopUpWindow: function(WINwindow) {
		var newWinObj	= {
			sendFocus2Parent: function(b) {
				this._handleMozzEngine();
				try {
					b.opener.window.focus();
				} catch(c) {}
			},
			_handleMozzEngine: function() {
				if(typeof window.mozPaintCount!=="undefined" || typeof navigator.webkitGetUserMedia==="function") {
					var b	= this.window.open("about:blank");
					b.close();
				}
			}
		};
		try {
			for(var i in newWinObj)
				WINwindow[i]	= newWinObj[i];
		} catch(e) {};
	},
	/**	END OF FOCUS-RELATED FUNCTIONS	*/
	/**	GENERAL/SETTINGS FUNCTIONS	*/
	/*	set/change/update options on-the-fly	*/
	setOptions: function(opts) {
		for(var i in opts)
			this.opts[i]	= opts[i];
	},
	/**	END OF GENERAL/SETTINGS FUNCTIONS	*/
	/**	POPUP-BLOCKER MODE FUNCTIONS	*/
	/*	generates unique window name	*/
	_getUniqueWinName: function(wName) {
		if(!wName)
			return wName;
		/*	finds first unique window name	*/
		for(var i=0; this.allWindowNames[wName+i]; i++);
		this.allWindowNames[wName+i]	= 1;
		return wName+i;
	},
	/*	closes an opened win; used /only/ for popup-blocker mode;	*/
	close: function(wName) {
		/*	if window is not present (?) => return false	*/
		if(!this.windowCache[wName])
			return false;
		/*	try-catch: better safe than sorry	*/
		try {
			this.windowCache[wName].close();
		} catch(err) {};
		/*	delete from opened wins.	*/
		delete(this.windowCache[wName]);
		return true;
	},
	/*	checks if a specific window is still open as blank; for popup-blocker mode; false if been closed or loaded	*/
	_isWindowOpen: function(wName) {
		return wName && this.windowCache[wName] && !this.windowCache[wName].closed && this.windowCache[wName];
	},
	/**
		@note	wraps the submit button inside a relative-positioned span and overlaps the button with an iframe,
			so the user will click inside the iframe when he wants to make a search
			IMPORTANT - this is intended to be used only in chrome
		@date	2013.03.21;
		@author	Tibor; Ciprian; Lorand;
		@param	bttn -> DOM input submit or button element; the one which should we wrapped
		@return	the generated iframe DOM element; false on fail
		@todo	- CSS CONFLICTS BOTH FOR IFRAME AND SPAN MIGHT APPEAR! -> TEST
	*/
	wrapButton: function(sButton, undefined) {
		/*	basic validation	*/
		if(!this.opts.chromePPBmode || !sButton)
			return false;
		if (typeof(jQuery)=='undefined' && typeof(sButton.click)!='function' && typeof(sButton.onclick)!='function')
			return false;
		/*	creating span into which the submit button will be placed	*/
		var span		= this._top.document.createElement('span');
		var sStyle		= span.style;
		sStyle.display	= "inline-block";
		sStyle.position	= "relative";
		sStyle.width	= "auto";
		sStyle.height	= "auto";
		sStyle.margin	= "0px";
		sStyle.marginBottom	= "0px";
		sStyle.marginTop	= "0px";
		sStyle.marginLeft	= "0px";
		sStyle.marginRight	= "0px";
		span.className	= "ppupHandlerCont";
		
		/*	creating the iframe that will overlap the submit button	*/
		var iframe		= this._top.document.createElement('iframe');
		/*	short-hand for iframe style and Search Button style obj.	*/
		var iStyle		= iframe.style;
		var bStyle		= sButton.style;
		
		iframe.frameBorder	= "0";
		iframe.src		= "about:blank";
		iStyle.border		= "none";
		iStyle.cursor		= "pointer";
		iStyle.position		= "absolute";
		iStyle.top		= "0";
		iStyle.left		= "0";
		if(sButton.width == undefined || !sButton.width || sButton.width == "0" || sButton.height == undefined || !sButton.height || sButton.height == "0") {
			iframe.width	= "100%";
			iframe.height	= "100%";
			iStyle.padding	= "0px";
			iStyle.margin	= "0px";
		} else {
			iframe.width		= sButton.width;
			iframe.height		= sButton.height;
			iStyle.padding		= bStyle.padding;
			iStyle.paddingTop	= bStyle.paddingTop;
			iStyle.paddingRight	= bStyle.paddingRight;
			iStyle.paddingBottom	= bStyle.paddingBottom;
			iStyle.paddingLeft	= bStyle.paddingLeft;
			iStyle.margin		= bStyle.margin;
			iStyle.marginTop	= bStyle.marginTop;
			iStyle.marginRight	= bStyle.marginRight;
			iStyle.marginBottom	= bStyle.marginBottom;
			iStyle.marginLeft	= bStyle.marginLeft;
		};
		/*	event binding at onLoad of the iframe	*/
		var self	= this;
		var iFrameOnLoad	= function () {
			var contentDoc;
			/*	Chrome; FF; Opera; Safari;	*/
			if(iframe.contentDocument)
				contentDoc	= iframe.contentDocument;
			/*	Chrome; FF; Opera/Safari; IE;	*/
			else if(iframe.contentWindow && iframe.contentWindow.document)
				contentDoc	= iframe.contentWindow.document;
			else
				return;
			contentDoc.body.style.margin	= "0";
			contentDoc.body.style.cursor	= "pointer";
			contentDoc.onclick	= function (e) {
				/*	@note	ORDER MATTERS! first should be default DOM click event, in case an input is clicked, form submit events are triggered only w/ this
					@date	2013 JUN 06
					@author	Tibi	*/
				if(typeof(sButton.click)=='function')
					sButton.click();
				else if (typeof(jQuery)!='undefined')
					jQuery(sButton).click();
				else if(typeof(sButton.onclick)=='function')
					sButton.onclick();
			};
			/*	@note	to be able to handle hover effect, we add/remove a CSS class to teh button, when teh mouse is hovered on the iframe
				@date	2013 JUN 06
				@author	Tibi	*/
			if (self.opts.useHover) {
				contentDoc.onmouseover	= function(){
					var n	= sButton.className.replace(/\bwindowOpener_mouseOver\b/g,'');
					sButton.className	= (n + ' windowOpener_mouseOver').replace(/ +/,' ').replace(/^ | $/g,'');
				};
				contentDoc.onmouseout	= function(){
					var n	= sButton.className.replace(/\bwindowOpener_mouseOver\b/g,'');
					sButton.className	= n.replace(/ +/g,' ').replace(/^ | $/g,'');
				};
			};
		};
		/*	binding the onload function	*/
		iframe.onload	= iFrameOnLoad;

		/*	@note	if button is positioned absolute, the button wrapper should also be positioned absolute, and the button itself should be positioned relative w/ position 0/0
			@date	2013 JUN 06
			@author	Tibi
			@NOTE	Finally we decided to not use JS to set these CSS rules since they can be set to the CSS class of the wrapper
			*/
		/*if (this._getStyle(sButton,'position')=='absolute') {
			span.style.position	= 'absolute';
			span.style.left		= this._getStyle(sButton,'left');
			span.style.top		= this._getStyle(sButton,'top');
			span.style.right	= this._getStyle(sButton,'right');
			span.style.bottom	= this._getStyle(sButton,'bottom');
			bStyle.position		= 'relative';
			bStyle.top		= '0px';
			bStyle.left		= '0px';
		} else if (this._getStyle(sButton,'float')!='none') {
			span.style.float	= this._getStyle(sButton,'float');
		};*/
		
		/*	adding the generated span to the button's parent, right before the button	*/
		sButton.parentNode.insertBefore(span, sButton);
		/*	moving the button into the span	*/
		span.appendChild(sButton);
		/*	attaching the iframe to the span	*/
		span.appendChild(iframe);
		return iframe;
	}

	/*	@note	reads out the merged CSS rule for an element
		@date	2013 JUN 06
		@author	Tibi
		*/
	/*_getStyle: function (elem, cssRule){
		var strValue = "";
		if(document.defaultView && document.defaultView.getComputedStyle)
			return document.defaultView.getComputedStyle(elem, "").getPropertyValue(cssRule);
		if(!elem.currentStyle)
			return false;
		cssRule = cssRule.replace(/\-(\w)/g, function (strMatch, p1){
			return p1.toUpperCase();
		});
		return elem.currentStyle[cssRule];
	}*/
	/**	END OF POPUP-BLOCKER MODE FUNCTIONS	*/
};