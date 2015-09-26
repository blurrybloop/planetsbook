<div id="menu" class="left_slide">
    <div>
        <img src="img/arrow.png" class="expand" />
        <a href="/">
            <h1>PlanetsBook</h1>
        </a>
    </div>
    <div class="items">
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
        <div class="bottom">
            <div>
                <img src="img/photos.png" /><p>Фотоальбом</p>
            </div>
            <div>
                <img src="img/video.png" /><p>Видео</p>
            </div>
            <div id="login">
                <img src="img/user.png" /><p>Вход</p>
            </div>
        </div>

    </div>
</div>
<div class="left_slide" id="planets_menu">
    <div>
        <img src="img/arrow.png" class="expand" />
        <h1>Планеты</h1>
    </div>
    <div class="items">
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
<div class="left_slide" id="moon_menu">
    <div>
        <img src="img/arrow.png" class="expand" />
        <h1>Спутники</h1>
    </div>
    <div class="items">
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
<div class="left_slide" id="search_menu">
    <div>
        <img src="img/arrow.png" class="expand" />
        <h1>Поиск</h1>
    </div>
    <div class="items">
        <div class="search_container">
            <input type="search" placeholder="Что вы хотите найти?" />
            <img src="img/search.png" />
        </div>
    </div>
</div>
<script src="js/jQuery/jquery-1.11.0.min.js"></script>
 <script>
     $('#main').click(function () {

             $('.left_slide').removeClass('expanded');
         });

         $('.expand').click(this, function () {
             if ($(this).parents('.left_slide').hasClass('expanded')) $(this).parents('.left_slide').removeClass('expanded');
             else $(this).parents('.left_slide').addClass('expanded');
         });

         $('#planets_item').click(function () {
             if ($('#planets_menu').hasClass('expanded')) $('#planets_menu').removeClass('expanded');
             else $('#planets_menu').addClass('expanded');
         });

         $('#moon_item').click(function () {
             if ($('#moon_menu').hasClass('expanded')) $('#moon_menu').removeClass('expanded');
             else $('#moon_menu').addClass('expanded');
         });

         $('#search_item').click(function () {
             if ($('#search_menu').hasClass('expanded')) $('#search_menu').removeClass('expanded');
             else $('#search_menu').addClass('expanded');
         }); 
    </script>
