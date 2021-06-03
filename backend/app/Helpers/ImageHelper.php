<?php


namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class ImageHelper
{
    /**
     * base64 To Image
     */
    public static function base64ToImage($image, $pathName)
    {
        $mainPath = '/uploads/'.$pathName;

        //image Name
        $imageName = uniqid().rand(0,10000000).".png";

        //initial path
        $path = public_path().$mainPath;

        //make directory
        File::makeDirectory($path, $mode = 0777, true, true);

        //full url
        $fullUrl = $path."/".$imageName;

        //image upload
        Image::make(file_get_contents($image))->save($fullUrl);

        //return image full url
        return url($mainPath."/".$imageName);
    }
}