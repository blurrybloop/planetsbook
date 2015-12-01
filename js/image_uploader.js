function ImageUploader(parent, allowMultiple, replace, pulseFrequency) {
    var i = 0;
    while ($("form[name=image_uploader_form" + i).length) i++;

    var _uploadForm = $("<form style='display: none;' name='image_uploader_form" + i + "' target='image_uploader_frame" + i + "' method='post' enctype='multipart/form-data'>" +
                            "<input type='hidden' name='MAX_FILE_SIZE' value=2000000 />" +
                            "<input type='file' name='images[]' id='image_uploader_images" + i + "' " + (allowMultiple ? 'multiple' : '') + " />" +
                            "<input type='hidden' name='image_path' id='image_path" + i + "'/>" +
							"<input type='hidden' name='page_id' id='page_id" + i + "'/>" +
                            "<input type='hidden' name='image_replace' id='image_replace" + i + "' value='" + (replace ? 1 : 0) + "'/>" +
                        "</form>");

    var _uploadFrame = $("<iframe style='display: none' id='image_uploader_frame" + i + "' name='image_uploader_frame" + i + "'></iframe>");

    var _parent = $(parent);
    var _uploaded = [];
    var tmr;

    _parent.append(_uploadFrame);
    _parent.append(_uploadForm);

    var pid;
    var self = this;
    
    this.onStartUploading = null;
    this.onUploaded = null;
    this.onStartDeleting = null;
    this.onDeleted = null;
    this.onError = null;

    $('#image_uploader_images' + i).change(function () {
        var newUploaded = [];
        if (replace) _uploaded = [];
        if (self.onStartUploading) self.onStartUploading.call(self);
        _uploadFrame.one('load', function () {
            var tmp_pid = $(this).contents().find('.page_id').html();

            var err = $(this).contents().find('.error');
            if (tmp_pid) {
                pid = tmp_pid;
                tmr = setInterval(function () {$.post('/pulse/', { 'page_id': pid });}, pulseFrequency);
            }
            $(this).contents().find('.path').each(function () {
                newUploaded.push($(this).html());
            });
            _uploaded.push(newUploaded);
            if (self.onUploaded) self.onUploaded.call(self, newUploaded);
            if (self.onError && err.length) self.onError.call(self, err.html());

        });

        $('#page_id' + i).attr('value', pid == undefined ? 0 : pid);

        _uploadForm.attr('action', '/image/upload/');
        setTimeout(function () { _uploadForm.submit(); }, 0);
    });


    this.upload = function(){
        $('#image_uploader_images' + i).click();
    }

    this.delete = function (path, args) {
        var ii = path.lastIndexOf('?');
        if (ii != -1) path = path.substr(0, ii);
        if (self.onStartDeleting) self.onStartDeleting.call(self);
        _uploadFrame.one('load', function () {
            var err = $(this).contents().find('.error'); 
			
            if (!replace) _uploaded = _uploaded.filter(function (val) {
                return val != path;
            });
            //if (self.onError && err.length) self.onError.call(self, err.html());
            /*else*/ if (/*err.length == 0 && */self.onDeleted) self.onDeleted.call(self, args);
        });

        $('#image_path' + i).attr('value', path);
        _uploadForm.attr('action', '/image/delete/');
        setTimeout(function () {_uploadForm.submit(); }, 0);
    }

    this.getUploaded = function(){
        return _uploaded;
    }

    this.reset = function () {
        _uploaded = [];
        if (tmr != undefined) clearInterval(tmr);
        $('#page_id' + i).attr('value', 0);
    }

}