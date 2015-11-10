var sticky = $('.sticky');
var cont = $('#content');
var lock = false;

$(window).scroll(function () {
    if (parseInt(cont.offset().top) < parseInt($(this).scrollTop())) sticky.addClass('sticked');
    else sticky.removeClass('sticked');
});

$(window).scroll();

$(window).resize(function () { sticky.width(sticky.parent().width()) });
$(window).resize();