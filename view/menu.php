<div id="sidebar_bg"></div>
<div id="menu" class="sidebar">
    <div>
        <img src="/img/arrow.png" class="expand" />
        <h1>PlanetsBook</h1>
    </div>
    <div class="items_wrapper vscroll">
        <div class="items">
            <div>
                <div class="vtop">
                    <a href="/">
                        <img src="/img/house.png" /><p>Домой</p>
                    </a>
                    <div id="search_item">
                        <img src="/img/search.png" /><p>Поиск</p>
                    </div>
                    <?php
                    foreach ($this->data['menu'] as $val){
                            if ($val['type'] == 0)
                            {
                                if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/sections/' . $val['data_folder'] . '/main_small.png'))
                                    $sf = '/sections/' . $val['data_folder'] . '/main_small.png';
                                else 
                                    $sf = $val['image'];
                                echo "<a href='{$val['href']}'><img src='{$sf}' /><p>{$val['title']}</p></a>";
                            }
                        }
                    ?>
                    <div id="planets_item">
                        <img src="/img/planet.png" /><p>Планеты</p>
                    </div>
                    <div id="moon_item">
                        <img src="/img/moon_small.png" /><p>Спутники</p>
                    </div>
                    <div>
                        <img src="/img/asteroids.png" /><p>Другое</p>
                    </div>
                </div>
            </div>
            <div>
                <div class="vbottom">
                    <div>
                        <img src="/img/photos.png" /><p>Фотоальбом</p>
                    </div>
                    <div>
                        <img src="/img/video.png" /><p>Видео</p>
                    </div>
                    <div id="user_item">
                        <img src="/img/user.png" /><p>[<?php echo (isset($this->data['user']) ? $this->data['user']['login'] : 'Гость')  ?>]</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<div class="sidebar" id="planets_menu">
    <div>
        <img src="/img/arrow.png" class="expand" />
        <h1>Планеты</h1>
    </div>
    <div class="items_wrapper vscroll">
        <div class="items">
            <div>
                <div class="vtop">
                    <?php
                    foreach ($this->data['menu'] as $val){
                        if ($val['type'] == 1) {
                            if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/sections/' . $val['data_folder'] . '/main_small.png'))
                                $sf = '/sections/' . $val['data_folder'] . '/main_small.png';
                            else 
                                $sf = $val['image'];
                            echo "<a href='{$val['href']}'><img src='{$sf}' /><p>{$val['title']}</p></a>";
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="sidebar" id="moon_menu">
    <div>
        <img src="/img/arrow.png" class="expand" />
        <h1>Спутники</h1>
    </div>
    <div class="items_wrapper vscroll">
        <div class="items">
            <div>
                <div>
                    <?php
                    foreach ($this->data['menu'] as $val)
                            if ($val['type'] == 2)
                            {
                                if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/sections/' . $val['data_folder'] . '/main_small.png'))
                                    $sf = '/sections/' . $val['data_folder'] . '/main_small.png';
                                else 
                                    $sf = $val['image'];
                                echo "<a href='{$val['href']}'><img src='{$sf}' /><p>{$val['title']}</p></a>";
                            }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="sidebar" id="search_menu">
    <div>
        <img src="/img/arrow.png" class="expand" />
        <h1>Поиск</h1>
    </div>
    <div class="items_wrapper vscroll">
        <div class="items">
            <div>
                <div>
                    <div class="search_container">
                        <input type="search" placeholder="Что вы хотите найти?" />
                        <img src="/img/search.png" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="sidebar" id="user_menu">
    <div>
        <img src="/img/arrow.png" class="expand" />
        <h1><?php echo (isset($this->data['user']) ? $this->data['user']['login'] : 'Гость')  ?></h1>
    </div>
    <div class="items_wrapper vscroll">
        <div class="items">
            <div>
                <div>
                    <div id="reg_item">
                        <img src="/img/register.png" /><p>Регистрация</p>
                    </div>
                    <?php if (isset($this->data['user'])) {  ?>
                    <div id="logout_item">
                        <img src="/img/logout.png" /><p>Выход</p>
                    </div>
                    <?php } else { ?>
                    <div id="login_item">
                        <img src="/img/login.png" /><p>Вход</p>
                    </div>
                    <?php } ?>
                    <a href="/">
                        <img src="/img/cabinet.png" /><p>Личный кабинет</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="sidebar" id="login_menu">
    <div>
        <img src="/img/arrow.png" class="expand" />
        <h1>Вход</h1>
    </div>
    <div class="items_wrapper vscroll">
        <form name="login_form" class="items">
            <div>
                <div>
                    <div class="log_message"></div>
                    <div>
                        <label for="login">
                            <span>
                                <img src="/img/question.png" />
                                <span class="tip"><b>Ваше уникальное имя пользователя.</b><br />Используйте значение, которое вы указали при регистрации.</span>
                            </span>Логин
                        </label>
                        <input id="login" type="text" name="login" required pattern="^[\w]{5,100}$" maxlength="100"/>
                        <br />

                    </div>
                    <div>
                        <label for="psw">
                            <span>
                                <img src="/img/question.png" />
                                <span class="tip"><b>Ваш секретный код.</b><br />Используйте значение, которое вы указали при регистрации.</span>
                            </span>Пароль
                        </label>
                        <input id="psw" type="password" name="password" required pattern="^[\w\<\>\!\~\@\#\$\%\^\&\*\(\)\+\=\-_\?\:\;\,\.\/\\]{6,}$"/>
                        <br />
                    </div>
                    <div>

                    </div>
                </div>
            </div>
            <div><div class="vbottom"><div><input id="login_submit" type="submit" value="Вход" /><input type="reset" /></div></div></div>
        </form>
    </div>
</div>


<div class="sidebar" id="reg_menu">
    <div>
        <img src="/img/arrow.png" class="expand" />
        <h1>Регистрация</h1>
    </div>
    <div class="items_wrapper vscroll">
        <form name="reg_form" class="items">
            <div>
                <div>
                    <div class="log_message"></div>
                    <div>
                        <label for="login">
                            <span>
                                <img src="/img/question.png" />
                                <span class="tip"><b>Ваше уникальное имя пользователя.</b><br />Обязательное поле.<br />Может содержать буквы латинского алфавита, цифры и символ подчеркивания.<br />Допустимая длина: 5 - 100 символов.<br /></span>
                            </span>Логин
                        </label>
                        <input id="login" type="text" name="login" required pattern="^[\w]{5,100}$" maxlength="100" />
                        <br />

                    </div>
                    <div>
                        <label for="psw">
                            <span>
                                <img src="/img/question.png" />
                                <span class="tip"><b>Ваш секретный код.</b><br />Обязательное поле.<br />Может содержать буквы латинского алфавита, цифры, а также символы !~<>@#$%^&*()+=-_?:;,./\<br />Минимальная длина: 6 символов.<br /><b>Помните: для лучшей безопасности используйте длинные пароли с как можно большим количеством не-буквенных символов.</b></span>
                            </span>Пароль
                        </label>
                        <input id="psw" type="password" name="password" required pattern="^[\w\<\>\!\~\@\#\$\%\^\&\*\(\)\+\=\-_\?\:\;\,\.\/\\]{6,}$"/>
                        <br />
                    </div>
                    <div>
                        <label for="mail">
                            <span>
                                <img src="/img/question.png" />
                                <span class="tip"><b>Ваш адрес электронной почты</b><br />Можете оставить это поле незаполненным.</span>
                            </span>E-mail
                        </label>
                        <input id="mail" type="email" name="email" />
                        <br />
                    </div>
                    <div>
                        <label for="real_name">
                            <span>
                                <img src="/img/question.png" />
                                <span class="tip"><b>Ваше настоящее имя</b><br />Можете оставить это поле незаполненным.</span>
                            </span>Имя
                        </label>
                        <input id="real_name" type="text" name="real_name" pattern="^[A-Za-zА-ЯЁІЇЄа-яёіїє\s]+$"/>
                        <br />
                    </div>
                </div>
            </div>
            <div><div class="vbottom"><div><input id="reg_submit" type="submit" value="Регистрация" /><input type="reset" /></div></div></div>
        </form>
    </div>
</div>
<script>
    var mcnt = 0;
     $(window).click(function (e) {
         if ($(e.target).closest('.sidebar').length == 0) {
             mcnt = 0;
             $('.sidebar').removeClass('expanded');
             $('#sidebar_bg').removeClass('expanded');
         }
         });

     $('.expand').click(this, function () {
             if ($(this).closest('.sidebar').hasClass('expanded')) {
                 $(this).closest('.sidebar').removeClass('expanded');
                 mcnt--;
             }
             else {
                 $(this).closest('.sidebar').addClass('expanded');
                 mcnt++; 
             }
             if (mcnt == 0) $('#sidebar_bg').removeClass('expanded');
             else $('#sidebar_bg').addClass('expanded');
         });

         $('[id$=_item]').click(function () {
             var i = '#' + this.id.substr(0, this.id.lastIndexOf('_item')) + '_menu';
             if ($(i).hasClass('expanded')) { $(i).removeClass('expanded'); mcnt--;}
             else { $(i).addClass('expanded'); mcnt++;}
             if (mcnt == 0) $('#sidebar_bg').removeClass('expanded');
             $('#sidebar_bg').addClass('expanded');
         });


         $('#reg_menu form').submit(function (e) {
             e.preventDefault();
             $('#reg_submit').addClass('loading');
             var j = $.post('/users/register/', $(this).serialize(), function () {
                 $('#reg_menu .log_message').html('Поздравляем с успешной регистрацией!').removeClass('success fail').addClass('success expanded');
             }).fail(function () {
                 $('#reg_menu .log_message').html($(j.responseText).html()).removeClass('success fail').addClass('fail expanded');
             }).always(function () {
                 $('#reg_submit').removeClass('loading');
                 setTimeout(function () { $('#reg_menu .log_message').removeClass('expanded'); }, 3000);
             });
         });

         $('#login_menu form').submit(function (e) {
             e.preventDefault();
             $('#login_submit').addClass('loading');
             var j = $.post('/users/login/', $(this).serialize(), function () {
                 location.reload();
             }).fail(function () {
                 $('#login_menu .log_message').html($(j.responseText).html()).removeClass('success fail').addClass('fail expanded');
             }).always(function () {
                 $('#login_submit').removeClass('loading');
                 setTimeout(function () { $('#login_menu .log_message').removeClass('expanded'); }, 3000);
             });
         });

         $('#logout_item').click(function () {
             var j = $.post('/users/logout/', [], function () {
                 location.reload();  
             })

         });
        
</script>