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
                <?php if ($this->outputMode == OUT_TEXT) { 
                          include 'bbtools.php';
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