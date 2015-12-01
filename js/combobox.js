
$('.js-combobox').each(function () {
    var i =$(this).attr('js-combobox-selected');
    var opt;
    if (i != undefined){
        opt = $(this).children('[js-combobox-option=' + i + ']').html();
    }
    var wrapper = (opt == undefined ? $(this).removeAttr('js-combobox-selected') : $(this).attr('js-combobox-selected', i)).children().wrapAll("<div class='js-combobox-options'></div>").parent();
    $("<div class='js-combobox-head'>" + 
            "<div>" + (opt == undefined ? "&nbsp" : opt) + "</div>" + 
            "<div class='js-combobox-arrow'>" + 
                "<img src='/img/down_arrow.png' />" + 
            "</div>" +
       "</div>").insertBefore(wrapper);
    if (opt != undefined) $(this).change();

});

$(window).click(function (e) {
    var clicked = $(e.target).closest('.js-combobox');
    $('.js-combobox').not(clicked).removeClass('js-combobox-expanded');
    if (clicked.length && $(e.target).closest('.js-combobox-options').length == 0) return;
    clicked.removeClass('js-combobox-expanded');
});

$('.js-combobox-options > *').click(function () {
    $(this).parent().siblings('.js-combobox-head').children('div:first-child').html($(this).html());
    $(this).closest('.js-combobox').attr('js-combobox-selected', $(this).attr('js-combobox-option')).change();
});

$('.js-combobox-head').click(function () {
    $(this).parent().toggleClass('js-combobox-expanded');
})