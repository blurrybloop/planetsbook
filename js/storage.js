function Storage(parent, position) {
    this._parent = $(parent).toggleClass('js-storage', true);
    this._userID = this._parent.data("userId");
    this._isAdmin = this._parent.data("admin");
    this._target = this._parent.data("target");
    this._selected = new ArrayDictonary();
    this._position = position ? position : 0;
    this.onSelected = null;
    var self = this;
    
    var storageContent = "<div>" +
                                        "<div class='log_message'><div></div></div>" +
                                        "<div class='page-header'>" +
                                            "<div>" +

                                                "<div class='js-combobox' data-combobox-selected='0'>" +
                                                    "<div data-combobox-option='0'>Название</div>" +
                                                    "<div data-combobox-option='1'>Дата добавления</div>" +
                                               " </div>" +
                                            "</div>" +
                                            "<div class='js-storage-search'>" +
                                                "<input type='search' placeholder='Поиск...' />" +
                                                "<img src='/img/search.png' />" +
                                            "</div>" +

                                        "</div>" +
                                        "<div class='page-body'></div>" +
                                        "<div class='page-footer'>" +
                                            "<div class='js-storage-pages'><img src='/img/arrow2.png'/><div></div><img src='/img/arrow.png'/></div>" +
                                            "<div class='js-storage-tools'>" +
                                                "<label class='js-storage-upload' for='js-storage-files" + this._position + "'>Загрузить</label>" +
                                                "<label class='js-storage-select'>Выбрать</label>" +
                                                "<label class='js-storage-ok invisible'>Подтвердить</label>" +
                                                "<label class='js-storage-delete invisible'>Удалить</label>" +
                                                "<label class='js-storage-descript invisible'>Описание</label>" +
                                                "<label class='js-storage-cancel invisible'>Сбросить</label>" +
                                            "</div>" +
                                        "</div>";
   
    this._myStorage = $(storageContent);
    this._sharedStorage =  $(storageContent).addClass('invisible');

    this._storages = $("<div class='pages'></div>").append(this._myStorage).append(this._sharedStorage);

    this._storages.find('.js-storage-search').each(function (v1) {
        $(this).children('input').on('input', function () {
            self.params[v1].search = $.trim($(this).val());
            if (self.params[v1].search.length == 0) self.params[v1].search = undefined;
            self.params[v1].page = 1;
            self.fetch(v1 == 0 ? self._userID : undefined, self.params[v1].search, self.params[v1].sort, self.params[v1].page);
        });
        $(this).children('img').click(function () { $(this).prev().trigger('input') });
    });

    
    this._storages.find('.js-storage-select').click(function () { $(this).addClass('invisible').nextAll().removeClass('invisible'); self.params[self._storageIndex].select = 1; })
    this._storages.find('.js-storage-ok').click(function () {
        $(this).addClass('invisible').nextAll().addClass('invisible');
        $(this).prevAll().removeClass('invisible');
        self.params[self._storageIndex].select = 0;
        if (self.onSelected) self.onSelected.call(self, self._selected);
        self._currentStorage.find('.page-body > div').removeClass('selected');
    })

    this._storages.find('.js-storage-cancel').click(function () {
        $(this).addClass('invisible').prevUntil('.js-storage-select').addClass('invisible');
        $(this).prevAll('.js-storage-select').removeClass('invisible');
        self.params[self._storageIndex].select = 0;
        self._selected.clear();
        self._currentStorage.find('.page-body > div').removeClass('selected');
    })

    this._storages.find('.js-storage-delete').click(function () {
        self.remove(self._selected.keys);
        self._selected.clear();
    });

    this._storages.find('.js-storage-descript').click(function () {
        var p = [];
        for (var i = 0; i < self._selected.length(); i++)
            p.push({ 'id': self._selected.keyAt(i), 'href': self._selected.valueAt(i) });
        self.showDescriptDialog('', p);
    });

    this._storages.find('.js-storage-pages > img').click(function () {
        var i = $(this).index();
        if (i == 0) self.params[self._storageIndex].page--;
        else if (i == 2) self.params[self._storageIndex].page++;
        self.fetch(self._storageIndex == 0 ? self._userID : 0, self.params[self._storageIndex].search, self.params[self._storageIndex].sort, self.params[self._storageIndex].page);
    });


    this._uploadFrame = $("<iframe id='js-storage-frame" + this._position + "' name='js-storage-frame" + this._position + "'></iframe>");
    this._uploadForm = $("<form id='js-storage-uploader" + this._position + "' method='post' target='js-storage-frame" + this._position + "' enctype='multipart/form-data'>" +
                                    "<input type='file' id='js-storage-files" + +this._position + "' name='files[]' multiple />" +
                                "</form>").change(function () { self.upload() });

    this._parent.append("<div class='tabs'>" +
                                    "<input type='radio' name='tabs-radio' id='tabs-radio-0' checked />" +
                                    "<label for='tabs-radio-0'>" +
                                        "<span>Мое хранилище</span>" +
                                    "</label>" +
                                    "<input type='radio' name='tabs-radio' id='tabs-radio-1' />" +
                                    "<label for='tabs-radio-1'>" +
                                        "<span>Общее хранилище</span>" +
                                    "</label>" +
                                "</div>").append(this._storages).append(this._uploadFrame).append(this._uploadForm);

    this._parent.find('.page-body').click(function (e) {
        if (e.target.className != 'page-body') {
            if (self.params[self._storageIndex].select) {
                var pic = $(e.target).closest('div');
                if (pic.is('.inactive')) return;
                var id = pic.data("fileId");
                if (pic.is('.selected')) self._selected.remove(id);
                else self._selected.set(id, pic.children('img').attr('src'));
                pic.toggleClass('selected');
            }
            else {
                var i = [];
                $(this).closest('.page-body').find('img').each(function () {
                    i.push({ 'src': $(this).attr('src'), 'title': $(this).attr('src'), 'date': $(this).parent().data("creationDate"), 'description': $(this).parent().data("description") })
                });
                var index = $(e.target).closest('div').index();
                var fs = new Fullscreen(i);
                fs.show(index);
            }
        }
    });

    this._currentStorage = this._myStorage;
    this._storageIndex = 0;
    this.params = [{ 'page': 1, 'sort': 1, 'select': 0 }, { 'page': 1, 'sort': 1, 'select': 0 }]

    var callback = function () {
        var page = parseInt(this.id.substr(this.id.lastIndexOf('-') + 1)) + 1;
        $('.pages>div:not(.invisible)').addClass('invisible');

        $('.pages>div:nth-child(' + page + ')').removeClass('invisible');

        if (page == 1) self._currentStorage = self._myStorage;
        else if (page == 2) self._currentStorage = self._sharedStorage;
        self._storageIndex = page - 1;
        self.fetch(self._storageIndex == 0 ? self._userID : 0, self.params[self._storageIndex].search, self.params[self._storageIndex].sort, self.params[self._storageIndex].page);
    }

    this._parent.find('.tabs>input[type=radio]').change(callback);
    //callback.call(this._parent.find('.tabs>input[type=radio]')[0]);

    this._storages.find('.js-combobox').each(function (v1) {
        $(this).change(function () {

            if (self.params[v1].sort == $(this).attr('data-combobox-selected')) return;
            self.params[v1].sort = $(this).attr('data-combobox-selected');
            if (v1 == self._storageIndex)
                self.fetch(self._storageIndex == 0 ? self._userID : 0, self.params[v1].search, self.params[v1].sort, self.params[v1].page);
        })
        $(this).attachCombobox();
    });

}

Storage.prototype.fetch = function (user, search, sort, page) {
    var self = this;

    var j = $.getJSON(this._target + 'fetch/', { 'user_id': user, 'search': search, 'sort': sort, 'page': page }, function (data) {
        self._currentStorage.children('.page-body').html("");
        self._currentStorage.find('.js-storage-pages > div').html(data.page.start + '-' + data.page.end);
        self.params[self._storageIndex].page = data.page.current;

        for (var i = 0; i < data.fetched.length; i++) {
            self._currentStorage.children('.page-body').append(
                $("<div class='" +(data.fetched[i].active ? ' ' : 'inactive ') + "loading " + ((self.params[self._storageIndex].select && self._selected.get(parseInt(data.fetched[i].id)) != undefined) ? 'selected' : '') + "'  data-file-id='" + data.fetched[i].id + "' data-creation-date='" + data.fetched[i].add_date + "' data-description='" + data.fetched[i].description + "'></div>").append(
                $("<img src='" + data.fetched[i].href + "'/>").load(function () {
                    $(this).parent().removeClass('loading');
                })));
        }
    }).fail(function () {
        self.showError(j.responseJSON);
    });
}

Storage.prototype.descript = function (id, text) {
    $.getJSON(this._target + 'descript/', { 'file_id': id, 'text': text });
}

Storage.prototype.upload = function () {
    var self = this;
    this._uploadFrame.one('load', function () {
        var p = JSON.parse($(this).contents().find('body').html());
        self.showDescriptDialog("<h2 style='text-align: left;'>Загрузили картинку?</h2><p style='text-align: left;'>Всего несколько слов помогут пользователям найти ее!</p>", p);
        self.fetch(self._storageIndex == 0 ? self._userID : 0, self.params[self._storageIndex].search, self.params[self._storageIndex].sort, self.params[self._storageIndex].page);
        if (p.error != undefined)
            self.showError(p);
        else
            self.showSuccess("Файлы были успешно загружены.");
    });

    this._uploadForm.attr('action', this._target + 'upload/');
    this._uploadForm.submit();
}


Storage.prototype.remove = function (ids) {
    var self = this;
    var c = ids.length;
    var j = $.getJSON(this._target + 'delete/', { 'file_id': ids }, function (data) {
        self.showSuccess('Удалено ' + c + ' файлов(а).');        
    }).fail(function () {
        self.showError(j.responseJSON);
    }).always(function () {
        self.fetch(self._storageIndex == 0 ? self._userID : 0, self.params[self._storageIndex].search, self.params[self._storageIndex].sort, self.params[self._storageIndex].page);
    });
}

Storage.prototype.showDescriptDialog = function (title, files) {
    var ii = 0;
    var self = this;
    if (!(files instanceof Array) || files.length == 0) return;
    var id = files[0].id;
    messageBox($("<div style='width: 100%'>" + title + "<img id='file-src' src='" + files[0].href + "' style='max-width:100%; max-height: 200px;'/><br/><input id='file-desc' style='width: 60%;' type='text' placeholder='Введите краткое описание...' /><br/></div>").append(
    $("<input type='button' value='ОК' />").click(function () {
        self.descript(id, $('#file-desc').val());
        if (++ii >= files.length) {
            msgboxClose();
            return;
        }
        id = files[ii].id;
        $('#file-src').attr('src', files[ii].href)

    })).append($("<input type='button' value='Пропустить' />").click(function () {
        if (++ii >= files.length) {
            msgboxClose();
            return;
        }
        id = files[ii].id;
        $('#file-src').attr('src', files[ii].href)

    })).append($("<input type='button' value='Пропустить все' />").click(function () {
        msgboxClose();
    })), 'center', '40%');
}

Storage.prototype.showError = function (responseJSON) {
    var lm = this._currentStorage.find('.log_message');
    lm.children().html('<p>Хьюстон, у нас проблемы!</p>' + formatError(responseJSON, "message", "details")).parent().removeClass('success fail').addClass('fail').css('height', lm.children().outerHeight(true));
    setTimeout(function () { lm.css('height', 0); }, 5000);
}

Storage.prototype.showSuccess = function (text) {
    var lm = this._currentStorage.find('.log_message');
    lm.children().html(text).parent().removeClass('success fail').addClass('success').css('height', lm.children().outerHeight(true));
    setTimeout(function () { lm.css('height', 0); }, 5000);
}

$('.js-storage').each(function (i) {
    new Storage($(this), i);
})