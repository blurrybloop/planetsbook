//Требует включения utils.js

function PlanetsSelector(parent, objects) {
    var planetID = 0; 
    var lockMove = false;
    var tmr;

    //начало констуирования элемента

    var planets = $("<div class='planets'><a></a></div>"); //контейнер для изображения
    var planet = $("<img class='object planet focused'/>").load(function () { //изображение
        $(this).removeClass('notransition moveback moveforward invisible').addClass('focused');
        lockMove = false; //можете переходить к другой планете

        tmr = setTimeout(function () { //начинаем загрузку спутников
            for (var i = 0; i < objects[planetID].moons.length; i++) {
                var pos = getMoonPosition(i);
                var moon = $("<img src=\"" + objects[planetID].moons[i].image + "\" class=\"object moon invisible\" onload=\"$(this).removeClass('invisible')\">").css(pos);
                planets.append(moon.wrap("<a href='" + objects[planetID].moons[i].href + "'></a>").parent());
                addTip.call(moon, '<h2>' + objects[planetID].moons[i].title + '</h2>' + objects[planetID].moons[i].description, pos);
            }
        }, 500);
    });

    planets.children('a').append(planet);

    //кнопки перехода
    var str = "<div class='button_selector'><div><div>";
    for (var i = 0; i < objects.length; i++)
        str += "<input name='ps_radio' type='radio' id='ps_radio_" + i + "'/><label for='ps_radio_" + i + "'><span class='tip'>" + objects[i].title + "</span></label>"
    var bSelector = $(str + "</div></div></div>");
    var pSelector = $("<div class='wheel_selector'></div>").append(planets).append(bSelector);

    $(parent).append(pSelector); //все готово, добавляем наш элемент к указанному родителю

    //конец констуирования элемента

    var transitionTimeout = 0; //время ожидания для ручного вызова события endTransition
    var d = planet.transitionDuration();
    for (var i = 0; i < d.length; i++)
        if (parseFloat(d[i]) > transitionTimeout) transitionTimeout = parseFloat(d[i]);
    transitionTimeout *= 1000; transitionTimeout += 50;

    //расстановка спутников (максимум 6) вокруг планеты
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
                return { left: (planetLeft - 10) + '%', top: '80%', height: '20%' }
                break;
        }
    }

    function addTip(text, position) {
        var p;
        if ($(this).hasClass('planet'))
            p = { right: '67%', bottom: '40%' };
        else if ($(this).hasClass('moon'))
            p = { right: (100 - parseInt(position.left)) + '%', top: position.top };
        $("<div class='tip'>" + text + "</div>").css(p).insertAfter(this);
    }

    //переход
    function move(dir) {
        if (lockMove) return; //куда это вы собрались?

        planet.siblings('.tip').remove();
        clearTimeout(tmr); //прерываем загрузку спутников
        var cl = dir > 0 ? "moveforward" : "moveback";
        var not_cl = cl == "moveforward" ? "moveback" : "moveforward";
        var callback = function () { //замена планеты
            if (planet.hasClass(cl) || !planet.hasClass(not_cl)) planet.removeClass(cl).addClass(not_cl);
            planet.attr('class', 'object notransition planet ' + cl + ' invisible').attr('src', objects[planetID].image).parent().attr('href', objects[planetID].href);
            addTip.call(planet, '<h2>' + objects[planetID].title + '</h2>' + objects[planetID].description);
        }
        $('.moon').remove();
        lockMove = true; //здесь ответственная часть - загрузка планеты, так что переходы нам ни к чему

        planet.transitionEnd(callback, transitionTimeout);
        planet.removeClass(cl).addClass(not_cl);
    }

    this.moveNext = function () {
        if (lockMove) return;
        var newID = planetID + 1;
        if (newID > objects.length - 1 || newID < 0) return;
        $('#ps_radio_' + newID).click(); //показываем на кнопках перехода текущую позицию
    }

    this.movePrev = function () {
        if (lockMove) return;
        var newID = planetID - 1;
        if (newID > objects.length - 1 || newID < 0) return;
        $('#ps_radio_' + newID).click();
    }

    //сначала покажем первую планету
    bSelector.find("input[name='ps_radio']").change(function () {
        var newID = parseInt(this.id[this.id.length - 1]);
        var dir = planetID - newID;
        planetID = newID;
        move(dir);
    });
    $('#ps_radio_0').click();
}