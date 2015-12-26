<?php

require_once 'StorageController.php';
require_once PATH_INCLUDE . 'GDExtensions.php';

class AvatarController extends StorageController
{
    protected function onFetch($data){     
        return ($s = getimagesize(PATH_ROOT .  $data['href'])) && $s[0] <= 100 && $s[1] <= 100;
    }

    protected function onUpload($file){
        if (!GDExtensions::fitToRect($file, 100, 100, $file))
            throw new ControllerException('Не удалось изменить размер изображения', GDExtensions::lastError());
        return TRUE;
    }
}
