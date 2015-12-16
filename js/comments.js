//Требует включения utils.js

function Comments(parent, articleID, allowAdd) {
    this._lock = false;
    this._edit = null;
    this._editField = null;
    this._parent = $(parent);
    this._article = articleID;

    this._curPage = 1;
    this._end_page = 0;
    this._lockFetch = false;
    var self = this;

    this.onUpdateComment;

    this._add = $("<article class='comment add'><img src='/img/comment.png'/>Комментировать...</article>").click(function () { self.edit($(this), $(this)); });
    if (allowAdd) this._parent.append(this._add);
    //<img src='/img/playback.png' />Еще...
    this._nextPage = $("<div></div>").click(this.fetch);
    this._parent.append(this._nextPage);
}

Comments.prototype._tools = "<div><img class='comm_bold' src='/img/bold.png' /><div class='tip'>Жирный<br />[b]Пример[/b]</div></div><div><img class='comm_italic' src='/img/italic.png' /><div class='tip'>Курсив<br />[i]Пример[/i]</div></div><div><img class='comm_underline' src='/img/underline.png' /><div class='tip'>Подчеркнутый<br />[u]Пример[/u]</div></div><div><img class='comm_strike' src='/img/strike.png' /><div class='tip'>Зачеркнутый<br />[s]Пример[/s]</div></div><div><img class='comm_sup' src='/img/superscript.png' /><div class='tip'>Верхний индекс<br />[sup]Пример[/sup]</div></div><div><img class='comm_sub' src='/img/subscript.png' /><div class='tip'>Нижний индекс<br />[sub]Пример[/sub]</div></div><div><img class='comm_left_align' src='/img/left_align.png' /><div class='tip'>Выравнивание по левому краю<br />[align=left]Пример[/align]</div></div><div><img class='comm_center_align' src='/img/center_align.png' /><div class='tip'>Выравнивание по центру<br />[align=center]Пример[/align]</div></div><div><img class='comm_right_align' src='/img/right_align.png' /><div class='tip'>Выравнивание по правому краю<br />[align=right]Пример[/align]</div></div><div><img class='comm_justify_align' src='/img/justify_align.png' /><div class='tip'>Выравнивание по ширине<br />[align=justify]Пример[/align]</div></div><div><img class='comm_ul' src='/img/list_bullets.png' /><div class='tip'>Маркированый список<br />[list=*]<br />[*]Один[/*]<br />[*]Два[/*]<br />[*]Три[/*]<br />[/list]</div></div><div><img class='comm_ol' src='/img/list_num.png' /><div class='tip'>Нумерованый список<br />[list=(1|A|a|i|I)]<br />[1]Один[/1]<br />[2]Два[/2]<br />[3]Три[/3]<br />[/list]</div></div><div><img class='comm_url' src='/img/link.png' /><div class='tip'>Ссылка<br />[url]planetsbook.pp.ua[/url]<br />или<br />[url=\"planetsbook.pp.ua\"]Пример[/url]</div></div><div><img class='comm_img' src='/img/picture.png' /><span class='tip'>Рисунок с подписью<br />[figure=(left|center|right|float-left|float-right) width=# height=#]<br />[img]test.png[/img]<br />[figcaption=(left|center|right|justify)]Подпись[/figcaption]<br />[/figure]</span></div><div><img class='comm_help' src='/img/question.png' /><div class='tip'>Справка</div></div>";

Comments.prototype._parse = function (data) {
    if (data == undefined || data["mode"] == undefined) return $("");
    var j = data;
    var s = "";
    var mode = j["mode"];
    if (j.hasOwnProperty("comments")) {
        for (var i = 0; i < j["comments"].length; i++) {
            s += "<article class='comment' data-comment-id='" + j["comments"][i]['id'] + "'>" +
                        "<div>" +
                            "<div class='user_name'>" + j["comments"][i]['login'] + "</div>" +
                            "<div>" +
                                "<a href='/users/profile?id=" + j["comments"][i]['user_id'] + "'>" +
                                "<img src='" + j["comments"][i]['avatar'] + "' style='width: 50%' />" +
                                "</a>" +
                            "</div>" +
                            j["comments"][i]['status'] +
                            "<div class='info'>" +
                                "<div>Репутация: <span style='color:" + (j["comments"][i]['rating'] > 0 ? 'green' : (j["comments"][i]['rating'] == 0 ? 'white' : 'red')) + "'>" + j["comments"][i]['rating'] + "</span></div>" +
                                "<div>Зарегистирован: <time>" + j["comments"][i]['reg_date'] + "</time></div>" +
                                "<div>Комментариев: <span class='comm_cnt'>" + j["comments"][i]['comments_cnt'] + "</span></div>" +
                            "</div>" +
                        "</div>" +
                        "<div>" +
                                "<div class='comm_header'>" + (mode != 1 ?
                                    "<time>" + j["comments"][i]['date_add'] + "</time>" +
                                    "<div class='rate'>" +
                                        (j["comments"][i]['allow_rate'] ? "<div><img class='comm_like' src='/img/like.png' /><div class='tip'>Нравится</div></div>" : "") +
                                        "<span style='color:" + (j["comments"][i]['rate'] > 0 ? 'green' : (j["comments"][i]['rate'] == 0 ? 'white' : 'red')) + "'>" + j["comments"][i]['rate'] + "</span>" +
                                        (j["comments"][i]['allow_rate'] ? "<div><img class='comm_dislike' src='/img/dislike.png' /><div class='tip'>Не нравится</div></div>" : "") +
                                    "</div>" : this._tools) +
                                "</div>" +
                                "<div class='comm_body'>" + (mode == 1 ? "<textarea placeholder='Введите ваш комментарий...'>" : "") + j["comments"][i]['comm_text'] + (mode == 1 ? "</textarea>" : "") +
                                    "<div class='clearfix'></div>" +
                                "</div>" +
                                "<div class='comm_footer maximized " + (mode == 1 ? "nohide" : "") + "'>" +
                                (
                                    mode == 0 ? (
                                        (j["comments"][i]['allow_delete'] ? "<div class='comm_delete'>&nbsp;</div>" : "") +
                                        (j["comments"][i]['allow_edit'] ? "<div class='comm_edit'>&nbsp;</div>" : "")
                                        ) :
                                    (mode == 1 ?
                                        ("<div class='comm_send'>&nbsp;</div><div class='comm_cancel'>&nbsp;</div><div class='comm_apply'>&nbsp;</div>") :
                                        ("<div class='comm_cancelApply'>&nbsp;</div><div class='comm_cancel'>&nbsp;</div><div class='comm_send'>&nbsp;</div>")
                                        )
                                ) +
                                "</div>" +
                        "</div>" +
                    "</article>";
        }
        var el = $(s);
        if (mode == 1)
            this._editField = el.find('textarea');
        return el;
    }
}

Comments.prototype.fetch = function () {
    if (this._lockFetch || this._curPage == this._end_page) return;
    var self = this;
    this._lockFetch = true;
    console.log(this._curPage);
    var j = $.getJSON("/comments/fetch/", { 'article_id': this._article, 'page': this._curPage++, 'page_size': 10 }, function (data) {
        var el = self._parse(data);
        if (el.length == 0) { self._curPage--; self._end_page = self._curPage; }
        if (self._curPage == 1) el = $('<div class="nocontent"><div>Пока нет ни одного комментария</div></div>');
        el.addClass('invisible');
        el.insertBefore(self._nextPage);
        setTimeout(function () { el.removeClass('invisible'); }, 0);
        if (self.onUpdateComment) self.onUpdateComment.call(el);
        self._lockFetch = false;
    }).fail(function () { messageBox(formatError(j.responseJSON, "message")); });
}

Comments.prototype.help = function () {
    var j = $.getJSON("/comments/help/", {}, function (data) {
        if (data["help"] != undefined)
            messageBox(data["help"], 'left', '60%');
    }).fail(function () { messageBox(formatError(j.responseJSON, "message")); });

}


Comments.prototype.performAction = function (comment, target, action, actionName, requestParams, noTransition) {
    var self = this;
    var transitionTimeout = 0; //время ожидания для ручного вызова события endTransition
    var d = comment.transitionDuration();
    for (var i = 0; i < d.length; i++)
        if (parseFloat(d[i]) > transitionTimeout) transitionTimeout = parseFloat(d[i]);
    transitionTimeout *= 1000; transitionTimeout += 50;
    target.addClass('loading');
    target.parent().addClass("nohide");

    var callback = function (data) {
        var el = noTransition ? self._parse(data) : self._parse(data).addClass('invisible');
        comment.transitionEnd(function () {
            target.removeClass('loading');
            var ret = action.call(comment, el);
            if (ret)
                setTimeout(function () { $(ret).each(function () { if (self.onUpdateComment) self.onUpdateComment.call($(this)); $(this).removeClass('invisible'); }) }, 0);
        }, noTransition ? 0 : transitionTimeout);
        comment.addClass('invisible');
    }

    if (actionName && requestParams) {
        var j = $.getJSON('/comments/' + actionName + '/', requestParams, callback).fail(function () { target.removeClass('loading'); self._lock = false; messageBox(formatError(j.responseJSON, "message"), "center"); });
    }
    else callback();
}

Comments.prototype.rate = function (comment, target, val) {
    if (this._lock) return;
    var self = this;
    this.performAction(comment, target, function (data) {
        var d = $(data);
        comment.replaceWith(d);
        self.updateUserInfo(d.children('div:first-child'));
        return d;
    }, 'rate', { 'comment_id': $(comment).attr('data-comment-id'), 'value': val }, true);
}

Comments.prototype.send = function (comment, target) {
    var isAdd = $(comment).hasClass('add');
    var self = this;
    this.performAction(comment, target, function (data) {
        target.parent().removeClass("nohide");
        var d = $(data);
        if (isAdd) { d.insertAfter(comment); comment.replaceWith(self._add) } else comment.replaceWith(d);
        self._lock = false;
        if (isAdd) self.updateUserInfo(d.children('div:first-child'));
        self._parent.find('.nocontent').remove();
        return isAdd ? $([d, self._add]) : d;
    }, isAdd ? "add" : "edit", isAdd ? { 'article_id': this._article, 'text': this._editField.val() } : { 'comment_id': $(comment).attr('data-comment-id'), 'text': this._editField.val() });
}

Comments.prototype.remove = function (comment, target) {
    if (this._lock) return;
    var self = this;
    var u = $('<div>' + $(comment).children('div:first-child').html() + '</div>');
    u.find('.comm_cnt').html(parseInt(u.find('.comm_cnt').text()) - 1);
    this.performAction(comment, target, function (data) {
        comment.remove();
        self.updateUserInfo(u);
    }, "delete", { 'comment_id': $(comment).attr('data-comment-id') });
}

Comments.prototype.apply = function (comment, target) {
    var self = this;
    var i = comment.attr('data-comment-id');
    var isAdd = $(comment).hasClass('add');
    this.performAction(comment, target, function (data) {
        var d = isAdd ? $(data).addClass('add') : $(data).attr('data-comment-id', i);
        self._edit = comment.replaceWith(d);
        return d;
    }, "preview", { 'text': this._editField.val() });
}

Comments.prototype.cancelApply = function (comment, target) {
    var self = this;
    this.performAction(comment, target, function () {
        comment.replaceWith(self._edit);
        return self._edit;
    });
}

Comments.prototype.cancel = function (comment, target) {
    var isAdd = $(comment).hasClass('add');
    var self = this;
    this.performAction(comment, target, function (data) {
        var d = $(isAdd ? self._add : data);
        comment.replaceWith(d);
        self._lock = false;
        return d;
    }, "html", isAdd ? null : { 'comment_id': $(comment).attr('data-comment-id') });
}

Comments.prototype.edit = function (comment, target) {
    var i = comment.attr('data-comment-id');
    var self = this;
    if (this._lock) { messageBox("<p>Вы пытались редактировать два комментария одновременно, поэтому мы заблокировали Ваше действие.</p><p>Отмените или подтвердите редактирование другого комментария.</p>", 'center'); return; }
    this._lock = true;
    var isAdd = $(comment).hasClass('add');
    this.performAction(comment, target, function (data) {
        target.parent().removeClass("nohide");
        var d = isAdd ? $(data).addClass('add') : $(data).attr('data-comment-id', i);
        comment.replaceWith(d);
        self._edit = d;
        return d;
    }, "text", { 'comment_id': isAdd ? 0 : $(comment).attr('data-comment-id') });
}

Comments.prototype.updateUserInfo = function (updatedUserInfo) {
    var userName = $(updatedUserInfo).children('.user_name').text();
    $('.comment > div > .user_name').each(function () {
        if ($(this).text() == userName) {
            $(this).parent().html($(updatedUserInfo).html());
        }
    });
}



$.fn.attachCommentHandlers = function () {
    this.unbind('click');
    this.click(function (e) {
        var t = $(e.target);
        if (t.hasClass('comm_edit') || t.hasClass('add')) $(this).parent().data('commentObject').edit($(this), t);
        else if (t.hasClass('comm_cancel')) $(this).parent().data('commentObject').cancel($(this), t);
        else if (t.hasClass('comm_send')) $(this).parent().data('commentObject').send($(this), t);
        else if (t.hasClass('comm_delete')) $(this).parent().data('commentObject').remove($(this), t);
        else if (t.hasClass('comm_apply')) $(this).parent().data('commentObject').apply($(this), t);
        else if (t.hasClass('comm_cancelApply')) $(this).parent().data('commentObject').cancelApply($(this), t);
        else if (t.hasClass('comm_like')) { $(this).parent().data('commentObject').rate($(this), t, 1); }
        else if (t.hasClass('comm_dislike')) $(this).parent().data('commentObject').rate($(this), t, -1);
        else if (t.hasClass('comm_bold')) $(this).parent().data('commentObject')._editField.makeBold();
        else if (t.hasClass('comm_italic')) $(this).parent().data('commentObject')._editField.makeItalic();
        else if (t.hasClass('comm_underline')) $(this).parent().data('commentObject')._editField.makeUnderline();
        else if (t.hasClass('comm_strike')) $(this).parent().data('commentObject')._editField.makeStrike();
        else if (t.hasClass('comm_sub')) $(this).parent().data('commentObject')._editField.makeSub();
        else if (t.hasClass('comm_sup')) $(this).parent().data('commentObject')._editField.makeSup();
        else if (t.hasClass('comm_left_align')) $(this).parent().data('commentObject')._editField.makeLeft();
        else if (t.hasClass('comm_center_align')) $(this).parent().data('commentObject')._editField.makeCenter();
        else if (t.hasClass('comm_right_align')) $(this).parent().data('commentObject')._editField.makeRight();
        else if (t.hasClass('comm_justify_align')) $(this).parent().data('commentObject')._editField.makeJustify();
        else if (t.hasClass('comm_ul')) $(this).parent().data('commentObject')._editField.makeUL();
        else if (t.hasClass('comm_ol')) $(this).parent().data('commentObject')._editField.makeOL();
        else if (t.hasClass('comm_url')) $(this).parent().data('commentObject')._editField.makeURL();
        else if (t.hasClass('comm_img')) $(this).parent().data('commentObject')._editField.makeFigure();
        else if (t.hasClass('comm_help')) $(this).parent().data('commentObject').help();
    });
}


$('.comments').each(function () {
    var c = new Comments($(this), $(this).data('articleId'), $(this).data('allowAdd'));
    $(this).data('commentObject', c);
    c.onUpdateComment = function () {
        this.attachCommentHandlers();
        iconSize();
    }
});


function iconSize() {
    var f = $('.comm_footer');
    f.each(function () {
        if (parseInt($(this).width()) < 380)
            $(this).removeClass('maximized');
        else
            $(this).addClass('maximized');
    })
}