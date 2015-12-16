
//Воспомогательные функции и расширяющие методы jQuery
//Подключить при использовании любого скрипта из папки /js

$.fn.percentLeft = function (parent) {
    return (parseInt($(this).css('left')) / parseInt($(parent).width())) * 100;
}

$.fn.percentBottom = function (parent) {
    return ((parseInt($(this).css('bottom')) / parseInt($(parent).height())) * 100);
}

$.fn.percentRight = function (parent) {
    return ((parseInt($(this).css('right')) / parseInt($(parent).width())) * 100);
}

$.fn.percentTop = function (parent) {
    return ((parseInt($(this).css('top')) / parseInt($(parent).height())) * 100);
}

//правильное название события для конкретного браузера 
function endTransitionEvent() {
    var el = document.createElement('fakeelement');
    var transitions = { 'transition': 'transitionend', 'MSTransition': 'msTransitionEnd', 'MozTransition': 'transitionend', 'WebkitTransition': 'webkitTransitionEnd' }
    for (var t in transitions)
        if (el.style[t] !== undefined)
            return transitions[t];
    return null;
}

$.fn.transitionEnd = function (callback, timeout) {
    var tEvent = endTransitionEvent();
    this.one(tEvent, callback || $.noop); //установка одноразового обработчика

    if (timeout != undefined) {
        var called = false;
        $(this).one(tEvent, function () { called = true; });
        setTimeout(function (e) {
            if (!called) { //что то пошло не так, событие не произошло
                if (tEvent) e.trigger(tEvent); //событие поддерживется, вызовем его вручную
                else callback(); //событие не поддерживается, вызовем обработчик вручную
            }
        }, timeout, this);
    }
    return this;
}

$.fn.transitionDuration = function(){
    var d = this.css('transition-duration');
    if (!d) return ['0s'];
    return d.split(/,\s?/);
}

$.fn.wrapSelected = function (openTag, closeTag) {
    var textarea = this;
    var value = textarea.val();
    var start = textarea[0].selectionStart;
    var end = textarea[0].selectionEnd;
    textarea.val(value.substr(0, start) + openTag + value.substring(start, end) + closeTag + value.substring(end, value.length));
    textarea[0].selectionStart = start;
    textarea[0].selectionEnd = end;
}

$.fn.makeBold = function () { this.first().wrapSelected('[b]', '[/b]'); }
$.fn.makeItalic = function () { this.first().wrapSelected('[i]', '[/i]'); }
$.fn.makeUnderline = function () { this.first().wrapSelected('[u]', '[/u]'); }
$.fn.makeStrike = function () { this.first().wrapSelected('[s]', '[/s]'); }
$.fn.makeSub = function () { this.first().wrapSelected('[sub]', '[/sub]'); }
$.fn.makeSup = function () { this.first().wrapSelected('[sup]', '[/sup]'); }
$.fn.makeUL = function () { this.first().wrapSelected('\r\n[list=*]\r\n[*]', '[/*]\r\n[/list]\r\n'); }
$.fn.makeOL = function () { this.first().wrapSelected('\r\n[list=1]\r\n[1]', '[/1]\r\n[/list]\r\n'); }
$.fn.makeURL = function () { this.first().wrapSelected('[url]', '[/url]'); }
$.fn.makeFigure = function () { this.first().wrapSelected('\r\n[figure]\r\n[img][/img]\r\n[figcaption]', '[/figcaption]\r\n[/figure]\r\n'); }
$.fn.makeLeft = function () { this.first().wrapSelected('\r\n[align=left]', '[/align]\r\n'); }
$.fn.makeCenter = function () { this.first().wrapSelected('\r\n[align=center]', '[/align]\r\n'); }
$.fn.makeRight = function () { this.first().wrapSelected('\r\n[align=right]', '[/align]\r\n'); }
$.fn.makeJustify = function () { this.first().wrapSelected('\r\n[align=justify]', '[/align]\r\n'); }

function formatError(obj) {
alert(obj);
    var formatted = "";
    if (obj && obj.error) {
        if (arguments.length == 1) {
            for (var prop in obj.error) {
                if (obj.error.hasOwnProperty(prop))
                    formatted += "<div class='" + prop + "'>" + obj.error[prop] + "</div>";
            }
        }
        else {
            for (var i = 1; i < arguments.length; i++) {
                if (obj.error[arguments[i]]) formatted += "<div class='" + arguments[i] + "'>" + obj.error[arguments[i]] + "</div>";
            }
        }
    }
    if (!formatted.length) formatted = "Неизвестная ошибка";
    return formatted;
}


function ArrayDictonary() {
    this.keys = [];
    this.values = [];
}

ArrayDictonary.prototype.set = function (key, val) {
    var pos = $.inArray(key, this.keys);
    if (~pos) {
        if (val != undefined) this.values[pos] = val;
        else this.values.splice(pos, 1);
    }
    else {
        if (val != undefined) {
            this.keys.push(key);
            this.values.push(val);
        }
    }
}

ArrayDictonary.prototype.get = function (key) {
    var pos = $.inArray(key, this.keys);
    if (~pos) return this.values[pos];
    else return undefined;
}

ArrayDictonary.prototype.keyAt = function (index) { return this.keys[index]; }
ArrayDictonary.prototype.valueAt = function (index) { return this.values[index]; }

ArrayDictonary.prototype.remove = function (key) {
    var pos = $.inArray(key, this.keys);
    if (~pos) {
        this.keys.splice(pos, 1);
        this.values.splice(pos, 1);
    }
}

ArrayDictonary.prototype.length = function () { return this.keys.length; }

ArrayDictonary.prototype.clear = function () {
    this.keys.splice(0, this.keys.length);
    this.values.splice(0, this.values.length);
}
