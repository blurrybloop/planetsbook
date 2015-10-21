
<div class="msgbox_container">
    <div>
        <div>
            <div class="msgbox">
                <div>
                    <div>✕</div>
                </div>
                <div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var sub = $('.msgbox_container > div > div').click
    $('.msgbox > div:first-child > div').click(function () {
        $(this).closest('.msgbox_container').removeClass('showed');
    });

    $('.msgbox_container > div > div').click(function (e) {
       if ($(e.target).closest('.msgbox').length == 0)
            $(this).closest('.msgbox_container').removeClass('showed');
    });

        function messageBox(content, align) {
            var c = $(content);
            var el = $('.msgbox > div:last-child');
            el.html('');
            el.append(c);
            $("<input type='checkbox' id='details_check' /><label for='details_check'>Детали<img src='/img/details_arrow.png' /></label>").insertBefore($('.details'));
            if (align) el.css('text-align', align);
            $('.msgbox_container').addClass('showed');
        }

</script>