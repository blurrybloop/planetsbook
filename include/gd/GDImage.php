<?php 

require_once 'GDHelpers.php';

class GDException extends Exception{
    function __construct($message, $code, Exception $previous = NULL){
        parent::__construct($message, $code = 0, $previous);
    }
}

class GDImage{
    protected $handle = null;
    protected $source = null;
    protected $format = null;

    const RESIZE_STRETCH = 0;
    const RESIZE_CROP = 1;
    const RESIZE_FIT = 2;

    function __construct($source = NULL, Size $s = NULL, $resizeMode = self::RESIZE_FIT){
        $s !== NULL or $s = new Size(100,100);

        if (!empty($source)){
            $this->source = $source;

            if (!$sizes = @getimagesize($source))
                throw new GDException('Файл не является допустимым форматом изображения.');

            list($type, $subtype) = explode('/', $sizes['mime']);

            if ($type != 'image')
                throw new GDException('Файл не является допустимым форматом изображения.');

            switch($subtype){
                case 'gif':
                    $this->handle = @imagecreatefromgif($source);
                    break;
                case 'jpeg':
                    $this->handle = @imagecreatefromjpeg($source);
                    break;
                case 'png':
                    $this->handle = @imagecreatefrompng($source);
                    break;
                default:
                    throw new GDException('Файл не является допустимым форматом изображения.');
            }

            if ($this->handle === FALSE)
                throw new GDException('Невозможно инициализировать GD поток');

            $this->format = $subtype;
            if ($s !== NULL) $this->size($s, $resizeMode);
        }
        else {
            if (($this->handle = @imagecreatetruecolor($s->width, $s->height)) === FALSE)
                throw new GDException('Невозможно инициализировать GD поток');

            imagefill($this->handle, 0, 0, imagecolorresolvealpha($this->handle,0,0,0,127));
        }
    }

    function __destruct(){
        if (is_resource($this->handle))
            @imagedestroy($this->handle);
    }

    function getHandle(){
        return $this->handle;
    }

    function drawObject(GDGraphicalObject $graphicalObject){
        $graphicalObject->draw($this->handle);
        return $this;
    }

    function size(Size $newSize = NULL, $resizeMode = self::RESIZE_FIT){
        if ($newSize !== NULL){
            if ($resizeMode == self::RESIZE_STRETCH) $this->stretch($newSize);
            else if ($resizeMode == self::RESIZE_CROP) $this->crop(new Boundary(0,0, $newSize->width, $newSize->height));
            else if ($resizeMode == self::RESIZE_FIT) $this->fit($newSize);
        }

        if (($w = @imagesx($this->handle)) === FALSE || ($h = @imagesy($this->handle)) === FALSE)
            throw new GDException('Не удалось получить размеры изображения.');

        return new Size($w, $h);
    }

    function stretch(Size $s){
        $ts = $this->size();
        if (($r = @imagecreatetruecolor($s->width, $s->height)) === FALSE)
            throw new GDException('Невозможно инициализировать GD поток');

        @imagealphablending($r, false);

        if (!@imagecopyresampled($r, $this->handle, 0,0,0,0,$s->width, $s->height, $ts->width, $ts->height)){
            @imagedestroy($r);
            throw new GDException("Не удалось растянуть изображение.");
        }

        @imagealphablending($r, true);
        @imagedestroy($this->handle);
        $this->handle = $r;
        return $this;
    }

    function crop(Boundary $s){
        if (($r = @imagecreatetruecolor($s->width(), $s->height())) === FALSE)
            throw new GDException('Невозможно инициализировать GD поток');

        @imagealphablending($r, false);

        if (!@imagecopy($r, $this->handle, 0,0,$s->left(),$s->top(), $s->width(), $s->height())){
            @imagedestroy($r);
            throw new GDException("Не удалось растянуть изображение.");
        }

        @imagealphablending($r, true);
        @imagedestroy($this->handle);
        $this->handle = $r;
        return $this;
    }

    function fit(Size $s){
        $ts = $this->size();

        $ratio = $ts->width / $ts->height;
        if ($ts->width <= $s->width && $ts->height <= $s->height) {
            $s->width = $ts->width;
            $s->height = $ts->height;
        }

        if ($s->height * $ratio > $s->width) $s->height = $s->width / $ratio;
        else $s->width = $s->height * $ratio;

        return $this->stretch($s);

        
    }

    function save($quality = 100){
        return $this->saveTo($this->format, $this->source, $quality);
    }

    function saveTo($format = NULL, $destination = NULL, $quality = 100){
        if ($destination == NULL)
            header("Content-type: image/$format");

        switch(strtolower($format)){
            case 'gif':
                $this->handle = @imagegif($this->handle, $destination);
                break;
            case 'jpeg':
                $this->handle = @imagejpeg($this->handle, $destination, $quality);
                break;
            case 'png':
                @imagesavealpha($this->handle, true);
                $this->handle = @imagepng($this->handle, $destination, (int)((100 - $quality)/11));
                break;
            default:
                throw new GDException('Недопустимый формат.');
        }
        if ($this->handle === FALSE)
            throw new GDException('Не удалось сохранить изображение.');

        return $this;
    }
}