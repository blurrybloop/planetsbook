<!DOCTYPE html>
<html>
<head>
    <?php require 'html_head.php' ?>
    <link rel="stylesheet" href="/css/article.css" />
    <link rel="stylesheet" href="/css/profile.css" />
    <link rel="stylesheet" href="/css/admin.css" />
    <link rel="stylesheet" href="/css/combobox.css" />
    <script src="/js/utils.js"></script>
    <script src="/js/image_uploader.js"></script>
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
                            <?php if (empty($this->data['subaction'])) { ?>
                            <h1>
                                Публикации
                            </h1>
                            <div style="text-align: center;"><div class="add"><a href="/admin/articles/add/">Новая статья</a> </div></div>
                            <div class="updown_head">
                                <div>
                                    Сортировка
                                    <div class="js-combobox" js-combobox-selected="<?php echo isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 0?>" id="cat_combo">
                                        <a js-combobox-option="0" href="?sort=0<?php if ($this->data['split']) echo '&split=1'; ?>">Дата добавления</a>
                                        <a js-combobox-option="1" href="?sort=1<?php if ($this->data['split']) echo '&split=1'; ?>">Популярность</a>
                                        <a js-combobox-option="2" href="?sort=2<?php if ($this->data['split']) echo '&split=1'; ?>">Алфавит</a>
                                        <a js-combobox-option="3" href="?sort=3<?php if ($this->data['split']) echo '&split=1'; ?>">Сначала непроверенные</a>

                                    </div>
                                </div>
                                <div>
                                    <label for="section_split">Разбить по разделам</label>
                                    <input type="checkbox" id="section_split" name="section_split" <?php if (!empty($this->data['split'])) echo 'checked' ?> />
                                </div>
                            </div>
                            <div class="updown">
                                <?php
                                  $old_section = '';
                                foreach ($this->data['articles'] as $article) {  ?>
                                <?php if (!empty($this->data['split']) && $old_section != $article['section_title']) { ?>
                                <h1>
                                    <?php echo $article['section_title'] ?>
                                </h1>
                                <?php
                                          $old_section = $article['section_title'];
                                      } ?>
                                <input name="item" id="article<?php echo $article['article_id'] ?>" type="checkbox" />
                                <div>
                                    <label for="article<?php echo $article['article_id'] ?>">
                                        <img src="/img/down_arrow.png" />
                                        <a href=<?php echo "/sections/{$article['data_folder']}/{$article['article_id']}/" ?>>
                                            <?php echo $article['title'] ?>
                                        </a>
                                        <span class="info">
                                            <span class="date">
                                                <?php echo $article['pub_date'] ?>
                                            </span>
                                            <span class="user">
                                                <?php echo $article['login'] ?>
                                            </span>
                                            <span class="views">
                                                <?php echo $article['views'] ?>
                                            </span>
                                            <?php if (!$this->data['split']) { ?>
                                            <span class="a_section">
                                                <?php echo $article['section_title'] ?>
                                            </span>
                                            <?php } ?>
                                        </span>
                                        <span class="updown_action">
                                            <span class="edit">
                                                <a href="/admin/articles/edit/?args=<?php echo $article['article_id']; ?>"><?php echo (!empty($article['verifier_id']) ?  'Редактировать' : 'Проверить'); ?></a>
                                            </span>
                                            <span class="remove">
                                                <a href="javascript:void(0)">Удалить</a>
                                            </span>
                                        </span>
                                    </label>
                                    <div class="updown_content">
                                        <?php
                                      echo file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/sections/{$article['data_folder']}/{$article['article_id']}/description.txt");
                                        ?>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                            <?php } else if ($this->data['subaction'] == 'add' || $this->data['subaction'] == 'edit') { ?>
                            <h1>
                                <?php if ($this->data['subaction'] == 'edit') echo 'Редактировать';
                                      else if ($this->data['user']['is_admin']) echo 'Опубликовать';
                                      else echo 'Добавить'; ?> статью
                            </h1>
                            <form name="pub_form" method="post">
                                <fieldset>
                                    <legend>Оглавление</legend>
                                    <label for="title">Название</label>
                                    <input name="title" id="title" type="text" required="" maxlength="100" pattern="^.+$" value="<?php if (!empty($this->data['article']['title'])) echo $this->data['article']['title']; ?>"/>
                                    <label for="description">Описание</label>
                                    <textarea name="description" id="description" required="" pattern="^.+$"><?php  if (!empty($this->data['article']['description'])) echo $this->data['article']['description']; ?></textarea>
                                    <label>Раздел</label>
                                    <div class="js-combobox" id="section_combo" js-combobox-selected="<?php if (!empty($this->data['article']['section_id'])) echo $this->data['article']['section_id']; else if (isset($_GET['section']) && is_numeric($_GET['section'])) echo $_GET['section']; ?>">
                                        <?php if (!empty($this->data['sections'])) {
                                                          foreach ($this->data['sections'] as $section) {
                                                              echo "<div js-combobox-option='{$section['id']}'>{$section['title']}</div>";
                                                          }
                                                      }?>
                                    </div>
                                </fieldset>
                                <fieldset id="edit_content">
                                    <legend>Содержание</legend>

                                    <div id="tools">
                                        <div>
                                            <img class='comm_bold' src='/img/bold.png' />
                                            <div class='tip'>
                                                Жирный
                                                <br />
                                                [b]Пример[/b]
                                            </div>
                                        </div>
                                        <div>
                                            <img class='comm_italic' src='/img/italic.png' />
                                            <div class='tip'>
                                                Курсив
                                                <br />
                                                [i]Пример[/i]
                                            </div>
                                        </div>
                                        <div>
                                            <img class='comm_underline' src='/img/underline.png' />
                                            <div class='tip'>
                                                Подчеркнутый
                                                <br />
                                                [u]Пример[/u]
                                            </div>
                                        </div>
                                        <div>
                                            <img class='comm_strike' src='/img/strike.png' />
                                            <div class='tip'>
                                                Зачеркнутый
                                                <br />
                                                [s]Пример[/s]
                                            </div>
                                        </div>
                                        <div>
                                            <img class='comm_sup' src='/img/superscript.png' />
                                            <div class='tip'>
                                                Верхний индекс
                                                <br />
                                                [sup]Пример[/sup]
                                            </div>
                                        </div>
                                        <div>
                                            <img class='comm_sub' src='/img/subscript.png' />
                                            <div class='tip'>
                                                Нижний индекс
                                                <br />
                                                [sub]Пример[/sub]
                                            </div>
                                        </div>
                                        <div>
                                            <img class='comm_left_align' src='/img/left_align.png' />
                                            <div class='tip'>
                                                Выравнивание по левому краю
                                                <br />
                                                [align=left]Пример[/align]
                                            </div>
                                        </div>
                                        <div>
                                            <img class='comm_center_align' src='/img/center_align.png' />
                                            <div class='tip'>
                                                Выравнивание по центру
                                                <br />
                                                [align=center]Пример[/align]
                                            </div>
                                        </div>
                                        <div>
                                            <img class='comm_right_align' src='/img/right_align.png' />
                                            <div class='tip'>
                                                Выравнивание по правому краю
                                                <br />
                                                [align=right]Пример[/align]
                                            </div>
                                        </div>
                                        <div>
                                            <img class='comm_justify_align' src='/img/justify_align.png' />
                                            <div class='tip'>
                                                Выравнивание по ширине
                                                <br />
                                                [align=justify]Пример[/align]
                                            </div>
                                        </div>
                                        <div>
                                            <img class='comm_ul' src='/img/list_bullets.png' />
                                            <div class='tip'>
                                                Маркированый список
                                                <br />
                                                [list=*]
                                                <br />
                                                [*]Один[/*]
                                                <br />
                                                [*]Два[/*]
                                                <br />
                                                [*]Три[/*]
                                                <br />
                                                [/list]
                                            </div>
                                        </div>
                                        <div>
                                            <img class='comm_ol' src='/img/list_num.png' />
                                            <div class='tip'>
                                                Нумерованый список
                                                <br />
                                                [list=(1|A|a|i|I)]
                                                <br />
                                                [1]Один[/1]
                                                <br />
                                                [2]Два[/2]
                                                <br />
                                                [3]Три[/3]
                                                <br />
                                                [/list]
                                            </div>
                                        </div>
                                        <div>
                                            <img class='comm_url' src='/img/link.png' />
                                            <div class='tip'>
                                                Ссылка
                                                <br />
                                                [url]planetsbook.pp.ua[/url]
                                                <br />
                                                или
                                                <br />
                                                [url="planetsbook.pp.ua"]Пример[/url]
                                            </div>
                                        </div>
                                        <div>
                                            <img class='comm_img' src='/img/picture.png' />
                                            <span class='tip'>
                                                Рисунок с подписью
                                                <br />
                                                [figure=(left|center|right|float-left|float-right) width=# height=#]
                                                <br />
                                                [img]test.png[/img]
                                                <br />
                                                [figcaption=(left|center|right|justify)]Подпись[/figcaption]
                                                <br />
                                                [/figure]
                                            </span>
                                        </div>
                                        <div>
                                            <img class='comm_preview' src='/img/eye.png' />
                                            <div class='tip'>Предпросмотр</div>
                                        </div>
                                    </div>

                                    <div id="article_content">
                                        <textarea id="contents" name="contents" required=""><?php if (!empty($this->data['article']['contents'])) echo $this->data['article']['contents']; ?></textarea>
                                    </div>
                                    <h2>Прикрепленные изображения</h2>
                                    <div class="img_thumbs">
                                        <?php if (!empty($this->data['article']['images'])) {
                                            foreach ($this->data['article']['images'] as $img) {
                                            ?>
                                        <div>
                                            <div class='thumb_action'>
                                                <div>
                                                    <div class='tip'>Вставить ВВ-код</div>
                                                </div>
                                                <div>
                                                    <div class='tip'>Удалить</div>
                                                </div>
                                            </div>
                                            <img src='<?php echo $img; ?>' /> <div><?php echo substr(strrchr($img, "/"), 1);; ?></div>
                                        </div>
                                        <?php }} ?>
                                    </div>
                                </fieldset>
                                <input type="hidden" name="section_id" id="section_id" />
				<?php if (!empty($this->data['article']['id'])) { ?> <input type="hidden" name="article_id" id="article_id" value="<?php echo $this->data['article']['id']; ?>"/> <?php } ?>
				<input type="hidden" name="article_action" id="article_action" value="<?php echo $this->data['subaction']; ?>"/>
                                <fieldset>
                                    <input type="submit" name="pub_submit" id="pub_submit" value="ОК" />
                                    <input type="reset" />
                                </fieldset>
                            </form>
                            <?php } ?>
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
            $('#section_combo').change(function () {
                $('#section_id').attr('value', $(this).attr('js-combobox-selected'));
        });

    <?php if (isset($_REQUEST['section'])) { ?>
    $('#section_combo').attr('js-combobox-selected', '<?php echo $_REQUEST['section']; ?>');
    $('#section_combo').change();
     <?php     }
     ?>

</script>
<script src="/js/combobox.js"></script>
<script>

<?php if (isset($this->data['splitter_href'])) { ?>
    $('#section_split').change(function () {
        location.assign('<?php echo $this->data['splitter_href']; ?>');
    });
<?php } ?>
    
    <?php if (empty($this->data['subaction'])) { ?>
    $('.remove > a').click(function () {
        var j = $.get('/admin/articles/delete/?args=' + $(this).closest('label').attr('for').replace('article', ''), {}, function () {
            location.reload();
        }).fail(function () {
            messageBox(j.responseText, 'left');
        });
    });
    <?php } ?>

    <?php if (!empty($this->data['subaction']) && ($this->data['subaction'] == 'add' || $this->data['subaction'] == 'edit')) { ?>

    var lock = false;


    var uploader = new ImageUploader(cont, true, false);

    uploader.onStartUploading = function () {
        lock = true;
    }

    uploader.onUploaded = function (images) {
        lock = false;
        $('#upload_avatar').removeClass('loading');
        for (var i = 0; i < images.length; i++) {
            var s;
            var ii = images[i].lastIndexOf('/');
            if (ii != -1 && ii < images[i].length - 1)
                s = images[i].substr(ii + 1);
            else s = images[i];
            if (window.contents)
                $(contents).first().wrapSelected('\r\n[figure width=100]\r\n[img]' + s + '[/img]\r\n[figcaption]', '[/figcaption]\r\n[/figure]\r\n');
            $('#edit_content > .img_thumbs').append("<div><div class='thumb_action'><div><div class='tip'>Вставить ВВ-код</div></div><div><div class='tip'>Удалить</div></div></div><img src='" + images[i] + "' /> <div>" + s + "</div></div>")
        }
    }

    uploader.onError = function (err) {
        messageBox('<p>Хьюстон, у нас проблемы!</p>' + err, 'left');
    }

    uploader.onDeleted = function (img) {
        if (img) img.parent().remove();
    }

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
        else if (t.hasClass('comm_img')) uploader.upload();
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
        var j = $.post('/admin/addarticle/', $(this).serialize(), function () {
            messageBox('<?php if ($this->data['subaction'] == 'add') {
                                  if ($this->data['user']['is_admin'])
                                      echo '<p>Спасибо за публикацию!</p><p>Ваша статья теперь доступна для просмотра <a href="\' + j.responseText + \'">здесь</a></p>';
                                  else
                                      echo  '<p>Большое спасибо за предложенную статью!</p>В ближайшее время мы проверим и опубликуем ее.</p>'; 
                              }
                            else if ($this->data['subaction'] == 'edit'){
                                 if (!empty($this->data['article']['verifier_id']))
                                     echo  '<p>Все изменения успешно внесены.</p>'; 
                                 else
                                     echo  '<p>Спасибо! Теперь эта статья доступна для просмотра всем пользователям.</p>'; 
                              } 
                                  ?>', 'left');
        }).fail(function () {
            messageBox(j.responseText, 'left');
        }).always(function () {
            lock = false;
            $('#pub_submit').removeClass('loading');
        });
    });

    $(pub_form).on('reset', function () {
        $('.thumb_action > div:nth-child(2)').click();
    });

    $('.img_thumbs').click(function (e) {
        var img = $(e.target).parent().next();
        if ($(e.target).is('.thumb_action > div:nth-child(2)')) {
<?php if (isset($this->data['subaction']) && $this->data['subaction'] == 'edit') { ?>
            var s;
            var ii = img.attr('src').lastIndexOf('/');
            if (ii != -1 && ii < img.attr('src').length - 1)
                s = img.attr('src').substr(ii + 1);
            else s = img.attr('src');
            var j = $.post('/admin/articles/deleteimg/?args=<?php echo $this->data['article']['id']; ?>', { 'img': s }).always(function () {
<?php } ?>
                uploader.delete(img.attr('src'), img);
          <?php if (isset($this->data['subaction']) && $this->data['subaction'] == 'edit') { ?>});<?php } ?>

        }
        else if ($(e.target).is('.thumb_action > div:nth-child(1)')) {
            if (window.contents)
                $(contents).first().wrapSelected('\r\n[figure width=100]\r\n[img]' + img.attr('src') + '[/img]\r\n[figcaption]', '[/figcaption]\r\n[/figure]\r\n');
        }
    });

    <?php } ?>

</script>
