
<div class="msgbox_container">
    <div>
        <div>
            <div class="msgbox">
                <div>
                    <div>?</div>
                </div>
                <div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
        $('.msgbox > div:first-child > div').click(function () {
            $(this).parents('.msgbox_container').removeClass('showed');
        });

        function messageBox(content) {
            $('.msgbox > div:last-child').html(content);
            $('.msgbox_container').addClass('showed');
        }

</script>