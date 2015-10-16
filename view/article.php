<!DOCTYPE html>
<html>
<head>
    <?php require 'html_head.php' ?>
    <link rel="stylesheet" href="/css/article.css" />
    <script src="/js/utils.js"></script>
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
                        
                        <h1><?php echo $this->data['article']['title'] ?></h1>
                        <div class="info">
                            <time class="date"><?php echo $this->data['article']['pub_date'] ?></time>
                            <div class="user"><?php echo $this->data['article']['login'] ?></div>
                            <div class="views"><?php echo $this->data['article']['views'] ?></div>
                        </div>
                    </header>
                    <?php 
                    echo file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/sections/{$this->data['section']['data_folder']}/{$this->data['article']['article_id']}/text.txt");
                    ?>

                   

                    <section class="comments">
                        <h1>Комментарии</h1>
                        <div class="add">Добавить комментарий</div>
                        <article>
                            <div class="comment">
                                <div>
                                    vasia_pupkin
                                    <div>
                                        <img src="/img/user_big.png" style="width: 50%" />
                                    </div>
                                    Пользователь
                                    <div class="info">
                                        <div>Репутация: <span style="color: green;">+5</span></div>
                                        <div>Зарегистирован: <time datetime="2015-09-29">29.09.2015</time></div>
                                        <div>Комментариев: 3</div>
                                    </div>
                                </div>
                                <div>
                                    <div class="comm_header">
                                        <time datetime="2015-10-02 17:40">02.10.2015 17:40</time><div class="rate"><img src="/img/like.png" /><span style="color: green;">+1</span><img src="/img/dislike.png" /></div>
                                    </div>
                                    <div class="comm_body">
                                        <p>Здесь хотелось бы упомянуть <u>об удивительном сходстве</u> цветка подсолнечника с Солнцем. Будто сама природа создала на Земле маленькую модель Солнца. Подобно солнечной короне, шляпка подсолнуха окружают желтые лепестки. Желто-оранжевые цветки подсолнечника имеют трубчатую форму, подобно трубчатым плазменным волокнам Солнца, Они также плотно, но, не сливаясь, покрывают поверхность шляпки подсолнечника. Цветки подсолнечника растут из черных семечек, подобных черным гранулам у основания плазменных волокон. Люди давно подметили схожесть этого растения с Солнцем и его особенность поворачиваться за Солнцем, поэтому и дали ему имя производное от Солнца.</p><p>Пятна на Солнце должны рассматриваться как явление положительное, хотя они и вызывают возмущение магнитного поля Земли, что может привести к сбоям в работе некоторых систем. Падение небесных тел увеличивает массу Солнца, которая непрерывно убывает, поддерживают его активность, и, как следствие, сдерживают удаление планет, а это очень важный положительный фактор.</p>
                                    </div>
                                    <div class="comm_footer maximized">
                                        <div class="comm_del"></div><div class="comm_edit">&nbsp;</div>
                                    </div>
                                </div>
                            </div>
                        </article>
                        <article>
                            <div class="comment">
                                <div>
                                    mudak007
                                    <div>
                                        <img src="/img/user_big.png" style="width: 50%" />
                                    </div>
                                    Пользователь
                                    <div class="info">
                                        <div>Репутация: <span style="color: red;">-3</span></div>
                                        <div>Зарегистирован: <time datetime="2015-09-29">30.09.2015</time></div>
                                        <div>Комментариев: 1</div>
                                    </div>
                                </div>
                                <div>

                                    <div class="comm_header">
                                        <time datetime="2015-10-02 17:40">02.10.2015 17:45</time><div class="rate"><img src="/img/like.png" /><span style="color: red;">-2</span><img src="/img/dislike.png" /></div>
                                    </div>
                                    <div class="comm_body">
                                        <p>
                                            азазазазазаззазазазазазазаза)))00)0))0)))
                                        </p>
                                    </div>
                                    <div class="comm_footer maximized">
                                        <div class="comm_del">&nbsp;</div><div class="comm_edit">&nbsp;</div>
                                    </div>
                                </div>
                            </div>
                        </article>
                        <article>
                            <div class="comment">
                                <div>
                                    PlanetsBook
                                    <div>
                                        <img src="img/favicon.png" style="width: 50%" />
                                    </div>
                                    Администратор
                                    <div class="info">
                                        <div>Репутация: <span style="color: green;">+10</span></div>
                                        <div>Зарегистирован: <time datetime="2015-09-29">04.09.2015</time></div>
                                        <div>Комментариев: 10</div>
                                    </div>
                                </div>
                                <div>
                                    <div class="comm_header">
                                        <time datetime="2015-10-02 17:40">02.10.2015 18:05</time><div class="rate"><img src="img/like.png" /><span>0</span><img src="img/dislike.png" /></div>
                                    </div>
                                    <div class="comm_body">
                                        <p>
                                            mudak007, ни смишно
                                        </p>
                                    </div>
                                    <div class="comm_footer maximized">
                                        <div class="comm_del">&nbsp;</div><div class="comm_edit">&nbsp;</div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </section>
                </article>
            </div>
        </div>
        <aside>
            <div class="sticky">
                <div>
                    <h1>Смотрите также</h1>
                    <ul>
                        <li><a href="/article_temp.php">Изменяется ли светимость Солнца?</a></li>
                        <li><a href="/article_temp.php">Солнечная активность</a></li>
                        <li><a href="/article_temp.php">Солнечная активность и атмосфера Солнца</a></li>
                    </ul>
                </div>
            </div>
        </aside>
    </div>

    <?php include("view/footer.php"); ?>
    <script src="js/utils.js"></script>
    <script>
    var sticky = $('.sticky');
    var cont = $('#content');

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

    $(window).resize(function () { iconSize(); sticky.width(sticky.parent().width()) });
    $(window).resize();

    var editComm = $("<div><div class='comm_header'><div><img class='comm_bold' src='img/bold.png'/><div class='tip'>Вставить тег жирного начертания</div></div><div><img class='comm_italic' src='img/italic.png'/><div class='tip'>Вставить тег курсивного начертания</div></div><div><img class='comm_underline' src='img/underline.png'/><div class='tip'>Вставить тег подчеркивания</div></div><div><img class='comm_left_align' src='img/left_align.png'/><div class='tip'>Выравнивание по левому краю</div></div><div><img class='comm_center_align' src='img/center_align.png'/><div class='tip'>Выравнивание по центру</div></div><div><img class='comm_right_align' src='img/right_align.png'/><div class='tip'>Выравнивание по правому краю</div></div><div><img class='comm_justify_align' src='img/justify_align.png'/><div class='tip'>Выравнивание по ширине</div></div></div><div class='comm_body'><form name='add_comm'><textarea name='add_comm' placeholder='Введите ваш комментарий...'></textarea></form></div><div class='comm_footer maximized nohide'><div class='comm_send'>&nbsp;</div><div class='comm_cancel'>&nbsp;</div><div class='comm_apply'>&nbsp;</div></div>");
    var prevComm = null;
    var lock = false;


    //расширяющие методы jQuery для комментариев

    $.fn.attachCommentHandlers = function()
    {
        this.click(function (e) {
            var t = $(e.target);
            if (t.hasClass('comm_edit') || t.hasClass('add')) $(this).beginEdit(t);
            else if (t.hasClass('comm_cancel')) $(this).cancelEdit(t);
            else if (t.hasClass('comm_apply')) $(this).apply(t);
            else if (t.hasClass('comm_cancel_apply')) $(this).cancelApply(t);
            else if (t.hasClass('comm_bold')) $(add_comm).children('textarea').makeBold();
            else if (t.hasClass('comm_italic')) $(add_comm).children('textarea').makeItalic();
            else if (t.hasClass('comm_underline')) $(add_comm).children('textarea').makeUnderline();
            else if (t.hasClass('comm_left_align')) $(add_comm).children('textarea').makeLeft();
            else if (t.hasClass('comm_center_align')) $(add_comm).children('textarea').makeCenter();
            else if (t.hasClass('comm_right_align')) $(add_comm).children('textarea').makeRight();
            else if (t.hasClass('comm_justify_align')) $(add_comm).children('textarea').makeJustify();
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

    $.fn.makeBold = function () { this.first().wrapSelected('[b]', '[/b]');}
    $.fn.makeItalic = function () { this.first().wrapSelected('[i]', '[/i]'); }
    $.fn.makeUnderline = function () { this.first().wrapSelected('[u]', '[/u]'); }
    $.fn.makeLeft = function () { this.first().wrapSelected('[align=left]', '[/align]'); }
    $.fn.makeCenter = function () { this.first().wrapSelected('[align=center]', '[/align]'); }
    $.fn.makeRight = function () { this.first().wrapSelected('[align=right]', '[/align]'); }
    $.fn.makeJustify = function () { this.first().wrapSelected('[align=justify]', '[/align]'); }

    $.fn.toggleVisibilty = function (callback) {
        this.transitionEnd(function () {
            var ret = callback.call(this);
            setTimeout(function (e) { $(e).removeClass('invisible')}, 10, ret);
        });
        if (endTransitionEvent() != null) this.addClass('invisible');
    }

    $.fn.apply = function (target) {
        target.addClass("loading");
        $(this).toggleVisibilty(function () {
            target.removeClass("loading");
            var field = $(this).children(':nth-child(2)');

            ////////////////////////////////////////////////
            var h = $(prevComm).children('.comm_header').html();
            if (h == undefined) h = "<time datetime='2015-10-02 17:40'>02.10.2015 18:05</time><div class='rate'><img src='img/like.png' /><span >0</span><img src='img/dislike.png' /></div>";
            editComm.replaceWith("<div><div class='comm_header'>" + h + "</div><div class='comm_body'>" + $(add_comm).children('textarea').val() + "</div><div class='comm_footer maximized edit'><div class='comm_cancel_apply'>&nbsp;</div><div class='comm_cancel'>&nbsp;</div><div class='comm_send'>&nbsp;</div></div>");
            ///////////////Здесь Ajax......////////////////
            iconSize();
            return this;
        });
    }

    $.fn.cancelApply = function (target) {
        target.addClass("loading");
        $(this).toggleVisibilty(function () {
            target.removeClass("loading");
            $(this).children(':nth-child(2)').replaceWith(editComm);
            iconSize();
            return this;
        });
    }

    $.fn.cancelEdit = function (target) {
        target.addClass("loading");
        $(this).toggleVisibilty(function () {
            target.removeClass("loading");
                var isAdd = $(this).hasClass('comm_add');
                (isAdd ? $(this).parent() : $(this).children(':nth-child(2)')).replaceWith(prevComm);
                if (isAdd) prevComm.attachCommentHandlers();
                lock = false;
                iconSize();
                return isAdd ? prevComm : this;
            });
    }

    $.fn.beginEdit = function (target) {
        if (lock) { messageBox("<p>Вы пытались редактировать два комментария одновременно, поэтому мы заблокировали Ваше действие.</p><p>Отмените или подтвердите редактирование другого комментария.</p>"); return; }
        lock = true;
        var isAdd = $(this).hasClass('add');
        target.addClass('loading');
        target.parent().addClass("nohide");
        $(this).toggleVisibilty(function () {
            target.removeClass('loading');
            target.parent().removeClass("nohide");
                var a = isAdd ? $("<article class='invisible' style='margin-bottom:4em'></div>").append($("<div class='comment comm_add'><div>PlanetsBook<div><img src='img/favicon.png' style='width: 50%' /></div>Администратор<div class='info'><div>Репутация: <span style='color: green;'>+10</span></div><div>Зарегистирован: <time datetime='2015-09-29'>04.09.2015</time></div><div>Комментариев: 10</div></div></div>").append(editComm)) : null;
                var field = isAdd ? $(this) : $(this).children(':nth-child(2)');
                var h = isAdd ? 150 : field.children(':nth-child(2)').height(), txt = isAdd ? "" : field.children(':nth-child(2)').html();
                prevComm = field.replaceWith(isAdd ? a : editComm);
                add_comm.reset();
                $(add_comm).children('textarea').height(parseInt(h) < 150 ? 150 : h).html(txt); //txt из ajax
                if (isAdd) a.children('.comment').attachCommentHandlers();
                iconSize();
                return isAdd ? a : this;
            });
    }
    $('.comment').attachCommentHandlers();
    $('.comments > .add').attachCommentHandlers();
    </script>
