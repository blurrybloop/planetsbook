<!DOCTYPE html>
<html>
<head>
    <?php require 'html_head.php' ?>
    <link rel="stylesheet" href="/css/article.css" />
    <link rel="stylesheet" href="/css/combobox.css" />
    <script src="/js/utils.js"></script>
</head>
<body>
    <?php if (is_file($_SERVER['DOCUMENT_ROOT'] . $this->data['section']['image'])) { ?>
    <img src="<?php echo $this->data['section']['image'] ?>" />
    <?php } ?>
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
                                    <?php echo $this->data['section']['title'] ?>
                                </h1>
                            </header>
                            <div class="desc">
                                <p>Добро пожаловать на один из разделов нашего сайта!</p>
                                <p>Вы можете выбрать перейти к понравившейся статье, щелкнув на ее названии. Для того, чтобы увидеть краткое описание статьи, нажмите на стрелку справа.</p>
                            </div>
                            <div class="updown_head">
                                <div class='add_article'>
                                    <?php
if($this->data['section']['allow_user_articles'] && isset($this->data['user'])) echo '<a href=\'/admin/publicate?section=' . $this->data['section']['id'] . '\'>' . (empty($this->data['user']['is_admin']) ? 'Предложить статью для публикации' : 'Опубликовать статью') . '</a>';
                                    ?>
                                </div>
                                <div>
                                    Сортировать по
                                    <div class="js-combobox" js-combobox-selected="<?php echo isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 0?>" id="cat_combo">
                                        <a js-combobox-option="0" href="?sort=0">дате публикации</a>
                                        <a js-combobox-option="1" href="?sort=1">популярности</a>
                                        <a js-combobox-option="2" href="?sort=2">алфавиту</a>
                                    </div>
                                </div>
                            </div>

                            <div class="updown <?php if (count($this->data['articles']) == 0) echo 'nocontent' ?>">
                                <?php
if (count($this->data['articles']) == 0) echo '<div>Здесь пока ничего нет.</div>';
foreach ($this->data['articles'] as $val){ ?>
                                <input name="item" id="article<?php echo $val['article_id'] ?>" type="checkbox" />
                                <div>
                                    <label for="article<?php echo $val['article_id'] ?>">
                                        <img src="/img/down_arrow.png" />
                                        <a href=<?php echo "/sections/{$this->data['section']['data_folder']}/{$val['article_id']}/" ?>>
                                            <?php echo $val['title'] ?>
                                        </a>
                                        <span class="info">
                                            <span class="date">
                                                <?php echo $val['pub_date'] ?>
                                            </span>
                                            <span class="user">
                                                <?php echo $val['login'] ?>
                                            </span>
                                            <span class="views">
                                                <?php echo $val['views'] ?>
                                            </span>
                                        </span>

                                    </label>
                                    <div class="updown_content">
                                        <?php
    echo file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/sections/{$this->data['section']['data_folder']}/{$val['article_id']}/description.txt");
                                        ?>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </article>
                    </div>
                </div>

            </div>
            <aside>
                <div class="sticky">
                    <div>
                        <h1>Смотрите также</h1>
                        <ul>
                            <li>
                                <a href="/article_temp.php">Земля</a>
                            </li>
                            <li>
                                <a href="/article_temp.php">Марс</a>
                            </li>
                            <li>
                                <a href="/article_temp.php">Луна</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </aside>
        </div>
        <?php include("view/footer.php"); ?>
    </div>



</body>
</html>
<script src="/js/combobox.js"></script>
<script src="/js/sticky.js"></script>
<script>
    var sticky = $('.sticky');
    var cont = $('#content');

    $(window).scroll(function () {
        if (parseInt(cont.offset().top) < parseInt($(this).scrollTop())) sticky.addClass('sticked');
        else sticky.removeClass('sticked');
    });

    $(window).scroll();

    $(window).resize(function () {
        cont.toggleClass('compact', parseInt($(window).width()) < 800);
        sticky.width(sticky.parent().width())
    });
    $(window).resize();

</script>
