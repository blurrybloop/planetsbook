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
                        <article>
                            <h1>Публикации, ожидающие проверки</h1>
                            <div class="updown <?php if (count($this->data['messages']) == 0) echo 'nocontent' ?>">
                                <?php
                            if (count($this->data['messages']) == 0) echo '<div>Пока нет ни одной публикации для проверки.</div>';
foreach ($this->data['messages'] as $val){
                                ?>
                                <input name="item" id="article<?php echo $val['article_id'] ?>" type="checkbox" />
                                <div>
                                    <label for="article<?php echo $val['article_id'] ?>">
                                        <img src="/img/down_arrow.png" />
                                        <a href=<?php echo "/sections/{$val['data_folder']}/{$val['article_id']}/" ?>>
                                            <?php echo $val['article_title'] ?>
                                        </a>
                                        <span class="info">
                                            <span class="date">
                                                <?php echo $val['pub_date'] ?>
                                            </span>
                                            <span class="user">
                                                <?php echo $val['login'] ?>
                                            </span>
                                            <span class="a_section">
                                                <?php echo $val['section_title'] ?>
                                            </span>
                                        </span>

                                    </label>
                                    <div class="updown_content">
                                        <?php
    echo @file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/sections/{$val['data_folder']}/{$val['article_id']}/description.txt");
                                        ?>
                                    </div>
                                </div>
                                <?php } ?>

                            </div>
                        </article>
                    </div>
                </div>
            </div>
            <?php include('admin_aside.php'); ?>
        </div>
        <?php include('footer.php'); ?>
    </div>


</body>
</html>
<script src="/js/sticky.js"></script>
