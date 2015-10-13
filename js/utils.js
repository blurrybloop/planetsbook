
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
}

$.fn.transitionDuration = function(){
    var d = this.css('transition-duration');
    if (!d) return ['0s'];
    return d.split(/,\s?/);
}