
(doc=>{
	Array.prototype.unique = function() { return this.filter((v, i, a) => a.indexOf(v) === i); };
	Array.prototype.validUnique = function(fn) {
		return this.filter((v, i, a) => (fn ? fn(v) : v) && a.indexOf(v) === i);
	};

	// full = Monday, December 12, 2022 at 12:16:21 PM Central European Standard Time
	// long = December 12, 2022 at 12:16:21 PM GMT+1
	// medium = Dec 12, 2022, 12:16:21 PM
	// short = 12/12/22, 12:16 PM
	let formats = {
//		LT   : {timeStyle: 'short'}, // Issue in Safari
		LT   : {hour: 'numeric', minute: 'numeric'},
		LLL  : {dateStyle: 'long', timeStyle: 'short'}
	};

	// Format momentjs/PHP date formats to Intl.DateTimeFormat
	// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Intl/DateTimeFormat
	Date.prototype.format = function (options, UTC, hourCycle) {
		if (typeof options == 'string') {
			if (formats[options]) {
				options = formats[options];
			} else {
				console.log('Date.format('+options+')');
				options = {};
			}
		}
		if (hourCycle) {
			options.hourCycle = hourCycle;
		}
		let el = doc.documentElement;
		return this.toLocaleString(el.dataset.dateLang || el.lang, options);
	};

	Element.prototype.closestWithin = function(selector, parent) {
		const el = this.closest(selector);
		return (el && el !== parent && parent.contains(el)) ? el : null;
	};

	Element.fromHTML = string => {
		const template = doc.createElement('template');
		template.innerHTML = string.trim();
		return template.content.firstChild;
	};

	/**
	 * https://github.com/tc39/proposal-regex-escaping
	 */
	if (!RegExp.escape){
		RegExp.escape = s => String(s).replace(/[\\^$*+?.()|[\]{}]/g, '\\$&');
	}

	/**
	 * Every time the function is executed,
	 * it will delay the execution with the given amount of milliseconds.
	 */
	if (!Function.prototype.debounce) {
		Function.prototype.debounce = function(ms) {
			let func = this, timer;
			return function(...args) {
				timer && clearTimeout(timer);
				timer = setTimeout(()=>{
					func.apply(this, args);
					timer = 0;
				}, ms);
			};
		};
	}

	/**
	 * No matter how many times the event is executed,
	 * the function will be executed only once, after the given amount of milliseconds.
	 */
	if (!Function.prototype.throttle) {
		Function.prototype.throttle = function(ms) {
			let func = this, timer;
			return function(...args) {
				timer = timer || setTimeout(()=>{
						func.apply(this, args);
						timer = 0;
					}, ms);
			};
		};
	}

})(document);

/**
 * Modified version of https://github.com/Bernardo-Castilho/dragdroptouch
 * This is to only support Firefox Mobile.
 * Because touchstart must call preventDefault() to prevent scrolling
 * but then it doesn't work native in Chrome on Android
 */

(doc => {
	let ua = navigator.userAgent.toLowerCase();
	// Chrome on mobile supports drag & drop
	if (ua.includes('mobile') && ua.includes('gecko/')) {

		let opt = { passive: false, capture: false },

			dropEffect = 'move',
			effectAllowed = 'all',
			data = {},

			dataTransfer,
			dragSource,
			isDragging,
			allowDrop,
			lastTarget,
			lastTouch,
			holdInterval,

			img;

/*
		class DataTransferItem
		{
			get kind() { return 'string'; }
		}
*/
		/** https://developer.mozilla.org/en-US/docs/Web/API/DataTransfer */
		class DataTransfer
		{
			get dropEffect() { return dropEffect; }
			set dropEffect(value) { dropEffect = value; }

			get effectAllowed() { return effectAllowed; }
			set effectAllowed(value) { effectAllowed = value; }

			get files() { return []; }
			get items() { return []; } // DataTransferItemList
			get types() { return Object.keys(data); }

			clearData(type) {
				if (type != null) {
					delete data[type];
				} else {
					data = {};
				}
			}

			getData(type) {
				return data[type] || '';
			}

			setData(type, value) {
				data[type] = value;
			}

			constructor() {
				this.setDragImage = setDragImage;
			}
		}

		const
		htmlDrag = b => doc.documentElement.classList.toggle('firefox-drag', b),

		setDragImage = (src, xOffset, yOffset) => {
			img?.remove();
			if (src) {
				// create drag image from custom element or drag source
				img = src.cloneNode(true);
				copyStyle(src, img);
				img._x = xOffset ?? src.clientWidth / 2;
				img._y = yOffset ?? src.clientHeight / 2;
			}
		},

		// clear all members
		reset = () => {
			if (dragSource) {
				clearInterval(holdInterval);
				// dispose of drag image element
				img?.remove();
				isDragging && dispatchEvent(lastTouch, 'dragend', dragSource);
				img = dragSource = lastTouch = lastTarget = dataTransfer = holdInterval = null;
				isDragging = allowDrop = false;
				htmlDrag(false);
			}
		},

		// get point for a touch event
		getPoint = e => {
			e = e.touches ? e.touches[0] : e;
			return { x: e.clientX, y: e.clientY };
		},

		touchend = e => {
			if (dragSource) {
				// finish dragging
				allowDrop && 'touchcancel' !== e.type && dispatchEvent(lastTouch, 'drop', lastTarget);
				reset();
			}
		},

		// get the element at a given touch event
		getTarget = pt => {
			let el = doc.elementFromPoint(pt.x, pt.y);
			while (el && getComputedStyle(el).pointerEvents == 'none') {
				el = el.parentElement;
			}
			return el;
		},

		// move the drag image element
		moveImage = pt => {
			requestAnimationFrame(() => {
				if (img) {
					img.style.left = Math.round(pt.x - img._x) + 'px';
					img.style.top = Math.round(pt.y - img._y) + 'px';
				}
			});
		},

		copyStyle = (src, dst) => {
			// remove potentially troublesome attributes
			['id','class','style','draggable'].forEach(att => dst.removeAttribute(att));
			// copy canvas content
			if (src instanceof HTMLCanvasElement) {
				let cSrc = src, cDst = dst;
				cDst.width = cSrc.width;
				cDst.height = cSrc.height;
				cDst.getContext('2d').drawImage(cSrc, 0, 0);
			}
			// copy style (without transitions)
			let cs = getComputedStyle(src);
			Object.entries(cs).forEach(([key, value]) => key.includes('transition') || (dst.style[key] = value));
			dst.style.pointerEvents = 'none';
			// and repeat for all children
			let i = src.children.length;
			while (i--) copyStyle(src.children[i], dst.children[i]);
		},

		// return false when cancelled
		dispatchEvent = (e, type, target) => {
			if (e && target) {
				let evt = new Event(type, {bubbles:true,cancelable:true});
				evt.button = 0;
				evt.buttons = 1;
				// copy event properties into new event
				['altKey','ctrlKey','metaKey','shiftKey'].forEach(k => evt[k] = e[k]);
				let src = e.touches ? e.touches[0] : e;
				['pageX','pageY','clientX','clientY','screenX','screenY','offsetX','offsetY'].forEach(k => evt[k] = src[k]);
				if (dragSource) {
					evt.dataTransfer = dataTransfer;
				}
				return target.dispatchEvent(evt);
			}
			return false;
		};

/*
		doc.addEventListener('pointerdown', e => {
			doc.addEventListener('pointermove', e => {
				e.clientX
			});
			doc.setPointerCapture(e.pointerId);
		});
		doc.addEventListener('pointerup', e => {
			doc.releasePointerCapture(e.pointerId);
		});
*/
		doc.addEventListener('touchstart', e => {
			// clear all variables
			reset();
			// ignore events that have been handled or that involve more than one touch
			if (e && !e.defaultPrevented && e.touches && e.touches.length < 2) {
				// get nearest draggable element
				dragSource = e.target.closest('[draggable]');
				if (dragSource) {
					// get ready to start dragging
					lastTouch = e;
//					dragSource.style.userSelect = 'none';

					// 1000 ms to wait, chrome on android triggers dragstart in 600
					holdInterval = setTimeout(() => {
						// start dragging
						dataTransfer = new DataTransfer();
						if ((isDragging = dispatchEvent(e, 'dragstart', dragSource))) {
							htmlDrag(true);

							let pt = getPoint(e);

							// create drag image from custom element or drag source
							img || setDragImage(dragSource);
							let style = img.style;
							style.top = style.left = '-9999px';
							style.position = 'fixed';
							style.pointerEvents = 'none';
							style.zIndex = '999999999';
							// add image to document
							moveImage(pt);
							doc.body.append(img);

							dispatchEvent(e, 'dragenter', getTarget(pt));
						} else {
							reset();
						}
					}, 1000);
				}
			}
		}, opt);

		doc.addEventListener('touchmove', e => {
			if (isDragging) {
				// continue dragging
				let pt = getPoint(e),
					target = getTarget(pt);
				lastTouch = e;
				if (target != lastTarget) {
					dispatchEvent(e, 'dragleave', lastTarget);
					dispatchEvent(e, 'dragenter', target);
					lastTarget = target;
				}
				moveImage(pt);
				allowDrop = !dispatchEvent(e, 'dragover', target);
			} else {
				reset();
			}
		}, opt);

		doc.addEventListener('touchend', touchend);
		doc.addEventListener('touchcancel', touchend);
	}

})(document);


(win => {

let
	scope = {},
	_scope = 'all';

const
	doc = document,
	// On Mac we use ⌘ else the Ctrl key
	meta = /Mac OS X/.test(navigator.userAgent) ? 'meta' : 'ctrl',
	_scopes = {
		all: {}
	},
	toArray = v => Array.isArray(v) ? v : v.split(/\s*,\s*/),

	exec = (event, cmd) => {
		try {
			// call the handler and stop the event if neccessary
			if (!event.defaultPrevented && cmd(event) === false) {
				event.preventDefault();
				event.stopPropagation();
			}
		} catch (e) {
			console.error(e);
		}
	},

	shortcuts = {
		on: () => doc.addEventListener('keydown', keydown),
		off: () => doc.removeEventListener('keydown', keydown),
		add: (keys, modifiers, scopes, method) => {
			if (null == method) {
				method = scopes;
				scopes = 'all';
			}
			toArray(scopes).forEach(scope => {
				if (!_scopes[scope]) {
					_scopes[scope] = {};
				}
				toArray(keys).forEach(key => {
					key = key.toLowerCase();
					if (!_scopes[scope][key]) {
						_scopes[scope][key] = {};
					}
					modifiers = toArray(modifiers)
						.map(key => 'meta' == key ? meta : key)
						.unique().sort().join('+');
					if (!_scopes[scope][key][modifiers]) {
						_scopes[scope][key][modifiers] = [];
					}
					_scopes[scope][key][modifiers].push(method);
				});
			});
		},
		setScope: value => {
			_scope = value || 'all';
			scope = _scopes[_scope] || {};
			console.log('Shortcuts scope set to: ' + _scope);
		},
		getScope: () => _scope,
		getMetaKey: () => 'meta' === meta ? '⌘' : 'Ctrl'
	},

	keydown = event => {
		let key = (event.key || '').toLowerCase().replace(' ','space'),
			modifiers = ['alt','ctrl','meta','shift'].filter(v => event[v+'Key']).join('+');
		scope[key]?.[modifiers]?.forEach(cmd => exec(event, cmd));
		!event.defaultPrevented && _scope !== 'all' && _scopes.all[key]?.[modifiers]?.forEach(cmd => exec(event, cmd));
	};

win.shortcuts = shortcuts;

shortcuts.on();

})(this);

/*!!
 * Hasher <http://github.com/millermedeiros/hasher>
 * @author Miller Medeiros
 * @version 1.1.2 (2012/10/31 03:19 PM)
 * Released under the MIT License
 */

(global => {

    //--------------------------------------------------------------------------------------
    // Private
    //--------------------------------------------------------------------------------------

    const
    _hashValRegexp = /#(.*)$/,
    _hashRegexp = /^[#/]+/,
    _hashTrim = /^\/+/g,
    _trimHash = hash => hash?.replace(_hashTrim, '') || '',
    _getWindowHash = () => {
        //parsed full URL instead of getting window.location.hash because Firefox decode hash value (and all the other browsers don't)
        var result = _hashValRegexp.exec( location.href );
        return result?.[1] ? decodeURIComponent(result[1]) : '';
    },
    _registerChange = newHash => {
        if (_hash !== newHash) {
            var oldHash = _hash;
            _hash = newHash; //should come before event dispatch to make sure user can get proper value inside event handler
            _dispatch(_trimHash(newHash), _trimHash(oldHash));
        }
    },
    _setHash = (path, replace) => {
        path = path ? '/' + path.replace(_hashRegexp, '') : path;
        if (path !== _hash){
            // we should store raw value
            _registerChange(path);
            if (path === _hash) {
                path = '#' + encodeURI(path)
                // we check if path is still === _hash to avoid error in
                // case of multiple consecutive redirects [issue #39]
                replace
                    ? location.replace(path)
                    : (location.hash = path);
            }
        }
    },
    _dispatch = (...args) => hasher.active && _bindings.forEach(callback => callback(...args)),

    //--------------------------------------------------------------------------------------
    // Public (API)
    //--------------------------------------------------------------------------------------

    hasher = /** @lends hasher */ {
        clear : () => {
            _bindings = [];
            hasher.active = true;
        },

        /**
         * Signal dispatched when hash value changes.
         * - pass current hash as 1st parameter to listeners and previous hash value as 2nd parameter.
         * @type signals.Signal
         */
        active : true,
        add : callback => _bindings.push(callback),

        /**
         * Start listening/dispatching changes in the hash/history.
         * <ul>
         *   <li>hasher won't dispatch CHANGE events by manually typing a new value or pressing the back/forward buttons before calling this method.</li>
         * </ul>
         */
        init : () => _dispatch(_trimHash(_hash)),

        /**
         * Set Hash value, generating a new history record.
         * @param {...string} path    Hash value without '#'.
         * @example hasher.setHash('lorem/ipsum/dolor') -> '#/lorem/ipsum/dolor'
         */
        setHash : path => _setHash(path),

        /**
         * Set Hash value without keeping previous hash on the history record.
         * @param {...string} path    Hash value without '#'.
         * @example hasher.replaceHash('lorem/ipsum/dolor') -> '#/lorem/ipsum/dolor'
         */
        replaceHash : path => _setHash(path, true)
    };

    var _hash = _getWindowHash(),
        _bindings = [];

    addEventListener('hashchange', () => _registerChange(_getWindowHash()));

    global.hasher = hasher;
})(this);

/** @license
 * Crossroads.js <http://millermedeiros.github.com/crossroads.js>
 * Released under the MIT license
 * Author: Miller Medeiros
 * Version: 0.7.1 - Build: 93 (2012/02/02 09:29 AM)
 */

(global => {

    const isFunction = obj => typeof obj === 'function';

    // Crossroads --------
    //====================

    global.Crossroads = class Crossroads {

        constructor() {
            this._routes = [];
        }

        addRoute(pattern, callback) {
            var route = new Route(pattern, callback, this);
            this._routes.push(route);
            return route;
        }

        parse(request) {
            request = request || '';
            var i = 0,
                routes = this._routes,
                n = routes.length,
                route;
            //should be decrement loop since higher priorities are added at the end of array
            while (n--) {
                route = routes[n];
                if ((!i || route.greedy) && route.match(request)) {
                    route.callback?.(...route._getParamsArray(request));
                    ++i;
                }
            }
        }
    }

    // Route --------------
    //=====================

    class Route {

        constructor(pattern, callback, router) {
            var isRegexPattern = pattern instanceof RegExp;
            Object.assign(this, {
                greedy: false,
                rules: {},
                _router: router,
                _pattern: pattern,
                _paramsIds: isRegexPattern ? null : captureVals(PARAMS_REGEXP, pattern),
                _optionalParamsIds: isRegexPattern ? null : captureVals(OPTIONAL_PARAMS_REGEXP, pattern),
                _matchRegexp: isRegexPattern ? pattern : compilePattern(pattern),
                callback: isFunction(callback) ? callback : null
            });
        }

        match(request) {
            // validate params even if regexp.
            var values = this._getParamsObject(request);
            return this._matchRegexp.test(request)
             && 0 == Object.entries(this.rules).filter(([key, validationRule]) => {
                var val = values[key],
                    isValid = false;
                if (key === 'normalize_'
                 || (val == null && this._optionalParamsIds?.includes(key))) {
                    isValid = true;
                }
                else if (validationRule instanceof RegExp) {
                    isValid = validationRule.test(val);
                }
                else if (Array.isArray(validationRule)) {
                    isValid = validationRule.includes(val);
                }
                else if (isFunction(validationRule)) {
                    isValid = validationRule(val, request, values);
                }
                // fail silently if validationRule is from an unsupported type
                return !isValid;
            }).length;
        }

        _getParamsObject(request) {
            var values = getParamValues(request, this._matchRegexp) || [],
                n = values.length;
            if (this._paramsIds) {
                while (n--) {
                    values[this._paramsIds[n]] = values[n];
                }
            }
            return values;
        }

        _getParamsArray(request) {
            var norm = this.rules.normalize_;
            return isFunction(norm)
                ? norm(request, this._getParamsObject(request))
                : getParamValues(request, this._matchRegexp);
        }

    }



    // Pattern Lexer ------
    //=====================

    const
        ESCAPE_CHARS_REGEXP = /[\\.+*?^$[\](){}/'#]/g, //match chars that should be escaped on string regexp
        UNNECESSARY_SLASHES_REGEXP = /\/$/g, //trailing slash
        OPTIONAL_SLASHES_REGEXP = /([:}]|\w(?=\/))\/?(:)/g, //slash between `::` or `}:` or `\w:`. $1 = before, $2 = after
        REQUIRED_SLASHES_REGEXP = /([:}])\/?(\{)/g, //used to insert slash between `:{` and `}{`

        REQUIRED_PARAMS_REGEXP = /\{([^}]+)\}/g, //match everything between `{ }`
        OPTIONAL_PARAMS_REGEXP = /:([^:]+):/g, //match everything between `: :`
        PARAMS_REGEXP = /(?:\{|:)([^}:]+)(?:\}|:)/g, //capture everything between `{ }` or `: :`

        //used to save params during compile (avoid escaping things that
        //shouldn't be escaped).
        SAVE_REQUIRED_PARAMS = '__CR_RP__',
        SAVE_OPTIONAL_PARAMS = '__CR_OP__',
        SAVE_REQUIRED_SLASHES = '__CR_RS__',
        SAVE_OPTIONAL_SLASHES = '__CR_OS__',
        SAVED_REQUIRED_REGEXP = new RegExp(SAVE_REQUIRED_PARAMS, 'g'),
        SAVED_OPTIONAL_REGEXP = new RegExp(SAVE_OPTIONAL_PARAMS, 'g'),
        SAVED_OPTIONAL_SLASHES_REGEXP = new RegExp(SAVE_OPTIONAL_SLASHES, 'g'),
        SAVED_REQUIRED_SLASHES_REGEXP = new RegExp(SAVE_REQUIRED_SLASHES, 'g'),

        captureVals = (regex, pattern) => {
            var vals = [], match;
            while ((match = regex.exec(pattern))) {
                vals.push(match[1]);
            }
            return vals;
        },

        getParamValues = (request, regexp) => {
            var vals = regexp.exec(request);
            vals?.shift();
            return vals;
        },
        compilePattern = pattern => {
            return new RegExp('^' + (pattern
                ? pattern
                    // tokenize, save chars that shouldn't be escaped
                    .replace(UNNECESSARY_SLASHES_REGEXP, '')
                    .replace(OPTIONAL_SLASHES_REGEXP, '$1'+ SAVE_OPTIONAL_SLASHES +'$2')
                    .replace(REQUIRED_SLASHES_REGEXP, '$1'+ SAVE_REQUIRED_SLASHES +'$2')
                    .replace(OPTIONAL_PARAMS_REGEXP, SAVE_OPTIONAL_PARAMS)
                    .replace(REQUIRED_PARAMS_REGEXP, SAVE_REQUIRED_PARAMS)
                    .replace(ESCAPE_CHARS_REGEXP, '\\$&')
                    // untokenize
                    .replace(SAVED_OPTIONAL_SLASHES_REGEXP, '\\/?')
                    .replace(SAVED_REQUIRED_SLASHES_REGEXP, '\\/')
                    .replace(SAVED_OPTIONAL_REGEXP, '([^\\/]+)?/?')
                    .replace(SAVED_REQUIRED_REGEXP, '([^\\/]+)')
                : ''
            ) + '/?$'); //trailing slash is optional
        };

})(this);

/* RainLoop Webmail (c) RainLoop Team | MIT */
(doc => {
	const
		defined = v => undefined !== v,
		/**
		 * @param {*} aItems
		 * @param {Function} fFileCallback
		 * @param {number=} iLimit = 20
		 */
		getDataFromFiles = (aItems, fFileCallback, iLimit) =>
		{
			if (aItems?.length)
			{
				let
					oFile,
					iCount = 0,
					bCallLimit = false
				;

				[...aItems].forEach(oItem => {
					if (oItem) {
						if (iLimit && iLimit < ++iCount) {
							if (!bCallLimit) {
								bCallLimit = true;
//								fLimitCallback(iLimit);
							}
						} else {
							oFile = getDataFromFile(oItem);
							oFile && fFileCallback(oFile);
						}
					}
				});
			}
		},

		addEventListeners = (element, obj) =>
			Object.entries(obj).forEach(([key, value]) => element.addEventListener(key, value)),

		/**
		 * @param {*} oFile
		 * @return {Object}
		 */
		getDataFromFile = oFile =>
		{
			return oFile.size
				? {
					fileName: (oFile.name || '').replace(/^.*\/([^/]*)$/, '$1'),
					size: oFile.size,
					file: oFile
				}
				: null; // Folder
		},

		eventContainsFiles = oEvent => oEvent.dataTransfer.types.includes('Files');

	class Queue extends Array
	{
		push(fn, ...args) {
			super.push([fn, args]);
			this.call();
		}
		call() {
			if (!this.running) {
				this.running = true;
				let f;
				while ((f = this.shift())) f[0](...f[1]);
				this.running = false;
			}
		}
	}

	/**
	 * @constructor
	 * @param {Object=} options
	 */
	class Jua
	{
		constructor(options)
		{
			let timer,
				el = options.clickElement;

			const self = this,
				timerStart = fn => {
					timerStop();
					timer = setTimeout(fn, 200);
				},
				timerStop = () => {
					timer && clearTimeout(timer);
					timer = 0;
				};

			self.oEvents = {
				onSelect: null,
				onStart: null,
				onComplete: null,
				onProgress: null,
				onDragEnter: null,
				onDragLeave: null,
				onBodyDragEnter: null,
				onBodyDragLeave: null
			};

			self.oXhrs = {};
			self.oUids = {};
			self.options = Object.assign({
					action: '',
					name: 'uploader',
					limit: 0,
//					clickElement:
//					dragAndDropElement:
				}, options || {});
			self.oQueue = new Queue();

			// clickElement
			if (el) {
				el.style.position = 'relative';
				el.style.overflow = 'hidden';
				if ('inline' === el.style.display) {
					el.style.display = 'inline-block';
				}

				self.generateNewInput(el);
			}

			el = options.dragAndDropElement;
			if (el) {
				addEventListeners(doc, {
					dragover: oEvent => {
						if (eventContainsFiles(oEvent)) {
							timerStop();
							if (el.contains(oEvent.target)) {
								oEvent.dataTransfer.dropEffect = 'copy';
								oEvent.stopPropagation();
							} else {
								oEvent.dataTransfer.dropEffect = 'none';
							}
							oEvent.preventDefault();
						}
					},
					dragenter: oEvent => {
						if (eventContainsFiles(oEvent)) {
							timerStop();
							oEvent.preventDefault();
							self.runEvent('onBodyDragEnter', oEvent);
							if (el.contains(oEvent.target)) {
								timerStop();
								self.runEvent('onDragEnter', el, oEvent);
							}
						}
					},
					dragleave: oEvent => {
						if (eventContainsFiles(oEvent)) {
							let oRelatedTarget = doc.elementFromPoint(oEvent.clientX, oEvent.clientY);
							if (!oRelatedTarget || !el.contains(oRelatedTarget)) {
								self.runEvent('onDragLeave', el, oEvent);
							}
							timerStart(() => self.runEvent('onBodyDragLeave', oEvent))
						}
					},
					drop: oEvent => {
						if (eventContainsFiles(oEvent)) {
							timerStop();
							oEvent.preventDefault();
							if (el.contains(oEvent.target)) {
								getDataFromFiles(
									oEvent.files || oEvent.dataTransfer.files,
									oFile => {
										if (oFile) {
											self.addFile(oFile);
										}
									},
									self.options.limit
								);
							}
						}
						self.runEvent('onDragLeave', oEvent);
						self.runEvent('onBodyDragLeave', oEvent);
					}
				});
			}
		}

		/**
		 * @param {string} sName
		 * @param {Function} fFunc
		 */
		on(sName, fFunc)
		{
			this.oEvents[sName] = fFunc;
			return this;
		}

		/**
		 * @param {string} sName
		 */
		runEvent(sName, ...aArgs)
		{
			this.oEvents[sName]?.apply(null, aArgs);
		}

		/**
		 * @param {string} sName
		 */
		getEvent(sName)
		{
			return this.oEvents[sName] || null;
		}

		/**
		 * @param {Object} oFileInfo
		 */
		addFile(oFileInfo)
		{
			const sUid = 'jua-uid-' + Jua.randomId(16) + '-' + (Date.now().toString()),
				fOnSelect = this.getEvent('onSelect');
			if (oFileInfo && (!fOnSelect || (false !== fOnSelect(sUid, oFileInfo))))
			{
				this.oUids[sUid] = true;
				this.oQueue.push((...args) => this.uploadTask(...args), sUid, oFileInfo);
			}
			else
			{
				this.cancel(sUid);
			}
		}

		/**
		 * @param {string} sUid
		 * @param {?} oFileInfo
		 */
		uploadTask(sUid, oFileInfo)
		{
			if (false === this.oUids[sUid] || !oFileInfo || !oFileInfo.file)
			{
				return false;
			}

			try
			{
				const
					self = this,
					oXhr = new XMLHttpRequest(),
					oFormData = new FormData(),
					sAction = this.options.action,
					fStartFunction = this.getEvent('onStart'),
					fProgressFunction = this.getEvent('onProgress')
				;

				oXhr.open('POST', sAction, true);

				if (fProgressFunction && oXhr.upload)
				{
					oXhr.upload.onprogress = oEvent => {
						if (oEvent && oEvent.lengthComputable && defined(oEvent.loaded) && defined(oEvent.total))
						{
							fProgressFunction(sUid, oEvent.loaded, oEvent.total);
						}
					};
				}

				oXhr.onreadystatechange = () => {
					if (4 === oXhr.readyState)
					{
						delete self.oXhrs[sUid];
						let bResult = false,
							oResult = null;
						if (200 === oXhr.status)
						{
							try
							{
								oResult = JSON.parse(oXhr.responseText);
								bResult = true;
							}
							catch (e)
							{
								console.error(e);
							}
						}
						this.getEvent('onComplete')(sUid, bResult, oResult);
					}
				};

				fStartFunction && fStartFunction(sUid);

				oFormData.append(this.options.name, oFileInfo.file);

				oXhr.send(oFormData);

				this.oXhrs[sUid] = oXhr;
				return true;
			}
			catch (oError)
			{
				console.error(oError)
			}

			return false;
		}

		generateNewInput(oClickElement)
		{
			if (oClickElement)
			{
				const self = this,
					limit = self.options.limit,
					oInput = doc.createElement('input'),
					onClick = ()=>oInput.click();

				oInput.type = 'file';
				oInput.tabIndex = -1;
				oInput.style.display = 'none';
				oInput.multiple = 1 != limit;

				oClickElement.addEventListener('click', onClick);

				oInput.addEventListener('input', () => {
					const fFileCallback = oFile => {
						self.addFile(oFile);
						setTimeout(() => {
							oInput.remove();
							oClickElement.removeEventListener('click', onClick);
							self.generateNewInput(oClickElement);
						}, 10);
					};
					if (oInput.files?.length) {
						getDataFromFiles(oInput.files, fFileCallback, limit);
					} else {
						fFileCallback({
							fileName: oInput.value.split(/\\\//).pop(),
							size: null,
							file : null
						});
					}
				});
			}
		}

		/**
		 * @param {string} sUid
		 */
		cancel(sUid)
		{
			this.oUids[sUid] = false;
			if (this.oXhrs[sUid])
			{
				try
				{
					this.oXhrs[sUid].abort && this.oXhrs[sUid].abort();
				}
				catch (oError)
				{
					console.error(oError);
				}

				delete this.oXhrs[sUid];
			}
		}
	}

	Jua.randomId = len => {
		let arr = new Uint8Array((len || 32) / 2);
		crypto.getRandomValues(arr);
		return arr.map(dec => dec.toString(16).padStart(2,'0')).join('');
	}

	this.Jua = Jua;

})(document);

/*!
	* Native JavaScript for Bootstrap v3.0.10 (https://thednp.github.io/bootstrap.native/)
	* Copyright 2015-2020 © dnp_theme
	* Licensed under MIT (https://github.com/thednp/bootstrap.native/blob/master/LICENSE)
	*/

(doc => {
	const
		setFocus = element => element.focus ? element.focus() : element.setActive(),
		isArrow = e => 'ArrowUp' === e.key || 'ArrowDown' === e.key;

	this.BSN = {
		Dropdown: function(toggleBtn) {
			let menu, menuItems = [];
			const self = this,
				parent = toggleBtn.parentNode,
				preventEmptyAnchor = e => {
					const t = e.target;
					('#' === (t.href || t.parentNode?.href)?.slice(-1)) && e.preventDefault();
				},
				open = bool => {
					menu?.classList.toggle('show', bool);
					parent.classList.toggle('show', bool);
					toggleBtn.setAttribute('aria-expanded', bool);
					toggleBtn.open = bool;
					if (bool) {
						toggleBtn.removeEventListener('click',clickHandler);
					} else {
						setTimeout(() => toggleBtn.addEventListener('click',clickHandler), 1);
					}
				},
				toggleEvents = () => {
					const action = (toggleBtn.open ? 'add' : 'remove') + 'EventListener';
					doc[action]('click',dismissHandler);
					doc[action]('keydown',preventScroll);
					doc[action]('keyup',keyHandler);
					doc[action]('focus',dismissHandler);
				},
				dismissHandler = e => {
					const eventTarget = e.target;
					if ((!menu.contains(eventTarget) && !toggleBtn.contains(eventTarget)) || e.type !== 'focus') {
						self.hide();
						preventEmptyAnchor(e);
					}
				},
				clickHandler = e => {
					self.show();
					preventEmptyAnchor(e);
				},
				preventScroll = e => isArrow(e) && e.preventDefault(),
				keyHandler = e => {
					if ('Escape' === e.key) {
						self.toggle();
					} else if (isArrow(e)) {
						let activeItem = doc.activeElement,
							isMenuButton = activeItem === toggleBtn,
							idx = isMenuButton ? 0 : menuItems.indexOf(activeItem);
						if (parent.contains(activeItem)) {
							if (!isMenuButton) {
								idx = 'ArrowUp' === e.key
									? (idx > 1 ? idx-1 : 0)
									: (idx < menuItems.length-1 ? idx+1 : idx);
							}
							menuItems[idx] && setFocus(menuItems[idx]);
						} else {
							console.log('activeElement not in menu');
						}
					}
				};
			self.show = () => {
				menu = parent.querySelector('.dropdown-menu');
				menuItems = [...menu.querySelectorAll('A')].filter(item => 'none' != item.parentNode.style.display);
				!('tabindex' in menu) && menu.setAttribute('tabindex', '0');
				open(true);
				setTimeout(() => {
					setFocus( menu.getElementsByTagName('INPUT')[0] || toggleBtn );
					toggleEvents();
				},1);
			};
			self.hide = () => {
				open(false);
				toggleEvents();
				setFocus(toggleBtn);
			};
			self.toggle = () => toggleBtn.open ? self.hide() : self.show();
			open(false);
			toggleBtn.Dropdown = self;
		}
	};

})(document);

/*!
 * Knockout JavaScript library v3.5.1-sm
 * (c) The Knockout.js team - http://knockoutjs.com/
 * License: MIT (http://www.opensource.org/licenses/mit-license.php)
 */

(R=>{function L(a,b){return a===b&&a!==Object(a)}function ca(a,b){var d;return()=>{d||(d=setTimeout(()=>{d=0;a()},b))}}function da(a,b){var d;return()=>{clearTimeout(d);d=setTimeout(a,b)}}function ea(a,b){b?.dispose?.()}function fa(a,b){var d=this.Lb,e=d[x];e.X||(this.Ma&&this.va[b]?(d.kb(b,a,this.va[b]),this.va[b]=null,--this.Ma):e.v[b]||d.kb(b,a,e.A?{S:a}:d.Cb(a)),a.ea&&a.Gb())}var J=R.document,M={},c="undefined"!==typeof M?M:{};c.U=(a,b)=>{a=a.split(".");for(var d=c,e=0,g=a.length-1;e<g;e++)d=
d[a[e]];d[a[g]]=b};c.g={extend:(a,b)=>b?Object.assign(a,b):a,K:(a,b)=>a&&Object.entries(a).forEach(d=>b(d[0],d[1])),Qa:a=>[...a.childNodes].forEach(b=>c.removeNode(b)),Wb:a=>{a=[...a];var b=(a[0]?.ownerDocument||J).createElement("div");a.forEach(d=>b.append(c.ha(d)));return b},ua:(a,b)=>Array.prototype.map.call(a,b?d=>c.ha(d.cloneNode(!0)):d=>d.cloneNode(!0)),pa:(a,b)=>{c.g.Qa(a);b&&a.append(...b)},xa:(a,b)=>{if(a.length){for(b=8===b.nodeType&&b.parentNode||b;a.length&&a[0].parentNode!==b;)a.splice(0,
1);for(;1<a.length&&a[a.length-1].parentNode!==b;)--a.length;if(1<a.length){b=a[0];var d=a[a.length-1];for(a.length=0;b!==d;)a.push(b),b=b.nextSibling;a.push(d)}}return a},Bb:a=>null==a?"":a.trim?a.trim():a.toString().replace(/^[\s\xa0]+|[\s\xa0]+$/g,""),Pa:a=>a.ownerDocument.documentElement.contains(1!==a.nodeType?a.parentNode:a),Db:(a,b)=>{if(!a?.nodeType)throw Error("element must be a DOM node when calling triggerEvent");a.dispatchEvent(new Event(b))},h:a=>c.W(a)?a():a,Za:(a,b)=>a.textContent=
c.g.h(b)};c.U("utils",c.g);c.U("unwrap",c.g.h);(()=>{let a=0,b="__ko__"+Date.now(),d=new WeakMap;c.g.l={get:(e,g)=>(d.get(e)||{})[g],set:(e,g,l)=>{d.has(e)?d.get(e)[g]=l:d.set(e,{[g]:l});return l},Ra(e,g,l){return this.get(e,g)||this.set(e,g,l)},clear:e=>d.delete(e),Z:()=>a++ +b}})();c.g.N=(()=>{var a=c.g.l.Z(),b={1:1,8:1,9:1},d={1:1,9:1};const e=(f,h)=>{var k=c.g.l.get(f,a);h&&!k&&(k=new Set,c.g.l.set(f,a,k));return k},g=f=>{var h=e(f);h&&(new Set(h)).forEach(k=>k(f));c.g.l.clear(f);d[f.nodeType]&&
l(f.childNodes,!0)},l=(f,h)=>{for(var k=[],n,p=0;p<f.length;p++)if(!h||8===f[p].nodeType)if(g(k[k.length]=n=f[p]),f[p]!==n)for(;p--&&!k.includes(f[p]););};return{addDisposeCallback:(f,h)=>{if("function"!=typeof h)throw Error("Callback must be a function");e(f,1).add(h)},Ya:(f,h)=>{var k=e(f);k&&(k.delete(h),k.size||c.g.l.set(f,a,null))},ha:f=>{c.u.I(()=>{b[f.nodeType]&&(g(f),d[f.nodeType]&&l(f.getElementsByTagName("*")))});return f},removeNode:f=>{c.ha(f);f.parentNode&&f.parentNode.removeChild(f)}}})();
c.ha=c.g.N.ha;c.removeNode=c.g.N.removeNode;c.U("utils.domNodeDisposal",c.g.N);c.extenders={debounce:(a,b)=>a.Da(d=>da(d,b)),rateLimit:(a,b)=>a.Da(d=>ca(d,b)),notify:(a,b)=>{a.ka="always"==b?null:L}};class ha{constructor(a,b,d){this.S=a;this.eb=b;this.za=d;this.Ha=!1;this.H=this.da=null}dispose(){this.Ha||(this.H&&c.g.N.Ya(this.da,this.H),this.Ha=!0,this.za(),this.S=this.eb=this.za=this.da=this.H=null)}s(a){this.da=a;c.g.N.addDisposeCallback(a,this.H=this.dispose.bind(this))}}c.P=function(){Object.setPrototypeOf(this,
N);N.init(this)};var N={init:a=>{a.R=new Map;a.R.set("change",new Set);a.jb=1},subscribe:function(a,b,d){var e=this;d=d||"change";var g=new ha(e,b?a.bind(b):a,()=>{e.R.get(d).delete(g);e.Ia?.(d)});e.Ja?.(d);e.R.has(d)||e.R.set(d,new Set);e.R.get(d).add(g);return g},B(a,b){b=b||"change";"change"===b&&this.Ea();if(this.na(b)){b="change"===b&&this.Eb||new Set(this.R.get(b));try{c.u.nb(),b.forEach(d=>{d.Ha||d.eb(a)})}finally{c.u.end()}}},ya(){return this.jb},Rb(a){return this.ya()!==a},Ea(){++this.jb},
Da(a){var b=this,d=c.W(b),e,g,l,f,h;b.ra||(b.ra=b.B,b.B=(n,p)=>{p&&"change"!==p?"beforeChange"===p?b.gb(n):b.ra(n,p):b.hb(n)});var k=a(()=>{b.ea=!1;d&&f===b&&(f=b.fb?b.fb():b());var n=g||h&&b.Ba(l,f);h=g=e=!1;n&&b.ra(l=f)});b.hb=(n,p)=>{p&&b.ea||(h=!p);b.Eb=new Set(b.R.get("change"));b.ea=e=!0;f=n;k()};b.gb=n=>{e||(l=n,b.ra(n,"beforeChange"))};b.ib=()=>{h=!0};b.Gb=()=>{b.Ba(l,b.L(!0))&&(g=!0)}},na(a){return(this.R.get(a)||[]).size},Ba(a,b){return!this.ka||!this.ka(a,b)},toString:()=>"[object Object]",
extend:function(a){var b=this;a&&c.g.K(a,(d,e)=>{d=c.extenders[d];"function"==typeof d&&(b=d(b,e)||b)});return b}};c.P.fn=Object.setPrototypeOf(N,Function.prototype);c.Vb=a=>"function"==typeof a?.subscribe&&"function"==typeof a.B;(()=>{let a=[],b,d=0;c.u={nb:e=>{a.push(b);b=e},end:()=>b=a.pop(),zb:e=>{if(b){if(!c.Vb(e))throw Error("Only subscribable things can act as dependencies");b.Jb.call(b.Kb,e,e.Fb||(e.Fb=++d))}},I(e,g,l){try{return a.push(b),b=void 0,e.apply(g,l||[])}finally{b=a.pop()}},ma:()=>
b?.o.ma(),Ca:()=>b?.Ca,o:()=>b?.o}})();const A=Symbol("_latestValue");c.$=a=>{function b(){if(0<arguments.length)return b.Ba(b[A],arguments[0])&&(b.cb(),b[A]=arguments[0],b.valueHasMutated()),this;c.u.zb(b);return b[A]}b[A]=a;Object.defineProperty(b,"length",{get:()=>b[A]?.length});c.P.fn.init(b);return Object.setPrototypeOf(b,E)};var E={toJSON:function(){let a=this[A];return a?.toJSON?.()||a},ka:L,L(){return this[A]},valueHasMutated:function(){this.B(this[A],"spectate");this.B(this[A])},cb(){this.B(this[A],
"beforeChange")}};Object.setPrototypeOf(E,c.P.fn);var D=c.$.Zb="__ko_proto__";E[D]=c.$;c.W=a=>{if((a="function"==typeof a&&a[D])&&a!==E[D]&&a!==c.o.fn[D])throw Error("Invalid object that looks like an observable; possibly from another Knockout instance");return!!a};c.vb=a=>"function"==typeof a&&(a[D]===E[D]||a[D]===c.o.fn[D]&&a.Sb);c.U("observable",c.$);c.U("isObservable",c.W);c.U("observable.fn",E);c.observableArray=a=>{a=a||[];if(!Array.isArray(a))throw Error("The argument passed when initializing an observable array must be an array, or null, or undefined.");
return Object.setPrototypeOf(c.$(a),c.observableArray.fn).extend({trackArrayChanges:!0})};const S=Symbol("IS_OBSERVABLE_ARRAY");c.observableArray.fn=Object.setPrototypeOf({[S]:1,remove:function(a){for(var b=this.L(),d=!1,e="function"!=typeof a||c.W(a)?f=>f===a:a,g=b.length;g--;){var l=b[g];if(e(l)){if(b[g]!==l)throw Error("Array modified during remove; cannot remove item");d||this.cb();d=!0;b.splice(g,1)}}d&&this.valueHasMutated()}},c.$.fn);Object.getOwnPropertyNames(Array.prototype).forEach(a=>{"function"===
typeof Array.prototype[a]&&"constructor"!=a&&("copyWithin fill pop push reverse shift sort splice unshift".split(" ").includes(a)?c.observableArray.fn[a]=function(...b){var d=this.L();this.cb();this.pb(d,a,b);b=d[a](...b);this.valueHasMutated();return b===d?this:b}:c.observableArray.fn[a]=function(...b){return this()[a](...b)})});c.isObservableArray=a=>!(!a||!a[S]);c.extenders.trackArrayChanges=(a,b)=>{function d(){if(k){var q=[].concat(a.L()||[]);if(a.na("arrayChange")){if(!l||1<k)l=c.g.qb(n,q,a.Ka);
var r=l}n=q;l=null;k=0;r?.length&&a.B(r,"arrayChange")}}function e(){g?d():(g=!0,h=a.subscribe(()=>++k,null,"spectate"),n=[].concat(a.L()||[]),l=null,f=a.subscribe(d))}a.Ka={};"object"==typeof b&&c.g.extend(a.Ka,b);a.Ka.sparse=!0;if(!a.pb){var g=!1,l=null,f,h,k=0,n,p=a.Ja,m=a.Ia;a.Ja=q=>{p?.call(a,q);"arrayChange"===q&&e()};a.Ia=q=>{m?.call(a,q);"arrayChange"!==q||a.na("arrayChange")||(f?.dispose(),h?.dispose(),h=f=null,g=!1,n=void 0)};a.pb=(q,r,u)=>{if(g&&!k){var t=[],z=q.length,w=u.length,y=0,B=
(ia,ja,ka)=>t[t.length]={status:ia,value:ja,index:ka};switch(r){case "push":y=z;case "unshift":for(q=0;q<w;++q)B("added",u[q],y+q);break;case "pop":y=z-1;case "shift":z&&B("deleted",q[y],y);break;case "splice":y=Math.min(Math.max(0,0>u[0]?z+u[0]:u[0]),z);z=1===w?z:Math.min(y+(u[1]||0),z);w=y+w-2;r=Math.max(z,w);for(var v=[],F=[],C=2;y<r;++y,++C)y<z&&F.push(B("deleted",q[y],y)),y<w&&v.push(B("added",u[C],y));c.g.tb(F,v);break;default:return}l=t}}}};var x=Symbol("_state");c.o=(a,b)=>{function d(){if(0<
arguments.length){if("function"!==typeof e)throw Error("Cannot write a value to a ko.computed unless you specify a 'write' option. If you wish to read the current value, don't pass any parameters.");e(...arguments);return this}g.X||c.u.zb(d);(g.V||g.A&&d.oa())&&d.T();return g.J}"object"===typeof a?b=a:(b=b||{},a&&(b.read=a));if("function"!=typeof b.read)throw Error("Pass a function that returns the value of the ko.computed");var e=b.write,g={J:void 0,Y:!0,V:!0,Aa:!1,ab:!1,X:!1,Xa:!1,A:!1,yb:b.read,
s:b.s||null,ia:b.ia,Oa:null,v:{},G:0,bc:null};d[x]=g;d.Sb="function"===typeof e;c.P.fn.init(d);Object.setPrototypeOf(d,K);b.pure&&(g.Xa=!0,g.A=!0,c.g.extend(d,la));g.s&&(g.ab=!0,g.s.nodeType||(g.s=null));g.A||d.T();g.s&&d.isActive()&&c.g.N.addDisposeCallback(g.s,g.Oa=()=>{d.dispose()});return d};var K={ka:L,ma(){return this[x].G},Pb(){var a=[];c.g.K(this[x].v,(b,d)=>a[d.fa]=d.S);return a},Sa(a){if(!this[x].G)return!1;var b=this.Pb();return b.includes(a)||!!b.find(d=>d.Sa&&d.Sa(a))},kb(a,b,d){if(this[x].Xa&&
b===this)throw Error("A 'pure' computed must not be called recursively");this[x].v[a]=d;d.fa=this[x].G++;d.ga=b.ya()},oa(){var a,b=this[x].v;for(a in b)if(Object.prototype.hasOwnProperty.call(b,a)){var d=b[a];if(this.qa&&d.S.ea||d.S.Rb(d.ga))return!0}},dc(){this[x].Aa||this.qa?.(!1)},isActive(){var a=this[x];return a.V||0<a.G},fc(){this.ea?this[x].V&&(this[x].Y=!0):this.sb()},Cb(a){return a.subscribe(this.sb,this)},sb(){this.qa?this.qa(!0):this.T(!0)},T(a){var b=this[x],d=b.ia,e=!1;if(!b.Aa&&!b.X){if(b.s&&
!c.g.Pa(b.s)||d?.()){if(!b.ab){this.dispose();return}}else b.ab=!1;try{b.Aa=!0,e=this.Nb(a)}finally{b.Aa=!1}return e}},Nb(a){var b=this[x],d=b.Xa?void 0:!b.G;var e={Lb:this,va:b.v,Ma:b.G};c.u.nb({Kb:e,Jb:fa,o:this,Ca:d});b.v={};b.G=0;a:{try{var g=b.yb();break a}finally{c.u.end(),e.Ma&&!b.A&&c.g.K(e.va,ea),b.Y=b.V=!1}g=void 0}b.G?e=this.Ba(b.J,g):(this.dispose(),e=!0);e&&(b.A?this.Ea():this.B(b.J,"beforeChange"),b.J=g,this.B(b.J,"spectate"),!b.A&&a&&this.B(b.J),this.ib&&this.ib());d&&this.B(b.J,"awake");
return e},L(a){var b=this[x];(b.V&&(a||!b.G)||b.A&&this.oa())&&this.T();return b.J},Da(a){var b=this;c.P.fn.Da.call(b,a);b.fb=()=>{b[x].A||(b[x].Y?b.T():b[x].V=!1);return b[x].J};b.qa=d=>{b.gb(b[x].J);b[x].V=!0;d&&(b[x].Y=!0);b.hb(b,!d)}},dispose:function(){var a=this[x];!a.A&&a.v&&c.g.K(a.v,(b,d)=>d.dispose?.());a.s&&a.Oa&&c.g.N.Ya(a.s,a.Oa);a.v=void 0;a.G=0;a.X=!0;a.Y=!1;a.V=!1;a.A=!1;a.s=void 0;a.ia=void 0;a.yb=void 0}},la={Ja(a){var b=this,d=b[x];if(!d.X&&d.A&&"change"==a){d.A=!1;if(d.Y||b.oa())d.v=
null,d.G=0,b.T()&&b.Ea();else{var e=[];c.g.K(d.v,(g,l)=>e[l.fa]=g);e.forEach((g,l)=>{var f=d.v[g],h=b.Cb(f.S);h.fa=l;h.ga=f.ga;d.v[g]=h});b.oa()&&b.T()&&b.Ea()}d.X||b.B(d.J,"awake")}},Ia(a){var b=this[x];b.X||"change"!=a||this.na("change")||(c.g.K(b.v,(d,e)=>{e.dispose&&(b.v[d]={S:e.S,fa:e.fa,ga:e.ga},e.dispose())}),b.A=!0,this.B(void 0,"asleep"))},ya(){var a=this[x];a.A&&(a.Y||this.oa())&&this.T();return c.P.fn.ya.call(this)}};Object.setPrototypeOf(K,c.P.fn);var O=c.$.Zb;K[O]=c.o;c.o.fn=K;c.U("computed",
c.o);c.isComputed=a=>"function"==typeof a&&a[O]===K[O];c.xb=a=>{if("function"===typeof a)return c.o(a,{pure:!0});a={...a,pure:!0};return c.o(a)};c.C={M:a=>{switch(a.nodeName){case "OPTION":return!0===a.__ko__hasDomDataOptionValue__?c.g.l.get(a,c.i.options.Wa):a.value;case "SELECT":return 0<=a.selectedIndex?c.C.M(a.options[a.selectedIndex]):void 0;default:return a.value}},Fa:(a,b)=>{switch(a.nodeName){case "OPTION":"string"===typeof b?(c.g.l.set(a,c.i.options.Wa,void 0),delete a.__ko__hasDomDataOptionValue__,
a.value=b):(c.g.l.set(a,c.i.options.Wa,b),a.__ko__hasDomDataOptionValue__=!0,a.value="number"===typeof b?b:"");break;case "SELECT":for(var d=-1,e=""===(b??""),g=a.options.length,l;g--;)if(l=c.C.M(a.options[g]),l==b||""===l&&e){d=g;break}if(0<=d||e&&1<a.size)a.selectedIndex=d;break;default:a.value=b??""}}};c.la=(()=>{var a=RegExp("\"(?:\\\\.|[^\"])*\"|'(?:\\\\.|[^'])*'|`(?:\\\\.|[^`])*`|/\\*(?:[^*]|\\*+[^*/])*\\*+/|//.*\n|/(?:\\\\.|[^/])+/w*|[^\\s:,/][^,\"'`{}()/:[\\]]*[^\\s,\"'`{}()/:[\\]]|[^\\s]",
"g"),b=/[\])"'A-Za-z0-9_$]+$/,d={"in":1,"return":1,"typeof":1},e=g=>{g=c.g.Bb(g);123===g.charCodeAt(0)&&(g=g.slice(1,-1));g+="\n,";var l=[],f=g.match(a),h=[],k=0;if(1<f.length){for(var n=0,p;p=f[n++];){var m=p.charCodeAt(0);if(44===m){if(0>=k){l.push(q&&h.length?{key:q,value:h.join("")}:{unknown:q||h.join("")});var q=k=0;h=[];continue}}else if(58===m){if(!k&&!q&&1===h.length){q=h.pop();continue}}else if(47===m&&1<p.length&&(47===p.charCodeAt(1)||42===p.charCodeAt(1)))continue;else 47===m&&n&&1<p.length?
(m=f[n-1].match(b))&&!d[m[0]]&&(g=g.slice(g.indexOf(p)+1),f=g.match(a),n=-1,p="/"):40===m||123===m||91===m?++k:41===m||125===m||93===m?--k:q||h.length||34!==m&&39!==m||(p=p.slice(1,-1));h.push(p)}if(0<k)throw Error("Unbalanced parentheses, braces, or brackets");}return l};return{ec:e,Yb:g=>{var l=[];e(g).forEach(f=>{l.push("'"+(f.key||f.unknown)+"':function(){return "+(f.value+" }"))});return l.join(",")},cc:(g,l)=>-1<g.findIndex(f=>f.key==l),Ga:(g,l,f,h,k)=>{if(g&&c.W(g))!c.vb(g)||k&&g.L()===h||
g(h);else throw Error(`${f} , must be observable`);}}})();(()=>{function a(f){return 8==f.nodeType&&e.test(f.nodeValue)}function b(f){return 8==f.nodeType&&g.test(f.nodeValue)}function d(f,h){for(var k=f,n=1,p=[];k=k.nextSibling;){if(b(k)&&(c.g.l.set(k,l,!0),!--n))return p;p.push(k);a(k)&&++n}if(!h)throw Error("Cannot find closing comment tag to match: "+f.nodeValue);return null}var e=/^\s*ko(?:\s+([\s\S]+))?\s*$/,g=/^\s*\/ko\s*$/,l="__ko_matchedEndComment__";c.m={aa:{},childNodes:f=>a(f)?d(f):f.childNodes,
ja:f=>{a(f)?(f=d(f))&&[...f].forEach(h=>c.removeNode(h)):c.g.Qa(f)},pa:(f,h)=>{a(f)?(c.m.ja(f),f.after(...h)):c.g.pa(f,h)},prepend:(f,h)=>{a(f)?f.nextSibling.before(h):f.prepend(h)},Ub:(f,h,k)=>{k?k.after(h):c.m.prepend(f,h)},firstChild:f=>{if(a(f))return f=f.nextSibling,!f||b(f)?null:f;let h=f.firstChild;if(h&&b(h))throw Error("Found invalid end comment, as the first child of "+f);return h},nextSibling:f=>{if(a(f)){var h=d(f,void 0);f=h?(h.length?h[h.length-1]:f).nextSibling:null}if((h=f.nextSibling)&&
b(h)){if(b(h)&&!c.g.l.get(h,l))throw Error("Found end comment without a matching opening comment, as child of "+f);return null}return h},Qb:a,ac:f=>(f=f.nodeValue.match(e))?f[1]:null}})();const T=new Map;c.ob=new class{Xb(a){switch(a.nodeType){case 1:return null!=a.getAttribute("data-bind");case 8:return c.m.Qb(a)}return!1}Ob(a,b){a:{switch(a.nodeType){case 1:var d=a.getAttribute("data-bind");break a;case 8:d=c.m.ac(a);break a}d=null}if(d)try{let g=T.get(d);if(!g){var e="with($context){with($data||{}){return{"+
c.la.Yb(d)+"}}}";g=new Function("$context","$element",e);T.set(d,g)}return g(b,a)}catch(g){throw g.message="Unable to parse bindings.\nBindings value: "+d+"\nMessage: "+g.message,g;}return null}};const G=Symbol("_subscribable"),H=Symbol("_ancestorBindingInfo"),U=Symbol("_dataDependency"),V={},I=c.g.l.Z();c.i={};c.ba=class{constructor(a,b,d,e){var g=this,l=a===V,f=l?void 0:a,h="function"==typeof f&&!c.W(f),k=e?.dataDependency;a=()=>{var p=h?f():f;p=c.g.h(p);b?(c.g.extend(g,b),H in b&&(g[H]=b[H])):
(g.$root=p,g.ko=c);g[G]=n;l?p=g.$data:g.$data=p;d?.(g,b,p);if(b?.[G]&&!c.u.o().Sa(b[G]))b[G]();k&&(g[U]=k);return g.$data};if(e?.exportDependencies)a();else{var n=c.xb(a);n.L();n.isActive()?n.ka=null:g[G]=void 0}}createChildContext(a,b){return new c.ba(a,this,(d,e)=>{d.$parent=e.$data;b.extend?.(d)},b)}extend(a,b){return new c.ba(V,this,d=>c.g.extend(d,"function"==typeof a?a(d):a),b)}};const W=a=>{a=c.g.l.get(a,I);var b=a?.D;b&&(a.D=null,b.wb())};class ma{constructor(a,b,d){this.H=a;this.da=b;this.ta=
new Set;this.F=!1;b.D||c.g.N.addDisposeCallback(a,W);d?.D&&(d.D.ta.add(a),this.za=d)}wb(){this.za?.D?.Mb(this.H)}Mb(a){this.ta.delete(a);this.ta.size||this.rb?.()}rb(){this.F=!0;this.da.D&&!this.ta.size&&(this.da.D=null,c.g.N.Ya(this.H,W),c.j.notify(this.H,c.j.ca),this.wb())}}c.j={F:"childrenComplete",ca:"descendantsComplete",subscribe:(a,b,d,e,g)=>{var l=c.g.l.Ra(a,I,{});l.wa||(l.wa=new c.P);g?.notifyImmediately&&l.Va[b]&&c.u.I(d,e,[a]);return l.wa.subscribe(d,e,b)},notify:(a,b)=>{var d=c.g.l.get(a,
I);if(d&&(d.Va[b]=!0,d.wa?.B(a,b),b==c.j.F))if(d.D)d.D.rb();else if(void 0===d.D&&d.wa?.na(c.j.ca))throw Error("descendantsComplete event not supported for bindings on this node");},$a:(a,b)=>{var d=c.g.l.Ra(a,I,{});d.D||(d.D=new ma(a,d,b[H]));return b[H]==d?b:b.extend(e=>{e[H]=d})}};const Y=(a,b)=>{for(var d,e=c.m.firstChild(b);d=e;)e=c.m.nextSibling(d),X(a,d);c.j.notify(b,c.j.F)},X=(a,b)=>{var d=a;if(1===b.nodeType||c.ob.Xb(b))d=Z(b,null,a);d&&!b.matches?.("SCRIPT,TEXTAREA,TEMPLATE")&&Y(d,b)},na=
a=>{var b=[],d={},e=[],g=l=>{if(!d[l]){var f=c.i[l];f&&(f.after&&(e.push(l),f.after.forEach(h=>{if(a[h]){if(e.includes(h))throw Error("Cannot combine the following bindings, because they have a cyclic dependency: "+e.join(", "));g(h)}}),e.length--),b.push({key:l,ub:f}));d[l]=!0}};c.g.K(a,g);return b},Z=(a,b,d)=>{var e=c.g.l.Ra(a,I,{}),g=e.Hb;if(!b){if(g)throw Error("You cannot apply bindings multiple times to the same element.");e.Hb=!0}g||(e.context=d);e.Va||(e.Va={});if(b&&"function"!==typeof b)var l=
b;else{var f=c.o(()=>{if(l=b?b(d,a):c.ob.Ob(a,d))d[G]?.(),d[U]?.();return l},{s:a});l&&f.isActive()||(f=null)}var h=d,k;if(l){var n=f?m=>()=>f()[m]():m=>l[m],p={get:m=>l[m]&&n(m)(),has:m=>m in l};c.j.F in l&&c.j.subscribe(a,c.j.F,()=>{var m=l[c.j.F]();if(m){var q=c.m.childNodes(a);q.length&&m(q,c.dataFor(q[0]))}});c.j.ca in l&&(h=c.j.$a(a,d),c.j.subscribe(a,c.j.ca,()=>{var m=l[c.j.ca]();m&&c.m.firstChild(a)&&m(a)}));na(l).forEach(m=>{var q=m.ub.init,r=m.ub.update,u=m.key;if(8===a.nodeType&&!c.m.aa[u])throw Error("The binding '"+
u+"' cannot be used with comment nodes");try{"function"==typeof q&&c.u.I(()=>{var t=q(a,n(u),p,h.$data,h);if(t&&t.controlsDescendantBindings){if(void 0!==k)throw Error("Multiple bindings ("+k+" and "+u+") are trying to control descendant bindings of the same element. You cannot use these bindings together on the same element.");k=u}}),"function"==typeof r&&c.o(()=>r(a,n(u),p,h.$data,h),{s:a})}catch(t){throw t.message='Unable to process binding "'+u+": "+l[u]+'"\nMessage: '+t.message,t;}})}return void 0===
k&&h};c.$b=a=>c.g.l.get(a,I)?.context;const P=a=>a&&a instanceof c.ba?a:new c.ba(a);c.applyBindingAccessorsToNode=(a,b,d)=>Z(a,b,P(d));c.mb=(a,b)=>{1!==b.nodeType&&8!==b.nodeType||Y(P(a),b)};c.Ib=(a,b)=>X(P(a),b);c.dataFor=a=>([1,8].includes(a?.nodeType)&&c.$b(a))?.$data;c.U("bindingHandlers",c.i);(()=>{var a=Object.create(null),b=new Map;c.components={get:(l,f)=>{if(b.has(l))f(b.get(l));else{var h=a[l];h?h.subscribe(f):(h=a[l]=new c.P,h.subscribe(f),g(l,k=>{b.set(l,k);delete a[l];h.B(k)}))}},register:(l,
f)=>{if(!f)throw Error("Invalid configuration for "+l);if(d[l])throw Error("Component "+l+" is already registered");d[l]=f}};var d=Object.create(null),e=(l,f)=>{throw Error(`Component '${l}': ${f}`);},g=(l,f)=>{var h={},k=d[l]||{},n=k.template;k=k.viewModel;if(n){n.element||e(l,"Unknown template value: "+n);n=n.element;var p=J.getElementById(n);p||e(l,"Cannot find element with ID "+n);p.matches("TEMPLATE")||e(l,"Template Source Element not a <template>");h.template=c.g.ua(p.content.childNodes)}k&&
("function"!==typeof k.createViewModel&&e(l,"Unknown viewModel value: "+k),h.createViewModel=k.createViewModel);f(h.template&&h.createViewModel?h:null)}})();(()=>{var a=0;c.i.component={init:(b,d,e,g,l)=>{var f,h,k,n=()=>{var p=f&&f.dispose;"function"===typeof p&&p.call(f);k&&k.dispose();h=f=k=null};c.m.ja(b);c.g.N.addDisposeCallback(b,n);c.o(()=>{var p=c.g.h(d());if("string"!==typeof p){var m=c.g.h(p.params);p=c.g.h(p.name)}if(!p)throw Error("No component name specified");var q=c.j.$a(b,l),r=h=++a;
c.components.get(p,u=>{if(h===r){n();if(!u)throw Error("Unknown component '"+p+"'");var t=u.template;if(!t)throw Error("Component '"+p+"' has no template");c.m.pa(b,c.g.ua(t));f=u.createViewModel(m,{element:b});c.mb(q.createChildContext(f,{}),b)}})},{s:b});return{controlsDescendantBindings:!0}}};c.m.aa.component=!0})();c.i.attr={update:(a,b)=>{b=c.g.h(b())||{};c.g.K(b,function(d,e){e=c.g.h(e);var g=d.indexOf(":");g="lookupNamespaceURI"in a&&0<g&&a.lookupNamespaceURI(d.slice(0,g));!1===e||null==e?
g?a.removeAttributeNS(g,d):a.removeAttribute(d):(e=e.toString(),g?a.setAttributeNS(g,d,e):a.setAttribute(d,e))})}};(()=>{c.i.checked={after:["value","attr"],init:function(a,b,d){var e="checkbox"==a.type,g="radio"==a.type;if(e||g){const m=c.xb(()=>{if(d.has("checkedValue"))return c.g.h(d.get("checkedValue"));if(n)return d.has("value")?c.g.h(d.get("value")):a.value});var l=()=>{if(!c.u.Ca()){var q=a.checked,r=m();if(q||!g&&!c.u.ma()){var u=c.u.I(b);if(h){var t=k?u.L():u,z=p;p=r;z!==r?q&&(t.push(r),
t.remove(z)):q?t.push(r):t.remove(r);k&&c.vb(u)&&u(t)}else e&&(void 0===r?r=q:q||(r=void 0)),c.la.Ga(u,d,"checked",r,!0)}}},f=b(),h=e&&c.g.h(f)instanceof Array,k=!(h&&f.push&&f.splice),n=g||h,p=h?m():void 0;c.o(l,null,{s:a});a.addEventListener("click",l);c.o(()=>{var q=c.g.h(b()),r=m();h?(a.checked=q.includes(r),p=r):a.checked=e&&void 0===r?!!q:m()===q},null,{s:a});f=void 0}}};c.i.checkedValue={update:function(a,b){a.value=c.g.h(b())}}})();var Q=(a,b,d)=>b&&b.split(/\s+/).forEach(e=>a.classList.toggle(e,
d));c.i.css={update:(a,b)=>{b=c.g.h(b());"object"==typeof b?c.g.K(b,(d,e)=>{e=c.g.h(e);Q(a,d,!!e)}):(b=c.g.Bb(b),Q(a,a.__ko__cssValue,!1),a.__ko__cssValue=b,Q(a,b,!0))}};c.i.enable={update:(a,b)=>{(b=c.g.h(b()))&&a.disabled?a.removeAttribute("disabled"):b||a.disabled||(a.disabled=!0)}};c.i.disable={update:(a,b)=>c.i.enable.update(a,()=>!c.g.h(b()))};c.i.event={init:function(a,b,d,e,g){c.g.K(b()||{},l=>{"string"==typeof l&&a.addEventListener(l,(...f)=>{var h=b()[l];if(h)try{e=g.$data;var k=h.apply(e,
[e,...f])}finally{!0!==k&&f[0].preventDefault()}})})}};const aa=a=>()=>{var b=a(),d=c.W(b)?b.L():b;if(!d||Array.isArray(d))return{foreach:b};c.g.h(b);return{foreach:d.data}};c.i.foreach={init:(a,b)=>c.i.template.init(a,aa(b)),update:(a,b,d,e,g)=>c.i.template.update(a,aa(b),d,e,g)};c.m.aa.foreach=!0;c.i.hasfocus={init:(a,b,d)=>{var e=l=>{a.__ko_hasfocusUpdating=!0;l=a.ownerDocument.activeElement===a;c.la.Ga(b(),d,"hasfocus",l,!0);a.__ko_hasfocusLastValue=l;a.__ko_hasfocusUpdating=!1},g=e.bind(null,
!0);e=e.bind(null,!1);a.addEventListener("focus",g);a.addEventListener("focusin",g);a.addEventListener("blur",e);a.addEventListener("focusout",e);a.__ko_hasfocusLastValue=!1},update:(a,b)=>{b=!!c.g.h(b());a.__ko_hasfocusUpdating||a.__ko_hasfocusLastValue===b||(b?a.focus():a.blur())}};c.i.html={init:()=>({controlsDescendantBindings:!0}),update:(a,b)=>{c.g.Qa(a);b=c.g.h(b());if(null!=b){const d=J.createElement("template");d.innerHTML="string"!=typeof b?b.toString():b;a.appendChild(d.content)}}};(()=>
{function a(b,d,e){c.i[b]={init:(g,l,f,h,k)=>{var n,p={};d&&(p={exportDependencies:!0});var m=f.has(c.j.ca);c.o(()=>{var q=c.g.h(l()),r=!e!==!q,u=!n;m&&(k=c.j.$a(g,k));if(r){p.dataDependency=c.u.o();var t=d?k.createChildContext("function"==typeof q?q:l,p):c.u.ma()?k.extend(null,p):k}u&&c.u.ma()&&(n=c.g.ua(c.m.childNodes(g),!0));r?(u||c.m.pa(g,c.g.ua(n)),c.mb(t,g)):(c.m.ja(g),c.j.notify(g,c.j.F))},{s:g});return{controlsDescendantBindings:!0}}};c.m.aa[b]=!0}a("if");a("ifnot",!1,!0);a("with",!0)})();
var ba={};c.i.options={init:a=>{if(!a.matches("SELECT"))throw Error("options binding applies only to SELECT elements");let b=a.length;for(;b--;)a.remove(b);return{controlsDescendantBindings:!0}},update:(a,b,d)=>{var e=a.multiple,g=0!=a.length&&e?a.scrollTop:null,l=c.g.h(b()),f=[];b=()=>Array.from(a.options).filter(m=>m.selected);var h=(m,q,r)=>{var u=typeof q;return"function"==u?q(m):"string"==u?m[q]:r},k=(m,q)=>{f.length&&(m=f.includes(c.C.M(q[0])),q[0].selected=m,p&&!m&&c.u.I(c.g.Db,null,[a,"change"]))};
e?f=b().map(c.C.M):0<=a.selectedIndex&&f.push(c.C.M(a.options[a.selectedIndex]));if(l){Array.isArray(l)||(l=[l]);var n=l.filter(m=>m??1)}var p=!1;l=k;d.has("optionsAfterRender")&&"function"==typeof d.get("optionsAfterRender")&&(l=(m,q)=>{k(m,q);c.u.I(d.get("optionsAfterRender"),null,[q[0],m!==ba?m:void 0])});c.g.Ab(a,n,(m,q,r)=>{r.length&&(f=r[0].selected?[c.C.M(r[0])]:[],p=!0);q=a.ownerDocument.createElement("option");m===ba?(c.g.Za(q),c.C.Fa(q,void 0)):(r=h(m,d.get("optionsValue"),m),c.C.Fa(q,c.g.h(r)),
m=h(m,d.get("optionsText"),r),c.g.Za(q,m));return[q]},{},l);n=f.length;(e?n&&b().length<n:n&&0<=a.selectedIndex?c.C.M(a.options[a.selectedIndex])!==f[0]:n||0<=a.selectedIndex)&&c.u.I(c.g.Db,null,[a,"change"]);c.u.Ca()&&c.j.notify(a,c.j.F);g&&20<Math.abs(g-a.scrollTop)&&(a.scrollTop=g)}};c.i.options.Wa=c.g.l.Z();c.i.style={update:(a,b)=>{c.g.K(c.g.h(b()||{}),(d,e)=>{e=c.g.h(e);if(null==e||!1===e)e="";if(/^--/.test(d))a.style.setProperty(d,e);else{d=d.replace(/-(\w)/g,(l,f)=>f.toUpperCase());var g=
a.style[d];a.style[d]=e;e===g||a.style[d]!=g||isNaN(e)||(a.style[d]=e+"px")}})}};c.i.submit={init:(a,b,d,e,g)=>{if("function"!=typeof b())throw Error("The value for a submit binding must be a function");a.addEventListener("submit",l=>{var f=b();try{var h=f.call(g.$data,a)}finally{!0!==h&&l.preventDefault()}})}};c.i.text={init:()=>({controlsDescendantBindings:!0}),update:(a,b)=>{8===a.nodeType&&(a.text||a.after(a.text=J.createTextNode("")),a=a.text);c.g.Za(a,b())}};c.m.aa.text=!0;c.i.textInput={init:(a,
b,d)=>{var e=a.value,g,l,f=()=>{clearTimeout(g);l=g=void 0;var k=a.value;e!==k&&(e=k,c.la.Ga(b(),d,"textInput",k))},h=()=>{var k=c.g.h(b())??"";void 0!==l&&k===l?setTimeout(h,4):a.value!==k&&(a.value=k,e=a.value)};a.addEventListener("input",f);a.addEventListener("change",f);c.o(h,{s:a})}};c.i.value={init:(a,b,d)=>{var e=a.matches("SELECT"),g=a.matches("INPUT");if(!g||"checkbox"!=a.type&&"radio"!=a.type){var l=new Set,f=d.get("valueUpdate"),h=null,k=()=>{h=null;var m=b(),q=c.C.M(a);c.la.Ga(m,d,"value",
q)};f&&("string"==typeof f?l.add(f):f.forEach(m=>l.add(m)),l.delete("change"));l.forEach(m=>{var q=k;(m||"").startsWith("after")&&(q=()=>{h=c.C.M(a);setTimeout(k,0)},m=m.slice(5));a.addEventListener(m,q)});var n=g&&"file"==a.type?()=>{var m=c.g.h(b());null==m||""===m?a.value="":c.u.I(k)}:()=>{var m=c.g.h(b()),q=c.C.M(a);if(null!==h&&m===h)setTimeout(n,0);else if(m!==q||void 0===q)e?(c.C.Fa(a,m),m!==c.C.M(a)&&c.u.I(k)):c.C.Fa(a,m)};if(e){var p;c.j.subscribe(a,c.j.F,()=>{p?d.get("valueAllowUnset")?
n():k():(a.addEventListener("change",k),p=c.o(n,{s:a}))},null,{notifyImmediately:!0})}else a.addEventListener("change",k),c.o(n,{s:a})}else c.applyBindingAccessorsToNode(a,{checkedValue:b})},update:()=>{}};c.i.visible={update:(a,b)=>{b=c.g.h(b());var d="none"!=a.style.display;b&&!d?a.style.display="":d&&!b&&(a.style.display="none")}};c.i.hidden={update:(a,b)=>a.hidden=!!c.g.h(b())};(function(a){c.i[a]={init:function(b,d,e,g,l){return c.i.event.init.call(this,b,()=>({[a]:d()}),e,g,l)}}})("click");
(()=>{let a=c.g.l.Z();class b{constructor(e){this.Na=e}Ua(...e){let g=this.Na;if(!e.length)return c.g.l.get(g,a)||(11===this.H?g.content:1===this.H?g:void 0);c.g.l.set(g,a,e[0])}}class d extends b{constructor(e){super(e);e&&(this.H=e.matches("TEMPLATE")&&e.content?e.content.nodeType:1)}}c.bb={Na:d,lb:b}})();(()=>{const a=(h,k,n)=>{var p;for(k=c.m.nextSibling(k);h&&(p=h)!==k;)h=c.m.nextSibling(p),n(p,h)},b=(h,k)=>{if(h.length){var n=h[0],p=n.parentNode;a(n,h[h.length-1],m=>(1===m.nodeType||8===m.nodeType)&&
c.Ib(k,m));c.g.xa(h,p)}},d=(h,k,n,p)=>{var m=(h&&(h.nodeType?h:0<h.length?h[0]:null)||n||{}).ownerDocument;if("string"==typeof n){m=m||J;m=m.getElementById(n);if(!m)throw Error("Cannot find template with ID "+n);n=new c.bb.Na(m)}else if([1,8].includes(n.nodeType))n=new c.bb.lb(n);else throw Error("Unknown template type: "+n);n=(n=n.Ua?n.Ua():null)?[...n.cloneNode(!0).childNodes]:null;if(!Array.isArray(n)||0<n.length&&"number"!=typeof n[0].nodeType)throw Error("Template engine must return an array of DOM nodes");
k&&(c.m.pa(h,n),b(n,p),c.j.notify(h,c.j.F));return n},e=(h,k,n)=>c.W(h)?h():"function"===typeof h?h(k,n):h,g=(h,k,n,p)=>{n=n||{};if(p){var m=p.nodeType?p:0<p.length?p[0]:null;return c.o(()=>{var q=k instanceof c.ba?k:new c.ba(k,null,null,{exportDependencies:!0}),r=e(h,q.$data,q);d(p,!0,r,q,n)},{ia:()=>!m||!c.g.Pa(m),s:m})}console.log("no targetNodeOrNodeArray")},l=(h,k,n,p,m)=>{var q,r=(w,y)=>{q=m.createChildContext(w,{extend:B=>B.$index=y});w=e(h,w,q);return d(p,!1,w,q,n)},u=(w,y)=>{b(y,q);q=null},
t=(w,y)=>{c.u.I(c.g.Ab,null,[p,w,r,n,u,y]);c.j.notify(p,c.j.F)};if(c.isObservableArray(k)){t(k.L());var z=k.subscribe(w=>{t(k(),w)},null,"arrayChange");z.s(p);return z}return c.o(()=>{var w=c.g.h(k)||[];Array.isArray(w)||(w=[w]);t(w)},{s:p})},f=c.g.l.Z();c.i.template={init:(h,k)=>{k=c.g.h(k());if("string"==typeof k||"name"in k)c.m.ja(h);else if(k=c.m.childNodes(h),k.length)k=c.g.Wb(k),(new c.bb.lb(h)).Ua(k);else throw Error("Anonymous template defined, but no template content was provided");return{controlsDescendantBindings:!0}},
update:(h,k,n,p,m)=>{p=k();k=c.g.h(p);n=null;"string"==typeof k?k={}:p="name"in k?k.name:h;var q=!!p;"foreach"in k?n=l(p,q&&k.foreach||[],k,h,m):q?(n=m,"data"in k&&(n=m.createChildContext(k.data,{exportDependencies:!0})),n=g(p,n,k,h)):c.m.ja(h);m=n;c.g.l.get(h,f)?.dispose?.();c.g.l.set(h,f,!m||m.isActive&&!m.isActive()?void 0:m)}};c.m.aa.template=!0})();c.g.tb=(a,b,d)=>{var e=0,g,l=b.length;l&&a.every(f=>{g=b.findIndex(h=>f.value===h.value);0<=g&&(f.moved=b[g].index,b[g].moved=f.index,b.splice(g,
1),e=g=0,--l);e+=l;return l&&(!d||e<d)})};c.g.qb=(()=>{var a=(b,d,e,g,l)=>{for(var f=Math.min,h=Math.max,k=[],n=-1,p=b.length,m,q=d.length,r=q-p||1,u=p+q+1,t,z,w;++n<=p;)for(z=t,k.push(t=[]),w=f(q,n+r),m=h(0,n-1);m<=w;m++)t[m]=m?n?b[n-1]===d[m-1]?z[m-1]:f(z[m]||u,t[m-1]||u)+1:m+1:n+1;f=[];h=[];r=[];n=p;for(m=q;n||m;)q=k[n][m]-1,m&&q===k[n][m-1]?h.push(f[f.length]={status:e,value:d[--m],index:m}):n&&q===k[n-1][m]?r.push(f[f.length]={status:g,value:b[--n],index:n}):(--m,--n,l.sparse||f.push({status:"retained",
value:d[m]}));c.g.tb(r,h,10*p);return f.reverse()};return(b,d,e)=>{b=b||[];d=d||[];return b.length<d.length?a(b,d,"added","deleted",e):a(d,b,"deleted","added",e)}})();(()=>{function a(e,g,l,f,h){var k=[],n=c.o(()=>{var p=g(l,h,c.g.xa(k,e))||[];if(0<k.length){var m=k.nodeType?[k]:k;if(0<m.length){var q=m[0],r=q.parentNode;p.forEach(u=>r.insertBefore(u,q));m.forEach(u=>c.removeNode(u))}f&&c.u.I(f,null,[l,p,h])}k.length=0;k.push(...p)},{s:e,ia:()=>!!k.find(c.g.Pa)});return{O:k,La:n.isActive()?n:void 0}}
var b=c.g.l.Z(),d=c.g.l.Z();c.g.Ab=(e,g,l,f,h,k)=>{g=g||[];Array.isArray(g)||(g=[g]);var n=c.g.l.get(e,b),p=[],m=0,q=0,r=[],u=[],t,z=v=>{t={sa:v,Ta:c.$(q++)};p.push(t)},w=v=>{t=n[v];t.Ta(q++);c.g.xa(t.O,e);p.push(t)};if(n){if(!k||n&&n._countWaitingForRemove)k=c.g.qb(Array.prototype.map.call(n,C=>C.sa),g,{sparse:!0});let v,F;for(k.forEach(C=>{v=C.moved;F=C.index;switch(C.status){case "deleted":for(;m<F;)w(m++);void 0===v&&(t=n[m],t.La&&(t.La.dispose(),t.La=void 0),c.g.xa(t.O,e).length&&t&&r.push.apply(r,
t.O));m++;break;case "added":for(;q<F;)w(m++);void 0!==v?(u.push(p.length),w(v)):z(C.value)}});q<g.length;)w(m++);p._countWaitingForRemove=0}else g.forEach(z);c.g.l.set(e,b,p);r.forEach(c.removeNode);var y=v=>{c.m.Ub(e,v,B);B=v};k=e.ownerDocument.activeElement;if(u.length)for(;null!=(g=u.shift());){for(t=p[g];g--;)if(f=p[g].O,f?.length){var B=f[f.length-1];break}t.O.forEach(y)}p.forEach(v=>{v.O||c.g.extend(v,a(e,l,v.sa,h,v.Ta));v.O.forEach(y);!v.Tb&&h&&(h(v.sa,v.O,v.Ta),v.Tb=!0,B=v.O[v.O.length-1])});
e.ownerDocument.activeElement!=k&&k?.focus();[].forEach(v=>v&&(v.sa=d))}})();R.ko=M})(this);

/* Copyright © 2011-2015 by Neil Jenkins. MIT Licensed. */
/* eslint max-len: 0 */

/**
  TODO: modifyBlocks function doesn't work very good.
  For example you have: UL > LI > [cursor here in text]
  Then create blockquote at cursor, the result is: BLOCKQUOTE > UL > LI
  not UL > LI > BLOCKQUOTE
*/

(() => {
  // source/node/TreeIterator.ts
  var SHOW_ELEMENT = 1;
  var SHOW_TEXT = 4;
  var SHOW_ELEMENT_OR_TEXT = 5;
  var filterAccept = NodeFilter.FILTER_ACCEPT;
  TreeWalker.prototype.previousPONode = function() {
    let current = this.currentNode;
    let node = current.lastChild;
    while (!node && current) {
      if (current === this.root) {
        break;
      }
      node = this.previousSibling();
      if (!node) {
        current = this.parentNode();
      }
    }
    node && (this.currentNode = node);
    return node;
  };
  var createTreeWalker = (root, whatToShow, filter) => document.createTreeWalker(root, whatToShow, filter ? {
    acceptNode: (node) => filter(node) ? filterAccept : NodeFilter.FILTER_SKIP
  } : null);

  // source/Constants.ts
  var ELEMENT_NODE = 1;
  var TEXT_NODE = 3;
  var DOCUMENT_FRAGMENT_NODE = 11;
  var ZWS = "\u200B";
  var ua = navigator.userAgent;
  var isMac = /Mac OS X/.test(ua);
  var isIOS = /iP(?:ad|hone)/.test(ua) || (isMac && !!navigator.maxTouchPoints);
  var isAndroid = /Android/.test(ua);
  var isWebKit = /WebKit\//.test(ua);
  var ctrlKey = isMac || isIOS ? "Meta-" : "Ctrl-";
  var cantFocusEmptyTextNodes = isWebKit;
  var notWS = /[^ \t\r\n]/;
  var indexOf = (array, value) => Array.prototype.indexOf.call(array, value);

  // source/node/Category.ts
  var inlineNodeNames = /^(?:#text|A|ABBR|ACRONYM|B|BR|BD[IO]|CITE|CODE|DATA|DEL|DFN|EM|FONT|HR|I|IMG|INPUT|INS|KBD|Q|RP|RT|RUBY|S|SAMP|SMALL|SPAN|STR(IKE|ONG)|SU[BP]|TIME|U|VAR|WBR)$/;
  var leafNodeNames = new Set(["BR", "HR", "IMG"]);
  var listNodeNames = new Set(["OL", "UL"]);
  var UNKNOWN = 0;
  var INLINE = 1;
  var BLOCK = 2;
  var CONTAINER = 3;
  var cache = new WeakMap();
  var isLeaf = (node) => isElement(node) && leafNodeNames.has(node.nodeName);
  var getNodeCategory = (node) => {
    switch (node.nodeType) {
      case TEXT_NODE:
        return INLINE;
      case ELEMENT_NODE:
      case DOCUMENT_FRAGMENT_NODE:
        if (cache.has(node)) {
          return cache.get(node);
        }
        break;
      default:
        return UNKNOWN;
    }
    let nodeCategory = Array.prototype.every.call(node.childNodes, isInline) ? (inlineNodeNames.test(node.nodeName) ? INLINE : BLOCK) : CONTAINER;
    cache.set(node, nodeCategory);
    return nodeCategory;
  };
  var isInline = (node) => getNodeCategory(node) === INLINE;
  var isBlock = (node) => getNodeCategory(node) === BLOCK;
  var isContainer = (node) => getNodeCategory(node) === CONTAINER;

  // source/node/Node.ts
  var createElement = (tag, props, children) => {
    const el = document.createElement(tag);
    if (props instanceof Array) {
      children = props;
      props = null;
    }
    setAttributes(el, props);
    children && el.append(...children);
    return el;
  };
  var areAlike = (node, node2) => !isLeaf(node) && (node.nodeType === node2.nodeType && node.nodeName === node2.nodeName && node.nodeName !== "A" && node.className === node2.className && node.style?.cssText === node2.style?.cssText);
  var hasTagAttributes = (node, tag, attributes) => node.nodeName === tag && Object.entries(attributes || {}).every(([k,v]) => node.getAttribute(k) === v);
  var getNearest = (node, root, tag, attributes) => {
    while (node && node !== root) {
      if (hasTagAttributes(node, tag, attributes)) {
        return node;
      }
      node = node.parentNode;
    }
    return null;
  };
  var getNodeBeforeOffset = (node, offset) => {
    let children = node.childNodes;
    while (offset && isElement(node)) {
      node = children[offset - 1];
      children = node.childNodes;
      offset = children.length;
    }
    return node;
  };
  var getNodeAfterOffset = (node, offset) => {
    if (isElement(node)) {
      const children = node.childNodes;
      if (offset < children.length) {
        node = children[offset];
      } else {
        while (node && !node.nextSibling) {
          node = node.parentNode;
        }
        node && (node = node.nextSibling);
      }
    }
    return node;
  };
  var getLength = (node) => isElement(node) || node instanceof DocumentFragment ? node.childNodes.length : node.length || 0;
  var empty = (node) => {
    const frag = document.createDocumentFragment(), childNodes = node.childNodes;
    childNodes && frag.append(...childNodes);
    return frag;
  };
  var detach = (node) => node.parentNode?.removeChild(node);
  var replaceWith = (node, node2) => node.parentNode?.replaceChild(node2, node);
  var getClosest = (node, root, selector) => {
    node = (node && !node.closest) ? node.parentElement : node;
    node = node?.closest(selector);
    return (node && root.contains(node)) ? node : null;
  };
  var isElement = (node) => node instanceof Element;
  var isTextNode = (node) => node instanceof Text;
  var isBrElement = (node) => "BR" === node?.nodeName;
  var setAttributes = (node, props) => {
    props && Object.entries(props).forEach(([k, v]) => {
      if (null == v) {
        node.removeAttribute(k);
      } else if ("style" === k && typeof v === "object") {
        Object.entries(v).forEach(([k2, v2]) => node.style[k2] = v2);
      } else {
        node.setAttribute(k, v);
      }
    });
  };

  // source/node/Whitespace.ts
  var notWSTextNode = (node) => isElement(node) ? isBrElement(node) : notWS.test(node.data);
  var isLineBreak = (br, isLBIfEmptyBlock) => {
    let block = br.parentNode;
    while (isInline(block)) {
      block = block.parentNode;
    }
    const walker = createTreeWalker(
      block,
      SHOW_ELEMENT_OR_TEXT,
      notWSTextNode
    );
    walker.currentNode = br;
    return !!walker.nextNode() || (isLBIfEmptyBlock && !walker.previousNode());
  };
  var removeZWS = (root, keepNode) => {
    const walker = createTreeWalker(root, SHOW_TEXT);
    let textNode;
    let index;
    while (textNode = walker.nextNode()) {
      while ((index = textNode.data.indexOf(ZWS)) > -1 && (!keepNode || textNode.parentNode !== keepNode)) {
        if (textNode.length === 1) {
          do {
            let parent = textNode.parentNode;
            textNode.remove();
            textNode = parent;
            walker.currentNode = parent;
          } while (isInline(textNode) && !getLength(textNode));
          break;
        } else {
          textNode.deleteData(index, 1);
        }
      }
    }
  };

  // source/range/Boundaries.ts
  var START_TO_START = 0;
  var START_TO_END = 1;
  var END_TO_END = 2;
  var END_TO_START = 3;
  var isNodeContainedInRange = (range, node, partial = true) => {
    const nodeRange = document.createRange();
    nodeRange.selectNode(node);
    return partial
      ? range.compareBoundaryPoints(END_TO_START, nodeRange) < 0
        && range.compareBoundaryPoints(START_TO_END, nodeRange) > 0
      : range.compareBoundaryPoints(START_TO_START, nodeRange) < 1
        && range.compareBoundaryPoints(END_TO_END, nodeRange) > -1;
  };
  var moveRangeBoundariesDownTree = range => {
    let { startContainer, startOffset, endContainer, endOffset } = range;
    while (!isTextNode(startContainer)) {
      let child = startContainer.childNodes[startOffset];
      if (!child || isLeaf(child)) {
        if (startOffset) {
          child = startContainer.childNodes[startOffset - 1];
          if (isTextNode(child)) {
            let prev;
            while (!child.length && (prev = child.previousSibling) && isTextNode(prev)) {
              child.remove();
              child = prev;
            }
            startContainer = child;
            startOffset = child.data.length;
          }
        }
        break;
      }
      startContainer = child;
      startOffset = 0;
    }
    if (endOffset) {
      while (!isTextNode(endContainer)) {
        const child = endContainer.childNodes[endOffset - 1];
        if (!child || isLeaf(child)) {
          if (isBrElement(child) && !isLineBreak(child)) {
            --endOffset;
            continue;
          }
          break;
        }
        endContainer = child;
        endOffset = getLength(endContainer);
      }
    } else {
      while (!isTextNode(endContainer)) {
        const child = endContainer.firstChild;
        if (!child || isLeaf(child)) {
          break;
        }
        endContainer = child;
      }
    }
    range.setStart(startContainer, startOffset);
    range.setEnd(endContainer, endOffset);
  };
  var moveRangeBoundariesUpTree = (range, startMax, endMax, root) => {
    let startContainer = range.startContainer;
    let startOffset = range.startOffset;
    let endContainer = range.endContainer;
    let endOffset = range.endOffset;
    let parent;
    if (!startMax) {
      startMax = range.commonAncestorContainer;
    }
    if (!endMax) {
      endMax = startMax;
    }
    while (!startOffset && startContainer !== startMax && startContainer !== root) {
      parent = startContainer.parentNode;
      startOffset = indexOf(parent.childNodes, startContainer);
      startContainer = parent;
    }
    while (endContainer !== endMax && endContainer !== root) {
      if (!isTextNode(endContainer) && isBrElement(endContainer.childNodes[endOffset]) && !isLineBreak(endContainer.childNodes[endOffset])) {
        ++endOffset;
      }
      if (endOffset !== getLength(endContainer)) {
        break;
      }
      parent = endContainer.parentNode;
      endOffset = indexOf(parent.childNodes, endContainer) + 1;
      endContainer = parent;
    }
    range.setStart(startContainer, startOffset);
    range.setEnd(endContainer, endOffset);
  };
  var moveRangeBoundaryOutOf = (range, tag, root) => {
    let parent = getClosest(range.endContainer, root, tag);
    if (parent && (parent = parent.parentNode)) {
      const clone = range.cloneRange();
      moveRangeBoundariesUpTree(clone, parent, parent, root);
      if (clone.endContainer === parent) {
        range.setStart(clone.endContainer, clone.endOffset);
        range.setEnd(clone.endContainer, clone.endOffset);
      }
    }
    return range;
  };

  // source/node/MergeSplit.ts
  var fixCursor = (node) => {
    let fixer = null;
    if (!isTextNode(node)) {
      if (isInline(node)) {
        let child = node.firstChild;
        if (cantFocusEmptyTextNodes) {
          while (isTextNode(child) && !child.data) {
            node.removeChild(child);
            child = node.firstChild;
          }
        }
        if (!child) {
          fixer = document.createTextNode(cantFocusEmptyTextNodes ? ZWS : "");
        }
      } else if (isElement(node) && !node.querySelector("BR")) {
        fixer = createElement("BR");
      }
      if (fixer) {
        try {
          node.appendChild(fixer);
        } catch (error) {
          didError({
            name: 'Squire: fixCursor – ' + error,
            message: 'Parent: ' + node.nodeName + '/' + node.innerHTML +
              ' appendChild: ' + fixer.nodeName
          });
        }
      }
    }
    return node;
  };
  var fixContainer = (container, root) => {
    let wrapper;
    [...container.childNodes].forEach((child) => {
      const isBR = isBrElement(child);
      if (!isBR && child.parentNode == root && isInline(child)) {
        wrapper = wrapper || createElement("DIV");
        wrapper.append(child);
      } else if (isBR || wrapper) {
        wrapper = wrapper || createElement("DIV");
        fixCursor(wrapper);
        child[isBR ? "replaceWith" : "before"](wrapper);
        wrapper = null;
      }
      isContainer(child) && fixContainer(child, root);
    });
    wrapper && container.append(fixCursor(wrapper));
    return container;
  };
  var split = (node, offset, stopNode, root) => {
    if (isTextNode(node) && node !== stopNode) {
      if (typeof offset !== "number") {
        throw new Error("Offset must be a number to split text node!");
      }
      if (!node.parentNode) {
        throw new Error("Cannot split text node with no parent!");
      }
      return split(node.parentNode, node.splitText(offset), stopNode, root);
    }
    let nodeAfterSplit = typeof offset === "number" ? offset < node.childNodes.length ? node.childNodes[offset] : null : offset;
    const parent = node.parentNode;
    if (!parent || node === stopNode || !isElement(node)) {
      return nodeAfterSplit;
    }
    const clone = node.cloneNode(false);
    while (nodeAfterSplit) {
      const next = nodeAfterSplit.nextSibling;
      clone.append(nodeAfterSplit);
      nodeAfterSplit = next;
    }
    if (node instanceof HTMLOListElement && getClosest(node, root, "BLOCKQUOTE")) {
      clone.start = (+node.start || 1) + node.childNodes.length - 1;
    }
    fixCursor(node);
    fixCursor(clone);
    node.after(clone);
    return split(parent, clone, stopNode, root);
  };
  var _mergeInlines = (node, fakeRange) => {
    const children = node.childNodes;
    let l = children.length;
    let frags = [];
    while (l--) {
      const child = children[l];
      const prev = l && children[l - 1];
      if (prev && isInline(child) && areAlike(child, prev)/* && !leafNodeNames.has(child.nodeName)*/) {
        if (fakeRange.startContainer === child) {
          fakeRange.startContainer = prev;
          fakeRange.startOffset += getLength(prev);
        }
        if (fakeRange.endContainer === child) {
          fakeRange.endContainer = prev;
          fakeRange.endOffset += getLength(prev);
        }
        if (fakeRange.startContainer === node) {
          if (fakeRange.startOffset > l) {
            --fakeRange.startOffset;
          } else if (fakeRange.startOffset === l) {
            fakeRange.startContainer = prev;
            fakeRange.startOffset = getLength(prev);
          }
        }
        if (fakeRange.endContainer === node) {
          if (fakeRange.endOffset > l) {
            --fakeRange.endOffset;
          } else if (fakeRange.endOffset === l) {
            fakeRange.endContainer = prev;
            fakeRange.endOffset = getLength(prev);
          }
        }
        detach(child);
        if (isTextNode(child)) {
          prev.appendData(child.data);
        } else {
          frags.unshift(empty(child));
        }
      } else if (isElement(child)) {
        child.append(...frags);
        frags = [];
        _mergeInlines(child, fakeRange);
      }
    }
  };
  var mergeInlines = (node, range) => {
    node = isTextNode(node) ? node.parentNode : node;
    if (isElement(node)) {
      const fakeRange = {
        startContainer: range.startContainer,
        startOffset: range.startOffset,
        endContainer: range.endContainer,
        endOffset: range.endOffset
      };
      _mergeInlines(node, fakeRange);
      range.setStart(fakeRange.startContainer, fakeRange.startOffset);
      range.setEnd(fakeRange.endContainer, fakeRange.endOffset);
    }
  };
  var mergeWithBlock = (block, next, range, root) => {
    let container = next;
    let parent;
    let offset;
    while ((parent = container.parentNode) && parent !== root && isElement(parent) && parent.childNodes.length === 1) {
      container = parent;
    }
    detach(container);
    offset = block.childNodes.length;
    const last = block.lastChild;
    if (isBrElement(last)) {
      last.remove();
      --offset;
    }
    block.append(empty(next));
    range.setStart(block, offset);
    range.collapse(true);
    mergeInlines(block, range);
  };
  var mergeContainers = (node, root) => {
    const prev = node.previousSibling;
    const first = node.firstChild;
    const isListItem = node.nodeName === "LI";
    if (isListItem && (!first || !listNodeNames.has(first.nodeName))) {
      return;
    }
    if (prev && areAlike(prev, node)) {
      if (!isContainer(prev)) {
        if (!isListItem) {
          return;
        }
        const block = createElement("DIV");
        block.append(empty(prev));
        prev.append(block);
      }
      detach(node);
      const needsFix = !isContainer(node);
      prev.append(empty(node));
      needsFix && fixContainer(prev, root);
      first && mergeContainers(first, root);
    } else if (isListItem) {
      const block = createElement("DIV");
      node.insertBefore(block, first);
      fixCursor(block);
    }
  };

  // source/Clean.ts
  var styleToSemantic = {
    fontWeight: {
      regexp: /^bold|^700/i,
      replace: () => createElement("B")
    },
    fontStyle: {
      regexp: /^italic/i,
      replace: () => createElement("I")
    },
    fontFamily: {
      regexp: notWS,
      replace: (family) => createElement("SPAN", {
        style: "font-family:" + family
      })
    },
    fontSize: {
      regexp: notWS,
      replace: (size) => createElement("SPAN", {
        style: "font-size:" + size
      })
    },
    textDecoration: {
      regexp: /^underline/i,
      replace: () => createElement("U")
    }
  /*
    textDecoration: {
      regexp: /^line-through/i,
      replace: doc => createElement("S")
    }
  */
  };
  var replaceStyles = (node) => {
    const style = node.style;
    let newTreeBottom;
    let newTreeTop;
    Object.entries(styleToSemantic).forEach(([attr, converter]) => {
      const css = style[attr];
      if (css && converter.regexp.test(css)) {
        const el = converter.replace(css);
        if (el.nodeName !== node.nodeName || el.className !== node.className) {
          if (!newTreeTop) {
            newTreeTop = el;
          }
          if (newTreeBottom) {
            newTreeBottom.append(el);
          }
          newTreeBottom = el;
          node.style.removeProperty(attr);
        }
      }
    });
    if (newTreeTop && newTreeBottom) {
      newTreeBottom.append(empty(node));
      if (node.style.cssText) {
        node.append(newTreeTop);
      } else {
        replaceWith(node, newTreeTop);
      }
    }
    return newTreeBottom || node;
  };
  var replaceWithTag = (tag) => (node) => {
    const el = createElement(tag);
    Array.prototype.forEach.call(node.attributes, (attr) => el.setAttribute(attr.name, attr.value));
    replaceWith(node, el);
    el.append(empty(node));
    return el;
  };
  var fontSizes = {
    1: "x-small",
    2: "small",
    3: "medium",
    4: "large",
    5: "x-large",
    6: "xx-large",
    7: "xxx-large",
    "-1": "smaller",
    "+1": "larger"
  };
  var stylesRewriters = {
    STRONG: replaceWithTag("B"),
    EM: replaceWithTag("I"),
    INS: replaceWithTag("U"),
    STRIKE: replaceWithTag("S"),
    SPAN: replaceStyles,
    FONT: (node) => {
      const face = node.face;
      const size = node.size;
      let color = node.color;
      let newTag = createElement("SPAN");
      let css = newTag.style;
      newTag.style.cssText = node.style.cssText;
      if (face) {
        css.fontFamily = face;
      }
      if (size) {
        css.fontSize = fontSizes[size];
      }
      if (color && /^#?([\dA-F]{3}){1,2}$/i.test(color)) {
        if (color.charAt(0) !== "#") {
          color = "#" + color;
        }
        css.color = color;
      }
      replaceWith(node, newTag);
      newTag.append(empty(node));
      return newTag;
    },
    TT: (node) => {
      const el = createElement("SPAN", {
        style: 'font-family:menlo,consolas,"courier new",monospace'
      });
      replaceWith(node, el);
      el.append(empty(node));
      return el;
    }
  };
  var allowedBlock = /^(?:A(?:DDRESS|RTICLE|SIDE|UDIO)|BLOCKQUOTE|CAPTION|D(?:[DLT]|IV)|F(?:IGURE|IGCAPTION|OOTER)|H[1-6]|HEADER|L(?:ABEL|EGEND|I)|O(?:L|UTPUT)|P(?:RE)?|SECTION|T(?:ABLE|BODY|D|FOOT|H|HEAD|R)|COL(?:GROUP)?|UL)$/;
  var blacklist = new Set(["HEAD", "META", "STYLE"]);
  var cleanTree = (node, preserveWS) => {
    const children = node.childNodes;
    let nonInlineParent = node;
    while (isInline(nonInlineParent)) {
      nonInlineParent = nonInlineParent.parentNode;
    }
/*
    const walker = createTreeWalker(
      nonInlineParent,
      SHOW_ELEMENT_OR_TEXT
    );
*/
    let i = children.length;
    while (i--) {
      let child = children[i];
      const nodeName = child.nodeName;
      if (isElement(child)) {
        const childLength = child.childNodes.length;
        if (stylesRewriters[nodeName]) {
          child = stylesRewriters[nodeName](child);
        } else if (blacklist.has(nodeName)) {
          child.remove();
          continue;
        } else if (!allowedBlock.test(nodeName) && !isInline(child)) {
          i += childLength;
          replaceWith(child, empty(child));
          continue;
        }
        if (childLength) {
          cleanTree(child, preserveWS || (nodeName === "PRE"));
        }
/*
      } else {
        if (isTextNode(child)) {
          let data = child.data;
          const startsWithWS = !notWS.test(data.charAt(0));
          const endsWithWS = !notWS.test(data.charAt(data.length - 1));
          if (preserveWS || (!startsWithWS && !endsWithWS)) {
            continue;
          }
          if (startsWithWS) {
            walker.currentNode = child;
            let sibling;
            while (sibling = walker.previousPONode()) {
              if (sibling.nodeName === "IMG" || isTextNode(sibling) && notWS.test(sibling.data)) {
                break;
              }
              if (!isInline(sibling)) {
                sibling = null;
                break;
              }
            }
            data = data.replace(/^[ \t\r\n]+/g, sibling ? " " : "");
          }
          if (endsWithWS) {
            walker.currentNode = child;
            let sibling;
            while (sibling = walker.nextNode()) {
              if (sibling.nodeName === "IMG" || (isTextNode(sibling) && notWS.test(sibling.data))) {
                break;
              }
              if (!isInline(sibling)) {
                sibling = null;
                break;
              }
            }
            data = data.replace(/[ \t\r\n]+$/g, sibling ? " " : "");
          }
          if (data) {
            child.data = data;
            continue;
          }
        }
        node.removeChild(child);
*/
      }
    }
    return node;
  };
  var removeEmptyInlines = (node) => {
    const children = node.childNodes;
    let l = children.length;
    while (l--) {
      const child = children[l];
      if (isElement(child) && !isLeaf(child)) {
        removeEmptyInlines(child);
        if (!child.firstChild && isInline(child)) {
          child.remove();
        }
      } else if (!child.data && isTextNode(child)) {
        node.removeChild(child);
      }
    }
  };
  var cleanupBRs = (node, root, keepForBlankLine) => {
    const brs = node.querySelectorAll("BR");
    let l = brs.length;
    while (l--) {
      const br = brs[l];
      const parent = br.parentNode;
      if (parent) {
        if (!isLineBreak(br, keepForBlankLine)) {
          detach(br);
        } else if (!isInline(parent)) {
          fixContainer(parent, root);
        }
      }
    }
  };
  var escapeHTML = (text) => text.replaceAll("&", "&amp;").replaceAll("<", "&lt;").replaceAll(">", "&gt;").replaceAll('"', "&quot;");

  // source/node/Block.ts
  var getBlockWalker = (node, root) => {
    const walker = createTreeWalker(root, SHOW_ELEMENT, isBlock);
    walker.currentNode = node;
    return walker;
  };
  var getPreviousBlock = (node, root) => {
    node = getBlockWalker(node, root).previousNode();
    return node !== root ? node : null;
  };
  var getNextBlock = (node, root) => {
    node = getBlockWalker(node, root).nextNode();
    return node !== root ? node : null;
  };
  var isEmptyBlock = (block) => !block.textContent && !block.querySelector("IMG");

  // source/range/Block.ts
  var getStartBlockOfRange = (range, root) => {
    const container = range.startContainer;
    let block;
    if (isInline(container)) {
      block = getPreviousBlock(container, root);
    } else if (container !== root && container instanceof HTMLElement && isBlock(container)) {
      block = container;
    } else {
      block = getNextBlock(getNodeBeforeOffset(container, range.startOffset), root);
    }
    return block && isNodeContainedInRange(range, block) ? block : null;
  };
  var getEndBlockOfRange = (range, root) => {
    const container = range.endContainer;
    let block;
    if (isInline(container)) {
      block = getPreviousBlock(container, root);
    } else if (container !== root && container instanceof HTMLElement && isBlock(container)) {
      block = container;
    } else {
      let node = getNodeAfterOffset(container, range.endOffset);
      if (!node || !root.contains(node)) {
        node = root;
        let child;
        while (child = node.lastChild) {
          node = child;
        }
      }
      block = getPreviousBlock(node, root);
    }
    return block && isNodeContainedInRange(range, block) ? block : null;
  };
  var createContentWalker = (root) => createTreeWalker(
    root,
    SHOW_ELEMENT_OR_TEXT,
    (node) => isTextNode(node) ? notWS.test(node.data) : node.nodeName === "IMG"
  );
  var rangeDoesStartAtBlockBoundary = (range, root) => {
    const startContainer = range.startContainer;
    const startOffset = range.startOffset;
    let nodeAfterCursor;
    if (isTextNode(startContainer)) {
      let i = startOffset;
      while (i--) {
        if (startContainer.data.charAt(i) !== ZWS) {
          return false;
        }
      }
      nodeAfterCursor = startContainer;
    } else {
      nodeAfterCursor = getNodeAfterOffset(startContainer, startOffset);
      if (!nodeAfterCursor || !root.contains(nodeAfterCursor)) {
        nodeAfterCursor = getNodeBeforeOffset(startContainer, startOffset);
        if (isTextNode(nodeAfterCursor) && nodeAfterCursor.length) {
          return false;
        }
      }
    }
    const block = getStartBlockOfRange(range, root);
    if (block) {
      const contentWalker = createContentWalker(block);
      contentWalker.currentNode = nodeAfterCursor;
      return !contentWalker.previousNode();
    }
  };
  var rangeDoesEndAtBlockBoundary = (range, root) => {
    const endContainer = range.endContainer;
    const endOffset = range.endOffset;
    let currentNode;
    if (isTextNode(endContainer)) {
      const text = endContainer.data;
      const length = text.length;
      for (let i = endOffset; i < length; ++i) {
        if (text.charAt(i) !== ZWS) {
          return false;
        }
      }
      currentNode = endContainer;
    } else {
      currentNode = getNodeBeforeOffset(endContainer, endOffset);
    }
    const block = getEndBlockOfRange(range, root);
    if (block) {
      const contentWalker = createContentWalker(block);
      contentWalker.currentNode = currentNode;
      return !contentWalker.nextNode();
    }
  };
  var expandRangeToBlockBoundaries = (range, root) => {
    const start = getStartBlockOfRange(range, root);
    const end = getEndBlockOfRange(range, root);
    if (start && end) {
      range.setStart(start, 0);
      range.setEnd(end, end.childNodes.length);
    }
  };

  // source/range/InsertDelete.ts
  var createRange = (startContainer, startOffset, endContainer, endOffset) => {
    const range = document.createRange();
    range.setStart(startContainer, startOffset);
    if (endContainer && typeof endOffset === "number") {
      range.setEnd(endContainer, endOffset);
    } else {
      range.setEnd(startContainer, startOffset);
    }
    return range;
  };
  var insertNodeInRange = (range, node) => {
    let { startContainer, startOffset, endContainer, endOffset } = range;
    let children;
    if (isTextNode(startContainer)) {
      const parent = startContainer.parentNode;
      children = parent.childNodes;
      if (startOffset === startContainer.length) {
        startOffset = indexOf(children, startContainer) + 1;
        if (range.collapsed) {
          endContainer = parent;
          endOffset = startOffset;
        }
      } else {
        if (startOffset) {
          const afterSplit = startContainer.splitText(startOffset);
          if (endContainer === startContainer) {
            endOffset -= startOffset;
            endContainer = afterSplit;
          } else if (endContainer === parent) {
            ++endOffset;
          }
          startContainer = afterSplit;
        }
        startOffset = indexOf(children, startContainer);
      }
      startContainer = parent;
    } else {
      children = startContainer.childNodes;
    }
    const childCount = children.length;
    if (startOffset === childCount) {
      startContainer.append(node);
    } else {
      startContainer.insertBefore(node, children[startOffset]);
    }
    if (startContainer === endContainer) {
      endOffset += children.length - childCount;
    }
    range.setStart(startContainer, startOffset);
    range.setEnd(endContainer, endOffset);
  };
  var extractContentsOfRange = (range, common, root) => {
    common = common || range.commonAncestorContainer;
    if (isTextNode(common)) {
      common = common.parentNode;
    }
    let endNode = split(range.endContainer, range.endOffset, common, root),
      frag = range.extractContents(),
      startContainer = common,
      startOffset = endNode ? indexOf(common.childNodes, endNode) : common.childNodes.length,
      after = common.childNodes[startOffset],
      before = after?.previousSibling,
      beforeText, afterText;
    if (isTextNode(before) && isTextNode(after)) {
      startContainer = before;
      startOffset = before.length;
      beforeText = before.data;
      afterText = after.data;
      if (beforeText.charAt(beforeText.length - 1) === ' ' && afterText.charAt(0) === ' ') {
        afterText = NBSP + afterText.slice(1);
      }
      before.appendData(afterText);
      detach(after);
    }
    range.setStart(startContainer, startOffset);
    range.collapse(true);
    fixCursor(common);
    return frag;
  };
  var getAdjacentInlineNode = (iterator, method, node) => {
    iterator.currentNode = node;
    let nextNode;
    while (nextNode = iterator[method]()) {
      if (isTextNode(nextNode) || isLeaf(nextNode)) {
        return nextNode;
      }
      if (!isInline(nextNode)) {
        return null;
      }
    }
    return null;
  };
  var deleteContentsOfRange = (range, root) => {
    const startBlock = getStartBlockOfRange(range, root);
    let endBlock = getEndBlockOfRange(range, root);
    const needsMerge = startBlock !== endBlock;
    if (startBlock && endBlock) {
      moveRangeBoundariesDownTree(range);
      moveRangeBoundariesUpTree(range, startBlock, endBlock, root);
    }
    const frag = extractContentsOfRange(range, null, root);
    moveRangeBoundariesDownTree(range);
    if (needsMerge) {
      endBlock = getEndBlockOfRange(range, root);
      if (startBlock && endBlock && startBlock !== endBlock) {
        mergeWithBlock(startBlock, endBlock, range, root);
      }
    }
    if (startBlock) {
      fixCursor(startBlock);
    }
    const child = root.firstChild;
    if (!child || isBrElement(child)) {
      fixCursor(root);
      root.firstChild && range.selectNodeContents(root.firstChild);
    }
    range.collapse(true);
    const startContainer = range.startContainer;
    const startOffset = range.startOffset;
    const iterator = createTreeWalker(root, SHOW_ELEMENT_OR_TEXT);
    let afterNode = startContainer;
    let afterOffset = startOffset;
    if (!isTextNode(afterNode) || afterOffset === afterNode.data.length) {
      afterNode = getAdjacentInlineNode(iterator, "nextNode", afterNode);
      afterOffset = 0;
    }
    let beforeNode = startContainer;
    let beforeOffset = startOffset - 1;
    if (!isTextNode(beforeNode) || beforeOffset === -1) {
      beforeNode = getAdjacentInlineNode(
        iterator,
        "previousPONode",
        afterNode || (isTextNode(startContainer) ? startContainer : startContainer.childNodes[startOffset] || startContainer)
      );
      if (isTextNode(beforeNode)) {
        beforeOffset = beforeNode.data.length;
      }
    }
    let node = null;
    let offset = 0;
    if (isTextNode(afterNode) && afterNode.data.charAt(afterOffset) === " " && rangeDoesStartAtBlockBoundary(range, root)) {
      node = afterNode;
      offset = afterOffset;
    } else if (isTextNode(beforeNode) && beforeNode.data.charAt(beforeOffset) === " ") {
      if (isTextNode(afterNode) && afterNode.data.charAt(afterOffset) === " " || rangeDoesEndAtBlockBoundary(range, root)) {
        node = beforeNode;
        offset = beforeOffset;
      }
    }
    node && node.replaceData(offset, 1, "\xA0");
    range.setStart(startContainer, startOffset);
    range.collapse(true);
    return frag;
  };
  var insertTreeFragmentIntoRange = (range, frag, root) => {
    const firstInFragIsInline = frag.firstChild && isInline(frag.firstChild);
    let node, blockContentsAfterSplit;
    fixContainer(frag, root);
    node = frag;
    while (node = getNextBlock(node, root)) {
      fixCursor(node);
    }
    if (!range.collapsed) {
      deleteContentsOfRange(range, root);
    }
    moveRangeBoundariesDownTree(range);
    range.collapse();
    const stopPoint = getClosest(range.endContainer, root, "BLOCKQUOTE") || root;
    let block = getStartBlockOfRange(range, root);
    const firstBlockInFrag = getNextBlock(frag, frag);
    const replaceBlock = !firstInFragIsInline && !!block && isEmptyBlock(block);
    if (block && firstBlockInFrag && !replaceBlock &&
    !getClosest(firstBlockInFrag, frag, "PRE,TABLE")) {
      moveRangeBoundariesUpTree(range, block, block, root);
      range.collapse(true);
      let container = range.endContainer;
      let offset = range.endOffset;
      cleanupBRs(block, root, false);
      if (isInline(container)) {
        const nodeAfterSplit = split(
          container,
          offset,
          getPreviousBlock(container, root) || root,
          root
        );
        container = nodeAfterSplit.parentNode;
        offset = indexOf(container.childNodes, nodeAfterSplit);
      }
      if (
        /*isBlock(container) && */
        offset !== getLength(container)
      ) {
        blockContentsAfterSplit = document.createDocumentFragment();
        while (node = container.childNodes[offset]) {
          blockContentsAfterSplit.append(node);
        }
      }
      mergeWithBlock(container, firstBlockInFrag, range, root);
      offset = indexOf(container.parentNode.childNodes, container) + 1;
      container = container.parentNode;
      range.setEnd(container, offset);
    }
    if (getLength(frag)) {
      if (replaceBlock && block) {
        range.setEndBefore(block);
        range.collapse();
        detach(block);
      }
      moveRangeBoundariesUpTree(range, stopPoint, stopPoint, root);
      let nodeAfterSplit = split(
        range.endContainer,
        range.endOffset,
        stopPoint,
        root
      );
      const nodeBeforeSplit = nodeAfterSplit ? nodeAfterSplit.previousSibling : stopPoint.lastChild;
      stopPoint.insertBefore(frag, nodeAfterSplit);
      if (nodeAfterSplit) {
        range.setEndBefore(nodeAfterSplit);
      } else {
        range.setEnd(stopPoint, getLength(stopPoint));
      }
      block = getEndBlockOfRange(range, root);
      moveRangeBoundariesDownTree(range);
      const container = range.endContainer;
      const offset = range.endOffset;
      if (nodeAfterSplit && isContainer(nodeAfterSplit)) {
        mergeContainers(nodeAfterSplit, root);
      }
      nodeAfterSplit = nodeBeforeSplit?.nextSibling;
      if (nodeAfterSplit && isContainer(nodeAfterSplit)) {
        mergeContainers(nodeAfterSplit, root);
      }
      range.setEnd(container, offset);
    }
    if (blockContentsAfterSplit && block) {
      const tempRange = range.cloneRange();
      mergeWithBlock(block, blockContentsAfterSplit, tempRange, root);
      range.setEnd(tempRange.endContainer, tempRange.endOffset);
    }
    moveRangeBoundariesDownTree(range);
  };

  // source/range/Contents.ts
/*
  var getTextContentsOfRange = (range) => {
    if (range.collapsed) {
      return "";
    }
    const startContainer = range.startContainer;
    const endContainer = range.endContainer;
    const filter = (node2) => isNodeContainedInRange(range, node2, true);
    const walker = createTreeWalker(
      range.commonAncestorContainer,
      SHOW_ELEMENT_OR_TEXT,
      filter
    );
    walker.currentNode = startContainer;
    let node = startContainer;
    let textContent = "";
    let addedTextInBlock = false;
    let value;
    if (!isElement(node) && !isTextNode(node) || !filter(node)) {
      node = walker.nextNode();
    }
    while (node) {
      if (isTextNode(node)) {
        value = node.data;
        if (value && /\S/.test(value)) {
          if (node === endContainer) {
            value = value.slice(0, range.endOffset);
          }
          if (node === startContainer) {
            value = value.slice(range.startOffset);
          }
          textContent += value;
          addedTextInBlock = true;
        }
      } else if (isBrElement(node) || addedTextInBlock && !isInline(node)) {
        textContent += "\n";
        addedTextInBlock = false;
      }
      node = walker.nextNode();
    }
    textContent = textContent.replace(/\xA0/g, " ");
    return textContent;
  };
*/

  // source/Clipboard.ts
  var extractRangeToClipboard = (event, range, root, cut) => {
    if (event.clipboardData) {
      let startBlock = getStartBlockOfRange(range, root),
        endBlock = getEndBlockOfRange(range, root),
        copyRoot = ((startBlock === endBlock) && startBlock) || root,
        contents, parent, newContents;
      if (cut) {
        contents = deleteContentsOfRange(range, root);
      } else {
        range = range.cloneRange();
        moveRangeBoundariesDownTree(range);
        moveRangeBoundariesUpTree(range, copyRoot, copyRoot, root);
        contents = range.cloneContents();
      }
      parent = range.commonAncestorContainer;
      if (isTextNode(parent)) {
        parent = parent.parentNode;
      }
      while (parent && parent !== copyRoot) {
        newContents = parent.cloneNode(false);
        newContents.append(contents);
        contents = newContents;
        parent = parent.parentNode;
      }
      let clipboardData = event.clipboardData;
      let body = document.body;
      let node = createElement("div");
      let html, text;
      node.append(contents);
      html = node.innerHTML;
      cleanupBRs(node, root, true);
      node.setAttribute("style",
        'position:fixed;overflow:hidden;bottom:100%;right:100%;');
      body.append(node);
      text = (node.innerText || node.textContent).replace(NBSP, ' '); // Replace nbsp with regular space
      node.remove();
      if (text !== html) {
        clipboardData.setData("text/html", html);
      }
      clipboardData.setData("text/plain", text);
      event.preventDefault();
      return true;
    }
  };
  var _onCut = function(event) {
    const range = this.getSelection();
    const root = this._root;
    if (range.collapsed) {
      event.preventDefault();
      return;
    }
    this.saveUndoState(range);
    if (!extractRangeToClipboard(event, range, root, true)) {
      setTimeout(() => {
        try {
          this._ensureBottomLine();
        } catch (error) {
          didError(error);
        }
      }, 0);
    }
    this.setSelection(range);
  };
  var _onCopy = function(event) {
    extractRangeToClipboard(
      event,
      this.getSelection(),
      this._root
    );
  };
  var _onPaste = function(event) {
    const clipboardData = event.clipboardData;
    const items = clipboardData?.items;
    let imageItem = null;
    let plainItem = null;
    let htmlItem = null;
    let self = this;
    let type;
    if (items) {
      [...items].forEach(item => {
        type = item.type;
        if (type === "text/html") {
          htmlItem = item;
        } else if (type === "text/plain" || type === "text/uri-list") {
          plainItem = item;
        } else if (item.kind === "file" && /^image\/(png|jpeg|webp)/.test(type)) {
          imageItem = item;
        }
      });
      if (htmlItem || plainItem || imageItem) {
        event.preventDefault();
        if (imageItem) {
          let reader = new FileReader();
          reader.onload = (event) => {
            let img = createElement("img", {src: event.target.result}),
              canvas = createElement("canvas"),
              ctx = canvas.getContext('2d');
            img.onload = ()=>{
              ctx.drawImage(img, 0, 0);
              let width = img.width, height = img.height;
              if (width > height) {
                if (width > 1024) {
                  height = height * 1024 / width;
                  width = 1024;
                }
              } else if (height > 1024) {
                width = width * 1024 / height;
                height = 1024;
              }
              canvas.width = width;
              canvas.height = height;
              ctx.drawImage(img, 0, 0, width, height);
              self.insertHTML('<img alt="" style="width:100%;max-width:'+width+'px" src="'+canvas.toDataURL()+'">', true);
            };
          }
          reader.readAsDataURL(imageItem.getAsFile());
        } else if (htmlItem && (!self.isShiftDown || !plainItem)) {
          htmlItem.getAsString(html => self.insertHTML(html, true));
        } else if (plainItem) {
          plainItem.getAsString(text => self.insertPlainText(text, true));
        }
      }
    }
  };

  // source/keyboard/KeyHelpers.ts
  var afterDelete = (self, range) => {
    try {
      range = range || self.getSelection();
      let node = range.startContainer;
      if (isTextNode(node)) {
        node = node.parentNode;
      }
      let parent = node;
      while (isInline(parent) && (!parent.textContent || parent.textContent === ZWS)) {
        node = parent;
        parent = node.parentNode;
      }
      if (node !== parent) {
        range.setStart(
          parent,
          indexOf(parent.childNodes, node)
        );
        range.collapse(true);
        node.remove();
        if (!isBlock(parent)) {
          parent = getPreviousBlock(parent, self._root) || parent;
        }
        fixCursor(parent);
        moveRangeBoundariesDownTree(range);
      }
      if (node === self._root && (node = node.firstChild) && isBrElement(node)) {
        detach(node);
      }
      self._ensureBottomLine();
      self.setRange(range);
    } catch (error) {
      didError(error);
    }
  };

  // source/keyboard/Backspace.ts
  var Backspace = (self, event, range) => {
    const root = self._root;
    self._removeZWS();
    self.saveUndoState(range);
    if (!range.collapsed) {
      event.preventDefault();
      deleteContentsOfRange(range, root);
      afterDelete(self, range);
    } else if (rangeDoesStartAtBlockBoundary(range, root)) {
      event.preventDefault();
      let current = getStartBlockOfRange(range, root);
      let previous;
      if (!current) {
        return;
      }
      fixContainer(current.parentNode, root);
      previous = getPreviousBlock(current, root);
      if (previous) {
        if (!previous.isContentEditable) {
          detachUneditableNode(previous, root);
          return;
        }
        mergeWithBlock(previous, current, range, root);
        current = previous.parentNode;
        while (current !== root && !current.nextSibling) {
          current = current.parentNode;
        }
        if (current !== root && (current = current.nextSibling)) {
          mergeContainers(current, root);
        }
        self.setSelection(range);
      } else if (current) {
        if (decreaseLevel(self, range, current)) {
          return;
        }
        self.setRange(range);
      }
    } else {
      self.setSelection(range);
      setTimeout(() => afterDelete(self), 0);
    }
  };

  // source/keyboard/Delete.ts
  var Delete = (self, event, range) => {
    const root = self._root;
    let current;
    let next;
    let originalRange;
    let cursorContainer;
    let cursorOffset;
    let nodeAfterCursor;
    self._removeZWS();
    self.saveUndoState(range);
    if (!range.collapsed) {
      event.preventDefault();
      deleteContentsOfRange(range, root);
      afterDelete(self, range);
    } else if (rangeDoesEndAtBlockBoundary(range, root)) {
      event.preventDefault();
      if (current = getStartBlockOfRange(range, root)) {
        fixContainer(current.parentNode, root);
        if (next = getNextBlock(current, root)) {
          if (!next.isContentEditable) {
            detachUneditableNode(next, root);
            return;
          }
          mergeWithBlock(current, next, range, root);
          next = current.parentNode;
          while (next !== root && !next.nextSibling) {
            next = next.parentNode;
          }
          if (next !== root && (next = next.nextSibling)) {
            mergeContainers(next, root);
          }
          self.setRange(range);
        }
      }
    } else {
      originalRange = range.cloneRange();
      moveRangeBoundariesUpTree(range, root, root, root);
      cursorContainer = range.endContainer;
      cursorOffset = range.endOffset;
      if (isElement(cursorContainer)) {
        nodeAfterCursor = cursorContainer.childNodes[cursorOffset];
        if (nodeAfterCursor?.nodeName === "IMG") {
          event.preventDefault();
          detach(nodeAfterCursor);
          moveRangeBoundariesDownTree(range);
          afterDelete(self, range);
          return;
        }
      }
      self.setSelection(originalRange);
      setTimeout(() => afterDelete(self), 0);
    }
  };

  // source/keyboard/Tab.ts
  var Tab = (self, event, range) => {
    self._removeZWS();
    range.collapsed
      && rangeDoesStartAtBlockBoundary(range, self._root)
      && getClosest(range.startContainer, self._root, "UL,OL,BLOCKQUOTE")
      && self.changeIndentationLevel("increase")
      && event.preventDefault();
  };
  var ShiftTab = (self, event, range) => {
    self._removeZWS();
    range.collapsed
      && rangeDoesStartAtBlockBoundary(range, self._root)
      && decreaseLevel(self, range, range.startContainer)
      && event.preventDefault();
  };

  // source/keyboard/Space.ts
  var Space = (self, event, range) => {
/*
    var _a;
    let node;
    const root = self._root;
    self._recordUndoState(range);
    self._getRangeAndRemoveBookmark(range);
    if (!range.collapsed) {
      deleteContentsOfRange(range, root);
      self._ensureBottomLine();
      self.setSelection(range);
      self._updatePath(range, true);
    } else if (rangeDoesEndAtBlockBoundary(range, root)) {
      const block = getStartBlockOfRange(range, root);
      if (block && block.nodeName !== "PRE") {
        const text = block.textContent?.trimEnd().replace(ZWS, "");
        if (text === "*" || text === "1.") {
          event.preventDefault();
          const walker = createTreeWalker(block, SHOW_TEXT);
          let textNode;
          while (textNode = walker.nextNode()) {
            textNode.data = cantFocusEmptyTextNodes ? ZWS : "";
          }
          if (text === "*") {
            self.makeUnorderedList();
          } else {
            self.makeOrderedList();
          }
          return;
        }
      }
    }
    node = range.endContainer;
    if (range.endOffset === getLength(node)) {
      do {
        if (node.nodeName === "A") {
          range.setStartAfter(node);
          break;
        }
      } while (!node.nextSibling && (node = node.parentNode) && node !== root);
    }
    if (self._config.addLinks) {
      const linkRange = range.cloneRange();
      moveRangeBoundariesDownTree(linkRange);
      const textNode = linkRange.startContainer;
      const offset = linkRange.startOffset;
      setTimeout(() => {
        linkifyText(self, textNode, offset);
      }, 0);
    }
    self.setSelection(range);
*/
    const root = self._root;
    self._recordUndoState(range);
    self._config.addLinks && addLinks(range.startContainer, root);
    self._getRangeAndRemoveBookmark(range);
/*
    let node = range.endContainer;
    if (range.collapsed && range.endOffset === getLength(node)) {
      do {
        if (node.nodeName === "A") {
          range.setStartAfter(node);
          break;
        }
      } while (!node.nextSibling && (node = node.parentNode) && node !== root);
    }
*/
    if (!range.collapsed) {
      deleteContentsOfRange(range, root);
      self._ensureBottomLine();
    }
    self.setRange(range);
  };

  // source/keyboard/KeyHandlers.ts
  var _onKey = function(event) {
    if (event.defaultPrevented) {
      return;
    }
    let key = event.key,
      range = this.getSelection(),
      root = this._root;
    if (key !== "Backspace" && key !== "Delete") {
      if (event.shiftKey) {
        key = "Shift-" + key;
      }
      if (event[osKey]) { key = ctrlKey + key; }
    }
    if (this._keyHandlers[key]) {
      this._keyHandlers[key](this, event, range);
    } else if (!range.collapsed && !event.isComposing && !event[osKey] && key.length === 1) {
      this.saveUndoState(range);
      deleteContentsOfRange(range, root);
      this._ensureBottomLine();
      this.setRange(range);
    } else if (range.collapsed && range.startContainer === root && root.children.length > 0) {
      const nextElement = root.children[range.startOffset];
      if (nextElement && !isBlock(nextElement)) {
        range = createRange(root.insertBefore(
          this.createDefaultBlock(), nextElement
        ), 0);
        if (isBrElement(nextElement)) {
          root.removeChild(nextElement);
        }
        const restore = this._willRestoreSelection;
        this.setSelection(range);
        this._willRestoreSelection = restore;
      }
    }
  };
  var mapKeyToFormat = (tag, remove) => {
    return (self, event) => {
      event.preventDefault();
      self.toggleTag(tag, remove);
    };
  };
  var mapKeyTo = (method) => (self, event) => {
    event.preventDefault();
    self[method]();
  };
  var toggleList = (type, methodIfNotInList) => (self, event) => {
    event.preventDefault();
    let parent = self.getSelectionClosest("UL,OL");
    if (type == parent?.nodeName) {
      self.removeList();
    } else {
      self[methodIfNotInList]();
    }
  };
  var changeIndentationLevel = (direction) => (self, event) => {
    event.preventDefault();
    self.changeIndentationLevel(direction);
  };
  var keyHandlers = {
    Tab,
    "Shift-Tab": ShiftTab,
    Space,
    ArrowLeft(self) {
      self._removeZWS();
    },
    ArrowRight(self) {
      self._removeZWS()
    },
    [ctrlKey + "b"]: mapKeyToFormat("B"),
    [ctrlKey + "i"]: mapKeyToFormat("I"),
    [ctrlKey + "u"]: mapKeyToFormat("U"),
    [ctrlKey + "Shift-7"]: mapKeyToFormat("S"),
    [ctrlKey + "Shift-5"]: mapKeyToFormat("SUB", "SUP"),
    [ctrlKey + "Shift-6"]: mapKeyToFormat("SUP", "SUB"),
    [ctrlKey + "Shift-8"]: toggleList("UL", "makeUnorderedList"),
    [ctrlKey + "Shift-9"]: toggleList("OL", "makeOrderedList"),
    [ctrlKey + "["]: changeIndentationLevel("decrease"),
    [ctrlKey + "]"]: changeIndentationLevel("increase"),
    [ctrlKey + "d"]: mapKeyTo("toggleCode"),
//    [ctrlKey + "z"]: mapKeyTo("undo"), // historyUndo
    [ctrlKey + "y"]: mapKeyTo("redo"), // historyRedo
    [ctrlKey + "Shift-Z"]: mapKeyTo("redo"),
    ["Redo"]: mapKeyTo("redo")
  };
  var blockTag = "DIV";
  var DOCUMENT_POSITION_PRECEDING = 2;

  var NBSP = '\u00A0';
  var win = document.defaultView;
  var osKey = isMac ? "metaKey" : "ctrlKey";
/*
  typeToBitArray = {
    1: 1,
    2: 2,
    3: 4,
    8: 128,
    9: 256,
    11: 1024
  },
*/

  var didError = error => console.error(error);
  var detachUneditableNode = (node, root) => {
    let parent;
    while (parent = node.parentNode) {
      if (parent === root || parent.isContentEditable) {
        break;
      }
      node = parent;
    }
    detach(node);
  };

  var mergeObjects = (base, extras, mayOverride) => {
    base = base || {};
    extras && Object.entries(extras).forEach(([prop,value])=>{
      if (mayOverride || !(prop in base)) {
        base[prop] = (value?.constructor === Object) ?
          mergeObjects(base[prop], value, mayOverride) :
          value;
      }
    });
    return base;
  };

  var createBookmarkNodes = () => [
    createElement("INPUT", {
      id: startSelectionId,
      type: "hidden"
    }),
    createElement("INPUT", {
      id: endSelectionId,
      type: "hidden"
    })
  ];

  var getListSelection = (range, root) => {
    let list = range.commonAncestorContainer;
    let startLi = range.startContainer;
    let endLi = range.endContainer;
    while (list && list !== root && !listNodeNames.has(list.nodeName)) {
      list = list.parentNode;
    }
    if (!list || list === root) {
      return null;
    }
    if (startLi === list) {
      startLi = startLi.childNodes[range.startOffset];
    }
    if (endLi === list) {
      endLi = endLi.childNodes[range.endOffset];
    }
    while (startLi && startLi.parentNode !== list) {
      startLi = startLi.parentNode;
    }
    while (endLi && endLi.parentNode !== list) {
      endLi = endLi.parentNode;
    }
    return [list, startLi, endLi];
  };
  var setDirection = (self, frag, dir) => {
    let walker = getBlockWalker(frag, self._root),
      node;
    while (node = walker.nextNode()) {
      if (node.nodeName === "LI") {
        node.parentNode.setAttribute("dir", dir);
        break;
      }
      node.setAttribute("dir", dir);
    }
    return frag;
  };
  var decreaseLevel = (self, range, node) =>
    getClosest(node, self._root, "UL,OL,BLOCKQUOTE") && self.changeIndentationLevel("decrease");
  var addLinks = (frag, root) => {
    let walker = createTreeWalker(frag, SHOW_TEXT, node => !getClosest(node, root, "A"));
    let node, data, parent, match, index, endIndex, child;
    while (node = walker.nextNode()) {
      data = node.data;
      parent = node.parentNode;
      while (match = linkRegExp.exec(data)) {
        index = match.index;
        endIndex = index + match[0].length;
        if (index) {
          child = document.createTextNode(data.slice(0, index));
          parent.insertBefore(child, node);
        }
        child = createElement("A", {
          href: match[1]
            ? (match[2] ? match[1] : "https://" + match[1])
            : "mailto:" + match[0]
        }, [data.slice(index, endIndex)]);
        parent.insertBefore(child, node);
        node.data = data = data.slice(endIndex);
      }
    }
  };

keyHandlers[ctrlKey + "b"] = mapKeyToFormat("B");
keyHandlers[ctrlKey + "i"] = mapKeyToFormat("I");
keyHandlers[ctrlKey + "u"] = mapKeyToFormat("U");
keyHandlers[ctrlKey + "Shift-7"] = mapKeyToFormat("S");
keyHandlers[ctrlKey + "Shift-5"] = mapKeyToFormat("SUB", "SUP");
keyHandlers[ctrlKey + "Shift-6"] = mapKeyToFormat("SUP", "SUB");
keyHandlers[ctrlKey + "Shift-8"] = toggleList("UL", "makeUnorderedList");
keyHandlers[ctrlKey + "Shift-9"] = toggleList("OL", "makeOrderedList");
keyHandlers[ctrlKey + "["] = changeIndentationLevel("decrease");
keyHandlers[ctrlKey + "]"] = changeIndentationLevel("increase");
keyHandlers[ctrlKey + "d"] = mapKeyTo("toggleCode");
keyHandlers[ctrlKey + "y"] = mapKeyTo("redo");
keyHandlers[ctrlKey + "Shift-Z"] = mapKeyTo("redo");
keyHandlers["Redo"] = mapKeyTo("redo");

class EditStack extends Array
{
    constructor(squire) {
      super();
      this.squire = squire;
      this.index = -1;
      this.inUndoState = false;
      this.limit = 0; // -1 means no limit
    }

    clear() {
      this.index = -1;
      this.length = 0;
    }

    stateChanged(/*canUndo, canRedo*/) {
      this.squire.fireEvent("undoStateChange", {
        canUndo: this.index > 0,
        canRedo: this.index + 1 < this.length
      });
      this.squire.fireEvent("input");
    }

    docWasChanged() {
      if (this.inUndoState) {
        this.inUndoState = false;
        this.stateChanged(/*true, false*/);
      } else
        this.squire.fireEvent("input");
    }

    /**
     * Leaves bookmark.
     */
    recordUndoState(range, replace) {
      if (!this.inUndoState || replace) {
        let undoIndex = this.index;
        let undoLimit = this.limit;
        let squire = this.squire;
        replace || ++undoIndex;
        undoIndex = Math.max(0, undoIndex);
        this.length = Math.min(undoIndex + 1, this.length);
        range && squire._saveRangeToBookmark(range);
        const html = squire._getRawHTML();
        if (undoLimit > 0 && undoIndex > undoLimit) {
          this.splice(0, undoIndex - undoLimit);
          undoIndex = undoLimit;
        }
        this[undoIndex] = html;
        this.index = undoIndex;
        this.inUndoState = true;
      }
    }

    saveUndoState(range) {
      let squire = this.squire;
      range = range || squire.getSelection();
      this.recordUndoState(range, true);
      squire._getRangeAndRemoveBookmark(range);
    }

    undo() {
      let squire = this.squire;
      if (this.index > 0 || !this.inUndoState) {
        this.recordUndoState(squire.getSelection());
        const undoIndex = this.index - 1;
        this.index = undoIndex;
        squire._setRawHTML(this[undoIndex]);
        let range = squire._getRangeAndRemoveBookmark();
        if (range) {
          squire.setSelection(range);
        }
        this.stateChanged(/*undoIndex > 0, true*/);
      }
    }

    redo() {
      let squire = this.squire,
        undoIndex = this.index + 1;
      if (undoIndex < this.length && this.inUndoState) {
        this.index = undoIndex;
        squire._setRawHTML(this[undoIndex]);
        let range = squire._getRangeAndRemoveBookmark();
        if (range) {
          squire.setSelection(range);
        }
        this.stateChanged(/*true, undoIndex + 1 < this.length*/);
      }
    }
}

  // source/Editor.ts
  var customEvents = new Set([
    "pathChange",
    "select",
    "input",
    "pasteImage",
    "undoStateChange"
  ]);
  var startSelectionId = "squire-selection-start";
  var endSelectionId = "squire-selection-end";
  var tagAfterSplit = {
    DT: "DD",
    DD: "DT",
    LI: "LI",
    PRE: "PRE"
  };
  var linkRegExp = /\b(?:((https?:\/\/)?(?:www\d{0,3}\.|[a-z0-9][a-z0-9.-]*\.[a-z]{2,}\/)(?:[^\s()<>]+|\([^\s()<>]+\))+(?:[^\s?&`!()[\]{};:'".,<>«»“”‘’]|\([^\s()<>]+\)))|([\w\-.%+]+@(?:[\w-]+\.)+[a-z]{2,}\b(?:\?[^&?\s]+=[^\s?&`!()[\]{};:'".,<>«»“”‘’]+(?:&[^&?\s]+=[^\s?&`!()[\]{};:'".,<>«»“”‘’]+)*)?))/i;
  var Squire = class {
    constructor(root, config) {
      this._root = root;
      this.setConfig(config);
      this._isFocused = false;
      this._lastSelection = null;
      this._willRestoreSelection = false;
      this._mayHaveZWS = false;
      this._path = "";
      this._pathRange = null;
      this._events = new Map();
      this.editStack = new EditStack(this);
      this._ignoreChange = false;
      this.addEventListener("selectionchange", () => this._isFocused && this._updatePath(this.getSelection()))
        .addEventListener("blur", () => this._willRestoreSelection = true)
        .addEventListener("pointerdown mousedown touchstart", () => this._willRestoreSelection = false)
        .addEventListener("focus", () => this._willRestoreSelection && this.setSelection(this._lastSelection))
        .addEventListener("cut", _onCut)
        .addEventListener("copy", _onCopy)
        .addEventListener("paste", _onPaste)
        .addEventListener("drop", (event) => {
          let types = event.dataTransfer.types;
          if (types.includes("text/plain") || types.includes("text/html")) {
            this.saveUndoState();
          }
        })
        .addEventListener("keydown keyup", (event) => this.isShiftDown = event.shiftKey)
        .addEventListener("keydown", _onKey)
        .addEventListener("pointerup keyup mouseup touchend", () => this.getSelection())
        .addEventListener("beforeinput", this._beforeInput);
      this._keyHandlers = Object.create(keyHandlers);
      this._mutation = new MutationObserver(() => this._docWasChanged());
      this._mutation.observe(root, {
        childList: true,
        attributes: true,
        characterData: true,
        subtree: true
      });
      root.setAttribute("contenteditable", "true");
      this.setHTML("");
      this._beforeInputTypes = {
        insertText: (event) => {
          if (isAndroid && event.data && event.data.includes("\n")) {
            event.preventDefault();
          }
        },
        insertLineBreak: (event) => {
          event.preventDefault();
          this.splitBlock(true);
        },
        insertParagraph: (event) => {
          event.preventDefault();
          this.splitBlock(false);
        },
        insertOrderedList: (event) => {
          event.preventDefault();
          this.makeOrderedList();
        },
        insertUnoderedList: (event) => {
          event.preventDefault();
          this.makeUnorderedList();
        },
        historyUndo: (event) => {
          event.preventDefault();
          this.undo();
        },
        historyRedo: (event) => {
          event.preventDefault();
        },
        formatRemove: (event) => {
          event.preventDefault();
          this.setStyle();
        },
        formatSetBlockTextDirection: (event) => {
          event.preventDefault();
          let dir = event.data;
          this.setTextDirection(dir === "null" ? null : dir);
        },
        formatBackColor: (event) => {
          event.preventDefault();
          this.setStyle({ backgroundColor: event.data });
        },
        formatFontColor: (event) => {
          event.preventDefault();
          this.setStyle({ color: event.data });
        },
        formatFontName: (event) => {
          event.preventDefault();
          this.setStyle({ fontFamily: event.data });
        },
/*
        formatIndent: event => {
          event.preventDefault();
          this.changeIndentationLevel("increase");
        },
        formatOutdent: event => {
          event.preventDefault();
          this.changeIndentationLevel("decrease");
        },
          this.saveUndoState();
        },
*/
        deleteContentBackward: (event) => {
          Backspace(this, event, this.getSelection());
        },
        deleteContentForward: (event) => {
          Delete(this, event, this.getSelection());
        }
      };
    }

    _beforeInput(event) {
      let type = event.isComposing ? "" : event.inputType;
      switch (type) {
        case "formatBold":
        case "formatItalic":
        case "formatUnderline":
        case "formatStrikeThrough":
        case "formatSuperscript":
        case "formatSubscript":
          event.preventDefault();
          this[type.slice(6).toLowerCase()]();
          break;
        case "formatJustifyFull":
        case "formatJustifyCenter":
        case "formatJustifyRight":
        case "formatJustifyLeft": {
          event.preventDefault();
          let alignment = type.slice(13).toLowerCase();
          this.setStyle({textAlign:alignment === "full" ? "justify" : alignment});
          break;
        }
        default:
          this._beforeInputTypes[type]?.(event);
      }
    }
    // --- Events
    handleEvent(event) {
      this.fireEvent(event.type, event);
    }
    fireEvent(type, detail) {
      let handlers = this._events.get(type);
      if (/^(?:focus|blur)/.test(type)) {
        const isFocused = this._root === document.activeElement;
        if (type === "focus") {
          if (!isFocused || this._isFocused) {
            return this;
          }
          this._isFocused = true;
        } else {
          if (isFocused || !this._isFocused) {
            return this;
          }
          this._isFocused = false;
        }
      }
      if (handlers) {
        const event = detail instanceof Event ? detail : new CustomEvent(type, {
          detail
        });
        handlers = handlers.slice();
        for (const handler of handlers) {
          try {
            handler.handleEvent ? handler.handleEvent(event) : handler.call(this, event);
          } catch (error) {
            error.details = 'Squire: fireEvent error. Event type: ' + type;
            didError(error);
          }
        }
      }
      return this;
    }
    addEventListener(types, fn) {
      if (!fn) {
        didError({
          name: 'Squire: addEventListener with null or undefined fn',
          message: 'Event type: ' + types
        });
        return this;
      }
      types.split(/\s+/).forEach((type) => {
        let handlers = this._events.get(type);
        let target = this._root;
        if (!handlers) {
          handlers = [];
          this._events.set(type, handlers);
          customEvents.has(type) || (type === "selectionchange" ? document : target).addEventListener(type, this, {capture:true,passive:"touchstart"===type});
        }
        handlers.push(fn);
      });
      return this;
    }
    removeEventListener(type, fn) {
      const handlers = this._events.get(type);
      if (handlers) {
        if (fn) {
          let l = handlers.length;
          while (l--) {
            if (handlers[l] === fn) {
              handlers.splice(l, 1);
            }
          }
        } else {
          handlers.length = 0;
        }
        if (!handlers.length) {
          this._events.delete(type);
          customEvents.has(type) || (type === "selectionchange" ? document : this._root).removeEventListener(type, this, true);
        }
      }
      return this;
    }
    // --- Focus
    focus() {
      this._root.focus({ preventScroll: true });
      return this;
    }
    blur() {
      this._root.blur();
      return this;
    }
    // ---
    _removeZWS() {
      if (this._mayHaveZWS) {
        removeZWS(this._root);
        this._mayHaveZWS = false;
      }
    }
    // ---
    _saveRangeToBookmark(range) {
      let [startNode, endNode] = createBookmarkNodes(),
        temp;
      insertNodeInRange(range, startNode);
      range.collapse();
      insertNodeInRange(range, endNode);
      if (startNode.compareDocumentPosition(endNode) & DOCUMENT_POSITION_PRECEDING) {
        startNode.id = endSelectionId;
        endNode.id = startSelectionId;
        temp = startNode;
        startNode = endNode;
        endNode = temp;
      }
      range.setStartAfter(startNode);
      range.setEndBefore(endNode);
    }
    _getRangeAndRemoveBookmark(range) {
      const root = this._root;
      const start = root.querySelector("#" + startSelectionId);
      const end = root.querySelector("#" + endSelectionId);
      if (start && end) {
        let startContainer = start.parentNode;
        let endContainer = end.parentNode;
        const startOffset = indexOf(startContainer.childNodes, start);
        let endOffset = indexOf(endContainer.childNodes, end);
        if (startContainer === endContainer) {
          --endOffset;
        }
        detach(start);
        detach(end);
        range = range || document.createRange();
        range.setStart(startContainer, startOffset);
        range.setEnd(endContainer, endOffset);
        mergeInlines(startContainer, range);
        if (startContainer !== endContainer) {
          mergeInlines(endContainer, range);
        }
        if (range.collapsed) {
          startContainer = range.startContainer;
          if (isTextNode(startContainer)) {
            endContainer = startContainer.childNodes[range.startOffset];
            if (!endContainer || !isTextNode(endContainer)) {
              endContainer = startContainer.childNodes[range.startOffset - 1];
            }
            if (isTextNode(endContainer)) {
              range.setStart(endContainer, 0);
              range.collapse(true);
            }
          }
        }
      }
      return range || null;
    }
    getSelection() {
      const sel = win.getSelection();
      const root = this._root;
      let range;
      if (this._isFocused && sel?.rangeCount) {
        range = sel.getRangeAt(0).cloneRange();
        const startContainer = range.startContainer;
        const endContainer = range.endContainer;
        if (startContainer && isLeaf(startContainer)) {
          range.setStartBefore(startContainer);
        }
        if (endContainer && isLeaf(endContainer)) {
          range.setEndBefore(endContainer);
        }
      }
      if (range && root.contains(range.commonAncestorContainer)) {
        this._lastSelection = range;
      } else {
        range = this._lastSelection;
        if (!document.contains(range.commonAncestorContainer)) {
          range = null;
        }
      }
      return range || createRange(root.firstChild, 0);
    }
    setSelection(range) {
      this._lastSelection = range;
      if (this._isFocused) {
        const selection = win.getSelection();
        if (selection) {
          selection.setBaseAndExtent(
            range.startContainer,
            range.startOffset,
            range.endContainer,
            range.endOffset
          );
        }
      } else {
        this._willRestoreSelection = true;
      }
      return this;
    }
    // ---
    // --- Path
    getPath() {
      return this._path;
    }
    _updatePath(range, force) {
      const anchor = range.startContainer,
        focus = range.endContainer;
      if (force || anchor !== this._pathRange.startContainer || focus !== this._pathRange.endContainer) {
        this._pathRange = range.cloneRange();
        let node = anchor === focus ? focus : null,
          newPath = (anchor && focus) ? (node ? this._getPath(focus) : "(selection)") : "";
        if (this._path !== newPath) {
          this._path = newPath;
          this.fireEvent("pathChange", {
            path: newPath,
            element: (!node || isElement(node)) ? node : node.parentElement
          });
        }
      }
      this.fireEvent(range.collapsed ? "cursor" : "select", {
        range
      });
    }
    _getPath(node) {
      const root = this._root;
      let path = "", style;
      if (node && node !== root) {
        path = this._getPath(node.parentNode, root);
        if (isElement(node)) {
          path += (path ? ">" : "") + node.nodeName;
          if (node.id) {
            path += "#" + node.id;
          }
          if (node.dir) {
            path += "[dir=" + node.dir + "]";
          }
          if (style = node.style.cssText) {
            path += "[style=" + style + "]";
          }
        }
      }
      return path;
    }
    // --- History
    _docWasChanged() {
      cache = new WeakMap();
      this._mayHaveZWS = cantFocusEmptyTextNodes;
      if (this._ignoreChange) {
        this._ignoreChange = false;
      } else {
        this.editStack.docWasChanged();
      }
    }
    /**
     * Leaves bookmark.
     */
    _recordUndoState(range, replace) {
      this.editStack.recordUndoState(range, replace);
    }
    saveUndoState(range) {
      this.editStack.saveUndoState(range);
    }
    undo() {
      this.editStack.undo();
    }
    redo() {
      this.editStack.redo();
    }
    // --- Get and set data
    getRoot() {
      return this._root;
    }
    _getRawHTML() {
      return this._root.innerHTML;
    }
    _setRawHTML(html) {
      if (html !== undefined) {
        const root = this._root;
        let node = root;
        root.innerHTML = html;
        do {
          fixCursor(node);
        } while (node = getNextBlock(node, root));
        this._ignoreChange = true;
      }
    }
    getHTML(withBookMark) {
      let html, range;
      if (withBookMark) {
        range = this.getSelection();
        this._saveRangeToBookmark(range);
      }
      html = this._getRawHTML().replace(/\u200B/g, "");
      withBookMark && this._getRangeAndRemoveBookmark(range);
      return html;
    }
    setHTML(html) {
      const root = this._root,
        frag = this._config.sanitizeToDOMFragment(html, false);
      cleanTree(frag);
      cleanupBRs(frag, root, false);
      fixContainer(frag, root);
      let node, walker = getBlockWalker(frag, root);
      while ((node = walker.nextNode()) && node !== root) {
        fixCursor(node);
      }
      this._ignoreChange = true;
      if (root.replaceChildren) {
        root.replaceChildren(frag);
      } else {
        while (root.lastChild)
          detach(root.lastChild);
        root.append(frag);
      }
      fixCursor(root);
      this.editStack.clear();
      const range = this._getRangeAndRemoveBookmark() || createRange(root.firstElementChild || root, 0);
      this.saveUndoState(range);
      this.setRange(range);
      return this;
    }
    /**
     * Insert HTML at the cursor location. If the selection is not collapsed
     * insertTreeFragmentIntoRange will delete the selection so that it is
     * replaced by the html being inserted.
     */
    insertHTML(html, isPaste) {
      let range = this.getSelection();
      if (isPaste) {
        let startFragmentIndex = html.indexOf("<!--StartFragment-->"),
          endFragmentIndex = html.lastIndexOf("<!--EndFragment-->");
        if (startFragmentIndex > -1 && endFragmentIndex > -1) {
          html = html.slice(startFragmentIndex + 20, endFragmentIndex);
        }
      }
      let frag = this._config.sanitizeToDOMFragment(html, isPaste);
      this.saveUndoState(range);
      try {
        let root = this._root, node = frag;
        addLinks(frag, frag);
        cleanTree(frag);
        cleanupBRs(frag, root, false);
        removeEmptyInlines(frag);
        frag.normalize();
        while (node = getNextBlock(node, frag)) {
          fixCursor(node);
        }
        insertTreeFragmentIntoRange(range, frag, root);
        range.collapse();
        moveRangeBoundaryOutOf(range, "A", root);
        this._ensureBottomLine();
        this.setRange(range);
        isPaste && this.focus();
      } catch (error) {
        didError(error);
      }
      return this;
    }
    insertElement(el, range) {
      range = range || this.getSelection();
      range.collapse(true);
      if (isInline(el)) {
        insertNodeInRange(range, el);
        range.setStartAfter(el);
      } else {
        const root = this._root;
        let splitNode = getStartBlockOfRange(range, root) || root;
        let nodeAfterSplit;
        while (splitNode !== root && !splitNode.nextSibling) {
          splitNode = splitNode.parentNode;
        }
        if (splitNode !== root) {
          const parent = splitNode.parentNode;
          nodeAfterSplit = split(parent, splitNode.nextSibling, root, root);
        }
        if (nodeAfterSplit) {
          nodeAfterSplit.before(el);
        } else {
          root.append(el);
          nodeAfterSplit = this.createDefaultBlock();
          root.append(nodeAfterSplit);
        }
        range.setStart(nodeAfterSplit, 0);
        range.setEnd(nodeAfterSplit, 0);
        moveRangeBoundariesDownTree(range);
      }
      this.focus();
      this.setSelection(range);
      this._updatePath(range);
      return this;
    }
    insertImage(src, attributes) {
      const img = createElement("IMG", mergeObjects({
        src: src
      }, attributes, true));
      this.insertElement(img);
      return img;
    }
    insertPlainText(plainText, isPaste) {
      const range = this.getSelection();
      if (range.collapsed && getClosest(range.startContainer, this._root, "PRE")) {
        let node = range.startContainer;
        let offset = range.startOffset;
        let text;
        if (!isTextNode(node)) {
          text = document.createTextNode("");
          node?.childNodes[offset].before(text);
          node = text;
          offset = 0;
        }
        node.insertData(offset, plainText);
        range.setStart(node, offset + plainText.length);
        range.collapse(true);
        this.setSelection(range);
        return this;
      }
      const lines = plainText.split(/\r?\n/),
        closeBlock = "</" + blockTag + ">",
        openBlock = "<" + blockTag + ">";
      lines.forEach((line, i) => {
        line = escapeHTML(line).replace(/ (?=(?: |$))/g, NBSP);
        lines[i] = i ? openBlock + (line || "<BR>") + closeBlock : line;
      });
      return this.insertHTML(lines.join(""), isPaste);
    }
    // --- Inline formatting
    /**
     * Extracts the font-family and font-size (if any) of the element
     * holding the cursor. If there's a selection, returns an empty object.
     */
    getFontInfo(range) {
      const fontInfo = {
        color: void 0,
        backgroundColor: void 0,
        family: void 0,
        size: void 0
      };
      range = range || this.getSelection();
      let seenAttributes = 0;
      let element = range.commonAncestorContainer, style, attr;
      if (range.collapsed || isTextNode(element)) {
        if (isTextNode(element)) {
          element = element.parentNode;
        }
        while (seenAttributes < 4 && element) {
          if (style = element.style) {
            if (!fontInfo.color && (attr = style.color)) {
              fontInfo.color = attr;
              ++seenAttributes;
            }
            if (!fontInfo.backgroundColor && (attr = style.backgroundColor)) {
              fontInfo.backgroundColor = attr;
              ++seenAttributes;
            }
            if (!fontInfo.family && (attr = style.fontFamily)) {
              fontInfo.family = attr;
              ++seenAttributes;
            }
            if (!fontInfo.size && (attr = style.fontSize)) {
              fontInfo.size = attr;
              ++seenAttributes;
            }
          }
          element = element.parentNode;
        }
      }
      return fontInfo;
    }
    /**
     * Looks for matching tag and attributes, so won't work if <strong>
     * instead of <b> etc.
     */
    hasFormat(tag, attributes, range) {
      tag = tag.toUpperCase();
      range = range || this.getSelection();
      if (!range.collapsed && isTextNode(range.startContainer) && range.startOffset === range.startContainer.length && range.startContainer.nextSibling) {
        range.setStartBefore(range.startContainer.nextSibling);
      }
      if (!range.collapsed && isTextNode(range.endContainer) && range.endOffset === 0 && range.endContainer.previousSibling) {
        range.setEndAfter(range.endContainer.previousSibling);
      }
      const root = this._root;
      const common = range.commonAncestorContainer;
      if (getNearest(common, root, tag, attributes)) {
        return true;
      }
      if (isTextNode(common)) {
        return false;
      }
      const walker = createTreeWalker(common, SHOW_TEXT, (node2) => isNodeContainedInRange(range, node2));
      let seenNode = false;
      let node;
      while (node = walker.nextNode()) {
        if (!getNearest(node, root, tag, attributes)) {
          return false;
        }
        seenNode = true;
      }
      return seenNode;
    }
    changeFormat(add, remove, range, partial) {
      range = range || this.getSelection();
      this.saveUndoState(range);
      if (remove) {
        range = this._removeFormat(
          remove.tag.toUpperCase(),
          remove.attributes || {},
          range,
          partial
        );
      }
      if (add) {
        range = this._addFormat(
          add.tag.toUpperCase(),
          add.attributes || {},
          range
        );
      }
      this.setRange(range);
      return this.focus();
    }
    _addFormat(tag, attributes, range) {
      const root = this._root;
      let node;
      if (range.collapsed) {
        const el = fixCursor(createElement(tag, attributes));
        insertNodeInRange(range, el);
        range.setStart(el.firstChild, el.firstChild.length);
        range.collapse(true);
        let block = el;
        while (isInline(block)) {
          block = block.parentNode;
        }
        removeZWS(block, el);
      } else {
        const filter = (node) => (isTextNode(node) || isBrElement(node) || node.nodeName === "IMG") && isNodeContainedInRange(range, node);
        const walker = createTreeWalker(
          range.commonAncestorContainer,
          SHOW_ELEMENT_OR_TEXT,
          filter
        );
        let { startContainer, startOffset, endContainer, endOffset } = range;
        walker.currentNode = startContainer;
        if (!isElement(startContainer) && !isTextNode(startContainer) || !filter(startContainer)) {
          startContainer = walker.nextNode();
          startOffset = 0;
        }
        if (startContainer) {
          do {
            node = walker.currentNode;
            if (!getNearest(node, root, tag, attributes)) {
              if (node === endContainer && node.length > endOffset) {
                node.splitText(endOffset);
              }
              if (node === startContainer && startOffset) {
                node = node.splitText(startOffset);
                if (endContainer === startContainer) {
                  endContainer = node;
                  endOffset -= startOffset;
                }
                startContainer = node;
                startOffset = 0;
              }
              const el = createElement(tag, attributes);
              replaceWith(node, el);
              el.append(node);
            }
          } while (walker.nextNode());
          if (!isTextNode(endContainer)) {
            if (isTextNode(node)) {
              endContainer = node;
              endOffset = node.length;
            } else {
              endContainer = node.parentNode;
              endOffset = 1;
            }
          }
          range = createRange(
            startContainer,
            startOffset,
            endContainer,
            endOffset
          );
        }
      }
      return range;
    }
    _removeFormat(tag, attributes, range, partial) {
      this._saveRangeToBookmark(range);
      let fixer;
      if (range.collapsed) {
        this._mayHaveZWS = cantFocusEmptyTextNodes;
        fixer = document.createTextNode(cantFocusEmptyTextNodes ? ZWS : "");
        insertNodeInRange(range, fixer);
      }
      let root = range.commonAncestorContainer;
      while (isInline(root)) {
        root = root.parentNode;
      }
      const { startContainer, startOffset, endContainer, endOffset } = range;
      const toWrap = [];
      const examineNode = (node, exemplar) => {
        if (isNodeContainedInRange(range, node, false)) {
          return;
        }
        let isText = isTextNode(node);
        let child;
        let next;
        if (!isNodeContainedInRange(range, node)) {
          if (node.nodeName !== "INPUT" && (!isText || node.data)) {
            toWrap.push([exemplar, node]);
          }
          return;
        }
        if (isText) {
          if (node === endContainer && endOffset !== node.length) {
            toWrap.push([exemplar, node.splitText(endOffset)]);
          }
          if (node === startContainer && startOffset) {
            node.splitText(startOffset);
            toWrap.push([exemplar, node]);
          }
        } else {
          for (child = node.firstChild; child; child = next) {
            next = child.nextSibling;
            examineNode(child, exemplar);
          }
        }
      };
      const formatTags = Array.prototype.filter.call(
        root.getElementsByTagName(tag),
        (el) => isNodeContainedInRange(range, el, true) && hasTagAttributes(el, tag, attributes)
      );
      partial || formatTags.forEach((node) => examineNode(node, node));
      toWrap.forEach(([el, node]) => {
        el = el.cloneNode(false);
        replaceWith(node, el);
        el.append(node);
      });
      formatTags.forEach((el) => replaceWith(el, empty(el)));
      if (cantFocusEmptyTextNodes && fixer) {
        fixer = fixer.parentNode;
        let block = fixer;
        while (block && isInline(block)) {
          block = block.parentNode;
        }
        if (block) {
          removeZWS(block, fixer);
        }
      }
      this._getRangeAndRemoveBookmark(range);
      fixer && range.collapse();
      mergeInlines(root, range);
      return range;
    }
    // ---
    bold() {
      this.toggleTag("B");
    }
    italic() {
      this.toggleTag("I");
    }
    underline() {
      this.toggleTag("U");
    }
    strikethrough() {
      this.toggleTag("S");
    }
    subscript() {
      this.toggleTag("SUB", "SUP");
    }
    superscript() {
      this.toggleTag("SUP", "SUB");
    }
    // ---
    makeLink(url, attributes) {
      const range = this.getSelection();
      if (range.collapsed) {
        insertNodeInRange(
          range,
          document.createTextNode(url.replace(/^[^:]*:\/*/, ""))
        );
      }
      attributes = mergeObjects(
        mergeObjects({
          href: url
        }, attributes, true),
        null,
        false
      );
      return this.changeFormat(
        {
          tag: "A",
          attributes
        },
        {
          tag: "A"
        },
        range
      );
    }
    removeLink() {
      return this.changeFormat(
        null,
        {
          tag: "A"
        },
        this.getSelection(),
        true
      );
    }
    // --- Block formatting
    _ensureBottomLine() {
      const root = this._root;
      const last = root.lastElementChild;
      if (!last || last.nodeName !== blockTag || !isBlock(last)) {
        root.append(this.createDefaultBlock());
      }
    }
    createDefaultBlock(children) {
      return fixCursor(
        createElement(blockTag, null, children)
      );
    }
    splitBlock(lineBreakOnly, range) {
      range = range || this.getSelection();
      const root = this._root;
      let block;
      let parent;
      let node;
      let nodeAfterSplit;
      this.editStack.inUndoState && this._docWasChanged();
      this._recordUndoState(range);
      this._removeZWS();
      this._getRangeAndRemoveBookmark(range);
      if (!range.collapsed) {
        deleteContentsOfRange(range, root);
      }
      if (this._config.addLinks) {
        moveRangeBoundariesDownTree(range);
        setTimeout(() => {
          addLinks(range.startContainer, root);
        }, 0);
      }
      block = getStartBlockOfRange(range, root);
      if (block && (parent = getClosest(block, root, "PRE"))) {
        moveRangeBoundariesDownTree(range);
        node = range.startContainer;
        const offset2 = range.startOffset;
        if (!isTextNode(node)) {
          node = document.createTextNode("");
          parent.insertBefore(node, parent.firstChild);
        }
        if (!lineBreakOnly && isTextNode(node) && (node.data.charAt(offset2 - 1) === "\n" || rangeDoesStartAtBlockBoundary(range, root)) && (node.data.charAt(offset2) === "\n" || rangeDoesEndAtBlockBoundary(range, root))) {
          node.deleteData(offset2 && offset2 - 1, offset2 ? 2 : 1);
          nodeAfterSplit = split(
            node,
            offset2 && offset2 - 1,
            root,
            root
          );
          node = nodeAfterSplit.previousSibling;
          if (!node.textContent) {
            detach(node);
          }
          node = this.createDefaultBlock();
          nodeAfterSplit.before(node);
          if (!nodeAfterSplit.textContent) {
            detach(nodeAfterSplit);
          }
          range.setStart(node, 0);
        } else {
          node.insertData(offset2, "\n");
          fixCursor(parent);
          if (node.length === offset2 + 1) {
            range.setStartAfter(node);
          } else {
            range.setStart(node, offset2 + 1);
          }
        }
        range.collapse(true);
        this.setRange(range);
        this._docWasChanged();
        return;
      }
      if (!block || lineBreakOnly || /^T[HD]$/.test(block.nodeName)) {
        moveRangeBoundaryOutOf(range, "A", root);
        insertNodeInRange(range, createElement("BR"));
        range.collapse();
        this.setRange(range);
        return;
      }
      block = getClosest(block, root, "LI") || block;
      if (isEmptyBlock(block) && (parent = getClosest(block, root, "UL,OL,BLOCKQUOTE"))) {
        return "BLOCKQUOTE" === parent.nodeName
          ? this.modifyBlocks((/* frag */) => this.createDefaultBlock(createBookmarkNodes()), range)
          : this.decreaseListLevel(range);
      }
      node = range.startContainer;
      const offset = range.startOffset;
      let splitTag = tagAfterSplit[block.nodeName] || blockTag;
      nodeAfterSplit = split(
        node,
        offset,
        block.parentNode,
        root
      );
      if (!hasTagAttributes(nodeAfterSplit, splitTag)) {
        block = createElement(splitTag);
        if (nodeAfterSplit.dir) {
          block.dir = nodeAfterSplit.dir;
        }
        replaceWith(nodeAfterSplit, block);
        block.append(empty(nodeAfterSplit));
        nodeAfterSplit = block;
      }
      removeZWS(block);
      removeEmptyInlines(block);
      fixCursor(block);
      while (isElement(nodeAfterSplit)) {
        let child = nodeAfterSplit.firstChild;
        let next;
        if (nodeAfterSplit.nodeName === "A" && (!nodeAfterSplit.textContent || nodeAfterSplit.textContent === ZWS)) {
          child = document.createTextNode("");
          replaceWith(nodeAfterSplit, child);
          nodeAfterSplit = child;
          break;
        }
        while (isTextNode(child) && !child.data) {
          next = child.nextSibling;
          if (!next || isBrElement(next)) {
            break;
          }
          detach(child);
          child = next;
        }
        if (!child || isBrElement(child) || isTextNode(child)) {
          break;
        }
        nodeAfterSplit = child;
      }
      range = createRange(nodeAfterSplit, 0);
      this.setRange(range);
    }
    modifyBlocks(modify, range) {
      range = range || this.getSelection();
      this._recordUndoState(range, true);
      const root = this._root;
      expandRangeToBlockBoundaries(range, root);
      moveRangeBoundariesUpTree(range, root, root, root);
      const frag = extractContentsOfRange(range, root, root);
      insertNodeInRange(range, modify.call(this, frag));
      if (range.endOffset < range.endContainer.childNodes.length) {
        mergeContainers(
          range.endContainer.childNodes[range.endOffset],
          root
        );
      }
      mergeContainers(
        range.startContainer.childNodes[range.startOffset],
        root
      );
      this._getRangeAndRemoveBookmark(range);
      this.setRange(range);
      return this;
    }
    // ---
    setTextDirection(direction) {
      return this.modifyBlocks(frag => setDirection(this, frag, direction)).focus();
    }
    increaseListLevel(range) {
      range = range || this.getSelection();
      const root = this._root;
      const listSelection = getListSelection(range, root);
      if (listSelection) {
        let [list, startLi, endLi] = listSelection;
        if (startLi && startLi !== list.firstChild) {
          this._recordUndoState(range, true);
          const type = list.nodeName;
          let newParent = startLi.previousSibling;
          let next;
          if (newParent.nodeName !== type) {
            newParent = createElement(type);
            startLi.before(newParent);
          }
          do {
            next = startLi === endLi ? null : startLi.nextSibling;
            newParent.append(startLi);
          } while (startLi = next);
          next = newParent.nextSibling;
          next && mergeContainers(next, root);
          this._getRangeAndRemoveBookmark(range);
          this.setRange(range);
        }
      }
      return this.focus();
    }
    decreaseListLevel(range) {
      range = range || this.getSelection();
      const root = this._root;
      const listSelection = getListSelection(range, root);
      if (listSelection) {
        let list = listSelection[0];
        let startLi = listSelection[1] || list.firstChild;
        let endLi = listSelection[2] || list.lastChild;
        let next, insertBefore;
        this._recordUndoState(range, true);
        if (startLi) {
          let newParent = list.parentNode;
          insertBefore = !endLi.nextSibling ?
            list.nextSibling :
            split(list, endLi.nextSibling, newParent, root);
          if (newParent !== root && newParent.nodeName === "LI") {
            newParent = newParent.parentNode;
            while (insertBefore) {
              next = insertBefore.nextSibling;
              endLi.append(insertBefore);
              insertBefore = next;
            }
            insertBefore = list.parentNode.nextSibling;
          }
          const makeNotList = !/^[OU]L$/.test(newParent.nodeName);
          do {
            next = startLi === endLi ? null : startLi.nextSibling;
            startLi.remove();
            if (makeNotList && startLi.nodeName === "LI") {
              startLi = this.createDefaultBlock([empty(startLi)]);
            }
            newParent.insertBefore(startLi, insertBefore);
          } while (startLi = next);
        }
        list.firstChild || detach(list);
        insertBefore && mergeContainers(insertBefore, root);
        this._getRangeAndRemoveBookmark(range);
        this.setRange(range);
      }
      return this.focus();
    }
    _makeList(frag, type) {
      let walker = getBlockWalker(frag, this._root),
        node, tag, prev, newLi;
      while (node = walker.nextNode()) {
        if (node.parentNode.nodeName === "LI") {
          node = node.parentNode;
          walker.currentNode = node.lastChild;
        }
        if (node.nodeName !== "LI") {
          newLi = createElement("LI");
          if (node.dir) {
            newLi.dir = node.dir;
          }
          if ((prev = node.previousSibling) && prev.nodeName === type) {
            prev.append(newLi);
            detach(node);
          } else {
            replaceWith(node, createElement(type, null, [newLi]));
          }
          newLi.append(empty(node));
          walker.currentNode = newLi;
        } else {
          node = node.parentNode;
          tag = node.nodeName;
          if (tag !== type && listNodeNames.has(tag)) {
            replaceWith(node,
              createElement(type, null, [empty(node)])
            );
          }
        }
      }
      return frag;
    }
    makeUnorderedList() {
      return this.modifyBlocks((frag) => this._makeList(frag, "UL")).focus();
    }
    makeOrderedList() {
      return this.modifyBlocks((frag) => this._makeList(frag, "OL")).focus();
    }
    removeList() {
      return this.modifyBlocks((frag) => {
        const root = this._root;
        frag.querySelectorAll("LI").forEach((item) => {
          if (isBlock(item)) {
            replaceWith(item, this.createDefaultBlock([empty(item)]));
          } else {
            fixContainer(item, root);
            replaceWith(item, empty(item));
          }
        });
        frag.querySelectorAll("UL, OL").forEach((list) => {
          const listFrag = empty(list);
          fixContainer(listFrag, root);
          replaceWith(list, listFrag);
        });
        return frag;
      }).focus();
    }
    // ---
    increaseQuoteLevel(range) {
      return this.modifyBlocks(
        (frag) => createElement(
          "BLOCKQUOTE",
          null,
          [frag]
        ),
        range
      ).focus();
    }
    decreaseQuoteLevel(range) {
      return this.modifyBlocks((frag) => {
        Array.prototype.filter.call(
          frag.querySelectorAll("blockquote"),
          (el) => !getClosest(el.parentNode, frag, "BLOCKQUOTE")
        ).forEach(
          (el) => replaceWith(el, empty(el))
        );
        return frag;
      }, range).focus();
    }
    // ---
    code() {
      const range = this.getSelection();
      if (range.collapsed || isContainer(range.commonAncestorContainer)) {
        return this.modifyBlocks((frag) => {
          const root = this._root;
          const output = document.createDocumentFragment();
          let walker = getBlockWalker(frag, root);
          let node;
          while (node = walker.nextNode()) {
            node.querySelectorAll("BR").forEach(br => {
              if (!isLineBreak(br, false)) {
                detach(br);
              } else {
                replaceWith(br, document.createTextNode("\n"));
              }
            });
            node.querySelectorAll("CODE").forEach(el => detach(el));
            if (output.childNodes.length) {
              output.append(document.createTextNode("\n"));
            }
            output.append(empty(node));
          }
          walker = createTreeWalker(output, SHOW_TEXT);
          while (node = walker.nextNode()) {
            node.data = node.data.replace(NBSP, " "); // nbsp -> sp
          }
          output.normalize();
          return fixCursor(
            createElement("PRE", null, [
              output
            ])
          );
        }, range).focus();
      }
      return this.changeFormat({ tag: "CODE" }, null, range);
    }
    removeCode() {
      const range = this.getSelection();
      const ancestor = range.commonAncestorContainer;
      const inPre = getClosest(ancestor, this._root, "PRE");
      if (inPre) {
        return this.modifyBlocks((frag) => {
          const root = this._root;
          const pres = frag.querySelectorAll("PRE");
          let l = pres.length;
          let pre, walker, node, value, contents, index;
          while (l--) {
            pre = pres[l];
            walker = createTreeWalker(pre, SHOW_TEXT);
            while (node = walker.nextNode()) {
              value = node.data;
              value = value.replace(/ (?=)/g, NBSP); // sp -> nbsp
              contents = document.createDocumentFragment();
              while ((index = value.indexOf("\n")) > -1) {
                contents.append(
                  document.createTextNode(value.slice(0, index))
                );
                contents.append(createElement("BR"));
                value = value.slice(index + 1);
              }
              node.before(contents);
              node.data = value;
            }
            fixContainer(pre, root);
            replaceWith(pre, empty(pre));
          }
          return frag;
        }, range).focus();
      }
      return this.changeFormat(null, { tag: "CODE" }, range);
    }
    toggleCode() {
      return (this.hasFormat("PRE") || this.hasFormat("CODE")) ? this.removeCode() : this.code();
    }
    // SnappyMail
    changeIndentationLevel(direction) {
      let parent = this.getSelectionClosest("UL,OL,BLOCKQUOTE");
      if (parent || "increase" === direction) {
        direction += (!parent || "BLOCKQUOTE" === parent.nodeName) ? "Quote" : "List";
        return this[direction + "Level"]();
      }
    }
    getSelectionClosest(selector) {
      return getClosest(this.getSelection().commonAncestorContainer, this._root, selector);
    }
    setAttribute(name, value) {
      let range = this.getSelection();
      let start = range?.startContainer || {};
      let end = range?.endContainer || {};
      if ("dir" == name || (isTextNode(start) && 0 === range.startOffset && start === end && end.length === range.endOffset)) {
        this._recordUndoState(range);
        setAttributes(start.parentNode, { [name]: value });
        this._docWasChanged();
      } else if (null == value) {
        this._recordUndoState(range);
        let node = getClosest(range.commonAncestorContainer, this._root, "*");
        range.collapsed ? setAttributes(node, { [name]: value }) : node.querySelectorAll("*").forEach((el) => setAttributes(el, { [name]: value }));
        this._docWasChanged();
      } else {
        this.changeFormat({
          tag: "SPAN",
          attributes: { [name]: value }
        }, null, range);
      }
      return this.focus();
    }
    setStyle(style) {
      this.setAttribute("style", style);
    }
    toggleTag(name, remove) {
      let range = this.getSelection();
      if (this.hasFormat(name, null, range)) {
        this.changeFormat(null, { tag: name }, range);
      } else {
        this.changeFormat({ tag: name }, remove ? { tag: remove } : null, range);
      }
    }
    setRange(range) {
      this.setSelection(range);
      this._updatePath(range, true);
    }

    setConfig(config) {
      this._config = mergeObjects({
        addLinks: true
      }, config, true);
      return this;
    }
  }

  // source/Legacy.ts
  win.Squire = Squire;
})();

/**
 * Modified version of https://github.com/mathiasbynens/punycode.js
 */

(() => {

'use strict';

const
	/** Highest positive signed 32-bit float value */
	maxInt = 2147483647, // aka. 0x7FFFFFFF or 2^31-1

	/** Bootstring parameters */
	base = 36,
	tMin = 1,
	tMax = 26,
	skew = 38,
	damp = 700,
	initialBias = 72,
	initialN = 128, // 0x80
	delimiter = '-', // '\x2D'

	/** Regular expressions */
	regexPunycode = /^xn--/,
	regexNonASCII = /[^\0-\x7F]/, // Note: U+007F DEL is excluded too.
	regexSeparators = /[\x2E\u3002\uFF0E\uFF61]/g, // RFC 3490 separators

	/** Error messages */
	errors = {
		'overflow': 'Overflow: input needs wider integers to process',
		'not-basic': 'Illegal input >= 0x80 (not a basic code point)',
		'invalid-input': 'Invalid input'
	},

	/** Convenience shortcuts */
	baseMinusTMin = base - tMin,
	floor = Math.floor,
	stringFromCharCode = String.fromCharCode,

	/*--------------------------------------------------------------------------*/

	/**
	 * A generic error utility function.
	 * @private
	 * @param {String} type The error type.
	 * @returns {Error} Throws a `RangeError` with the applicable error message.
	 */
	error = type => {
		throw new RangeError(errors[type])
	},

	/**
	 * A simple `Array#map`-like wrapper to work with domain name strings or email
	 * addresses.
	 * @private
	 * @param {String} domain The domain name or email address.
	 * @param {Function} callback The function that gets called for every
	 * character.
	 * @returns {String} A new string of characters returned by the callback
	 * function.
	 */
	mapDomain = (domain, callback) => {
		// In email addresses, only the domain name should be punycoded.
		// Leave the local part (i.e. everything up to `@`) intact.
		const parts = (domain || '').split('@');
		parts.push(
			parts.pop()
			.split(regexSeparators)
			.map(label => callback(label))
			.join('.')
		);
		return parts.join('@');
	},

	/**
	 * Creates an array containing the numeric code points of each Unicode
	 * character in the string. While JavaScript uses UCS-2 internally,
	 * this function will convert a pair of surrogate halves (each of which
	 * UCS-2 exposes as separate characters) into a single code point,
	 * matching UTF-16.
	 * @see <https://mathiasbynens.be/notes/javascript-encoding>
	 * @name decode
	 * @param {String} string The Unicode input string (UCS-2).
	 * @returns {Array} The new array of code points.
	 */
	ucs2decode = string => {
		const output = [];
		let counter = 0;
		const length = string.length;
		while (counter < length) {
			const value = string.charCodeAt(counter++);
			if (value >= 0xD800 && value <= 0xDBFF && counter < length) {
				// It's a high surrogate, and there is a next character.
				const extra = string.charCodeAt(counter++);
				if ((extra & 0xFC00) == 0xDC00) { // Low surrogate.
					output.push(((value & 0x3FF) << 10) + (extra & 0x3FF) + 0x10000);
				} else {
					// It's an unmatched surrogate; only append this code unit, in case the
					// next code unit is the high surrogate of a surrogate pair.
					output.push(value);
					counter--;
				}
			} else {
				output.push(value);
			}
		}
		return output;
	},

	/**
	 * Converts a basic code point into a digit/integer.
	 * @see `digitToBasic()`
	 * @private
	 * @param {Number} codePoint The basic numeric code point value.
	 * @returns {Number} The numeric value of a basic code point (for use in
	 * representing integers) in the range `0` to `base - 1`, or `base` if
	 * the code point does not represent a value.
	 */
	basicToDigit = codePoint => {
		if (codePoint >= 0x30 && codePoint < 0x3A) {
			return 26 + (codePoint - 0x30);
		}
		if (codePoint >= 0x41 && codePoint < 0x5B) {
			return codePoint - 0x41;
		}
		if (codePoint >= 0x61 && codePoint < 0x7B) {
			return codePoint - 0x61;
		}
		return base;
	},

	/**
	 * Converts a digit/integer into a basic code point.
	 * @see `basicToDigit()`
	 * @private
	 * @param {Number} digit The numeric value of a basic code point.
	 * @returns {Number} The basic code point whose value (when used for
	 * representing integers) is `digit`, which needs to be in the range
	 * `0` to `base - 1`. If `flag` is non-zero, the uppercase form is
	 * used; else, the lowercase form is used. The behavior is undefined
	 * if `flag` is non-zero and `digit` has no uppercase form.
	 */
	digitToBasic = (digit, flag) =>
		//  0..25 map to ASCII a..z or A..Z
		// 26..35 map to ASCII 0..9
		digit + 22 + 75 * (digit < 26) - ((flag != 0) << 5),

	/**
	 * Bias adaptation function as per section 3.4 of RFC 3492.
	 * https://tools.ietf.org/html/rfc3492#section-3.4
	 * @private
	 */
	adapt = (delta, numPoints, firstTime) => {
		let k = 0;
		delta = firstTime ? floor(delta / damp) : delta >> 1;
		delta += floor(delta / numPoints);
		for (/* no initialization */; delta > baseMinusTMin * tMax >> 1; k += base) {
			delta = floor(delta / baseMinusTMin);
		}
		return floor(k + (baseMinusTMin + 1) * delta / (delta + skew));
	},

	/**
	 * Converts a Punycode string of ASCII-only symbols to a string of Unicode
	 * symbols.
	 * @memberOf punycode
	 * @param {String} input The Punycode string of ASCII-only symbols.
	 * @returns {String} The resulting string of Unicode symbols.
	 */
	decode = input => {
		// Don't use UCS-2.
		const output = [];
		const inputLength = input.length;
		let i = 0;
		let n = initialN;
		let bias = initialBias;

		// Handle the basic code points: let `basic` be the number of input code
		// points before the last delimiter, or `0` if there is none, then copy
		// the first basic code points to the output.

		let basic = input.lastIndexOf(delimiter);
		if (basic < 0) {
			basic = 0;
		}

		for (let j = 0; j < basic; ++j) {
			// if it's not a basic code point
			if (input.charCodeAt(j) >= 0x80) {
				error('not-basic');
			}
			output.push(input.charCodeAt(j));
		}

		// Main decoding loop: start just after the last delimiter if any basic code
		// points were copied; start at the beginning otherwise.

		for (let index = basic > 0 ? basic + 1 : 0; index < inputLength; /* no final expression */) {

			// `index` is the index of the next character to be consumed.
			// Decode a generalized variable-length integer into `delta`,
			// which gets added to `i`. The overflow checking is easier
			// if we increase `i` as we go, then subtract off its starting
			// value at the end to obtain `delta`.
			const oldi = i;
			for (let w = 1, k = base; /* no condition */; k += base) {

				if (index >= inputLength) {
					error('invalid-input');
				}

				const digit = basicToDigit(input.charCodeAt(index++));

				if (digit >= base) {
					error('invalid-input');
				}
				if (digit > floor((maxInt - i) / w)) {
					error('overflow');
				}

				i += digit * w;
				const t = k <= bias ? tMin : (k >= bias + tMax ? tMax : k - bias);

				if (digit < t) {
					break;
				}

				const baseMinusT = base - t;
				if (w > floor(maxInt / baseMinusT)) {
					error('overflow');
				}

				w *= baseMinusT;

			}

			const out = output.length + 1;
			bias = adapt(i - oldi, out, oldi == 0);

			// `i` was supposed to wrap around from `out` to `0`,
			// incrementing `n` each time, so we'll fix that now:
			if (floor(i / out) > maxInt - n) {
				error('overflow');
			}

			n += floor(i / out);
			i %= out;

			// Insert `n` at position `i` of the output.
			output.splice(i++, 0, n);

		}

		return String.fromCodePoint(...output);
	},

	/**
	 * Converts a string of Unicode symbols (e.g. a domain name label) to a
	 * Punycode string of ASCII-only symbols.
	 * @memberOf punycode
	 * @param {String} input The string of Unicode symbols.
	 * @returns {String} The resulting Punycode string of ASCII-only symbols.
	 */
	encode = input => {
		const output = [];

		// Convert the input in UCS-2 to an array of Unicode code points.
		input = ucs2decode(input);

		// Cache the length.
		const inputLength = input.length;

		// Initialize the state.
		let n = initialN;
		let delta = 0;
		let bias = initialBias;

		// Handle the basic code points.
		for (const currentValue of input) {
			if (currentValue < 0x80) {
				output.push(stringFromCharCode(currentValue));
			}
		}

		const basicLength = output.length;
		let handledCPCount = basicLength;

		// `handledCPCount` is the number of code points that have been handled;
		// `basicLength` is the number of basic code points.

		// Finish the basic string with a delimiter unless it's empty.
		if (basicLength) {
			output.push(delimiter);
		}

		// Main encoding loop:
		while (handledCPCount < inputLength) {

			// All non-basic code points < n have been handled already. Find the next
			// larger one:
			let m = maxInt;
			for (const currentValue of input) {
				if (currentValue >= n && currentValue < m) {
					m = currentValue;
				}
			}

			// Increase `delta` enough to advance the decoder's <n,i> state to <m,0>,
			// but guard against overflow.
			const handledCPCountPlusOne = handledCPCount + 1;
			if (m - n > floor((maxInt - delta) / handledCPCountPlusOne)) {
				error('overflow');
			}

			delta += (m - n) * handledCPCountPlusOne;
			n = m;

			for (const currentValue of input) {
				if (currentValue < n && ++delta > maxInt) {
					error('overflow');
				}
				if (currentValue === n) {
					// Represent delta as a generalized variable-length integer.
					let q = delta;
					for (let k = base; /* no condition */; k += base) {
						const t = k <= bias ? tMin : (k >= bias + tMax ? tMax : k - bias);
						if (q < t) {
							break;
						}
						const qMinusT = q - t;
						const baseMinusT = base - t;
						output.push(
							stringFromCharCode(digitToBasic(t + qMinusT % baseMinusT, 0))
						);
						q = floor(qMinusT / baseMinusT);
					}

					output.push(stringFromCharCode(digitToBasic(q, 0)));
					bias = adapt(delta, handledCPCountPlusOne, handledCPCount === basicLength);
					delta = 0;
					++handledCPCount;
				}
			}

			++delta;
			++n;

		}
		return output.join('');
	};

	/*--------------------------------------------------------------------------*/

	/** Define the public API */
	window.IDN = {
		/**
		 * A string representing the current Punycode.js version number.
		 * @memberOf punycode
		 * @type String
		 */
		version: '2.3.1',

		/**
		 * Converts a Punycode string representing a domain name or an email address
		 * to Unicode. Only the Punycoded parts of the input will be converted, i.e.
		 * it doesn't matter if you call it on a string that has already been
		 * converted to Unicode.
		 * @memberOf punycode
		 * @param {String} input The Punycoded domain name or email address to
		 * convert to Unicode.
		 * @returns {String} The Unicode representation of the given Punycode
		 * string.
		 */
		toUnicode: input => mapDomain(
			input,
			string => regexPunycode.test(string) ? decode(string.slice(4).toLowerCase()) : string
		),

		/**
		 * Converts a Unicode string representing a domain name or an email address to
		 * Punycode. Only the non-ASCII parts of the domain name will be converted,
		 * i.e. it doesn't matter if you call it with a domain that's already in
		 * ASCII.
		 * @memberOf punycode
		 * @param {String} input The domain name or email address to convert, as a
		 * Unicode string.
		 * @returns {String} The Punycode representation of the given domain name or
		 * email address.
		 */
		toASCII: input => mapDomain(
			input,
			string => (regexNonASCII.test(string) ? 'xn--' + encode(string) : string).toLowerCase()
		)
	};
})();

/* eslint max-len: 0 */

(doc => {

const
	removeElements = 'HEAD,LINK,META,NOSCRIPT,SCRIPT,TEMPLATE,TITLE',
	allowedElements = 'A,B,BLOCKQUOTE,BR,DIV,FONT,H1,H2,H3,H4,H5,H6,HR,IMG,LI,OL,P,SPAN,STRONG,TABLE,TD,TH,TR,U,UL',
	allowedAttributes = 'abbr,align,background,bgcolor,border,cellpadding,cellspacing,class,color,colspan,dir,face,frame,height,href,hspace,id,lang,rowspan,rules,scope,size,src,style,target,type,usemap,valign,vspace,width'.split(','),

	i18n = (str, def) => rl.i18n(str) || def,

	ctrlKey = shortcuts.getMetaKey() + ' + ',

	createElement = name => doc.createElement(name),

	tpl = createElement('template'),

	trimLines = html => html.trim().replace(/^(<div>\s*<br\s*\/?>\s*<\/div>)+/, '').trim(),
	htmlToPlain = html => rl.Utils.htmlToPlain(html).trim(),
	plainToHtml = text => rl.Utils.plainToHtml(text),

	forEachObjectValue = (obj, fn) => Object.values(obj).forEach(fn),

	getFragmentOfChildren = parent => {
		let frag = doc.createDocumentFragment();
		frag.append(...parent.childNodes);
		return frag;
	},

	SquireDefaultConfig = {
/*
		addLinks: true // allow_smart_html_links
*/
		sanitizeToDOMFragment: (html, isPaste/*, squire*/) => {
			tpl.innerHTML = (html||'')
				.replace(/<\/?(BODY|HTML)[^>]*>/gi,'')
				.replace(/<!--[^>]+-->/g,'')
				.replace(/<span[^>]*>\s*<\/span>/gi,'')
				.trim();
			tpl.querySelectorAll('a:empty,span:empty').forEach(el => el.remove());
			if (isPaste) {
				tpl.querySelectorAll(removeElements).forEach(el => el.remove());
				tpl.querySelectorAll('*').forEach(el => {
					if (!el.matches(allowedElements)) {
						el.replaceWith(getFragmentOfChildren(el));
					} else if (el.hasAttributes()) {
						[...el.attributes].forEach(attr => {
							let name = attr.name.toLowerCase();
							if (!allowedAttributes.includes(name)) {
								el.removeAttribute(name);
							}
						});
					}
				});
			}
			return tpl.content;
		}
	};

class SquireUI
{
	constructor(container) {
		const
			clr = createElement('input'),
			doClr = name => input => {
				// https://github.com/the-djmaze/snappymail/issues/826
				clr.style.left = (input.offsetLeft + input.parentNode.offsetLeft) + 'px';
				clr.style.width = input.offsetWidth + 'px';

				clr.value = '';
				clr.onchange = () => squire.setStyle({[name]:clr.value});
				// Chrome 110+ https://github.com/the-djmaze/snappymail/issues/1199
//				clr.oninput = () => squire.setStyle({[name]:clr.value});
				setTimeout(()=>clr.click(),1);
			},

			actions = {
				mode: {
					plain: {
//						html: '〈〉',
//						cmd: () => this.setMode('plain' == this.mode ? 'wysiwyg' : 'plain'),
						select: [
							[i18n('SETTINGS_GENERAL/EDITOR_HTML'),'wysiwyg'],
							[i18n('SETTINGS_GENERAL/EDITOR_PLAIN'),'plain']
						],
						cmd: s => this.setMode('plain' == s.value ? 'plain' : 'wysiwyg')
					}
				},
				font: {
					fontFamily: {
						select: {
							'sans-serif': {
								Arial: "'Nimbus Sans L', 'Liberation sans', 'Arial Unicode MS', Arial, Helvetica, Garuda, Utkal, FreeSans, sans-serif",
								Tahoma: "'Luxi Sans', Tahoma, Loma, Geneva, Meera, sans-serif",
								Trebuchet: "'DejaVu Sans Condensed', Trebuchet, 'Trebuchet MS', sans-serif",
								Lucida: "'Lucida Sans Unicode', 'Lucida Sans', 'DejaVu Sans', 'Bitstream Vera Sans', 'DejaVu LGC Sans', sans-serif",
								Verdana: "'DejaVu Sans', Verdana, Geneva, 'Bitstream Vera Sans', 'DejaVu LGC Sans', sans-serif"
							},
							monospace: {
								Courier: "'Liberation Mono', 'Courier New', FreeMono, Courier, monospace",
								Lucida: "'DejaVu Sans Mono', 'DejaVu LGC Sans Mono', 'Bitstream Vera Sans Mono', 'Lucida Console', Monaco, monospace"
							},
							sans: {
								Times: "'Nimbus Roman No9 L', 'Times New Roman', Times, FreeSerif, serif",
								Palatino: "'Bitstream Charter', 'Palatino Linotype', Palatino, Palladio, 'URW Palladio L', 'Book Antiqua', Times, serif",
								Georgia: "'URW Palladio L', Georgia, Times, serif"
							}
						},
						cmd: s => squire.setStyle({ fontFamily: s.value })
					},
					fontSize: {
						select: ['11px','13px','16px','20px','24px','30px'],
						defaultValueIndex: 2,
						cmd: s => squire.setStyle({ fontSize: s.value })
						// TODO: maybe consider using https://developer.mozilla.org/en-US/docs/Web/CSS/font-size#values
						// example:
						// select: ['xx-small', 'x-small',' small',' medium', 'large', 'x-large', 'xx-large', 'xxx-large'],
						// defaultValueIndex: 3,
					},
// 					dir: {
// 						select: [
// 							[i18n('EDITOR/DIR_LTR', 'LTR'),'ltr'],
// 							[i18n('EDITOR/DIR_RTL', 'RTL'),'rtl'],
// 							[i18n('EDITOR/DIR_AUTO', 'Auto'),'auto'],
// 							['',''],
// 						],
// 						cmd: s => {
// 							squire.setAttribute('dir', s.value || null);
// //							squire.setStyle({ 'unicode-bidi': 'plaintext' });
// 						}
// 					}
				},
				dir: {
					dir_ltr: {
						html: '⁋',
						cmd: () => squire.setTextDirection('ltr')
					},
					dir_rtl: {
						html: '¶',
						cmd: () => squire.setTextDirection('rtl')
					}
				},
				colors: {
					textColor: {
						html: 'A<sub>▾</sub>',
						cmd: doClr('color')
					},
					backgroundColor: {
						html: '🎨', /* ▧ */
						cmd: doClr('backgroundColor')
					},
				},
				inline: {
					bold: {
						html: 'B',
						cmd: () => this.doAction('bold'),
						key: 'B',
						matches: 'B,STRONT'
					},
					italic: {
						html: 'I',
						cmd: () => this.doAction('italic'),
						key: 'I',
						matches: 'I'
					},
					underline: {
						html: '<u>U</u>',
						cmd: () => this.doAction('underline'),
						key: 'U',
						matches: 'U'
					},
					strike: {
						html: '<s>S</s>',
						cmd: () => this.doAction('strikethrough'),
						key: 'Shift + 7',
						matches: 'S'
					},
					sub: {
						html: 'Xₙ',
						cmd: () => this.doAction('subscript'),
						key: 'Shift + 5',
						matches: 'SUB'
					},
					sup: {
						html: 'Xⁿ',
						cmd: () => this.doAction('superscript'),
						key: 'Shift + 6',
						matches: 'SUP'
					}
				},
				block: {
					ol: {
						html: '#',
						cmd: () => this.doList('OL'),
						key: 'Shift + 8',
						matches: 'OL'
					},
					ul: {
						html: '⋮',
						cmd: () => this.doList('UL'),
						key: 'Shift + 9',
						matches: 'UL'
					},
					quote: {
						html: '"',
						cmd: () => {
							let parent = squire.getSelectionClosest('UL,OL,BLOCKQUOTE')?.nodeName;
							('BLOCKQUOTE' == parent) ? squire.decreaseQuoteLevel() : squire.increaseQuoteLevel();
						},
						matches: 'BLOCKQUOTE'
					},
					indentDecrease: {
						html: '⇤',
						cmd: () => squire.changeIndentationLevel('decrease'),
						key: ']'
					},
					indentIncrease: {
						html: '⇥',
						cmd: () => squire.changeIndentationLevel('increase'),
						key: '['
					}
				},
				targets: {
					link: {
						html: '🔗',
						cmd: () => {
							let node = squire.getSelectionClosest('A'),
								url = prompt("Link", node?.href || "https://");
							if (url != null) {
								url.length ? squire.makeLink(url) : (node && squire.removeLink());
							}
						},
						matches: 'A'
					},
					imageUrl: {
						html: '🖼️',
						cmd: () => {
							let node = squire.getSelectionClosest('IMG'),
								src = prompt("Image", node?.src || "https://");
							src?.length ? squire.insertImage(src) : node?.remove();
						},
						matches: 'IMG'
					},
					imageUpload: {
						html: '📂️',
						cmd: () => browseImage.click(),
						matches: 'IMG'
					}
				},
/*
				table: {
					// TODO
				},
*/
				changes: {
					undo: {
						html: '↶',
						cmd: () => squire.undo(),
						key: 'Z'
					},
					redo: {
						html: '↷',
						cmd: () => squire.redo(),
						key: 'Y'
					},
					source: {
						html: '👁',
						cmd: btn => {
							this.setMode('source' == this.mode ? 'wysiwyg' : 'source');
							btn.classList.toggle('active', 'source' == this.mode);
						}
					}
				},

				clear: {
					removeStyle: {
						html: '⎚',
						cmd: () => squire.setStyle()
					}
				}
			},

			plain = createElement('textarea'),
			wysiwyg = createElement('div'),
			toolbar = createElement('div'),
			browseImage = createElement('input'),
			squire = new Squire(wysiwyg, SquireDefaultConfig);

		clr.type = 'color';
		toolbar.append(clr);
		// Chrome https://github.com/the-djmaze/snappymail/issues/1199
		let clrid = 'squire-colors',
			colorlist = doc.getElementById(clrid),
			add = hex => colorlist.append(new Option(hex));
		if (!colorlist) {
			colorlist = createElement('datalist');
			colorlist.id = clrid;
			// Color blind safe Tableau 10 by Maureen Stone
			add('#4E79A7');
			add('#F28E2B');
			add('#E15759');
			add('#76B7B2');
			add('#59A14F');
			add('#EDC948');
			add('#B07AA1');
			add('#FF9DA7');
			add('#9C755F');
			add('#BAB0AC');
			doc.body.append(colorlist);
		}
		clr.setAttribute('list', clrid);

		browseImage.type = 'file';
		browseImage.accept = 'image/*';
		browseImage.style.display = 'none';
		browseImage.onchange = () => {
			if (browseImage.files.length) {
				let reader = new FileReader();
				reader.readAsDataURL(browseImage.files[0]);
				reader.onloadend = () => reader.result && squire.insertImage(reader.result);
			}
		}

		plain.className = 'squire-plain';
		wysiwyg.className = 'squire-wysiwyg';
		wysiwyg.dir = 'auto';
		this.mode = ''; // 'plain' | 'wysiwyg'
		this.container = container;
		this.squire = squire;
		this.plain = plain;
		this.wysiwyg = wysiwyg;

		dispatchEvent(new CustomEvent('squire-toolbar', {detail:{squire:this,actions:actions}}));

		toolbar.className = 'squire-toolbar btn-toolbar';
		let group, action/*, touchTap*/;
		for (group in actions) {
			let toolgroup = createElement('div');
			toolgroup.className = 'btn-group';
			toolgroup.id = 'squire-toolgroup-'+group;
			for (action in actions[group]) {
				let cfg = actions[group][action], input, ev = 'click';
				if (cfg.input) {
					input = createElement('input');
					input.type = cfg.input;
					ev = 'change';
				} else if (cfg.select) {
					input = createElement('select');
					input.className = 'btn';
					if (Array.isArray(cfg.select)) {
						cfg.select.forEach(value => {
							value = Array.isArray(value) ? value : [value, value];
							var option = new Option(value[0], value[1]);
							option.style[action] = value[1];
							input.append(option);
						});
					} else {
						Object.entries(cfg.select).forEach(([label, options]) => {
							let group = createElement('optgroup');
							group.label = label;
							Object.entries(options).forEach(([text, value]) => {
								var option = new Option(text, value);
								option.style[action] = value;
								group.append(option);
							});
							input.append(group);
						});
					}
					ev = 'input';
				} else {
					input = createElement('button');
					input.type = 'button';
					input.className = 'btn';
					input.innerHTML = cfg.html;
					input.action_cmd = cfg.cmd;
/*
					input.addEventListener('pointerdown', () => touchTap = input, {passive:true});
					input.addEventListener('pointermove', () => touchTap = null, {passive:true});
					input.addEventListener('pointercancel', () => touchTap = null);
					input.addEventListener('pointerup', e => {
						if (touchTap === input) {
							e.preventDefault();
							cfg.cmd(input);
						}
						touchTap = null;
					});
*/
				}
				input.addEventListener(ev, () => cfg.cmd(input));
				cfg.hint = i18n('EDITOR/' + action.toUpperCase());
				if (cfg.hint) {
					input.title = cfg.key ? cfg.hint + ' (' + ctrlKey + cfg.key + ')' : cfg.hint;
				} else if (cfg.key) {
					input.title = ctrlKey + cfg.key;
				}
				input.dataset.action = action;
				input.tabIndex = -1;
				cfg.input = input;
				toolgroup.append(input);
			}
			toolgroup.children.length && toolbar.append(toolgroup);
		}

		this.modeSelect = actions.mode.plain.input;

		let changes = actions.changes;
		changes.undo.input.disabled = changes.redo.input.disabled = true;
		squire.addEventListener('undoStateChange', e => {
			changes.undo.input.disabled = !e.detail.canUndo;
			changes.redo.input.disabled = !e.detail.canRedo;
		});

		actions.font.fontSize.input.selectedIndex = actions.font.fontSize.defaultValueIndex;

//		squire.addEventListener('focus', () => shortcuts.off());
//		squire.addEventListener('blur', () => shortcuts.on());

		container.append(toolbar, wysiwyg, plain);

		/**
		 * @param {string} fontName
		 * @return {string}
		 */
		const normalizeFontName = (fontName) => fontName.trim().replace(/(^["']*|["']*$)/g, '').trim().toLowerCase();

		/** @type {string[]} - lower cased array of available font families*/
		const fontFamiliesLowerCase = Object.values(actions.font.fontFamily.input.options).map(option => option.value.toLowerCase());

		/**
		 * A theme might have CSS like div.squire-wysiwyg[contenteditable="true"] {
		 * font-family: 'Times New Roman', Times, serif; }
		 * so let's find the best match squire.getRoot()'s font
		 * it will also help to properly handle generic font names like 'sans-serif'
		 * @type {number}
		 */
		let defaultFontFamilyIndex = 0;
		const squireRootFonts = getComputedStyle(squire.getRoot()).fontFamily.split(',').map(normalizeFontName);
		fontFamiliesLowerCase.some((family, index) => {
			const matchFound = family.split(',').some(availableFontName => {
				const normalizedFontName = normalizeFontName(availableFontName);
				return squireRootFonts.some(squireFontName => squireFontName === normalizedFontName);
			});
			if (matchFound) {
				defaultFontFamilyIndex = index;
			}
			return matchFound;
		});

		/**
		 * Instead of comparing whole 'font-family' strings,
		 * we are going to look for individual font names, because we might be
		 * editing a Draft started in another email client for example
		 *
		 * @type {Object.<string,number>}
		 */
		const fontNamesMap = {};
		/**
		 * @param {string} fontFamily
		 * @param {number} index
		 */
		const processFontFamilyString = (fontFamily, index) => {
			fontFamily.split(',').forEach(fontName => {
				const key = normalizeFontName(fontName);
				if (fontNamesMap[key] === undefined) {
					fontNamesMap[key] = index;
				}
			});
		};
		// first deal with the default font family
		processFontFamilyString(fontFamiliesLowerCase[defaultFontFamilyIndex], defaultFontFamilyIndex);
		// and now with the rest of the font families
		fontFamiliesLowerCase.forEach((fontFamily, index) => {
			if (index !== defaultFontFamilyIndex) {
				processFontFamilyString(fontFamily, index);
			}
		});

		// -----

		squire.addEventListener('pathChange', e => {

			const squireRoot = squire.getRoot();
			let elm = e.detail.element;

			forEachObjectValue(actions, entries => {
				forEachObjectValue(entries, cfg => {
//					cfg.matches && cfg.input.classList.toggle('active', elm && elm.matches(cfg.matches));
					cfg.matches && cfg.input.classList.toggle('active', elm && elm.closestWithin(cfg.matches, squireRoot));
				});
			});

			if (elm) {
				// try to find font-family and/or font-size and set "select" elements' values

				let sizeSelectedIndex = actions.font.fontSize.defaultValueIndex;
				let familySelectedIndex = defaultFontFamilyIndex;

				let familyFound = false;
				let sizeFound = false;
				do {
					if (!familyFound && elm.style.fontFamily) {
						familyFound = true;
						familySelectedIndex = -1; // show empty select if we don't know the font
						const fontNames = elm.style.fontFamily.split(',');
						for (let i = 0; i < fontNames.length; i++) {
							const index = fontNamesMap[normalizeFontName(fontNames[i])];
							if (index !== undefined) {
								familySelectedIndex = index;
								break;
							}
						}
					}

					if (!sizeFound && elm.style.fontSize) {
						sizeFound = true;
						// -1 is ok because it will just show a black <select>
						sizeSelectedIndex = actions.font.fontSize.select.indexOf(elm.style.fontSize);
					}

					elm = elm.parentElement;
				} while ((!familyFound || !sizeFound) && elm && elm !== squireRoot);

				actions.font.fontFamily.input.selectedIndex = familySelectedIndex;
				actions.font.fontSize.input.selectedIndex = sizeSelectedIndex;
			}
		});
/*
		squire.addEventListener('cursor', e => {
			console.dir({cursor:e.detail.range});
		});
		squire.addEventListener('select', e => {
			console.dir({select:e.detail.range});
		});
*/
	}

	doAction(name) {
		this.squire[name]();
		this.squire.focus();
	}

	doList(type) {
		let parent = this.squire.getSelectionClosest('UL,OL')?.nodeName,
			fn = {UL:'makeUnorderedList',OL:'makeOrderedList'};
		(parent == type) ? this.squire.removeList() : this.squire[fn[type]]();
	}
/*
	testPresenceinSelection(format, validation) {
		return validation.test(this.squire.getPath()) || this.squire.hasFormat(format);
	}
*/
	setMode(mode) {
		if (this.mode != mode) {
			let cl = this.container.classList, source = 'source' == this.mode;
			cl.remove('squire-mode-'+this.mode);
			if ('plain' == mode) {
				this.plain.value = htmlToPlain(source ? this.plain.value : this.squire.getHTML(), true);
			} else if ('source' == mode) {
				this.plain.value = this.squire.getHTML();
			} else {
				this.setData(source ? this.plain.value : plainToHtml(this.plain.value, true));
				mode = 'wysiwyg';
			}
			this.mode = mode; // 'wysiwyg' or 'plain'
			cl.add('squire-mode-'+mode);
			this.onModeChange?.();
			setTimeout(()=>this.focus(),1);
		}
		this.modeSelect.selectedIndex = 'plain' == this.mode ? 1 : 0;
	}

	on(type, fn) {
		if ('mode' == type) {
			this.onModeChange = fn;
		} else {
			this.squire.addEventListener(type, fn);
			this.plain.addEventListener(type, fn);
		}
	}

	execCommand(cmd, cfg) {
		if ('insertSignature' == cmd) {
			cfg = Object.assign({
				clearCache: false,
				isHtml: false,
				insertBefore: false,
				signature: ''
			}, cfg);

			if (cfg.clearCache) {
				this._prev_txt_sig = null;
			} else try {
				const signature = cfg.isHtml ? htmlToPlain(cfg.signature) : cfg.signature;
				if ('plain' === this.mode) {
					let
						text = this.plain.value,
						prevSignature = this._prev_txt_sig;
					if (prevSignature) {
						text = text.replace(prevSignature, '').trim();
					}
					this.plain.value = cfg.insertBefore ? '\n\n' + signature + '\n\n' + text : text + '\n\n' +  signature;
				} else {
					const squire = this.squire,
						root = squire.getRoot(),
						br = createElement('br'),
						div = createElement('div');
					div.className = 'rl-signature';
					div.innerHTML = cfg.isHtml ? cfg.signature : plainToHtml(cfg.signature);
					root.querySelectorAll('div.rl-signature').forEach(node => node.remove());
					cfg.insertBefore ? root.prepend(div) : root.append(div);
					// Move cursor above signature
					div.before(br);
					div.before(br.cloneNode());
//					squire._docWasChanged();
				}
				this._prev_txt_sig = signature;
			} catch (e) {
				console.error(e);
			}
		}
	}

	getData() {
		return 'source' == this.mode ? this.plain.value : trimLines(this.squire.getHTML());
	}

	setData(html) {
//		this.plain.value = html;
		const squire = this.squire;
		squire.setHTML(trimLines(html));
		const node = squire.getRoot(),
			range = squire.getSelection();
		range.setStart(node, 0);
		range.setEnd(node, 0);
		squire.setSelection( range );
	}

	getPlainData() {
		return this.plain.value;
	}

	setPlainData(text) {
		this.plain.value = text;
	}

	blur() {
		this.squire.blur();
	}

	focus() {
		if ('plain' == this.mode) {
			this.plain.focus();
			this.plain.setSelectionRange(0, 0);
		} else {
			this.squire.focus();
		}
	}
}

this.SquireUI = SquireUI;

})(document);
