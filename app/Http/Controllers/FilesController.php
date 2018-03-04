<?php namespace App\Http\Controllers;


use \Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;


class FilesController extends Controller {

    public function __construct()
    {
    }

    /**
     * Image Optimization
     * @param $old_image
     * @param $new_image_path   picture detstination url
     * @param $new_image_name   picture destination name
     * @param $width             the newest
     * @param $height
     * @return boolean
     */
    public function insertMiniImage($old_image,$new_image_path,$new_image_name,$width,$height){

        $type_old_image = mime_content_type($old_image);

        switch ($type_old_image) {
            case 'image/jpeg' :
                $src_img = imagecreatefromjpeg($old_image);
                break;
            case 'image/png' :
                $src_img = imagecreatefrompng($old_image);
                break;
            case 'image/jpg' :
                $src_img = imagecreatefromjpeg($old_image);

                break;
            default:
                return false;
                break;
        }
        if(!$src_img) {
            return 0;
        }
        list($old_width, $old_height) = getimagesize($old_image);

        $ratio_orig = $old_width/$old_height;

          if ($width/$height > $ratio_orig) {
            $width = $height*$ratio_orig;
        } else {
            $height = $width/$ratio_orig;
        }
        //in order to save the quality
        $thumbnail = imagecreatetruecolor($width,$height);
        if(!$thumbnail) {
            return 0;
        }
        //Resizing
        $result = imagecopyresampled($thumbnail, $src_img, 0, 0, 0, 0, $width, $height, $old_width, $old_height);
        if(!$result) {
            return 0;
        }
        //Save picture
        $result = imagejpeg($thumbnail, $new_image_path.$new_image_name,100);
        if(!$result) {
            return 0;
        }
        //delete the temp instance
        $result = imagedestroy($thumbnail);
        if(!$result) {
            return 0;
        }
        return 1;
    }

    

    /**
     * File Upload
     * @param file, original path , path after minimization
     */
    public function uploadImage($file,$path, $path_min)
    {  

        $imagename = time().'.'.$file->getClientOriginalExtension();
        $destinationPath = $path;
        $file->move($destinationPath, $imagename);
        $this->insertMiniImage($destinationPath.$imagename,$path_min,$imagename,100,100);
        return  $destinationPath.$imagename ;
   
    }
    
}
