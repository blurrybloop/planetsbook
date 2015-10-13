<?php include('/view/header.php'); ?>
<link rel="stylesheet" href="css/planet_selector.css" />
<script src="js/utils.js"></script>
<script src="js/planet_selector.js"></script>
<?php include('/view/footer.php'); ?>
<script>var ps = new PlanetsSelector('#content', [
{
    image: 'img/sun.png',
    title: 'Солнце',
    description: 'Солнце',
    href: '/read.php',
    moons: []
},
{
    image: '/img/mercury.png',
    title: 'Меркурий',
    description: 'Меркурий',
    href: '/read.php',
    moons: []
},
{
    image: '/img/venus.png',
    title: 'Венера',
    description: 'Венера',
    href: '/read.php',
    moons: []
},
{
    image: '/img/earth.png',
    title: 'Земля',
    description: 'Земля',
    href: '/read.php',
    moons: [{image: '/img/moon.png', description: 'Луна', href: '/read.php'}]
},
{
    image: '/img/mars.png',
    title: 'Марс',
    description: 'Марс',
    href: '/read.php',
    moons: [{ image: '/img/phobos_deimos.png', description: 'Фобос и Деймос', href: '/read.php' }]
},
{
    image: '/img/jupiter.png',
    title: 'Юпитер',
    description: 'Юпитер',
    href: '/read.php',
    moons: [{ image: '/img/io.png', description: 'Ио', href: '/read.php' },
            { image: '/img/europa.png', description: 'Европа', href: '/read.php' },
            { image: '/img/callisto.png', description: 'Каллисто', href: '/read.php' },
            { image: '/img/ganymede.png', description: 'Ганимед', href: '/read.php' }
    ]
},
{
    image: '/img/saturn.png',
    title: 'Сатурн',
    description: 'Сатурн',
    href: '/read.php',
    moons: [{ image: '/img/titan.png', description: 'Титан', href: '/read.php' }]
},
{
    image: '/img/uranus.png',
    title: 'Уран',
    description: 'Уран',
    href: '/read.php',
    moons: []
},
{
    image: '/img/neptune.png',
    title: 'Нептун',
    description: 'Нептун',
    href: '/read.php',
    moons: [{ image: '/img/triton.png', description: 'Тритон', href: '/read.php' }]
},
{
    image: '/img/pluto.png',
    title: 'Плутон',
    description: 'Плутон',
    href: '/read.php',
    moons: [{ image: '/img/charon.png', description: 'Харон', href: '/read.php' }]
}
]);

    setTimeout(function (e) {
        e.children('.wheel_tip').addClass('invisible');
    },
    5000,
    $('#content').append("<img class='wheel_tip' src='img/scroll_tip.png' />"));

    var onwheel = function (e) {
        if (e.originalEvent) {
            $('#content > .wheel_tip').addClass('invisible');
            var delta = e.originalEvent.deltaY || e.originalEvent.detail || e.originalEvent.wheelDelta;
            if (delta > 0) ps.moveNext();
            else if (delta < 0) ps.movePrev();
            return false;
        }
    };

    if ('onwheel' in document)
        $(document).on('wheel', '#content', onwheel);
    else if ('onmousewheel' in document)
        $(document).on('mousewheel', '#content', onwheel);
    else
        $(document).on('MozMousePixelScroll', '#content', onwheel);

</script>

<!--[if lte IE 9]>
  <script>
       $(document).ready(function () {
           messageBox("<p>Мы заметили, что вы используете косой, кривой и устаревший браузер, известный также под названием Internet Explorer.<p><p>Вы можете сделать следующее:</p><ol><li>Установить последнюю версию одного из популярных браузеров. (например: <a target='_blank' href='https://www.mozilla.org/firefox/new/'>Firefox</a>, <a target='_blank' href='http://www.opera.com/'>Opera</a>, <a target='_blank' href='https://www.google.com/chrome/browser/'>Chrome</a>)</li><li>Продолжить пользоваться косым, кривым и устаревшим браузером. Но если вы заметите странное поведение сайта, пеняйте на себя))</li></ol>");
       });
</script>
       -->



