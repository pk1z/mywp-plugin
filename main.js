/**
 * Created by root on 02.06.14.
 */

var onmessage = function (e) {
    console.log(e.data);
    if ('ready' == e.data.action ){
        json = jQuery('input#uptolike_json').val();
        initConstr(json);
    }
    if (('json' in e.data) && ('code' in e.data)) {
        $('input#uptolike_json').val(e.data.json);
        $('#widget_code').val(e.data.code);
        //console.log(e.data);
        //document.getElementById("widget_code").innerText = e.data.code;
        jQuery('#settings_form').submit();
    }
  //  if ('code' in e.data) {

        //jQuery('#settings_form').submit();
    //}
    if (e.data.url.indexOf('constructor.html', 0) != -1) {
        document.getElementById("cons_iframe").style.height = e.data.size + 'px';
    }
    if (e.data.url.indexOf('statistics.html', 0) != -1) {
        document.getElementById("stats_iframe").style.height = e.data.size + 'px';
    }
// console.log(e.data.size);
//  console.log(e.data.url);
};

if (typeof window.addEventListener != 'undefined') {
    window.addEventListener('message', onmessage, false);
} else if (typeof window.attachEvent != 'undefined') {
    window.attachEvent('onmessage', onmessage);
}
var getCode = function () {
    var win = document.getElementById("cons_iframe").contentWindow;
    win.postMessage({action: 'getCode'}, "*");
};
function initConstr(jsonStr) {
     var win = document.getElementById("cons_iframe").contentWindow;
    //document.getElementById('cons_iframe').src = 'http://dev3.lembrd.com:7070/api/constructor.html';
    if ('' !== jsonStr) {
        console.log({action: 'initialize', json: jsonStr});
        win.postMessage({action: 'initialize', json: jsonStr}, "*");
    }

}


function regMe(my_mail) {
    str = jQuery.param({ email: my_mail, partner: 'cms', projectId: 'cms' + document.location.host})
    dataURL = "http://dev3.lembrd.com:7070/api/getCryptKeyWithUserReg.json";
    jQuery.getJSON(dataURL + "?" + str + "&callback=?", {}, function (result) {
        var jsonString = JSON.stringify(result);
        var result = JSON.parse(jsonString);
        //console.log(result.statusCode);
        if ('ALREADY_EXISTS' == result.statusCode) {
            alert('Пользователь с таким email уже зарегистрирован, обратитесь в службу поддержки.');
        } else if ('MAIL_SENDED' == result.statusCode) {
            alert('Ключ отправлен вам на email. Теперь необходимо войти.');
            $('.reg_block').toggle('fast');
            $('.reg_btn').toggle('fast');
            $('.enter_btn').toggle('fast');
            $('.enter_block').toggle('fast');

        } else if ('ILLEGAL_ARGUMENTS' == result.statusCode) {
            alert('Email указан неверно.')
        }
    });
}

function hashChange(){
    var hsh = document.location.hash
    if (('#reg' == hsh) || ('#enter' == hsh)) {

        $('.nav-tab-wrapper a').removeClass('nav-tab-active');
        $('a.nav-tab#stat').addClass('nav-tab-active');
        $('.wrapper-tab').removeClass('active');
        $('#con_stat').addClass('active');

        if ('#reg' == hsh) {
            $('.reg_btn').show();
            $('.reg_block').show();
            $('.enter_btn').hide();
            $('.enter_block').hide();
        }
        if ('#enter' == hsh) {
            $('.reg_btn').hide();
            $('.reg_block').hide();
            $('.enter_btn').show();
            $('.enter_block').show();
        }
    }
}

window.onhashchange = function() {
    hashChange();
}

jQuery(document).ready(function () {
    $ = jQuery;

    $('input.id_number').css('width','520px');//TODO dafuq? fixit
    $('.uptolike_email').val($('#uptolike_email').val())//init fields with hidden value (server email)
    $('.enter_block input.id_number').attr('value', $('table input.id_number').val());




    jQuery('div.enter_block').hide();
    jQuery('div.reg_block').hide();

    $('.reg_btn').click(function(){
        $('.reg_block').toggle('fast');
        $('.enter_btn').toggle('fast');
    })

    $('.enter_btn').click(function(){
        $('.enter_block').toggle('fast');
        $('.reg_btn').toggle('fast');
    })

    $('.reg_block button').click(function(){
        my_email = $('.reg_block .uptolike_email').val();
        regMe(my_email);
    })

    $('.enter_block button').click(function(){
        my_email = $('.enter_block input.uptolike_email').val();
        my_key = $('.enter_block input.id_number').val();
        $('table input.id_number').attr('value',my_key);
        $('table input#uptolike_email').attr('value',my_email);
        //alert('done, check fields');
        jQuery('#settings_form').submit();
    })

    json = jQuery('input#uptolike_json').val();
    //console.log(json);
    //создаем конструктор. Если настроек ещё не было (json=''), создаём конструктор без настроек
    initConstr(json);

    if (jQuery('#id_number').val() == '') {
        //jQuery('#uptolike_email').after('<a href="options-general.php?page=uptolike_settings&regme">Зарегистрироваться</a>');
        jQuery('#uptolike_email').after('<button type="button" onclick="regMe();">Зарегистрироваться</button>');
    }
    jQuery('#widget_code').parent().parent().attr('style', 'display:none');
    jQuery('#uptolike_json').parent().parent().attr('style', 'display:none')
    jQuery('table .id_number').parent().parent().attr('style', 'display:none')
    jQuery('#uptolike_email').parent().parent().attr('style', 'display:none')

    $('.nav-tab-wrapper a').click(function () {
        var click_id = $(this).attr('id');
        if (click_id != $('.nav-tab-wrapper a.nav-tab-active').attr('id')) {
            $('.nav-tab-wrapper a').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            $('.wrapper-tab').removeClass('active');
           // console.log(click_id);
            $('#con_' + click_id).addClass('active');
        }
    });


    hashChange();

});

