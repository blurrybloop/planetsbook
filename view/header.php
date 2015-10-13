<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>PlanetsBook</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="shortcut icon" href="/img/favicon.png" type="image/png">
    <script src="js/html5shiv.js"></script>
</head>
<body style="background-color: black">
    <div id="menu" class="sidebar">
        <div>
            <img src="img/arrow.png" class="expand" />
            <h1>PlanetsBook</h1>
        </div>
        <div class="items_wrapper vscroll">
            <div class="items">
                <div>
                    <div class="vtop">
                        <a href="/">
                            <img src="img/house.png" /><p>Домой</p>
                        </a>
                        <div id="search_item">
                            <img src="img/search.png" /><p>Поиск</p>
                        </div>
                        <div>
                            <img src="img/system.png" /><p>Солнечная система</p>
                        </div>
                        <div>
                            <img src="img/sun_small.png" /><p>Солнце</p>
                        </div>
                        <div id="planets_item">
                            <img src="img/planet.png" /><p>Планеты</p>
                        </div>
                        <div id="moon_item">
                            <img src="img/moon_small.png" /><p>Спутники</p>
                        </div>
                        <div>
                            <img src="img/asteroids.png" /><p>Другое</p>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="vbottom">
                        <div>
                            <img src="img/photos.png" /><p>Фотоальбом</p>
                        </div>
                        <div>
                            <img src="img/video.png" /><p>Видео</p>
                        </div>
                        <div id="user_item">
                            <img src="img/user.png" /><p>[Гость]</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="sidebar" id="planets_menu">
        <div>
            <img src="img/arrow.png" class="expand" />
            <h1>Планеты</h1>
        </div>
        <div class="items_wrapper vscroll">
            <div class="items">
                <div>
                    <div class="vtop">
                        <a href="/">
                            <img src="img/mercury.png" /><p>Меркурий</p>
                        </a>
                        <a href="/">
                            <img src="img/venus.png" /><p>Венера</p>
                        </a>
                        <a href="/">
                            <img src="img/earth.png" /><p>Земля</p>
                        </a>
                        <a href="/">
                            <img src="img/mars.png" /><p>Марс</p>
                        </a>
                        <a href="/">
                            <img src="img/jupiter.png" /><p>Юпитер</p>
                        </a>
                        <a href="/">
                            <img src="img/saturn.png" /><p>Сатурн</p>
                        </a>
                        <a href="/">
                            <img src="img/uranus.png" /><p>Уран</p>
                        </a>
                        <a href="/">
                            <img src="img/neptune.png" /><p>Нептун</p>
                        </a>
                        <a href="/">
                            <img src="img/pluto.png" /><p>Плутон</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar" id="moon_menu">
        <div>
            <img src="img/arrow.png" class="expand" />
            <h1>Спутники</h1>
        </div>
        <div class="items_wrapper vscroll">
            <div class="items">
                <div>
                    <div>
                        <a href="/">
                            <img src="img/moon.png" /><p>Луна</p>
                        </a>
                        <a href="/">
                            <img src="img/phobos_deimos.png" /><p>Фобос и Деймос</p>
                        </a>
                        <a href="/">
                            <img src="img/io.png" /><p>Ио</p>
                        </a>
                        <a href="/">
                            <img src="img/europa.png" /><p>Европа</p>
                        </a>
                        <a href="/">
                            <img src="img/ganymede.png" /><p>Ганимед</p>
                        </a>
                        <a href="/">
                            <img src="img/callisto.png" /><p>Каллисто</p>
                        </a>
                        <a href="/">
                            <img src="img/titan.png" /><p>Титан</p>
                        </a>
                        <a href="/">
                            <img src="img/triton.png" /><p>Тритон</p>
                        </a>
                        <a href="/">
                            <img src="img/charon.png" /><p>Харон</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar" id="search_menu">
        <div>
            <img src="img/arrow.png" class="expand" />
            <h1>Поиск</h1>
        </div>
        <div class="items_wrapper vscroll">
            <div class="items">
                <div>
                    <div>
                        <div class="search_container">
                            <input type="search" placeholder="Что вы хотите найти?"/>
                            <img src="img/search.png" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="sidebar" id="user_menu">
        <div>
            <img src="img/arrow.png" class="expand" />
            <h1>Гость</h1>
        </div>
        <div class="items_wrapper vscroll">
            <div class="items">
                <div>
                    <div>
                        <div id="reg_item">
                            <img src="img/register.png" /><p>Регистрация</p>
                        </div>
                        <div id="login_item">
                            <img src="img/login.png" /><p>Вход</p>
                        </div>
                        <a href="/">
                            <img src="img/cabinet.png" /><p>Личный кабинет</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="sidebar" id="login_menu">
        <div>
            <img src="img/arrow.png" class="expand" />
            <h1>Вход</h1>
        </div>
        <div class="items_wrapper vscroll">
            <form name="login_form" class="items">
                <div>
                    <div>
                            <div class="log_message">Ошибка: неверный пароль</div>
                            <div>
                                <label for="login"><span>
    <img src="/img/question.png" />
    <div class="tip"><b>Ваше уникальное имя пользователя.</b><br />Используйте значение, которое вы указали при регистрации.</div>
</span>Логин
                                </label>
                                <input id="login" type="text" required pattern="[A-Za-z]{6,}"/>
                                <br />
                                
                            </div>
                            <div>
                                <label for="psw"><span>
    <img src="/img/question.png" />
    <div class="tip"><b>Ваш секретный код.</b><br />Используйте значение, которое вы указали при регистрации.</div>
</span>Пароль</label>
                                <input id="psw" type="password" required/>
                                <br />
                            </div>
                            <div>
                                
                            </div>
                    </div>
                </div>
                <div><div class="vbottom"><div><input type="submit" value="Вход"/><input type="reset" /></div></div></div>
            </form>
        </div>
    </div>


    <div class="sidebar" id="reg_menu">
        <div>
            <img src="img/arrow.png" class="expand" />
            <h1>Регистрация</h1>
        </div>
        <div class="items_wrapper vscroll">
            <form name="reg_form" class="items">
                <div>
                    <div>
                        <div class="log_message">Ошибка: неверный пароль</div>
                        <div>
                            <label for="login">
                                <span>
                                    <img src="/img/question.png" />
                                    <div class="tip"><b>Ваше уникальное имя пользователя.</b><br />Обязательное поле.</br>Может содержать буквы латинского алфавита, цифры и символ подчеркивания.<br />Допустимая длина: 5 - 100 символов.<br /></div>
                                </span>Логин
                            </label>
                            <input id="login" type="text" required pattern="[A-Za-z0-9_]{5,100}" maxlength="100" />
                            <br />

                        </div>
                        <div>
                            <label for="psw">
                                <span>
                                    <img src="/img/question.png" />
                                    <div class="tip"><b>Ваш секретный код.</b><br />Обязательное поле.<br />Может содержать буквы латинского алфавита, цифры, а также символы !@#$%^&*()+=-_?:;,./\<br />Минимальная длина: 6 символов.<br /><b>Помните: для лучшей безопасности используйте длинные пароли с как можно большим количеством не-буквенных символов.</b></div>
                                </span>Пароль
                            </label>
                            <input id="psw" type="password" required />
                            <br />
                        </div>
                        <div>
                            <label for="mail">
                                <span>
                                    <img src="/img/question.png" />
                                    <div class="tip"><b>Ваш адрес электронной почты</b><br />Можете оставить это поле незаполненным.</div>
                                </span>E-mail
                            </label>
                            <input id="mail" type="email" />
                            <br />
                        </div>
                        <div>
                            <label for="real_name">
                                <span>
                                    <img src="/img/question.png" />
                                    <div class="tip"><b>Ваше настоящее имя</b><br />Можете оставить это поле незаполненным.</div>
                                </span>Имя
                            </label>
                            <input id="real_name" type="text"/>
                            <br />
                        </div>
                    </div>
                </div>
                <div><div class="vbottom"><div><input type="submit" value="Регистрация" /><input type="reset" /></div></div></div>
            </form>
        </div>
    </div>

    <script src="js/jQuery/jquery-1.11.0.min.js"></script>
    <script>

     $(window).click(function (e) {
         if ($(e.target).closest('.sidebar').length == 0)
             $('.sidebar').removeClass('expanded');
         });

     $('.expand').click(this, function () {
             if ($(this).parents('.sidebar').hasClass('expanded')) {
                 $(this).parents('.sidebar').removeClass('expanded');
             }
             else {
                 $(this).parents('.sidebar').addClass('expanded');
             }
         });

         $('[id$=_item]').click(function () {
             var i = '#' + this.id.substr(0, this.id.lastIndexOf('_item')) + '_menu';
             if ($(i).hasClass('expanded')) $(i).removeClass('expanded');
             else $(i).addClass('expanded');
         });

         $('#login_menu form').submit(function () {
             setTimeout(function (e) { e.removeClass('expanded'); }, 3000, $('#login_menu .log_message').addClass('expanded'))
             return false;
         });
    </script>

    <div class="msgbox_container">
        <div>
            <div>
                <div class="msgbox">
                    <div>
                        <div>✕</div>
                    </div>
                    <div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <img src="img/earth.png" style="position: fixed; right: -5%; bottom:-35%; width: 35%; opacity: .5" />
    <script>
        $('.msgbox > div:first-child > div').click(function () {
            $(this).parents('.msgbox_container').removeClass('showed');
        });

        function messageBox(content) {
            $('.msgbox > div:last-child').html(content);
            $('.msgbox_container').addClass('showed');
        }

    </script>
    <div id="main">
        <div class="banner">
            <a href="/">
                <img src="img/logo.png" />
            </a>
        </div>
        <div id="content">
