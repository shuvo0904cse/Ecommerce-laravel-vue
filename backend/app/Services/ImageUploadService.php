<?php


namespace App\Services;


use App\Helpers\MessageHelper;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class ImageUploadService
{
    protected $message;

    public function __construct()
    {
        $this->message = new MessageHelper();
    }

    /**
     * Image Upload
     */
    public function upload($image, $destination)
    {
        try {
            $imageName = $this->imageUpload($image, $destination);
            return $imageName;
        } catch (\Exception $ex) {
            $this->message::throwException($ex);
        }
    }

    /**
     * upload
     */
    protected function imageUpload($image, $destinationPath)
    {
        if ($image != '') {
            //image Name
            $imageName = $this->generateImageName($image);

            //mainPath
            $mainPath = "/app/public/".$destinationPath;

            //full directory
            $fullPath = storage_path($mainPath);

            #get all sizes for images
            $sizes = self::arrayOfSize();

            $originalImageLink = null;

            if (count($sizes) > 0) {
                foreach ($sizes as $size) {
                    $directory = $fullPath . "/" . $size;

                    //Check Directory
                    self::checkDirectory($directory);

                    //image resize
                    self::resize($size, $image, $directory, $imageName, $originalImageLink);

                    //if full size then return original link
                    if($size == "full-size") $originalImageLink = $directory."/".$imageName;
                }
            }
            return $imageName;
        }
    }

    /**
     * Generate Image Name
     */
    protected function generateImageName($image)
    {
        return time().'.'.$image->getClientOriginalName();
    }

    /**
     * resize
     */
    protected function resize($size, $image, $directory, $imageName, $originalImageLink = null)
    {
        if($size == "full-size"){
            $image->move($directory, $imageName);
        }else{
            $img = Image::make($originalImageLink);
            $img->resize($size, $size, function ($constraint) {
                $constraint->aspectRatio();
            })->save($directory.'/'.$imageName);
        }
    }

    /**
     *  Check if Directory Exists
     */
    protected function checkDirectory($target_location)
    {
        if (!is_dir($target_location)) File::makeDirectory($target_location, 0777, true, true);
        return $target_location;
    }

    /**
     * Array Of Size
     */
    protected function arrayOfSize()
    {
        $arrayOfSize = array(
            'picture'   => config("settings.image.full"),
            'thumbnail' => config("settings.image.medium"),
            'tiny'      => config("settings.image.tiny"),
        );
        return $arrayOfSize;
    }
}