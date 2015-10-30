<!DOCTYPE html>
<html>
<head>
<?php require 'html_head.php' ?>
    <link rel="stylesheet" href="/css/article.css" />
    <link rel="stylesheet" href="/css/profile.css" />
    <script src="/js/utils.js"></script>
</head>
<body>

<?php 
echo $this->data['menu'];
require 'msgbox.php' 
?>
        <div id="main">
            <div class="banner">
                <a href="/">
                    <img src="/img/logo.png" />
                </a>
            </div>
            <div id="content">
                <div>
                    <div>
                        <div class="read">
                            <div>
                            </div>
                        </div>

                    </div>
                    <aside>
                        <div class="sticky">
                            <div>
                                <div class="section"><div><p>Просмотр</p></div></div>
                                <div class="section selected"><div><p>Изменить</p></div></div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
            <?php include('footer.php'); ?>
        </div>
</body>
</html>
<script>
    var sticky = $('.sticky');
    var cont = $('#content');

    $(window).scroll(function () {
        if (parseInt(cont.offset().top) < parseInt($(this).scrollTop())) sticky.addClass('sticked');
        else sticky.removeClass('sticked');
    });

    $(window).scroll();

    $(window).resize(function () { sticky.width(sticky.parent().width()) });
    $(window).resize();
    
    $('.section').click(function () {
        location.reload();
        //$('#sel').css('top', $(this).position().top + ($(this).height() - $('#sel').height()) / 2);
        //setTimeout(function () { $('#sel').removeClass('notransition') }, 0);
    });

    //$('#sel').addClass('notransition');
    //$('.section:nth-child(2)').click();
</script>

