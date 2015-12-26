<!DOCTYPE html>
<html>
<head>
    <?php require 'html_head.php' ?>
    <link rel="stylesheet" href="/css/article.css" />
    <link rel="stylesheet" href="/css/profile.css" />
    <link rel="stylesheet" href="/css/admin.css" />
    <link rel="stylesheet" href="/css/combobox.css" />
    <style>
        .storage-usage{
            display: table;
            min-width: 500px;
        }

        .storage-usage>*{
            display: table-cell;
        }

        #date-select{
            width: 20%;
            vertical-align: middle;
        }

        #date-select + div > img{
            transition: visibility .3s, opacity .3s;
            visibility: visible;
            opacity: 1;
        }

        #date-select + div.loading{
            background: url(/img/loading.gif) center center no-repeat;
            background-size: 1em 1em;
        }

        #date-select + div.loading > img{
            visibility: hidden;
            opacity: 0;
        }


    </style>
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
                            <h1>Статистика</h1>
                            <div>
                                <?php if (isset($this->data['stats']['total_users'])) { ?>
                                <div>
                                    Зарегистрированных пользователей: <?php echo $this->data['stats']['total_users'] ?>
                                </div>
                                <?php } ?>
                                <?php if (isset($this->data['stats']['total_admins'])) { ?>
                                <div>
                                    Администраторов: <?php echo $this->data['stats']['total_admins'] ?>
                                </div>
                                <?php } ?>
                                <?php if (isset($this->data['stats']['last_user'])) { ?>
                                <div>
                                    Последний зарегистрированный пользователь: <?php echo '<a href="/users/profile/?id=' . $this->data['stats']['last_user']['id'] . '">' . $this->data['stats']['last_user']['login'] . '</a> в ' . $this->data['stats']['last_user']['reg_date'] ?>
                                </div>
                                <?php } ?>
                                <?php if (isset($this->data['stats']['storage_usage'])) { ?>
                                <div>
                                    Использование хранилища: <?php echo $this->data['stats']['storage_usage'] ?>
                                </div>
                                <?php } ?>
                                <?php if (isset($this->data['stats']['total_sections'])) { ?>
                                <div>
                                    Разделов: <?php echo $this->data['stats']['total_sections'] ?>
                                </div>
                                <?php } ?>
                                <?php if (isset($this->data['stats']['total_articles'])) { ?>
                                <div>
                                    Публикаций: <?php echo $this->data['stats']['total_articles'] ?>
                                </div>
                                <?php } ?>
                                <?php if (isset($this->data['stats']['total_unverified_articles'])) { ?>
                                <div>
                                    Непроверенных публикаций: <?php echo $this->data['stats']['total_unverified_articles'] ?>
                                </div>
                                <?php } ?>
                                <?php if (isset($this->data['stats']['total_comments'])) { ?>
                                <div>
                                    Комментариев: <?php echo $this->data['stats']['total_comments'] ?>
                                </div>
                                <?php } ?>
                                <?php if (isset($this->data['stats']['top_articles'])) { ?>
                                <div style="text-align:center">
                                    <img src="/admin/stats/?top_articles" />
                                </div>
                                <ol>
                                <?php foreach ($this->data['stats']['top_articles'] as $a) { ?>
                                    <li>
                                        <?php echo '<a href="' . $a['href'] . '/">' . $a['title'] . '</a> от <a href="/users/profile/?id=' . $a['user_id'] . '">' . $a['login'] . '</a>'   ?>
                                        </li>
                                <?php } ?>
                                </ol>
                                <?php } ?>
                                <div class="storage-usage">
                                    <form id="date-select">
                                <label for="fromdate">Начальная дата</label>
                                   <input type="date" id="fromdate" value="<?php echo date('Y-m-d', strtotime('-15 days')); ?>" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" required/>
                                    <br/>
                                <label for="todate" value="<?php echo date('Y-m-d'); ?>">Конечная дата</label>
                                        <input type="date" id="todate" value="<?php echo date('Y-m-d'); ?>" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" required/>
                                    <br />
                                Разрешение
                                <div id="resolution" class="js-combobox" data-combobox-selected="1">
                                    <div data-combobox-option="1">День</div>
                                    <div data-combobox-option="2">Месяц</div>
                                </div>
                                        <input type="submit" value="Обновить"/>
                                    </form>
                                <div style="text-align:center;">                                   
                                    <img id="storage-usage-chart"/>
                                </div>
                                </div>
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
<script src="/js/combobox.js"></script>
<script>
    $("#storage-usage-chart").error(function () {
        $(this).attr('src', '/img/nophoto.png');
    });

    $('#date-select').submit(function (e) {
        e.preventDefault();

        var from = $('#fromdate').val();
        var to = $('#todate').val();
        var res = $('#resolution').attr('data-combobox-selected');
        $("#storage-usage-chart").parent().addClass('loading').children().one('load', function () {
            $(this).parent().removeClass('loading');
        }).attr('src', '/admin/stats/?storage_usage&start_date=' + from + '&end_date=' + to + '&resolution=' + res);
    });

    $('#date-select').submit();
</script>
