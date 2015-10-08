$(document).ready(function () { $('#9').click(); addTip(); registerClick(); });

var objects = new Array("sun.png", "mercury.png", "venus.png", "earth.png", "mars.png", "jupiter.png", "saturn.png", "uranus.png", "neptune.png", "pluto.png");
var desc = new Array("Солнце", "Меркурий", "Венера", "Земля", "Марс", "Юпитер", "Сатурн", "Уран", "Нептун", "Плутон");
var moons = new Array(null, null, null, new Array("moon.png"), new Array("phobos_deimos.png"), new Array("io.png", "europa.png", "ganymede.png", "callisto.png"), new Array("titan.png"), null, new Array("triton.png"), new Array("charon.png"));
var moonDesc = new Array(null, null, null, new Array("Луна"), new Array("Фобос и Деймос"), new Array("Ио", "Европа", "Ганимед", "Каллисто"), new Array("Титан"), null, new Array("Тритон"), new Array("Харон"));
var tmr;
var planetID = 0;
var lockWheel = false;
var tooltips = new Array();
for (var i = 0; i < 7; i++) tooltips[i] = $("<div class='tip'><div></div></div>");

$.fn.percentLeft = function (parentSelector) {
    return (parseInt($(this).css('left')) / parseInt($(parentSelector).css('width'))) * 100;
}

$.fn.percentBottom = function (parentSelector) {
    return ((parseInt($(this).css('bottom')) / parseInt($(parentSelector).css('height'))) * 100);
}

function getMoonPosition(index) {
    var planetLeft = $('.planet').percentLeft('.planets');
    var planetRight = ((parseInt($('.planet').css('left')) + parseInt($('.planet').css('width'))) / parseInt($('.planets').css('width'))) * 100;
    switch (index) {
        case 0:
            return { left: (planetRight + 10) + '%', top: 0, height: '20%' }
            break;
        case 1:
            return { left: (planetRight + 20) + '%', top: '50%', height: '20%' }
            break;
        case 2:
            return { left: (planetRight + 10) + '%', top: '80%', height: '20%' }
        case 3:
            return { left: (planetLeft - 10) + '%', top: 0, height: '20%' }
            break;
        case 4:
            return { left: (planetLeft - 20) + '%', top: '50%', height: '20%' }
            break;
        case 5:
            cssSet = { left: (planetLeft - 10) + '%', top: '80%', height: '20%' }
    }
}

function endTransitionEvent() {
    var el = document.createElement('fakeelement');
    var transitions = { 'transition': 'transitionend', 'MSTransition': 'msTransitionEnd', 'MozTransition': 'transitionend', 'WebkitTransition': 'webkitTransitionEnd' }
    for (var t in transitions)
        if (el.style[t] !== undefined)
            return transitions[t];
    return null;
}

function addTip() {
    tooltips[0].css({ right: '70%', bottom: '50%' }).insertAfter('.planet').children().html(desc[planetID]);
}

var move = function (dir) {
    $('.planets .tip').remove();
    clearTimeout(tmr);
    var cl = dir > 0 ? "moveforward" : "moveback";
    var not_cl = cl == "moveforward" ? "moveback" : "moveforward";
    var callback = function () {
        $('.planet').replaceWith($("<img src=\"img/" + objects[planetID] + "\" class = \"object planet " + cl + " invisible\"/>").load(function () {
            $(this).removeClass(cl + ' invisible').addClass('focused');
            addTip();
            lockWheel = false;
            tmr = setTimeout(function () {
                for (var i = 0; moons[planetID] != null && i < moons[planetID].length; i++) {
                    var pos = getMoonPosition(i);
                    var moon = $("<img src=\"img/" + moons[planetID][i] + "\" class=\"object moon invisible\" onload=\"$(this).removeClass('invisible')\">").css(pos).insertAfter('.planets .tip');
                    tooltips[i + 1].css({ right: (100 - parseInt(pos.left)) + '%', top: pos.top }).insertAfter(moon).children().html(moonDesc[planetID][i]);
                }
            }, 500);
        }));
    }
    $('.moon').remove();
    if (endTransitionEvent() == null) {
        $('.planet').removeClass(cl).addClass(not_cl);
        callback();
    }
    else {
        $(this).one(endTransitionEvent(), callback);
        lockWheel = true;
        $('.planet').removeClass(cl).addClass(not_cl);

    }
}

var onwheel = function (e) {
    if (e.originalEvent && !lockWheel) {
        $(".wheel_selector>img").addClass('invisible');
        var delta = e.originalEvent.deltaY || e.originalEvent.detail || e.originalEvent.wheelDelta;
        var newID = planetID + Math.sign(delta);
        if (newID > 9 || newID < 0) return false;
        $('#' + (9 - newID)).click();
        return false;
    }

};

if ('onwheel' in document)
    $(document).on('wheel', '.planets', onwheel);
else if ('onmousewheel' in document)
    $(document).on('mousewheel', '.planets', onwheel);
else
    $(document).on('MozMousePixelScroll', '.planets', onwheel);

function registerClick() {
    $('input[type="radio"]').change(function () {
        var newID = 9 - this.id;
        if (newID >= planetID) {
            planetID = newID;
            move(-1);
        }
        else {
            planetID = newID;
            move(1);
        }
    });
}

setTimeout(function () { $(".wheel_selector>img").addClass('invisible'); }, 5000);