<?php

require_once 'StorageController.php';
require_once PATH_INCLUDE . 'gd/GDImage.php';

class SmallImageController extends StorageController
{
    protected function onFetch($data){     
        return ($s = getimagesize(PATH_ROOT .  $data['href'])) && $s[0] <= 25 && $s[1] <= 25;
    }

    protected function onUpload($file){
        try {
            (new GDImage($file['tmp_name'], new Size(25,25), GDImage::RESIZE_FIT))->save();
        }
        catch (Exception $ex){
            $this->errors[] = $file['name'] . ' - ' . $ex->getMessage();
            return FALSE;
        }

        return TRUE;
    }
}
