function Fullscreen(images) {

    this.images = images;
    this.onStart = function() {};
    this.onEnd = function () { };
    this.index = 0;
}

Fullscreen.prototype.show = function (index) {
    this.index = index;
    var self = this;
    this.image = $("<img src='" + this.images[index].src + "'>");
    this.content = $("<div class='invisible fullscreen'>" +
                    "<div class='head'>" +
                        "<div>" + (self.images[self.index].title ? self.images[self.index].title : "") + "</div><img class='close' src='/img/cross.png'/>" +
                    "</div>"+
                    "<div class='foot'>" + 
                        "<time class='date'>" + (self.images[self.index].date ? self.images[self.index].date : "") + "</time>" + 
			"<div class='description'>" + (self.images[self.index].description ? self.images[self.index].description : "") + "</div>" + 
                    "</div>" + 
                    "<div class='arrow-left'></div>" + 
                    "<div class='arrow-right'></div>" + 
                   "</div>"
                   ).append(this.image).click(function (e) {
                       if (e.target.className == 'arrow-left') self.movePrev();
                       else if (e.target.className == 'arrow-right') self.moveNext();
                       else if (e.target.className == 'close') self.close();
                       else if (e.target.className == 'fullscreen') self.close();
                   });

    if ($('body > .fullscreen').length) {
        $('body > .fullscreen').replaceWith(this.content);
    }
    else {
        $('body').append(this.content);
    }
    setTimeout(function () { self.content.removeClass('invisible') }, 0);

    this.transitionTimeout = 0;
    var d = this.content.children('img').transitionDuration();
    for (var i = 0; i < d.length; i++)
        if (parseFloat(d[i]) > this.transitionTimeout) this.transitionTimeout = parseFloat(d[i]);
    this.transitionTimeout *= 1000; this.transitionTimeout += 50;

}

Fullscreen.prototype.moveNext = function(){
    if (this.index >= this.images.length - 1) {
        if (this.onEnd instanceof Function) this.onEnd();
        return;
    }

    this.index++;
    var self = this;

    this.image.transitionEnd(function () {
        self.content.children('.head').children('div').html(self.images[self.index].title ? self.images[self.index].title : "");
	self.content.children('.foot').children('.date').html(self.images[self.index].date ? self.images[self.index].date : "");
	self.content.children('.foot').children('.description').html(self.images[self.index].description ? self.images[self.index].description : "")
        self.image.attr('class', 'invisible notransition moved-right').attr('src', self.images[self.index].src);
        setTimeout(function () {
            self.image.removeClass('invisible notransition moved-right');
        }, 100);
    }, this.transitionTimeout);

    this.image.addClass('moved-left');
    
}

Fullscreen.prototype.movePrev = function () {
    if (this.index <= 0) {
        if (this.onStart instanceof Function) this.onStart();
        return;
    }

    this.index--;

    var self = this;

    this.image.transitionEnd(function () {
        self.content.children('.head').children('div').html(self.images[self.index].title ? self.images[self.index].title : "");
	self.content.children('.foot').children('.date').html(self.images[self.index].date ? self.images[self.index].date : "");
	self.content.children('.foot').children('.description').html(self.images[self.index].description ? self.images[self.index].description : "")
        self.image.attr('class', 'invisible notransition moved-left loading').attr('src', self.images[self.index].src).one('load', function () {
            self.image.removeClass('loading');
        });
        setTimeout(function () {
            self.image.removeClass('invisible notransition moved-left');
        }, 100);
    }, this.transitionTimeout);

    this.image.addClass('moved-right');
}

Fullscreen.prototype.close = function () {
    var self = this;
    this.image.transitionEnd(function () {
        self.content.remove();
    }, 300);

    this.content.addClass('invisible');
}



