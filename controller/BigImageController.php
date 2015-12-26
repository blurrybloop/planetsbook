<?php

require_once 'StorageController.php';
require_once PATH_INCLUDE . 'GDExtensions.php';

class BigImageController extends StorageController
{
    protected function onFetch($data){     
        return ($s = getimagesize(PATH_ROOT .  $data['href'])) && $s[0] <= 500 && $s[1] <= 500;
    }

    protected function onUpload($file){
        if (!GDExtensions::fitToRect($file, 500, 500, $file))
            throw new ControllerException('Не удалось изменить размер изображения', GDExtensions::lastError());
        return TRUE;
    }
}
