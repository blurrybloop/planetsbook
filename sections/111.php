
<?php
header('Content-type: text/html; charset=utf-8');
define('PATH_CONTROLLER',$_SERVER['DOCUMENT_ROOT'] .'/controller/');
define('PATH_VIEW', $_SERVER['DOCUMENT_ROOT'] . '/view/');
include($_SERVER['DOCUMENT_ROOT'] .'/include/mysql.php');
include($_SERVER['DOCUMENT_ROOT'] .'/include/app.php');
$app=new Application();
$app->Run();
//include("view/header.php"); ?>
<!--<link rel="stylesheet" href="css/article.css" />
<div>
    <div>
        <div class="read">
            <article>
                <header>
                    <h1>Солнце</h1>
                </header>
                <div class="desc">
                    <p>Добро пожаловать на один из разделов нашего сайта!</p>
                    <p>Вы можете выбрать перейти к понравившейся статье, щелкнув на ее названии. Для того, чтобы увидеть краткое описание статьи, нажмите на стрелку справа.</p>
                </div>
                <div class="sort">
                    Сортировать по
                    <div class="combobox">
                        <div class="combohead">
                            <div></div>
                            <label for="c0" class="arrow">
                                <img src="img/down_arrow.png" />
                            </label>
                        </div>
                        <input type="checkbox" id="c0" />
                        <div class="options">
                            <div>дате публикации</div>
                            <div>популярности</div>
                            <div>алфавиту</div>
                        </div>
                    </div>
                </div>
                <div class="updown">

                    <input name="item" id="0" type="checkbox" />
                    <div>
                        <label for="0">
                            <img src="img/down_arrow.png" />
                            <a href="/article_temp.php">Гипотеза о происхождении пятен на Солнце</a>
                            <span class="info">
                                <span class="date">26.09.2015</span>
                                <span class="user">admin</span>
                                <span class="views">5</span>
                            </span>

                        </label>
                        <div class="updown_content">
                            <p>Давно известно, что на Землю, луны и др. планеты нередко падают метеориты и др. небесные тела. Земля и другие планеты неплохо защищены от таких "бомбардировок" своей атмосферой, в которой сгорает большинство небольших падающих объектов. А вот луны, не имеющие атмосферы, буквально испещрены ударными кратерами.</p>
                            <p>Солнце не только не является исключением, но, наоборот, в силу своего гигантского притяжения, в тысячи раз чаще подвержено таким "бомбардировкам". Но в отличие от лун, где каждое падение навечно оставляет след, огонь на поверхности Солнца уничтожает со временем все следы падений.</p>
                        </div>
                    </div>
                    <input name="item" id="1" type="checkbox" />
                    <div>
                        <label for="1">
                            <img src="img/down_arrow.png" />
                            <a href="/article_temp.php">Изменяется ли светимость Солнца?</a>
                            <span class="info">
                                <span class="date">26.09.2015</span>
                                <span class="user">admin</span>
                                <span class="views">10</span>
                            </span>
                        </label>
                        <div class="updown_content">
                            <p>Историческая геология свидетельствует, что в прежние геологические эпохи временами наступали похолодания. Наиболее естественно объяснить оледенения изменениями светимости Солнца.</p>
                        </div>
                    </div>
                    <input name="item" id="2" type="checkbox" />
                    <div>
                        <label for="2">
                            <img src="img/down_arrow.png" />
                            <a href="/article_temp.php">Солнечная активность</a>
                            <span class="info">
                                <span class="date">25.09.2015</span>
                                <span class="user">vasia</span>
                                <span class="views">15</span>
                            </span>
                        </label>
                        <div class="updown_content">
                            <p>Важной особенностью короны является ее лучистая структура. Корональные лучи имеют самую разнообразную форму. С одиннадцатилетним циклом Солнца меняется общий вид солнечной короны. В эпоху минимума корона имеет округлую форму, она как бы «причесана». В эпоху максимума корональные лучи раскинуты во все стороны. Часто, особенно когда на Солнце имеются большие группы пятен, в хромосфере возникают вспышки. Они похожи на огромные взрывы, длящиеся всего лишь несколько минут. За несколько минут в маленькой области высвобождается энергия порядка 100 000 миллиардов кВт/час: столько же тепла поступает от Солнца на Землю в год!</p>
                        </div>
                    </div>
                    <input name="item" id="3" type="checkbox" />
                    <div>
                        <label for="3">
                            <img src="img/down_arrow.png" />
                            <a href="/article_temp.php">Солнечная активность и атмосфера Солнца</a>
                            <span class="info">
                                <span class="date">24.09.2015</span>
                                <span class="user">2314</span>
                                <span class="views">20</span>
                            </span>
                        </label>
                        <div class="updown_content">
                            <p>Фотосфера - самый нижний слой атмосферы Солнца, в котором температура довольно быстро убывает от 8000 до 4000 К. Следствием конвективного движения вещества в верхних слоях Солнца является своеобразный вид фотосферы - грануляция. Фотосфера как бы состоит из отдельных зерен - гранул, размеры которых составляют в среднем несколько сотен (до 1000) километров. Гранула- это поток горячего газа, поднимающийся вверх. В темных промежутках между гранулами находится более холодный газ, опускающийся вниз. Каждая гранула существует всего 5-10 мин, затем на ее месте появляется новая, которая отличается от прежней по форме и размерам. Общая наблюдаемая картина при этом не меняется. Вещество фотосферы нагревается за счет энергии, поступающей из недр Солнца, а излучение, которое уходит в межпланетное пространство, уносит энергию, поэтому наружные слои фотосферы охлаждаются. В самых верхних слоях фотосферы плотность вещества составляет 10-3 - 10-4 кг/м3.</p>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </div>
    <aside>
        <div class="sticky">
            <div>
                <h1>Смотрите также</h1>
                <ul>
                    <li><a href="/article_temp.php">Земля</a></li>
                    <li><a href="/article_temp.php">Марс</a></li>
                    <li><a href="/article_temp.php">Луна</a></li>
                </ul>
            </div>
        </div>
    </aside>
</div>
<?php //include("view/footer.php"); ?>
<script src="js/utils.js"></script>
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

        $('.combobox > input[type=checkbox]').removeAttr('checked');


        $('#main').click(function (e) {
            if ($(e.target).parents('.combobox').length && $(e.target).parents('.options').length == 0) return;
            $('.combobox > input[type=checkbox]:checked').click();
        });

        $('.options > div').click(function () {
            $(this).parent().siblings('.combohead').children('div').html($(this).html());
        });

        $('.combobox > .options > div')[0].click();
</script>-->
