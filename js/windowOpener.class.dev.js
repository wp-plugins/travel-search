/*	@note	this object will be used to open up the popups from each form
	@date	2013 JUN 06
	@author	Tibi	*/
TGSB_WindowOpener	= function(opts) {
			/*	try to reach the top window if this is not it;
				will work only from the same domain;
				needed to adjust new windows' sizes to top window;	*/
			this._top	= self;
			if (top!=self)
				try {
					if(top.document.location.toString())
						this._top	= top;
				} catch(err) {};

			/*	options moved here to be unique for each object	*/
			this.opts	= {
				/*	open windows as popUnder or popUps	*/
				popUnder		: false,
				/*	max. screen resolution to use, for restrict sizes and fallback case	*/
				maxScreenWidth		: screen.width,
				maxScreenHeight		: screen.height,
				/*	min. win height and width; customizable	*/
				minWinWidth		: 0,
				minWinHeight		: 0,
				/*	for the cascading model	*/
				winWidth		: parseInt(screen.width*0.8),
				winHeight		: parseInt(screen.height*0.8),
				/*	open mode: tileWindows (how Kayak does) vs left+top offset (LowFares); tab|side|cascade|fix	*/
				style			: 'cascade',
				heightRatio		: null,
				/*	sending top, left, width, height to the newly opened window;
					needed for Popup-Blocker get-around solution,
					because WIN1 can NOT resize/move WIN2 after WIN2 was opened,
					especially if they are from different domains
				*/
				sendPosition		: false,
				/*	position: used for style:'fix' | also used when placeholder windows (not tabs) are opened	*/
				position		: {left:0, top:0, width:100, height:100},
				/*	enable/disable chrome popup-blocker trick	*/
				chromePPBmode		: false,
				/**
					@note	generates the link that will be used to in the placeholder | if returns false, about:blank will be used
					@date	2013-NOV-27
					@author	Tibi
					@param	the parameter specified to openPlaceholder | can be of any type (but must be defined)
					@return	String the link to the placeholder page
				*/
				buildPlaceHolderUrl	: function(obj){},
				/**
					@note	generates the HTML that should be used to populate the blank placeholders (windows w/ about:blank)
					@date	2013-NOV-27
					@author	Tibi
					@param	the parameter specified to openPlaceholder | can be of any type (but must be defined)
					@return	String the html to load in the blank page
				*/
				buildPlaceHolderHtml	: function(obj){},
				/**
					@note	generates the window name 
					@date	2013-NOV-27
					@author	Tibi
					@param	the parameter specified to `openPlaceholder` and the second parameter specified to `open` method
						can be of any type (but must not evaluate to boolean false)
					@return	String the name of the window
				*/
				buildWindowName		: function(obj){}
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
			/*generates an ID of 5 chanarcters that will be appended to each window name*/
			this.guid		= Math.random().toString(36).substr(2, 5);
};
/*	the prototype of the class	*/
TGSB_WindowOpener.prototype	= {

	/**
		@note	opens a new window | may load window into a placeholder window if we have any
		@date	2013-NOV-27
		@param	url	the link to open
		@param	obj	the object to pass to the window name builder - generally the button for the merchant
		@param	wIndex	the index of the windows - used for opening multiple windows
		@param	totalWinCount	the total nr. of windows that will be opened - used for opening multiple windows
		@author	Tibi
	*/
	open: function(url, obj, wIndex, totalWinCount) {
		if(!url)
			return true;
		var wName	= typeof(this.opts.buildWindowName)=='function' ? this.opts.buildWindowName(obj) : false;
		/*	check if it is already opened	*/
		var wNameOrObj	= this._isWindowOpen(wName) || this._getUniqueWinName(wName);

		/*	decide the target: WIN object or WIN name/string	*/
		var wnd	= this._openWindow(url, wNameOrObj, wIndex, totalWinCount);
		/*	check if the window has been opened	*/
		if(!wnd)
			return false;
		/*	if there's a win name present && we have a cached window object for the name, we remove the cached window object	*/
		if(wName && this.windowCache[wName])
			delete this.windowCache[wName];
		return wnd;
	},
	
	/**
		@note	Opens up a placeholder window, used in IE and in Chrome 30+
		@date	2013-NOV-18
		@author	Tibi
	*/
	openPlaceholder:	function(obj) {
		if (!obj)
			return false;
		var pURL	= typeof(this.opts.buildPlaceHolderUrl)=='function'	? this.opts.buildPlaceHolderUrl(obj)	: false;
		var pHTML	= typeof(this.opts.buildPlaceHolderHtml)=='function'	? this.opts.buildPlaceHolderHtml(obj)	: false;
		var wName	= typeof(this.opts.buildWindowName)=='function'		? this.opts.buildWindowName(obj)	: false;

		if (!pURL)
			pURL	= 'about:blank';

		/*	generate a unique, not yet used window name; needed to be able to open in the same window another window	*/
		var uniqueWinName	= this._getUniqueWinName(wName);

		var wnd	= this.opts.chromePPBmode ?	this._openChromePlaceholder(uniqueWinName, pURL, pHTML)
							: this._openDefaultPlaceholder(uniqueWinName, pURL, pHTML);

		if (!wnd)	return false;

		if (pURL=='about:blank') {
			this._populateBlankPage(wnd, pHTML);
		}

		/*	storing the window object	*/
		this.windowCache[wName]	= wnd;

		return wnd;
	},
	
	/**
		@note	closes an opened placeholder win;
		@date	2013-NOV-27
		@author	Tibi
		@param	any param that's used in the window name generator that's not evaluated to false
		@return boolean true if window was closed, false if params were invalid or window was not found in cache
	*/
	closePlaceholder: function(obj) {
		if (!obj)
			return false;
		var wName	= typeof(this.opts.buildWindowName)=='function'		? this.opts.buildWindowName(obj)	: false;
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
	
	/**	END OF FOCUS-RELATED FUNCTIONS	*/
	/**	GENERAL/SETTINGS FUNCTIONS	*/
	/*	set/change/update options on-the-fly	*/
	setOptions: function(opts) {
		for(var i in opts)
			this.opts[i]	= opts[i];
	},
	
	/**
		@note	check if chrome version is higher or equal to param version
		@param	int version of chrome to check
		@return	bool true on success, false on fail
		@date	2013-Aug-26
		@author	Rudolf Csuha
	*/
	isChromeMinVer: function(version) {

		// basic input validation
		if (!version)
			return false;
		// if not chrome 
		if (typeof(window['chrome']) == 'undefined')
			return false;
	
		// check if version is higher or equal to the param
		if (parseInt(window.navigator.appVersion.match(/Chrome\/(\d+)\./)[1], 10) >= parseInt(version))
			return true;
		else 
			return false;	
	},
	
	/****************************** PRIVATE METHODS ****************************/
	
	/*	actual open call in a separate fn; thus, dynamically calculated coordinates can be added later;
		ATTENTION/UPDATE:	wName can be both a window object or a window name
	*/
	_openWindow: function(url, wNameOrObj, wIndex, totalWinCount) {
		var t	= this._newWinTop(wIndex, totalWinCount);
		var l	= this._newWinLeft(wIndex, totalWinCount);
		var w	= this._newWinWidth(wIndex, totalWinCount);
		var h	= this._newWinHeight(wIndex, totalWinCount);

		/*	handling window object passed as param.
			the case when a placeholder window was already opened for the current landingpage
			Currently Chrome 30+ and IE	*/
		if(wNameOrObj && typeof(wNameOrObj) == 'object')
			return this._changeWindowLocation(wNameOrObj, url, t, l, w, h);

		/**	checking if chrome solutions should be used or not - if yes, using them	*/
		if (this.opts.chromePPBmode)
			return this._openChromePopup(url, wNameOrObj);

		/**	if not the chrome solution should be used, we use the regular solution	*/
		wnd	= this._openDefaultPopup(url, wNameOrObj, t, l, w, h);
		/*	HANDLE FOCUS/BLUR WHEN POPUPS ARE OPENED, USING BLANK WINDOWS!	*/
		if(!this.opts.popUnder)
			this._focusWindow(wnd);
		else	this._blurWindow(wnd);

		return wnd;
	},
	
	/**	@note	called when a popup is opened in Chrome
		@date	2013-NOV-21
		@author	Tibi
		@param	String	link to open
		@param	String	window name
		@return	Object window object if possible, boolean true if can't get window object, boolean false if window can't be opened	*/
	_openChromePopup:	function(url, wNameOrObj){
		if (!url)
			return false;
		/*	we generate a unique window name if we don't have yet any placeholder window object or window name
			in chrome 30+ this is mandatory to be able to get the window object, in other chromes we do it only to
			have a name for each window	*/
		if (!wNameOrObj)
			wNameOrObj	= this._generateWindowName();
		if (this.isChromeMinVer(30)) {
			/*	in chrome 30 we work only with tabs
				focus/blur on windows can't be manipulated and placeholders are also opened as tabs	*/
			var openMode	= this.opts.popUnder ? 'tab' : 'focusedtab';
			this._openWithClickTrigger('about:blank', openMode, wNameOrObj);
			/*	we get the window handler for the opened window here	*/
			var wnd	= window.open(url, wNameOrObj);
			return wnd;
		}
		var openMode	= 'window';
		if (this.opts.style=='tab' || this.opts.popUnder)
			openMode	= this.opts.popUnder ? 'tab' : 'focusedtab';
		this._openWithClickTrigger(url, openMode);
		return true;
	},
	
	/**	@note	default popup opener, at the moment used in FF, Opera and Safari
			in IE it's not used since we use placeholders
		@date	2013-NOV-21
		@author	Tibi
		@param	String	link to open
		@param	String	window name
		@param	Integer	top position
		@param	Integer	left position
		@param	Integer	window width
		@param	Integer	widow height
		@return	Object window object, boolean false if window can't be opened	*/
	_openDefaultPopup:	function(url, wNameOrObj, t, l, w, h){
		if (!url)
			return false;
		var params	= this.opts.style == 'tab' ? '' :
						'toolbar=0,scrollbars=1,location=1,statusbar=1,menubar=0,resizable=1,'+
						'top='+t+',left='+l+',width='+w+',height='+h;
		try {
			var win	= this._top.window.open(url, wNameOrObj, params);
			return win;
		} catch(a) {
			return false;
		}
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
	_changeWindowLocation: function(windowObj, url, t, l, w, h) {
		if(!windowObj || typeof(windowObj) != 'object')
			return false;
		try {windowObj.resizeTo(w,h);} catch(err){}
		try {windowObj.moveTo(l,t);} catch(err){}
		if (this.opts.sendPosition)
			url		= this._addPos2Url(url, t, l, w, h);
		if (this.opts.popUnder)
			windowObj.location	= url;
		else	{
			windowObj	= this._top.open(url,windowObj.name);
			this._focusWindow(windowObj);
		};
		return windowObj;
	},
	
	_populateBlankPage: function(wnd, html){
		if(!wnd || typeof(wnd) != 'object' || !html)
			return false;
		wnd.document.open();
		wnd.document.write(html);
		wnd.document.close();
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
			return false;
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
		if (typeof(wnd)!='object')
			return false;
		/*	window blur for each browser	*/
		wnd.blur();
		/*	AppleWebKit-based browsers fix (Chrome, Safari)	*/
		if(navigator.userAgent.toLowerCase().indexOf("applewebkit")>-1)
			this._top.window.blur();
		this._top.window.focus();
		this._extendPopUpWindow(wnd);
		try{
			wnd.sendFocus2Parent(wnd);
		} catch(h) {};
		try{
			this._top.window.open('javascript:window.focus();','_self');
		}catch(err){}
		return true;
	},
	/*	focuses the opened win; for popup mode when blank windows are also used;	*/
	_focusWindow: function(wnd) {
		if (wnd && wnd.focus)
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
	
	/**	
		@note	Generates a unique window name that's not used yet on the current page
		@date	2013-NOV-21
		@author	Tibi
	*/
	_generateWindowName: function(){
		var wName	= 'rnd_' + Math.random().toString(36).substr(2,8);
		return this._getUniqueWinName(wName);
	},
	
	/**	END OF GENERAL/SETTINGS FUNCTIONS	*/
	/**	POPUP-BLOCKER MODE FUNCTIONS	*/
	/*	generates unique window name	*/
	_getUniqueWinName: function(wName) {
		if(!wName)
			return false;
		/*	format window's name: punctuations are not supported in IE	*/
		wName	= this._normalizeWindowName(wName);
		wName	+= '_' + this.guid + '_';
		/*	finds first unique window name	*/
		for(var i=0; this.allWindowNames[wName+i]; i++);
		this.allWindowNames[wName+i]	= 1;
		return wName+i;
	},

	/**
		@note	checks if a specific window is still open as blank; for popup-blocker mode; false if been closed or loaded
	*/
	_isWindowOpen: function(wName) {
		return wName && this.windowCache[wName] && !this.windowCache[wName].closed && this.windowCache[wName];
	},
	
	/**
		@note	function used to open a placeholder window in Chrome (used in Chrome 30+)
		@date	2013-NOV-21
		@author	Tibi
		@param	unique window name | mandatory
		@param	placeholder URL | mandatory - for blank page use about:blank
	*/
	_openChromePlaceholder:		function(uniqueWinName, pURL, pHTML){
		if (!uniqueWinName || !pURL)
			return false;
		/*	opening up a blank page
			at this point we won't have the window object so we will open a new page in the window
			opened w/ _openWithClickTrigger to have the win. handler
			In chrome placeholder windows are always tabs since focus/blur can't be handled in chrome 30+ and in previous versions there's no need for placeholder	*/
		this._openWithClickTrigger('about:blank', 'tab', uniqueWinName);
		/*	we get the window handler for the opened window here	*/
		return window.open(pURL, uniqueWinName);
	},
	
	/**
		@note	Opens up a placeholder window in the background
			Currently used only in IE
		@date	2013-NOV-21
		@author	Tibi
		@param	unique window name | mandatory
		@param	placeholder URL | mandatory - for blank page use about:blank
	*/
	_openDefaultPlaceholder:	function(uniqueWinName, pURL, pHTML){
		if (!uniqueWinName || !pURL)
			return false;
		/**	opening up a regular window with the size spefied in `position` option	*/
		var p	= this.opts.position;
		var wnd	= this._openDefaultPopup(pURL, uniqueWinName, p.top, p.left, p.width, p.height);
		/*	check if the window has been opened	*/
		if(!wnd)
			return false;
		/*	popunders: handle focus/blur in each browser	*/
		this._blurWindow(wnd);
		return wnd;
	},
	
	/**
	 *	@note	opens link by creating a link (<a>) and fireing a ctrl+click or shift+click event on it
	 		used in chrome
	 *.	@param	String, url to open
	 *	@param	String openMode posible values tab|window|focusedtab
	 *	@param	String wName the name of the newly opened window	- needed to identify a placeholder | added by Tibi
	 									if placeholder not found, name won't be considered
	 *	@return	Boolean true or window object (if possible) on success, false on fail
	 *	@date	2013-Aug-26
	 *	@author	Rudolf Csuha
	*/
	_openWithClickTrigger: function(url, openMode, wName) {
		// basic input validation
		if (!url || !openMode)
			return false;
		// create new "<a>" tag on the fly
		var a		= document.createElement('a');
		// set the href attribute with url param
		a.href		= url;
		/**	@note	if the window should have a name, we assign teh name as the target to make the link open in that window	*/
		if (wName)
			a.target	= wName;
		// add "<a>" tag to the body
		document.body.appendChild(a);
		// set ctrl key to depressed if open mode is tab	
		var ctrlKey	= openMode == 'tab' || openMode == 'focusedtab' ? true : false;
		// set shift key to depressed if open mode is window
		var shiftKey	= openMode == 'window' || openMode == 'focusedtab' ? true : false;
		// create a mouse click event
		var event	= this._getClickEvent(/*ctrl:*/ctrlKey, /*shift:*/shiftKey, /*alt:*/false);
		//Fire the event
		a.dispatchEvent(event);
		// remove newly created "<a>" from body
		a.parentNode.removeChild(a);
		return true;
	},
	
	/**	@note	Buildsup a click event object that can be fired on any DOM element
		@date	2013-NOV-21
		@author	Tibi	*/
	_getClickEvent:	 function(ctrl, shft, alt){
		var event	= document.createEvent("MouseEvents");
		var mOpts = {
			type		: 'click',
			canBubble	: false,
			cancelable	: true,
			view		: window,
			detail		: 0,
			screenX		: 0,
			screenY		: 0,
			clientX		: 0,
			clientY		: 0,
			ctrlKey		: ctrl	? true : false,
			altKey		: alt	? true : false,
			shiftKey	: shft	? true : false,
			metaKey		: true,
			button		: 0,
			relatedTarget	: null
		};
		event.initMouseEvent(	mOpts.type,	mOpts.canBubble,	mOpts.cancelable,	mOpts.view,	mOpts.detail,
					mOpts.screenX,	mOpts.screenY,		mOpts.clientX,		mOpts.clientY,	mOpts.ctrlKey,
					mOpts.altKey,	mOpts.shiftKey,		mOpts.metaKey,		mOpts.button,	mOpts.relatedTarget
				);
		return event;
	}
};