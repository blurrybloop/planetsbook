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
                                <section>
                                    <img src="<?php echo '/avatars/' . $this->data['profile']['avatar'] . '.png'?>" />
                                </section>
                                <section class="user_info">
                                    <section>
                                        <h1><?php echo $this->data['profile']['login']?></h1>
                                        <h2><?php echo ($this->data['profile']['is_admin'] ? 'Администратор' : 'Пользователь')?></h2>
                                    </section>
                                    <section class="general_info">
                                        <div>Зарегистрирован <?php echo $this->data['profile']['reg_date']?></div>
                                        <div>В последний раз был на сайте <?php echo (empty($this->data['profile']['last_visit']) ? 'неизвестно когда' : $this->data['profile']['last_visit'])?></div>
                                        <div>Рейтинг: <?php
                                            $r = $this->data['profile']['rating'];
                                            if ($r > 0) echo '<span style="color:green">+' . $this->data['profile']['rating'] . '</span>';
                                            else if ($r == 0) echo '<span style="color:white">' . $this->data['profile']['rating'] . '</span>';
                                            else echo '<span style="color:red">' . $this->data['profile']['rating'] . '</span>';?>
                                            </div>
                                        <div>Комментариев: <?php echo $this->data['profile']['comments_cnt']?></div>
                                    </section>
                                    <section class="contacts">
                                        <h2>Контакты</h2>
                                        <?php if (!empty($this->data['profile']['email'])) { ?>
                                        <div>E-mail: <a href="mailto:<?php echo $this->data['profile']['email']?>"><?php echo $this->data['profile']['email']?></a></div>
                                        <?php } ?>
                                        <?php if (!empty($this->data['profile']['real_name'])) { ?>
                                        <div>Настоящее имя: <?php echo $this->data['profile']['real_name']?></div>
                                        <?php } ?>
                                        <?php if (!empty($this->data['profile']['skype'])) { ?>
                                        <div>Skype: <?php echo $this->data['profile']['skype']?></div>
                                        <?php } ?>
                                        <?php if (!empty($this->data['profile']['vk'])) { ?>
                                        <div>ВКонтакте: <a href="<?php echo 'http://vk.com/' . $this->data['profile']['vk']?>"><?php echo $this->data['profile']['vk']?></a></div>
                                        <?php } ?>
                                        <?php if (!empty($this->data['profile']['facebook'])) { ?>
                                        <div>Facebook: <a href="<?php echo $this->data['profile']['facebook']?>"><?php echo $this->data['profile']['facebook']?></a></div>
                                        <?php } ?>
                                        <?php if (!empty($this->data['profile']['twitter'])) { ?>
                                        <div>Twitter: <a href="<?php $this->data['profile']['twitter']?>"><?php echo $this->data['profile']['twitter']?></a></div>
                                        <?php } ?>
                                        <?php if (!empty($this->data['profile']['site'])) { ?>
                                        <div>Сайт: <a href="<?php echo $this->data['profile']['site']?>"><?php echo $this->data['profile']['site']?></a></div>
                                        <?php } ?>
                                        <?php if (!empty($this->data['profile']['from_where'])) { ?>
                                        <div>Откуда: <?php echo $this->data['profile']['from_where']?></div>
                                        <?php } ?>
                                    </section>
                                </section>
                            </div>
                        </div>

                    </div>
                    <aside>
                        <div class="sticky">
                            <div>
                                <div id="sel"><div></div></div>
                                <div class="section"><p>Просмотр</p></div>
                                <?php if ($this->data['user']['id'] == $this->data['profile']['id']) { ?><div class="section"><p>Изменить</p></div> <?php } ?>
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
        $('#sel').css('top',  $(this).position().top + ($(this).height() - $('#sel').height()) / 2);
    });

    $('.section:nth-child(2)').click();

</script>

