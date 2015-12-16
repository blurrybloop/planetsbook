<!DOCTYPE html>
<html>
<head>
    <?php require 'html_head.php' ?>
    <link rel="stylesheet" href="/css/article.css" />
    <link rel="stylesheet" href="/css/comments.css" />
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
                        <?php if (empty($this->data['article']['verifier_id'])) { ?>
                        <div id="verify_log" class="log_message success"><div><p>Эта статья еще не проверена, и поэтому невидима для обычных пользователей. <a href="/admin/articles/edit/?article_id=<?php echo $this->data['article']['article_id']; ?>">Проверить</a></p></div></div>
                        <?php } ?>
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
                                        <a href="<?php echo '/users/profile?id=' . $this->data['article']['user_id'] ?>">
                                        <?php echo $this->data['article']['login'] ?>
                                        </a>
                                    </div>
                                    <div class="views">
                                        <?php echo $this->data['article']['views'] ?>
                                    </div>
                                </div>
                            </header>
                            <?php

                            echo $this->data['article']['contents'];
                            ?>
                            <div class="clearfix"></div>


                            <section id="cc" class="comments" data-article-id="<?php echo $this->data['article']['article_id'] ?>" data-allow-add="<?php echo $this->validateRights([USER_REGISTERED],0, FALSE) ? '1' : '0' ?>">
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
                                <?php foreach ($this->data['see_also'] as $sa) {?>
                                <li>
                                    <a href="<?php echo $sa['href']?>"><?php echo $sa['title']?></a>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
        <?php include("footer.php"); ?>
    </div>
</body>
</html>
<script src="/js/comments.js"></script>
<script>

    $('#verify_log').css('height', $('#verify_log > *').outerHeight(true));

    var sticky = $('.sticky');
    var commentsBlock = $('#cc');
    var cont = $('#content');

    $(window).scroll(function(){
        if (parseInt($(window).height()) + parseInt($(this).scrollTop()) > parseInt(commentsBlock.height()) + parseInt(commentsBlock.position().top) + 200) {
            commentsBlock.data('commentObject').fetch();
        }

     if (parseInt(cont.offset().top) < parseInt($(this).scrollTop())) sticky.addClass('sticked');
        else sticky.removeClass('sticked');
    });

    $(window).scroll();

     $(window).resize(function() {
         cont.toggleClass('compact', parseInt($(window).width()) < 800);
         sticky.width(sticky.parent().width());
     });
     $(window).resize();
</script>
