<?php

class GDExtensions{
    static private $error;

    static public function lastError(){
        return self::$error;
    }

    static public function fitToRect($source, $width, $height, $destination, $autoExtension = FALSE) {

        if ($autoExtension)
            $pinfo = pathinfo($destination);

        if (!$sizes = @getimagesize($source)) {
            self::$error = 'Файл не является допустимым форматом изображения.';
            return FALSE;
        }
        list($type, $subtype) = explode('/', $sizes['mime']);

        if ($type != 'image') {
            self::$error = 'Файл не является допустимым форматом изображения.';
            return FALSE;
        }

        switch($subtype){
            case 'gif':
                $source_img = @imagecreatefromgif($source);
                break;
            case 'jpeg':
                $source_img = @imagecreatefromjpeg($source);
                break;
            case 'png':
                $source_img = @imagecreatefrompng($source);
                break;
        }

        if ($source_img === FALSE) {
            self::$error = 'Не удалось создать дескриптор изображения-источника.';
            return FALSE;
        }

        @imagealphablending($source_img, true);

        $sizes['ratio'] = $sizes[0] / $sizes[1];
        if ($sizes[0] <= $width && $sizes[1] <= $height) {
            $width = $sizes[0];
            $height = $sizes[1];
        }
        if ($sizes[0] - $width > $sizes[1] - $height) $height = $width / $sizes['ratio'];
        else $width = $height * $sizes['ratio'];
        if (($thumb = @imagecreatetruecolor($width, $height)) === FALSE) {
            self::$error = 'Не удалось создать дескриптор нового изображения.';
            return FALSE;
        }

        @imagealphablending($thumb, false);
        @imagesavealpha($thumb, true);

        if (@imagecopyresampled($thumb, $source_img, 0, 0, 0, 0, $width, $height, $sizes[0], $sizes[1]) === FALSE) {
            self::$error = 'Не удалось выполнить изменение размера изображения.';
            return FALSE;
        }

        $savefile = $autoExtension ? ($pinfo['dirname'] . '/' . $pinfo['filename'] . '.png') : $destination;

        if (@imagepng($thumb, $savefile) === FALSE) {
            self::$error = 'Не удалось сохранить изображение.';
            return FALSE;
        }

        @imagedestroy($thumb);
        @imagedestroy($source_img);

        return $savefile;
    }
}

