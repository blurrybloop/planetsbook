//Требует включения utils.js

function Comments(parent, articleID, allowAdd) {
    var lock = false;
    var edit = null;
    var _parent = $(parent);
    var _article = articleID;

    var curPage = 1;
    var lockFetch = false;
    var self = this;

    this.onUpdateComment;

    this.fetch = function () {
            if (lockFetch) return;
            lockFetch = true;
            $.post("/comments/fetch/", { args: [_article, curPage++, 10] }, function (data) {
                var el = $(data);
                el.addClass('invisible');
                el.insertBefore(nextPage);
                setTimeout(function () { el.removeClass('invisible'); }, 0);
                if (self.onUpdateComment) self.onUpdateComment.call(el);
                lockFetch = false;
            });
    }

    var add = $("<article class='comment add'>Добавить комментарий</article>").click(function () { self.edit($(this), $(this)); });
    _parent.append(add);
    var nextPage = $("<div>Еще...</div>").click(this.fetch);
    _parent.append(nextPage);

    this.performAction = function (comment, target, action, actionName, requestParams, noTransition) {
        var transitionTimeout = 0; //время ожидания для ручного вызова события endTransition
        var d = comment.transitionDuration();
        for (var i = 0; i < d.length; i++)
            if (parseFloat(d[i]) > transitionTimeout) transitionTimeout = parseFloat(d[i]);
        transitionTimeout *= 1000; transitionTimeout += 50;
        target.addClass('loading');
        target.parent().addClass("nohide");

        var callback = function (data) {
            var el = noTransition ? $(data) : $(data).addClass('invisible');
            comment.transitionEnd(function () {
                target.removeClass('loading');
                var ret = action.call(comment, el);
                if (ret)
                    setTimeout(function () { $(ret).each(function () { if (self.onUpdateComment) self.onUpdateComment.call($(this)); $(this).removeClass('invisible'); }) }, 0);
            }, noTransition ? 0 : transitionTimeout);
            comment.addClass('invisible');
        }

        if (actionName && requestParams) {
            var j = $.post('/comments/' + actionName + '/', requestParams, callback).fail(function () { target.removeClass('loading'); lock = false; messageBox(j.responseText, 'center'); });
        }
        else callback();
    }

    this.rate = function (comment, target, val) {
        this.performAction(comment, target, function (data) {
            var d = $(data);
            comment.replaceWith(d);
            return d;
        }, val > 0 ? 'like' : 'dislike', { args: [$(comment).attr('id').replace('comm', '')] }, true);
    }

    this.send = function (comment, target) {
        var isAdd = $(comment).hasClass('add');
        this.performAction(comment, target, function (data) {
            target.parent().removeClass("nohide");
            var d = $(data);
            if (isAdd) {d.insertAfter(comment); comment.replaceWith(add)} else comment.replaceWith(d);
            lock = false;
            return isAdd ? $([d, add]) : d;
        }, isAdd ? "add" : "edit", { args: isAdd ? [_article, edit.find('#edit_field').val()] : [$(comment).attr('id').replace('comm', ''), edit.find('#edit_field').val()] });
    }

    this.delete = function (comment, target) {
        this.performAction(comment, target, function (data) {
            comment.remove();
        }, "delete", { args: [$(comment).attr('id').replace('comm', '')] });
    }

    this.apply = function (comment, target) {
        var i = comment.attr('id');
        var isAdd = $(comment).hasClass('add');
        this.performAction(comment, target, function (data) {
            var d = isAdd ? $(data).addClass('add') : $(data).attr('id', i);
            edit = comment.replaceWith(d);
            lock = false;
            return d;
        }, "preview", { args: [$(edit_field).val()] });
    }

    this.cancelApply = function (comment, target) {
        this.performAction(comment, target, function () {
                comment.replaceWith(edit);
                return edit;
            });
        }

    this.cancel = function (comment, target) {
        var isAdd = $(comment).hasClass('add');
        this.performAction(comment, target, function (data) {
                var d = $(isAdd ? add : data);
                comment.replaceWith(d);
                lock = false;
                return d;
        }, "html", isAdd ? null : { args: [$(comment).attr('id').replace('comm', '')] });
        }

    this.edit = function (comment, target) {
            var i = comment.attr('id');
            if (lock) { messageBox("<p>Вы пытались редактировать два комментария одновременно, поэтому мы заблокировали Ваше действие.</p><p>Отмените или подтвердите редактирование другого комментария.</p>", 'center'); return; }
            lock = true;
            var isAdd = $(comment).hasClass('add');
            this.performAction(comment, target, function (data) {
                target.parent().removeClass("nohide");
                var d = isAdd ? $(data).addClass('add') : $(data).attr('id', i);
                comment.replaceWith(d);
                edit = d;
                return d;
            }, "text", { args: isAdd ? [0] : [$(comment).attr('id').replace('comm', '')] });
        }

        
}