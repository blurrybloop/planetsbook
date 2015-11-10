<!DOCTYPE html>
<html>
<head>
    <?php require 'html_head.php' ?>
    <link rel="stylesheet" href="/css/article.css" />
    <link rel="stylesheet" href="/css/profile.css" />
    <link rel="stylesheet" href="/css/admin.css" />
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
                            
                                <h1><?php echo ($this->data['user']['is_admin'] ? 'Опубликовать' : 'Добавить'); ?> статью</h1>
                                <form name="pub_form" method="post">
                                    <fieldset>
                                        <legend>Оглавление</legend>
                                        <label for="title">Название</label>
                                        <input name="title" id="title" type="text" required maxlength="100" pattern="^.+$" />
                                        <label for="description">Описание</label>
                                        <textarea name="description" id="description" required pattern="^.+$"></textarea>
                                        <label>Раздел</label>
                                        <div class="combobox">
                                            <div class="combohead">
                                                <div></div>
                                                <div class="arrow">
                                                    <img src="/img/down_arrow.png" />
                                                </div>
                                            </div>
                                            <div class="options">
                                                <?php if (!empty($this->data['sections'])) {
                                                          foreach ($this->data['sections'] as $section) {
                                                              echo "<div id='section{$section['id']}'>{$section['title']}</div>";
                                                          } 
                                                      }?>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <fieldset id="edit_content">
                                        <legend>Содержание</legend>
                                        
                                        <div id="tools">
                                            <div><img class='comm_bold' src='/img/bold.png' /><div class='tip'>Жирный<br />[b]Пример[/b]</div></div>
                                            <div><img class='comm_italic' src='/img/italic.png' /><div class='tip'>Курсив<br />[i]Пример[/i]</div></div>
                                            <div><img class='comm_underline' src='/img/underline.png' /><div class='tip'>Подчеркнутый<br />[u]Пример[/u]</div></div>
                                            <div><img class='comm_strike' src='/img/strike.png' /><div class='tip'>Зачеркнутый<br />[s]Пример[/s]</div></div>
                                            <div><img class='comm_sup' src='/img/superscript.png' /><div class='tip'>Верхний индекс<br />[sup]Пример[/sup]</div></div>
                                            <div><img class='comm_sub' src='/img/subscript.png' /><div class='tip'>Нижний индекс<br />[sub]Пример[/sub]</div></div>
                                            <div><img class='comm_left_align' src='/img/left_align.png' /><div class='tip'>Выравнивание по левому краю<br />[align=left]Пример[/align]</div></div>
                                            <div><img class='comm_center_align' src='/img/center_align.png' /><div class='tip'>Выравнивание по центру<br />[align=center]Пример[/align]</div></div>
                                            <div><img class='comm_right_align' src='/img/right_align.png' /><div class='tip'>Выравнивание по правому краю<br />[align=right]Пример[/align]</div></div>
                                            <div><img class='comm_justify_align' src='/img/justify_align.png' /><div class='tip'>Выравнивание по ширине<br />[align=justify]Пример[/align]</div></div>
                                            <div><img class='comm_ul' src='/img/list_bullets.png' /><div class='tip'>Маркированый список<br />[list=*]<br />[*]Один[/*]<br />[*]Два[/*]<br />[*]Три[/*]<br />[/list]</div></div>
                                            <div><img class='comm_ol' src='/img/list_num.png' /><div class='tip'>Нумерованый список<br />[list=(1|A|a|i|I)]<br />[1]Один[/1]<br />[2]Два[/2]<br />[3]Три[/3]<br />[/list]</div></div>
                                            <div><img class='comm_url' src='/img/link.png' /><div class='tip'>Ссылка<br />[url]planetsbook.pp.ua[/url]<br />или<br />[url="planetsbook.pp.ua"]Пример[/url]</div></div>
                                            <label for="hh"><img class='comm_img' src='/img/picture.png' /><span class='tip'>Рисунок с подписью<br />[figure=(left|center|right|float-left|float-right) width=# height=#]<br />[img]test.png[/img]<br />[figcaption=(left|center|right|justify)]Подпись[/figcaption]<br />[/figure]</span></label>
                                            <div><img class='comm_preview' src='/img/eye.png' /><div class='tip'>Предпросмотр</div></div>
                                        </div>

                                        <div id="article_content">
                                            <textarea id="contents" name="contents" required></textarea>
                                        </div>
                                        <h2>Прикрепленные изображения</h2>
                                        <div class="img_thumbs"></div>
                                    </fieldset>
                                    <input type="hidden" name="section_id" id="section_id"/>
                                    <fieldset>
                                        <input type="submit" name="pub_submit" id="pub_submit" value="<?php echo ($this->data['user']['is_admin'] ? 'Публиковать' : 'Предложить'); ?>"/>
                                        <input type="reset" />
                                    </fieldset>
                                </form>
                            <form name="images_form" target="superframe" method="post" enctype="multipart/form-data">
                                <input type="file" name="images[]" id="hh" multiple />
                            </form>
                            </div>
                        </div>
                    </div>
                <?php include('admin_aside.php'); ?>
                </div>
            <?php include('footer.php'); ?>
            </div>
        </div>


</body>
</html>
<script src="/js/sticky.js"></script>
<script>
    $('.read').append('<iframe id="superframe" name="superframe"></iframe>');
    var lock = false;

        $('#main').click(function (e) {
            if ($(e.target).parents('.combobox').length && $(e.target).parents('.options').length == 0) return;
            $('.combobox').removeClass('expanded');
        });

        $('.combobox .options > *').click(function () {
            $('#section_id').attr('value', $(this).attr('id').replace('section', ''));
            $(this).parent().siblings('.combohead').children('div:first-child').html($(this).html());
        });

        $('.combohead').click(function () {
            $(this).parent().toggleClass('expanded');
        })

    <?php if (isset($_REQUEST['section'])) {
              echo "if ($('#section{$_REQUEST['section']}').length != 0) $('#section{$_REQUEST['section']}').click(); else $('.options > div:first-child').click()";
          }
          else echo "$('.options > div:first-child').click()";
    ?>

    $('#tools').click(function (e) {
        var t = $(e.target);
        if (t.hasClass('comm_bold')) $(contents).makeBold();
        else if (t.hasClass('comm_italic')) $(contents).makeItalic();
        else if (t.hasClass('comm_underline')) $(contents).makeUnderline();
        else if (t.hasClass('comm_strike')) $(contents).makeStrike();
        else if (t.hasClass('comm_sub')) $(contents).makeSub();
        else if (t.hasClass('comm_sup')) $(contents).makeSup();
        else if (t.hasClass('comm_left_align')) $(contents).makeLeft();
        else if (t.hasClass('comm_center_align')) $(contents).makeCenter();
        else if (t.hasClass('comm_right_align')) $(contents).makeRight();
        else if (t.hasClass('comm_justify_align')) $(contents).makeJustify();
        else if (t.hasClass('comm_ul')) $(contents).makeUL();
        else if (t.hasClass('comm_ol')) $(contents).makeOL();
        else if (t.hasClass('comm_url')) $(contents).makeURL();
        else if (t.hasClass('comm_help')) comments.help();
    });

    var preview = false;

    $('.comm_preview').click(function () {
        lock = true;
        var transitionTimeout = 0; //время ожидания для ручного вызова события endTransition
        var d = $(article_content).transitionDuration();
        for (var i = 0; i < d.length; i++)
            if (parseFloat(d[i]) > transitionTimeout) transitionTimeout = parseFloat(d[i]);
        transitionTimeout *= 1000; transitionTimeout += 50;
        $('.comm_preview').attr('src', '/img/loading.gif');

        var callback = function (data) {
            $(article_content).transitionEnd(function () {
                lock = false;
                $('.comm_preview').attr('src', preview ? '/img/eye.png' : '/img/edit.png');
                $('.comm_preview + .tip').html(preview ? 'Предпросмотр' : 'Редактировать');
                var dt = new Date;
                if (preview) {
                    $('#article_content > div').remove();
                    $(contents).css('display', 'block');
                }
                else {
                    var d = $('<div><article>' + data + '</article></div>');
                    d.find('img').each(function () {
                        $(this).attr('src', $(this).attr('src') + '?' + dt.getTime());
                    });
                    $(contents).css('display', 'none');
                    d.insertAfter($(contents));
                }
                preview = !preview;
                setTimeout(function () { $(article_content).removeClass('invisible'); }, 0);
            }, transitionTimeout);
            $(article_content).addClass('invisible');
        }

        if (preview) {
            callback();
        }
        else {
            var j = $.post('/admin/preview/', { args: [$(contents).val()] }, callback).fail(function () { $('.comm_preview').removeClass('loading'); messageBox(j.responseText, 'center'); });
        }
    });

    $(pub_form).submit(function (e) {
        if (lock) return;
        lock = true;
        e.preventDefault();
            $('#pub_submit').addClass('loading');
            var j = $.post('/admin/addarticle/', $(this).serialize(), function(){
                messageBox('<?php if ($this->data['user']['is_admin']) echo '<p>Спасибо за публикацию!</p><p>Ваша статья теперь доступна для просмотра <a href="\' + j.responseText + \'">здесь</a></p>'; else echo  '<p>Большое спасибо за предложенную статью!</p>В ближайшее время мы проверим и опубликуем ее.</p>' ?>', 'left');
           }).fail(function(){
                messageBox(j.responseText, 'left');
           }).always(function () {
               lock = false;
                $('#pub_submit').removeClass('loading');
           });
    });

    $(pub_form).on('reset', function () {
        $('.thumb_action > div:nth-child(2)').click();
    });

    var pid;

    $(hh).change(function () {
        if (lock) return;
        lock = true;
        $('#superframe').one('load', function () {
            pid = $(this).contents().find('.page_id').html();
            if (pid) {
                 setInterval(function () { $.post('/pulse/', { 'page_id': pid }); }, 20000);
            }
            $(this).contents().find('.path').each(function () {
                if (window.contents)
                    $(contents).first().wrapSelected('\r\n[figure width=100]\r\n[img]' + $(this).html() + '[/img]\r\n[figcaption]', '[/figcaption]\r\n[/figure]\r\n');
                $('#edit_content > .img_thumbs').append("<div><div class='thumb_action'><div><div class='tip'>Вставить ВВ-код</div></div><div><div class='tip'>Удалить</div></div></div><img src='" + $(this).html() + "' /> <div>" + $(this).html() + "</div></div>")
            });
            lock = false;
        });
        $(images_form).attr('action', '/admin/uploadImg/?args=' + (pid ? pid : 0));
        setTimeout(function () { $(images_form).submit(); }, 0);
    });

    $('.img_thumbs').click(function (e) {
        var img = $(e.target).parent().next();
        if ($(e.target).is('.thumb_action > div:nth-child(2)')) {
            var j = $.post('/admin/removeImg', { 'args': [img.attr('src')] }, function () {
                img.parent().remove();
            }).fail(function () {
                messageBox('<p>Хьюстон, у нас проблемы!</p>' + j.responseText, 'left');
            });
        }
        else if ($(e.target).is('.thumb_action > div:nth-child(1)')) {
            if (window.contents)
                $(contents).first().wrapSelected('\r\n[figure width=100]\r\n[img]' + img.attr('src') + '[/img]\r\n[figcaption]', '[/figcaption]\r\n[/figure]\r\n');
        }
    });



</script>
