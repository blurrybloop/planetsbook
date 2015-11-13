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
                            <h1>
                                Добавить раздел
                            </h1>
                            <form name="section_form" method="post">
                                <fieldset>
                                    <label for="title">Название</label>
                                    <input name="title" id="title" type="text" required="" maxlength="50" pattern="^.+$" />
                                    <label for="description">Описание</label>
                                    <textarea name="description" id="description" required="" pattern="^.+$"></textarea>
                                    <label>Категория</label>
                                    <div class="js-combobox" js-combobox-selected="0" id="cat_combo">
                                        <div js-combobox-option="0">Солнце и Солнечная система</div>
                                        <div js-combobox-option="1">Планеты</div>
                                        <div js-combobox-option="2">Спутники</div>
                                        <div js-combobox-option="3">Другое</div>
                                    </div>
                                    <div style="display: none;">
                                        <label>Планета</label>
                                        <div class="js-combobox" id="planet_combo">
                                            <?php foreach ($this->data['planets'] as $planet) {
                                                      echo "<div js-combobox-option={$planet['id']}>{$planet['title']}</div>";
                                                  } ?>

                                        </div>
                                    </div>

                                    <label for="data_folder">Папка с данными</label>
                                    <input name="data_folder" id="data_folder" type="text" required="" maxlength="255" pattern="^[A-Za-z0-9_]{1,255}$" />
                                    <label for="allow_user_articles">Разрешить пользователям предлагать публикации</label>
                                    <input name="allow_user_articles" id="allow_user_articles" type="checkbox" />
                                    <br />
                                    <label for="show_main">Отображать на главной</label>
                                    <input name="show_main" id="show_main" type="checkbox" />
                                </fieldset>

                                <fieldset class="section_images">
                                    <legend>Изображения</legend>
                                    <div>
                                        <div id="big_image">
                                            <div>
                                                <div>
                                                    <img src="/img/nophoto.png" />
                                                    <div class="tip">
                                                        Большое изображение.
                                                        <br />
                                                        Оно отображается на главной странице и в нижнем правом углу при переходе к соответсвующему разделу.
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="buttons">
                                                <label class="upload">Загрузить</label>
                                                <label class="remove">Удалить</label>
                                                <label class="reset">Отменить</label>
                                            </div>
                                        </div>
                                        <div id="small_image">
                                            <div>
                                                <div>
                                                    <img src="/img/nophoto.png" />
                                                    <div class="tip">
                                                        Маленькое изображение.
                                                        <br />
                                                        Оно отображается в меню.
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="buttons">
                                                <label class="upload">Загрузить</label>
                                                <label class="remove">Удалить</label>
                                                <label class="reset">Отменить</label>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <input type="hidden" name="cat_id" id="cat_id" />
                                <input type="hidden" name="planet_id" id="planet_id" />
                                <input type="hidden" name="big_image_action" value="0" />
                                <input type="hidden" name="big_image_path" />
                                <input type="hidden" name="small_image_action" value="0" />
                                <input type="hidden" name="small_image_path" />

                                <fieldset>
                                    <input type="submit" name="section_submit" id="section_submit" value="Добавить" />
                                    <input type="reset" />
                                </fieldset>
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
    $('#cat_combo').change(function () {
        if ($(this).attr('js-combobox-selected') == 2)
            $(this).next().css('display', 'block');
        else
            $(this).next().css('display', 'none');
        $('#cat_id').attr('value', $(this).attr('js-combobox-selected'));
    });

    $('#planet_combo').change(function () {
        $('#planet_id').attr('value', $(this).attr('js-combobox-selected'));
    });

</script>
<script src="/js/combobox.js"></script>
<script>

    var lock = false;

    var bigUploader = new ImageUploader(cont, false, true);
    var smallUploader = new ImageUploader(cont, false, true);

    bigUploader.onStartUploading = function () {
        lock = true;
        $('#big_image .upload').addClass('loading');
    }

    bigUploader.onUploaded = function (images) {
        lock = false;
        $('#big_image .upload').removeClass('loading');
        if (images.length) {
            $('input[name=big_image_action]').attr('value', 1);
            $('input[name=big_image_path]').attr('value', images[0]);
            var d = new Date();
            $('#big_image img').attr('src', images[0] + '?' + d.getTime());
        }
    }

    smallUploader.onStartUploading = function () {
        lock = true;
        $('#small_image .upload').addClass('loading');
    }

    smallUploader.onUploaded = function (images) {
        lock = false;
        $('#small_image .upload').removeClass('loading');
        if (images.length) {
            $('input[name=small_image_action]').attr('value', 1);
            $('input[name=small_image_path]').attr('value', images[0]);
            var d = new Date();
            $('#small_image img').attr('src', images[0] + '?' + d.getTime());
        }
    }

    bigUploader.onError = smallUploader.onError = function (err) {
        messageBox('<p>Хьюстон, у нас проблемы!</p>' + err, 'left');
    }

    $('#big_image .upload').click(function () {
        bigUploader.upload();
    });

    $('#big_image .remove').click(function () {
         if (lock) return;
         $('input[name=big_image_action]').attr('value', 2);
        bigUploader.delete($('#big_image img').attr('src'));
        $('#big_image img').attr('src', '/img/nophoto.png');
    });

    $('#small_image .upload').click(function () {
        smallUploader.upload();
    });

    $('#small_image .remove').click(function () {
         if (lock) return;
         $('input[name=small_image_action]').attr('value', 2);
         smallUploader.delete($('#small_image img').attr('src'));
        $('#small_image img').attr('src', '/img/nophoto.png');
    });

    $('#big_image .reset').click(function () {
        if (lock) return;
        $('input[name=big_image_action]').attr('value', 0);
        bigUploader.delete($('#big_image img').attr('src'));
        $('#big_image img').attr('src', '/img/nophoto.png');
    });

    $('#small_image .reset').click(function () {
        if (lock) return;
        $('input[name=big_image_action]').attr('value', 0);
        bigUploader.delete($('#small_image img').attr('src'));
        $('#small_image img').attr('src', '/img/nophoto.png');
    });

    $(section_form).submit(function (e) {
        if (lock) return false;
        lock = true;
        e.preventDefault();
            $('#section_submit').addClass('loading');
            var j = $.post('/admin/addsection/', $(this).serialize(), function(){
                messageBox('<p>Раздел добавлен!</p><a href="' + j.responseText + '">Добавить публикацию</a>', 'left');
           }).fail(function(){
                messageBox(j.responseText, 'left');
           }).always(function () {
               lock = false;
                $('#section_submit').removeClass('loading');
           });
    });

</script>
