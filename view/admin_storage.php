<!DOCTYPE html>
<html>
<head>
    <?php require 'html_head.php' ?>
    <link rel="stylesheet" href="/css/article.css" />
    <link rel="stylesheet" href="/css/profile.css" />
    <link rel="stylesheet" href="/css/admin.css" />
    <link rel="stylesheet" href="/css/combobox.css" />
    <link rel="stylesheet" href="/css/storage.css" />
    <link rel="stylesheet" href="/css/fullscreen.css" />
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
                            <h1>Управление хранилищем</h1>
                            <div class='js-storage' data-target="/storage/" data-user-id='<?php echo $this->data['user']['id'] ?>' data-admin='<?php echo (int)$this->data['user']['is_admin'] ?>'></div>
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
<script src="/js/storage.js"></script>
<script src="/js/fullscreen.js"></script>
<script src="/js/combobox.js"></script>