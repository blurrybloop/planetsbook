<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="/img/favicon.png" type="image/png">
    <title>PlanetsBook</title>
    <link rel="stylesheet" href="/css/main.css">
    <style>
    #content > div{
        top: 40px;
        left: 40px;
        position: relative;
        text-align: center;
        height:100%;
    }

    #content > div > img{
        position: relative;
        left: -20px;
        margin-right: 30px;
        vertical-align: top;
    }

    #content > div > div{
        text-align:left;
        display:inline-block;
        max-width: 50%;
    }

    </style>
</head>
<body>
    <div id="main">
        <div class="banner">
            <a href="/">
                <img src="/img/logo.png" />
            </a>
        </div>
        <div id="content">
            <div>
                <img src="/img/astronaut.png" />
                <div>
                    <h1>Упс!</h1>
                    Похоже, у вас отключен JavaScript.<br/>Пожалуйста, исправьте эту проблему для работы с сайтом.
                </div>
            </div>
        </div>
        <?php include('/view/footer.php'); ?>
    </div>
</body>
</html>
