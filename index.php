<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>PlanetsBook</title>
    <link rel="stylesheet" href="css/main.css">
     <link rel="stylesheet" href="css/wheel.css">
    <script src="js/html5shiv.js"></script>
</head>
<body style="background-color: black">
        
        <div id="main">
         <?php include('/view/header.php'); ?>
        <div class="wheel_selector">
            <img src="img/scroll_tip.png" />
            <div class="planets">
                <a href="/read.php">
                    <img class="object planet focused" src="img/sun.png" /></a>

            </div>
            <div class="button_selector">
                <div>
                    <div>
                        <input name="radio" type="radio" id="0" />
                        <label for="0">
                            <div class="tooltip">
                                <div class="tiptext">Плутон</div>
                                <div class="tipimg">
                                    <img src="img/tooltip.png" /></div>
                            </div>
                        </label>
                        <input name="radio" type="radio" id="1" />
                        <label for="1">
                            <div class="tooltip">
                                <div class="tiptext">Нептун</div>
                                <div class="tipimg">
                                    <img src="img/tooltip.png" /></div>
                            </div>
                        </label>
                        <input name="radio" type="radio" id="2" />
                        <label for="2">
                            <div class="tooltip">
                                <div class="tiptext">Уран</div>
                                <div class="tipimg">
                                    <img src="img/tooltip.png" /></div>
                            </div>
                        </label>
                        <input name="radio" type="radio" id="3" />
                        <label for="3">
                            <div class="tooltip">
                                <div class="tiptext">Сатурн</div>
                                <div class="tipimg">
                                    <img src="img/tooltip.png" /></div>
                            </div>
                        </label>
                        <input name="radio" type="radio" id="4" />
                        <label for="4">
                            <div class="tooltip">
                                <div class="tiptext">Юпитер</div>
                                <div class="tipimg">
                                    <img src="img/tooltip.png" /></div>
                            </div>
                        </label>
                        <input name="radio" type="radio" id="5" />
                        <label for="5">
                            <div class="tooltip">
                                <div class="tiptext">Марс</div>
                                <div class="tipimg">
                                    <img src="img/tooltip.png" /></div>
                            </div>
                        </label>
                        <input name="radio" type="radio" id="6" />
                        <label for="6">
                            <div class="tooltip">
                                <div class="tiptext">Земля</div>
                                <div class="tipimg">
                                    <img src="img/tooltip.png" /></div>
                            </div>
                        </label>
                        <input name="radio" type="radio" id="7" />
                        <label for="7">
                            <div class="tooltip">
                                <div class="tiptext">Венера</div>
                                <div class="tipimg">
                                    <img src="img/tooltip.png" /></div>
                            </div>
                        </label>
                        <input name="radio" type="radio" id="8" />
                        <label for="8">
                            <div class="tooltip">
                                <div class="tiptext">Меркурий</div>
                                <div class="tipimg">
                                    <img src="img/tooltip.png" /></div>
                            </div>
                        </label>
                        <input name="radio" type="radio" id="9" checked="checked" />
                        <label for="9">
                            <span class="tooltip">
                                <span class="tiptext">Солнце</span>
                                <span class="tipimg">
                                    <img src="img/tooltip.png" /></span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

    </div>
     <?php include('/view/slide_menu.php'); ?>
    <?php include('/view/footer.php'); ?>
    <script src="js/jQuery/jquery-1.11.0.min.js"></script>
    <script src="js/wheel.js"></script>
   
</body>
</html>
