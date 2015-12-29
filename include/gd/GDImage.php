<?php 

require_once 'GDHelpers.php';

/**
 * Исключение GD2
 */
class GDException extends Exception{
    /**
     * Возвращает новый экземпляр исключения
     * @param string $message Сообщение 
     * @param int $code Код ошибки
     * @param Exception $previous Предыдущее исключение
     */
    function __construct($message, $code = 0, Exception $previous = NULL){
        parent::__construct($message, $code = 0, $previous);
    }
}

/**
 * Представляет изображение GD2
 */
class GDImage{
    protected $handle = NULL;
    protected $source = NULL;
    protected $format = NULL;

    /** Растяжение изображения по размерам */
    const RESIZE_STRETCH = 0;
    /** Обрезка изображения */
    const RESIZE_CROP = 1;
    /** Подгонка размеров с сохранением пропорций */
    const RESIZE_FIT = 2;

    /**
     * Возвращает новый экземпляр GDImage
     * @param string $source Путь к исходному файлу. NULL - создание пустого изображения
     * @param Size $s Размер изображения. NULL - использовать размеры исходного файла
     * @param int $resizeMode Режим изменения размера. Используется, если $s не совпадает с размерами исходного изображения
     * @throws GDException 
     */
    function __construct($source = NULL, Size $s = NULL, $resizeMode = self::RESIZE_FIT){
        $s !== NULL or $s = new Size(100,100);

        if (!empty($source)){
            $this->source = (string)$source;

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

            if ($this->handle === FALSE){
                $this->handle = NULL;
                throw new GDException('Невозможно инициализировать GD поток');
            }

            $this->format = (string)$subtype;
            if ($s !== NULL) $this->size($s, $resizeMode);
        }
        else {
            if (($this->handle = @imagecreatetruecolor($s->width, $s->height)) === FALSE){
                $this->handle = NULL;
                throw new GDException('Невозможно инициализировать GD поток');
            }

            imagefill($this->handle, 0, 0, imagecolorresolvealpha($this->handle,0,0,0,127));
        }
    }

    /** Осовобождает ресурсы, связанные с экземпляром */
    function __destruct(){
        if (is_resource($this->handle))
            @imagedestroy($this->handle);
    }

    /**
     * Возвращает ресурс изображения, используемый экземпляром
     * @return null|resource
     */
    function getHandle(){
        return $this->handle;
    }

    /**
     * Рисует содержимое экземпляра GDGraphicalObject
     * @param GDGraphicalObject $graphicalObject Графический объект
     * @return GDImage
     */
    function drawObject(GDGraphicalObject $graphicalObject){
        $graphicalObject->draw($this->handle);
        return $this;
    }

    /**
     * Возвращает и/или устанавливает размеры изображения
     * @param Size $newSize Новые размеры изображения. NULL - не изменять размеры
     * @param int $resizeMode Режим изменения размера
     * @throws GDException 
     * @return Size
     */
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

    /**
     * Растягивает изображение
     * @param Size $s Новые размеры изображения
     * @throws GDException 
     * @return GDImage
     */
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


    /**
     * Обрезает изображение
     * @param Boundary $s Границы области обрезки
     * @throws GDException 
     * @return GDImage
     */
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

    /**
     * Подгоняет изображение с сохранением пропорций
     * @param Size $s Новые размеры изображения
     * @return GDImage
     */
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

    /**
     * Сохраняет изображение в исходный файл
     * @param int $quality Качество изображения (от 0 до 100)
     * @return GDImage
     */
    function save($quality = 100){
        return $this->saveTo($this->format, $this->source, $quality);
    }

    /**
     * Сохраняет изображение
     * @param string $format Формат изображения
     * @param string $destination Путь к сохраняемому файлу. NULL - вывод на экран
     * @param int $quality Качество изображения
     * @throws GDException 
     * @return GDImage
     */
    function saveTo($format = NULL, $destination = NULL, $quality = 100){
        if ($destination == NULL)
            header("Content-type: image/$format");

        switch(strtolower($format)){
            case 'gif':
                $ret = @imagegif($this->handle, $destination);
                break;
            case 'jpeg':
                $ret = @imagejpeg($this->handle, $destination, $quality);
                break;
            case 'png':
                @imagesavealpha($this->handle, true);
                $ret = @imagepng($this->handle, $destination, (int)((100 - $quality)/11));
                break;
            default:
                throw new GDException('Недопустимый формат.');
        }
        if ($ret === FALSE)
            throw new GDException('Не удалось сохранить изображение.');

        return $this;
    }
}