<?php

require_once 'StorageController.php';
require_once PATH_INCLUDE . 'GDExtensions.php';

class SmallImageController extends StorageController
{
    protected function onFetch($data){     
        return ($s = getimagesize(PATH_ROOT .  $data['href'])) && $s[0] <= 25 && $s[1] <= 25;
    }

    protected function onUpload($file){
        if (!GDExtensions::fitToRect($file, 25, 25, $file))
            throw new ControllerException('Не удалось изменить размер изображения', GDExtensions::lastError());
        return TRUE;
    }
}
