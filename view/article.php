<!DOCTYPE html>
<html>
<head>
    <?php require 'html_head.php' ?>
    <link rel="stylesheet" href="/css/article.css" />
    <script src="/js/utils.js"></script>
    <script src="/js/comments.js"></script>
</head>
<body>
    <img src="<?php echo $this->data['section']['image'] ?>" />
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
                        <article>
                            <header>

                                <h1>
                                    <?php echo $this->data['article']['title'] ?>
                                </h1>
                                <div class="info">
                                    <time class="date">
                                        <?php echo $this->data['article']['pub_date'] ?>
                                    </time>
                                    <div class="user">
                                        <?php echo $this->data['article']['login'] ?>
                                    </div>
                                    <div class="views">
                                        <?php echo $this->data['article']['views'] ?>
                                    </div>
                                </div>
                            </header>
                            <?php
                    echo file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/sections/{$this->data['section']['data_folder']}/{$this->data['article']['article_id']}/text.txt");
                            ?>



                            <section class="comments">
                                <h1>Комментарии</h1>
                            </section>
                        </article>
                    </div>
                </div>
                <aside>
                    <div class="sticky">
                        <div>
                            <h1>Смотрите также</h1>
                            <ul>
                                <li>
                                    <a href="/article_temp.php">Изменяется ли светимость Солнца?</a>
                                </li>
                                <li>
                                    <a href="/article_temp.php">Солнечная активность</a>
                                </li>
                                <li>
                                    <a href="/article_temp.php">Солнечная активность и атмосфера Солнца</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
        <?php include("view/footer.php"); ?>
    </div>
</body>
</html>
<script>
    var sticky = $('.sticky');
    var commentsBlock = $('.comments');
    var cont = $('#content');
    var comments = new Comments(commentsBlock, <?php echo $this->data['article']['article_id'] ?>, <?php echo $this->validateRights([USER_REGISTERED],0, FALSE) ? '1' : '0' ?>);

        $.fn.attachCommentHandlers = function () {
        this.unbind('click');
        this.click(function (e) {
            var t = $(e.target);
            if (t.hasClass('comm_edit') || t.hasClass('add')) comments.edit($(this), t);
            else if (t.hasClass('comm_cancel')) comments.cancel($(this), t);
            else if (t.hasClass('comm_send')) comments.send($(this), t);
            else if (t.hasClass('comm_delete')) comments.delete($(this), t);
            else if (t.hasClass('comm_apply')) comments.apply($(this), t);
            else if (t.hasClass('comm_cancelApply')) comments.cancelApply($(this), t);
            else if (t.hasClass('comm_like')) {comments.rate($(this), t, 1);}
            else if (t.hasClass('comm_dislike')) comments.rate($(this), t, -1);
            else if (t.hasClass('comm_bold')) $(edit_field).makeBold();
            else if (t.hasClass('comm_italic')) $(edit_field).makeItalic();
            else if (t.hasClass('comm_underline')) $(edit_field).makeUnderline();
            else if (t.hasClass('comm_strike')) $(edit_field).makeStrike();
            else if (t.hasClass('comm_sub')) $(edit_field).makeSub();
            else if (t.hasClass('comm_sup')) $(edit_field).makeSup();
            else if (t.hasClass('comm_center_align')) $(edit_field).makeCenter();
            else if (t.hasClass('comm_right_align')) $(edit_field).makeRight();
            else if (t.hasClass('comm_justify_align')) $(edit_field).makeJustify();
            else if (t.hasClass('comm_ul')) $(edit_field).makeUL();
            else if (t.hasClass('comm_ol')) $(edit_field).makeOL();
            else if (t.hasClass('comm_url')) $(edit_field).makeURL();
            else if (t.hasClass('comm_img')) $(edit_field).makeFigure();
            else if (t.hasClass('comm_help')) comments.help();
        });
    }

    $.fn.wrapSelected = function (openTag, closeTag) {
        var textarea = this;
        var value = textarea.val();
        var start = textarea[0].selectionStart;
        var end = textarea[0].selectionEnd;
        textarea.val(value.substr(0, start) + openTag + value.substring(start, end) + closeTag + value.substring(end, value.length));
        textarea[0].selectionStart = start;
        textarea[0].selectionEnd = end;
    }

    $.fn.makeBold = function () { this.first().wrapSelected('[b]', '[/b]'); }
    $.fn.makeItalic = function () { this.first().wrapSelected('[i]', '[/i]'); }
    $.fn.makeUnderline = function () { this.first().wrapSelected('[u]', '[/u]'); }
    $.fn.makeStrike = function () { this.first().wrapSelected('[s]', '[/s]'); }
    $.fn.makeSub = function () { this.first().wrapSelected('[sub]', '[/s]'); }
    $.fn.makeSup = function () { this.first().wrapSelected('[sup]', '[/sup]'); }
    $.fn.makeUL = function () { this.first().wrapSelected('\r\n[list=*]\r\n[*]', '[/*]\r\n[/list]\r\n'); }
    $.fn.makeOL = function () { this.first().wrapSelected('\r\n[list=1]\r\n[1]', '[/1]\r\n[/list]\r\n'); }
    $.fn.makeURL = function () { this.first().wrapSelected('[url]', '[/url]'); }
    $.fn.makeFigure = function () { this.first().wrapSelected('\r\n[figure]\r\n[img][/img]\r\n[figcaption]', '[/figcaption]\r\n[/figure]\r\n'); }
    $.fn.makeLeft = function () { this.first().wrapSelected('\r\n[align=left]', '[/align]\r\n'); }
    $.fn.makeCenter = function () { this.first().wrapSelected('\r\n[align=center]', '[/align]\r\n'); }
    $.fn.makeRight = function () { this.first().wrapSelected('\r\n[align=right]', '[/align]\r\n'); }
    $.fn.makeJustify = function () { this.first().wrapSelected('\r\n[alignjustify]', '[/align]\r\n'); }

    comments.onUpdateComment = function(){
        this.attachCommentHandlers();
        iconSize();
    }

    $(window).scroll(function(){
        if (parseInt($(window).height()) + parseInt($(this).scrollTop()) > parseInt(commentsBlock.height()) + parseInt(commentsBlock.position().top) + 300) {
            comments.fetch();
        }
    });

    $(window).scroll(function () {
        if (parseInt(cont.offset().top) < parseInt($(this).scrollTop())) sticky.addClass('sticked');
        else sticky.removeClass('sticked');
    });

    $(window).scroll();

     function iconSize() {
        var f = $('.comm_footer')
        if (parseInt(f.width()) < 380)
            f.removeClass('maximized');
        else
            f.addClass('maximized');
    }

    $(window).resize(function() {iconSize(); sticky.width(sticky.parent().width());});
    $(window).resize();

</script>
