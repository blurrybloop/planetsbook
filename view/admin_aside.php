<aside>
    <div class="sticky">
        <div>
            <?php if (isset($this->data['user']['id']) && $this->data['user']['is_admin']) { ?>
            <div class="section <?php if ($this->data['action'] == 'messages') echo 'selected' ?>"><div><a href="/admin/">Сообщения</a></div></div>
            <div class="section <?php if ($this->data['action'] == 'sections') echo 'selected' ?>"><div><a href="/admin/sections/">Разделы</a></div></div>
            <?php } ?>
            <div class="section <?php if ($this->data['action'] == 'articles') echo 'selected' ?>">
                <div>
                    <a href="/admin/articles/">Публикации</a>
                </div>
            </div>
            <?php if (isset($this->data['user']['id']) && $this->data['user']['is_admin']) { ?>
            <div class="section"><div><a href="/admin/users/">Пользователи</a></div></div>
            <div class="section <?php if ($this->data['action'] == 'storage') echo 'selected' ?>"><div><a href="/admin/storage/">Хранилище</a></div></div>
            <?php } ?>

        </div>
    </div>
</aside>