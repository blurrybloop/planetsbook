<?php
if (!isset($this->data['comments'])) return;
if (count($this->data['comments']) == 0){ ?>
  <div class="nocontent"><div>Пока нет ни одного комментария.</div>
  <?php if (isset($this->data['user'])) echo '<div>Станьте первым, кто оставит свой отзыв об этой статье.</div>' ?>

    </div>
<?php   
}
foreach ($this->data['comments'] as $comment) {?>
<article class="comment" <?php if (isset($comment['id'])) echo "id='comm{$comment['id']}'" ?> >
        <div>
            <div class='user_name'><?php echo $comment['login'] ?></div>
            <div>
                <?php if (isset($comment['user_id'])) { ?>
                <a href="<?php echo '/users/profile?id=' . $comment['user_id'] ?>">
                    <?php } ?>
                    <img src="<?php if (file_exists(PATH_AVATAR . $comment['avatar'] . '.png')) echo $this->app->config['path']['avatar'] . $comment['avatar'] . '.png';  ?>" style="width: 50%" />
                <?php if (isset($comment['user_id'])) { ?>
                </a>
                <?php } ?>
            </div>
            <?php  echo $comment['is_admin'] ? 'Администратор' : 'Пользователь' ?>
            <div class="info">
                <div>Репутация: 
                <?php
    $rate =  isset($comment['rating']) ? $comment['rating'] : 0;
    if ($rate > 0) echo "<span style='color: green;'>+$rate</span>";
    else if ($rate == 0) echo "<span style='color: white;'>$rate</span>";
    else echo "<span style='color: red;'>$rate</span>"; 
                ?>
                </div>
                <div>Зарегистирован: <time><?php echo $comment['reg_date'] ?></time></div>
                <div>Комментариев: <span class="comm_cnt"><?php echo (isset($comment['comments_cnt']) ? $comment['comments_cnt'] : '0');?></span></div>
            </div>
        </div>
        <div>
            <div class="comm_header">
                <?php if ($this->outputMode == OUT_TEXT) { ?>
                <div><img class='comm_bold' src='/img/bold.png' /><div class='tip'>Жирный<br />[b]Пример[/b]</div></div>
                <div><img class='comm_italic' src='/img/italic.png' /><div class='tip'>Курсив<br/>[i]Пример[/i]</div></div>
                <div><img class='comm_underline' src='/img/underline.png' /><div class='tip'>Подчеркнутый<br />[u]Пример[/u]</div></div>
                <div><img class='comm_strike' src='/img/strike.png' /><div class='tip'>Зачеркнутый<br />[s]Пример[/s]</div></div>
                <div><img class='comm_sup' src='/img/superscript.png' /><div class='tip'>Верхний индекс<br />[sup]Пример[/sup]</div></div>
                <div><img class='comm_sub' src='/img/subscript.png' /><div class='tip'>Нижний индекс<br />[sub]Пример[/sub]</div></div>
                <div><img class='comm_left_align' src='/img/left_align.png' /><div class='tip'>Выравнивание по левому краю<br />[align=left]Пример[/align]</div></div>
                <div><img class='comm_center_align' src='/img/center_align.png' /><div class='tip'>Выравнивание по центру<br />[align=center]Пример[/align]</div></div>
                <div><img class='comm_right_align' src='/img/right_align.png' /><div class='tip'>Выравнивание по правому краю<br />[align=right]Пример[/align]</div></div>
                <div><img class='comm_justify_align' src='/img/justify_align.png' /><div class='tip'>Выравнивание по ширине<br />[align=justify]Пример[/align]</div></div>
                <div><img class='comm_ul' src='/img/list_bullets.png' /><div class='tip'>Маркированый список<br />[list=*]<br />[*]Один[/*]<br />[*]Два[/*]<br />[*]Три[/*]<br />[/list]</div></div>
                <div><img class='comm_ol' src='/img/list_num.png' /><div class='tip'>Нумерованый список<br />[list=(1|A|a|i|I)]<br/>[1]Один[/1]<br />[2]Два[/2]<br />[3]Три[/3]<br />[/list]</div></div>
                <div><img class='comm_url' src='/img/link.png' /><div class='tip'>Ссылка<br />[url]planetsbook.pp.ua[/url]<br/>или<br/>[url="planetsbook.pp.ua"]Пример[/url]</div></div>
                <div><img class='comm_img' src='/img/picture.png' /><div class='tip'>Рисунок с подписью<br />[figure=(left|center|right|float-left|float-right) width=# height=#]<br/>[img]test.png[/img]<br />[figcaption=(left|center|right|justify)]Подпись[/figcaption]<br/>[/figure]</div></div>
                <div><img class='comm_help' src='/img/question.png' /><div class='tip'>Справка</div></div>
                <?php
 }
                      else {
                ?>
                <time><?php echo $comment['date_add'] ?></time><div class="rate"><?php if (isset($this->data['user'])) { ?><div><img class="comm_like" src="/img/like.png" /><div class="tip">Нравится</div></div> <?php } ?>
                <?php $rate =  isset($comment['rate']) ? $comment['rate'] : 0;
                      if ($rate > 0) echo "<span style='color: green;'>+$rate</span>";
                      else if ($rate == 0) echo "<span style='color: white;'>$rate</span>";
                      else echo "<span style='color: red;'>$rate</span>";
                ?>
                <?php if (isset($this->data['user'])) { ?><div ><img  class="comm_dislike" src="/img/dislike.png" /><div class="tip">Не нравится</div></div><?php } ?></div>
                <?php } ?>
            </div>
            <div class="comm_body">
                <?php if ($this->outputMode == OUT_TEXT) { ?>
                <textarea id='edit_field' placeholder='Введите ваш комментарий...'><?php if (isset($comment['comm_text'])) echo $comment['comm_text'] ?></textarea>
                <?php
                      }
                      else {
                          $this->parser->text = $comment['comm_text'];
                            echo $this->parser->parse();
                      }
                ?>
                <div class="clearfix"></div>
            </div>
            <div class="comm_footer maximized <?php if ($this->outputMode == OUT_TEXT) echo 'nohide' ?>">
                <?php if ($this->outputMode == OUT_NORMAL) { ?>
                <?php if ($this->validateRights([USER_ADMIN], $comment['id'], FALSE)) { ?><div class="comm_delete">&nbsp;</div><?php } ?>
                <?php
                  if ($this->validateRights(NULL, $comment['id'], FALSE)) {?>
                <div class="comm_edit">&nbsp;</div>
                <?php } ?>
                <?php } else if ($this->outputMode == OUT_TEXT) { ?>
                <div class='comm_send'>&nbsp;</div><div class='comm_cancel'>&nbsp;</div><div class='comm_apply'>&nbsp;</div>
                <?php } else if ($this->outputMode == OUT_PREVIEW) {?>
                <div class='comm_cancelApply'>&nbsp;</div><div class='comm_cancel'>&nbsp;</div><div class='comm_send'>&nbsp;</div>
                <?php } ?>
            </div>
        </div>
</article>
<?php } ?>