<div id="sidebar_bg" class="<?php if (isset($_SESSION['login_success']) || isset($_SESSION['logout_success'])) echo 'expanded'?>"></div>
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
                        <img src="/img/movie.png" /><p>Видео</p>
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

<div class="sidebar <?php if (isset($_SESSION['login_success']) || isset($_SESSION['logout_success'])) echo 'expanded'?>" id="user_menu">
    <div>
        <img src="/img/arrow.png" class="expand" />
        <h1><?php echo (isset($this->data['user']) ? $this->data['user']['login'] : 'Гость')  ?></h1>
    </div>
    <div class="items_wrapper vscroll">
        <div class="items">
            <div>
                <div>
                    <div class="log_message"><div></div></div>
                    <div id="reg_item">
                        <img src="/img/notepad.png" /><p>Регистрация</p>
                    </div>
                    <?php if (isset($this->data['user'])) {  ?>
                    <div id="logout_item">
                        <img src="/img/on_off.png" /><p>Выход</p>
                    </div>
                    <a href="<?php echo '/users/profile/?id=' . $this->data['user']['id'] ?>">
                        <img src="/img/profile.png" /><p>Мой профиль</p>
                    </a>
                    <?php if ($this->data['user']['is_admin']) {?> 
                    
                    <a href="/admin/">
                        <img src="/img/wrench.png" /><p>Администрирование</p>
                    </a>
                     <?php } ?>
                    <?php } else { ?>
                    <div id="login_item">
                        <img src="/img/on_off.png" /><p>Вход</p>
                    </div>
                    <?php } ?>
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
                    <div class="log_message"><div></div></div>
                    <div>
                        <label for="login">
                                <span class="tip"><b>Ваше уникальное имя пользователя.</b><br />Используйте значение, которое вы указали при регистрации.</span>Логин
                        </label>
                        <input id="login" type="text" name="login" required pattern="^[\w]{5,100}$" maxlength="100"/>
                        <br />

                    </div>
                    <div>
                        <label for="psw">
                            <span class="tip"><b>Ваш секретный код.</b><br />Используйте значение, которое вы указали при регистрации.</span>Пароль
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
                    <div class="log_message"><div></div></div>
                    <div>
                        <label for="login">
                                <span class="tip"><b>Ваше уникальное имя пользователя.</b><br />Обязательное поле.<br />Может содержать буквы латинского алфавита, цифры и символ подчеркивания.<br />Допустимая длина: 5 - 100 символов.<br /></span>Логин
                        </label>
                        <input id="login" type="text" name="login" required pattern="^[\w]{5,100}$" maxlength="100" />
                        <br />

                    </div>
                    <div>
                        <label for="psw">
                                <span class="tip"><b>Ваш секретный код.</b><br />Обязательное поле.<br />Может содержать буквы латинского алфавита, цифры, а также символы !~<>@#$%^&*()+=-_?:;,./\<br />Минимальная длина: 6 символов.<br /><b>Помните: для лучшей безопасности используйте длинные пароли с как можно большим количеством не-буквенных символов.</b></span>Пароль
                        </label>
                        <input id="psw" type="password" name="password" required pattern="^[\w\<\>\!\~\@\#\$\%\^\&\*\(\)\+\=\-_\?\:\;\,\.\/\\]{6,}$"/>
                        <br />
                    </div>
                    <div>
                        <label for="mail">
                                <span class="tip"><b>Ваш адрес электронной почты</b><br />Можете оставить это поле незаполненным.</span>E-mail
                        </label>
                        <input id="mail" type="email" name="email" />
                        <br />
                    </div>
                    <div>
                        <label for="real_name">
                                <span class="tip"><b>Ваше настоящее имя</b><br />Можете оставить это поле незаполненным.</span>Имя
                        </label>
                        <input id="real_name" type="text" name="real_name" pattern="^[A-Za-zА-ЯЁІЇЄа-яёіїє\s]+$" maxlength="50"/>
                        <br />
                    </div>
                </div>
            </div>
            <div><div class="vbottom"><div><input id="reg_submit" type="submit" value="Регистрация" /><input type="reset" /></div></div></div>
        </form>
    </div>
</div>
<script>
    var mcnt = <?php if (isset($_SESSION['login_success']) || isset($_SESSION['logout_success'])) echo '1'; else echo '0'?>;
     $('#sidebar_bg').click(function (e) {

             mcnt = 0;
             $('.sidebar').removeClass('expanded');
             $('#sidebar_bg').removeClass('expanded');

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
                 $('#reg_menu').removeClass('expanded');
                 mcnt--;
                 $('#user_menu .log_message > div').html('<p>Отлично!</p><p>Вы только что успешно зарегистрировались на нашем сайте.</p>').parent().removeClass('success fail').addClass('success').css('height', $('#user_menu .log_message > *').outerHeight(true));
                 setTimeout(function () { $('#user_menu .log_message').css('height', 0); }, 3000);
             }).fail(function () {
                 $('#reg_menu .log_message > div').html('<p>Хьюстон, у нас проблемы!</p><br/>' + j.responseText).parent().removeClass('success fail').addClass('fail').css('height', $('#reg_menu .log_message > *').outerHeight(true));
             }).always(function () {
                 $('#reg_submit').removeClass('loading');
                 setTimeout(function () { $('#reg_menu .log_message').css('height', 0); }, 3000);
             });
         });

         $('#login_menu form').submit(function (e) {
             e.preventDefault();
             $('#login_submit').addClass('loading');
             var j = $.post('/users/login/', $(this).serialize(), function () {
                location.reload();
             }).fail(function () {
                 $('#login_menu .log_message > div').html('<p>Хьюстон, у нас проблемы!</p><br/>' + j.responseText).parent().removeClass('success fail').addClass('fail').css('height', $('#login_menu .log_message > *').outerHeight(true));
             }).always(function () {
                 $('#login_submit').removeClass('loading');
                 setTimeout(function () { $('#login_menu .log_message').css('height', 0); }, 3000);
             });
         });

         $('#logout_item').click(function () {
             var j = $.post('/users/logout/', [], function () {
                 location.reload();
             }).fail(function(){
                $('#user_menu .log_message > div').html('<p>Хьюстон, у нас проблемы!</p><br/>' + j.responseText).parent().removeClass('success success').addClass('fail').css('height', $('#user_menu .log_message > *').outerHeight(true));
                 setTimeout(function () { $('#user_menu .log_message').css('height', 0); }, 3000);
            });

         });

         <?php
            if (isset($_SESSION['login_success'])){ ?>
                $('#user_menu .log_message > div').html('<p> <?php echo ($this->data['user']['last_visit'] ? 'С возвращением, ' : 'Добро пожаловать, ') ; echo $this->data['user']['login']; ?>!</p>').parent().removeClass('success fail').addClass('success').css('height', $('#user_menu .log_message > *').outerHeight(true));
                 setTimeout(function () { $('#user_menu .log_message').css('height', 0); }, 5000);
        <?php   unset($_SESSION['login_success']);
            } else if (isset($_SESSION['logout_success'])) {
        ?>
                $('#user_menu .log_message > div').html('<p>Удачного дня!</p>').parent().removeClass('success fail').addClass('success').css('height', $('#user_menu .log_message > *').outerHeight(true));
                 setTimeout(function () { $('#user_menu .log_message').css('height', 0); }, 5000);
    <?php unset($_SESSION['logout_success']);
            } ?>

</script>