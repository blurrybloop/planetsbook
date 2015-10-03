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
                            <div class="tip">
                                <div class="tiptext">Плутон</div>
                                <div class="tipimg">
                                    <img src="img/tooltip.png" /></div>
                            </div>
                        </label>
                        <input name="radio" type="radio" id="1" />
                        <label for="1">
                            <div class="tip">
                                <div class="tiptext">Нептун</div>
                                <div class="tipimg">
                                    <img src="img/tooltip.png" /></div>
                            </div>
                        </label>
                        <input name="radio" type="radio" id="2" />
                        <label for="2">
                            <div class="tip">
                                <div class="tiptext">Уран</div>
                                <div class="tipimg">
                                    <img src="img/tooltip.png" /></div>
                            </div>
                        </label>
                        <input name="radio" type="radio" id="3" />
                        <label for="3">
                            <div class="tip">
                                <div class="tiptext">Сатурн</div>
                                <div class="tipimg">
                                    <img src="img/tooltip.png" /></div>
                            </div>
                        </label>
                        <input name="radio" type="radio" id="4" />
                        <label for="4">
                            <div class="tip">
                                <div class="tiptext">Юпитер</div>
                                <div class="tipimg">
                                    <img src="img/tooltip.png" /></div>
                            </div>
                        </label>
                        <input name="radio" type="radio" id="5" />
                        <label for="5">
                            <div class="tip">
                                <div class="tiptext">Марс</div>
                                <div class="tipimg">
                                    <img src="img/tooltip.png" /></div>
                            </div>
                        </label>
                        <input name="radio" type="radio" id="6" />
                        <label for="6">
                            <div class="tip">
                                <div class="tiptext">Земля</div>
                                <div class="tipimg">
                                    <img src="img/tooltip.png" /></div>
                            </div>
                        </label>
                        <input name="radio" type="radio" id="7" />
                        <label for="7">
                            <div class="tip">
                                <div class="tiptext">Венера</div>
                                <div class="tipimg">
                                    <img src="img/tooltip.png" /></div>
                            </div>
                        </label>
                        <input name="radio" type="radio" id="8" />
                        <label for="8">
                            <div class="tip">
                                <div class="tiptext">Меркурий</div>
                                <div class="tipimg">
                                    <img src="img/tooltip.png" /></div>
                            </div>
                        </label>
                        <input name="radio" type="radio" id="9" checked="checked" />
                        <label for="9">
                            <span class="tip">
                                <span class="tiptext">Солнце</span>
                                <span class="tipimg">
                                    <img src="img/tooltip.png" /></span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <!--[if lte IE 9]> 
        <div class="msgbox_container">
            <div>
                <div>
                    <div class="msgbox">
                        <div>
                            <div>✕</div>
                        </div>
                        <div>
                            <p>
                                Мы заметили, что вы используете косой, кривой и устаревший браузер, известный также под названием Internet Explorer.<p>
                            <p>Вы можете сделать следующее:</p>
                            <ol>
                                <li>Установить последнюю версию одного из популярных браузеров. (например: <a target="_blank" href="https://www.mozilla.org/firefox/new/">Firefox</a>, <a target="_blank" href="http://www.opera.com/">Opera</a>, <a target="_blank" href="https://www.google.com/chrome/browser/">Chrome</a>)</li>
                                <li>Продолжить пользоваться косым, кривым и устаревшим браузером. Но если вы заметите странное поведение сайта, пеняйте на себя))</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        -->
    <?php include('/view/footer.php'); ?>
    <script src="js/wheel.js"></script>
   <script>
       $('.msgbox > div > div').click(function () {
           $(this).parents('.msgbox_container').removeClass('showed');
       });

       $(document).ready(function () {
           $('.msgbox_container').addClass('showed');
       });
       //$(window).resize(function () {
       //    var msg = $('.msgbox');
       //    msg.children('div:first-child + div').css('max-height', parseInt(msg.parent().height()) * 0.6);
       //});
   </script>
