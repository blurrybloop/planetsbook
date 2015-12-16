
<script>
    function msgboxClose() {
        $('.msgbox_container').last().transitionEnd(function(){ $(this).remove() }, 250).removeClass('showed');
    }

    function messageBox(content, align, _width) {
        var container = $('<div class="msgbox_container">' +
            '<div>' +
                '<div>' +
                '</div>' +
            '</div>' +
        '</div>').click(function (e) {
            if ($(e.target).closest('.msgbox').length == 0) {
                msgboxClose();
            }
        });

        var c = $(content).wrap("div");
        $("<input type='checkbox' id='details_check' /><label for='details_check'>Детали<img src='/img/details_arrow.png' /></label>").insertBefore(c.find('.details'))

        var box = $('<div class="msgbox"></div>')
                .append($('<div><div>✕</div></div>').children().first().click(function () { msgboxClose() }).parent())
                .append(c).css('text-align', align ? align : 'left').width(_width ? _width : '30%');
        container.children().children().append(box);
        $('body').append(container);
        setTimeout( function() { container.addClass('showed') }, 0);
 }

</script>