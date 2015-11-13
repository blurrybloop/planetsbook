<aside>
    <div class="sticky">
        <div>
            <?php if (isset($this->data['user']['id']) && $this->data['user']['is_admin']) { ?>
            <div class="section <?php if ($this->action == 'messages') echo 'selected' ?>"><div><a href="/admin/">Сообщения</a></div></div>
            <div class="section <?php if ($this->action == 'sections') echo 'selected' ?>"><div><a href="/admin/sections">Разделы</a></div></div>
            <?php } ?>
            <div class="section <?php if ($this->action == 'publicate') echo 'selected' ?>">
                <div>
                    <a href="/admin/publicate">Публикации</a>
                </div>
            </div>
            <?php if (isset($this->data['user']['id']) && $this->data['user']['is_admin']) { ?>
            <div class="section"><div><a>Пользователи</a></div></div>
            <?php } ?>
        </div>
    </div>
</aside>