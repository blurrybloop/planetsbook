<?php
if (empty($this->data)) return;
foreach ($this->data as $comment) {?>
<article class="comment" <?php if (isset($comment['id'])) echo "id='comm{$comment['id']}'" ?> >
        <div>
            <?php echo $comment['login'] ?>
            <div>
                <img src="/img/user_big.png" style="width: 50%" />
            </div>
            <?php  echo $comment['is_admin'] ? 'Администратор' : 'Пользователь' ?>
            <div class="info">
                <div>Репутация: <span style="color: green;">+5</span></div>
                <div>Зарегистирован: <time><?php echo $comment['reg_date'] ?></time></div>
                <div>Комментариев: 3</div>
            </div>
        </div>
        <div>
            <div class="comm_header">
                <?php if ($this->outputMode == 1) { ?>
                <div><img class='comm_bold' src='/img/bold.png' /><div class='tip'>Вставить тег жирного начертания</div></div><div><img class='comm_italic' src='/img/italic.png' /><div class='tip'>Вставить тег курсивного начертания</div></div><div><img class='comm_underline' src='/img/underline.png' /><div class='tip'>Вставить тег подчеркивания</div></div><div><img class='comm_left_align' src='/img/left_align.png' /><div class='tip'>Выравнивание по левому краю</div></div><div><img class='comm_center_align' src='/img/center_align.png' /><div class='tip'>Выравнивание по центру</div></div><div><img class='comm_right_align' src='/img/right_align.png' /><div class='tip'>Выравнивание по правому краю</div></div><div><img class='comm_justify_align' src='/img/justify_align.png' /><div class='tip'>Выравнивание по ширине</div></div>
                <?php
 }
                      else {
                ?>
                <time><?php echo $comment['add_date'] ?></time><div class="rate"><div ><img class="comm_like" src="/img/like.png" /></div>
                <?php $rate =  isset($comment['rate']) ? $comment['rate'] : 0;
                      if ($rate > 0) echo "<span style='color: green;'>+$rate</span>";
                      else if ($rate == 0) echo "<span style='color: white;'>$rate</span>";
                      else echo "<span style='color: red;'>$rate</span>";
                ?>
                <div ><img  class="comm_dislike" src="/img/dislike.png" /></div></div>
                <?php } ?>
            </div>
            <div class="comm_body">
                <?php if ($this->outputMode == 1) { ?>
                <textarea id='edit_field' placeholder='Введите ваш комментарий...'><?php if (isset($comment['comm_text'])) echo $comment['comm_text'] ?></textarea>
                <?php
                      }
                      else {
                          $this->parser->text = $comment['comm_text'];
                            echo $this->parser->parse();
                      }
                ?>
            </div>
            <div class="comm_footer maximized <?php if ($this->outputMode == 1) echo 'nohide' ?>">
                <?php if ($this->outputMode == 0) { ?>
                <div class="comm_delete"></div>
                <?php if ($comment['user_id'] == 1) {?>
                <div class="comm_edit">&nbsp;</div>
                <?php } ?>
                <?php } else if ($this->outputMode ==1) { ?>
                <div class='comm_send'>&nbsp;</div><div class='comm_cancel'>&nbsp;</div><div class='comm_apply'>&nbsp;</div>
                <?php } else if ($this->outputMode == 2) {?>
                <div class='comm_cancelApply'>&nbsp;</div><div class='comm_cancel'>&nbsp;</div><div class='comm_send'>&nbsp;</div>
                <?php } ?>
            </div>
        </div>
</article>
<?php } ?>