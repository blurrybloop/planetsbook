<!DOCTYPE html>
<html>
<head>
<?php require 'html_head.php' ?>
    <link rel="stylesheet" href="/css/planet_selector.css" />
    <script src="/js/utils.js"></script>
    <script src="/js/planet_selector.js"></script>
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
            <div id="content"></div>
            <?php include('footer.php'); ?>
        </div>
</body>
</html>
<script></script>
<script>
var ps = new PlanetsSelector('#content', <?php file_put_contents('dump.txt', json_encode(array_values($this->data['show']))); echo json_encode(array_values($this->data['show'])) ?>);

    setTimeout(function (e) {
        e.children('.wheel_tip').addClass('invisible');
    },
    5000,
    $('#content').append("<img class='wheel_tip' src='/img/scroll_tip.png' />"));

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
