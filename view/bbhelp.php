<h1>Как редактировать комментарии?</h1>
<p>На нашем сайте поддерживаются комментарии в виде простого текста, а также некоторые возможности форматирования с помощью BB-кодов.</p>
<h2>Что такое BB-коды?</h2>
<p>BB-коды — это набор тегов форматирования, которые влияют на оформление (вид) текста. BB-коды принципиально похожи на HTML и аналогичны. Ниже представлен список доступных BB-кодов и пояснения по их применению.</p>
<h2>Стилизация текста</h2>
<p>Отображение (вид) текста может быть изменено его вложением в представленные теги.</p>
<p>[b]Полужирное начертание[/b] выводит <?php $this->parser->text = '[b]Полужирное начертание[/b]'; echo $this->parser->parse();?></p>
<p>[i]Курсивный текст[/i] выводит <?php $this->parser->text = '[i]Курсивный текст[/i]'; echo $this->parser->parse();?></p>
<p>[u]Подчёркнутый текст[/u] выводит <?php $this->parser->text = '[u]Подчёркнутый текст[/u]'; echo $this->parser->parse();?></p>
<p>[s]Зачёркнутый текст[/s] выводит <?php $this->parser->text = '[s]Зачёркнутый текст[/s]'; echo $this->parser->parse();?></p>
<p>H[sub]2[/sub]SO[sub]4[/sub], 10[sup]-9[/sup] выводит <?php $this->parser->text = 'H[sub]2[/sub]SO[sub]4[/sub], 10[sup]-9[/sup]'; echo $this->parser->parse();?></p>
<p>[color=#FF0000]Красный текст[/color] выводит <?php $this->parser->text = '[color=#FF0000]Красный текст[/color]'; echo $this->parser->parse();?></p>
<p>[color=blue]Синий текст[/color] выводит <?php $this->parser->text = '[color=blue]Синий текст[/color]'; echo $this->parser->parse();?></p>
<p>[b][u]Подчёркивание текста с полужирным начертанием[/u][/b] выводит <?php $this->parser->text = '[b][u]Подчёркивание текста с полужирным начертанием[/u][/b]'; echo $this->parser->parse();?></p>
<p>[h1]Текст заголовка[/h1] выводит <h1 style="font-size: 11pt;">Текст заголовка</h1></p>
<h2>Выравнивание текста</h2>
<p>Для выравнивания текста используется тег "align". Вы можете указать вырванивание по левому (left), правому (right) краю, центру (center) и ширине (justify):</p>
<p>[align=left]Lorem ipsum dolor sit amet, consectetur adipiscing elit.[/align] выводит</p>
<?php $this->parser->text = '[align=left]Lorem ipsum dolor sit amet, consectetur adipiscing elit.[/align]'; echo $this->parser->parse();?>
<p>[align=right]Lorem ipsum dolor sit amet, consectetur adipiscing elit.[/align] выводит</p>
<?php $this->parser->text = '[align=right]Lorem ipsum dolor sit amet, consectetur adipiscing elit.[/align]'; echo $this->parser->parse();?>
<p>[align=center]Lorem ipsum dolor sit amet, consectetur adipiscing elit.[/align] выводит</p>
<?php $this->parser->text = '[align=center]Lorem ipsum dolor sit amet, consectetur adipiscing elit.[/align]'; echo $this->parser->parse();?>
<p>[align=justify]Lorem ipsum dolor sit amet, consectetur adipiscing elit.[/align] выводит</p>
<?php $this->parser->text = '[align=justify]Lorem ipsum dolor sit amet, consectetur adipiscing elit.[/align]'; echo $this->parser->parse();?>
<h2>Ссылки</h2>
<p>Вы можете создавать ссылки на другие документы или адреса электронной почты, используя следующие теги:</p>
<p>[url="planetsbook.pp.ua"]PlanetsBook[/url] выводит <?php $this->parser->text = '[url="planetsbook.pp.ua"]PlanetsBook[/url]'; echo $this->parser->parse();?></p>
<p>[url]planetsbook.pp.ua[/url] выводит <?php $this->parser->text = '[url]planetsbook.pp.ua[/url]'; echo $this->parser->parse();?></p>
<h2>Списки</h2>
<p>Для создания списка вы можете использовать тег «list». Вы можете создавать два типа списков, используя этот тег.</p>
<p>[list]<br />[*]Пример элемента списка 1.[/*]<br />[*]Пример элемента списка 2.[/*]<br />[*]Пример элемента списка 3.[/*]<br />[/list]<br /> выводит маркированный список.
    <?php $this->parser->text = '[list][*]Пример элемента списка 1.[/*][*]Пример элемента списка 2.[/*][*]Пример элемента списка 3.[/*][/list]'; echo $this->parser->parse();?>
</p>

<p>
    [list=1]<br/>[*]Пример элемента списка 1.[/*]<br />[*]Пример элемента списка 2.[/*]<br />[*]Пример элемента списка 3.[/*]<br />[/list]<br /> выводит нумерованный список.
    <?php $this->parser->text = '[list=1][*]Пример элемента списка 1.[/*][*]Пример элемента списка 2.[/*][*]Пример элемента списка 3.[/*][/list]'; echo $this->parser->parse();?>
</p>
<h2>Рисунки</h2>
<p>Для простого вывода рисунка используйте тег "img":</p>
<p>[img]http://planetsbook.pp.ua/img/logo.png[/img] выводит</p>
<?php $this->parser->text = '[img]/img/logo.png[/img]'; echo $this->parser->parse();?>
<p>Если вы хотите изменить ширину, высоту или позицию рисунка, добавить подпись, вложите тег "img" в тег "figure"</p>
<p>[figure=center width=400]<br/>[img]http://planetsbook.pp.ua/img/logo.png[/img]<br/>[figcaption=left]Тестовый рисунок[/figcaption]<br/>[/figure] выводит</p>
<?php $this->parser->text = '[figure=center width=400][img]/img/logo.png[/img][figcaption=left]Тестовый рисунок[/figcaption][/figure]'; echo $this->parser->parse();?>
<h2>Разделители</h2>
<p>Для вставки горизонтальной разделительной линии используйте тег "hr".</p>
<p>[hr][/hr] выводит</p>
<?php $this->parser->text = '[hr][/hr]'; echo $this->parser->parse();?>
<h2>Как увидеть результат применения BB-кодов?</h2>
<p>Если вы хотите узнать, как будет выглядеть ваш комментарий после отправки, используйте кнопку "Предпросмотр". Вы всегда сможете вернуться в режим редактирования, если результат вас не устроит.</p>

