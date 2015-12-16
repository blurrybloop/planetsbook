$.fn.attachCombobox = function () {   
    $(this).toggleClass('js-combobox', true);
    if ($(this).children('[data-combobox-option]').length == 0) return undefined;
    var i = $(this).attr('data-combobox-selected');
    var opt;
    if (i != undefined) {
        opt = $(this).children('[data-combobox-option=' + i + ']').html();
    }
    
    var wrapper = (opt == undefined ? $(this).removeAttr('data-combobox-selected') : $(this).attr('data-combobox-selected', i)).children().wrapAll("<div class='js-combobox-options'></div>").click(function () {
        $(this).parent().siblings('.js-combobox-head').children('div:first-child').html($(this).html());
        $(this).closest('.js-combobox').attr('data-combobox-selected', $(this).attr('data-combobox-option')).change();
    }).parent();
    $("<div class='js-combobox-head'>" +
            "<div>" + (opt == undefined ? "&nbsp" : opt) + "</div>" +
            "<div class='js-combobox-arrow'>" +
                "<img src='/img/down_arrow.png' />" +
            "</div>" +
       "</div>").click(function () {
           $(this).parent().toggleClass('js-combobox-expanded');
       }).insertBefore(wrapper);
    if (opt != undefined) $(this).change();
    return $(this);
}

$('.js-combobox').each(function () {
    $(this).attachCombobox();
});

$(window).click(function (e) {
    var clicked = $(e.target).closest('.js-combobox');
    $('.js-combobox').not(clicked).removeClass('js-combobox-expanded');
    if (clicked.length && $(e.target).closest('.js-combobox-options').length == 0) return;
    clicked.removeClass('js-combobox-expanded');
});