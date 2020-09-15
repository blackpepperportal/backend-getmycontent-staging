<?php

namespace App\Repositories;

use App\Helpers\Helper;

use Log, Validator, Setting, Exception, DB;

class ProductRepository {

	/**
     *
     * @method user_product_pictures_save()
     *
     * @uses To Upoad Product Pictures
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param 
     *
     * @return
     */
    public static function user_product_pictures_save($files, $user_product_id) {

        $allowedfileExtension=['jpeg','jpg','png'];

        // Single file upload

        if(!is_array($files)) {
            
            $file = $files;

            $user_product_pictures = new \App\UserProductPicture;

            $user_product_pictures->user_product_id = $user_product_id;

            $user_product_pictures->picture = Helper::upload_file($file, FILE_PATH_HOST);

            $user_product_pictures->save();

            return true;
       
        }

        // Multiple files upload
        foreach($files as $file) {

            $filename = $file->getClientOriginalName();

            $extension = $file->getClientOriginalExtension();

            $check_picture = in_array($extension, $allowedfileExtension);
            
            if($check_picture) {

                $user_product_pictures = new \App\UserProductPicture;

	            $user_product_pictures->user_product_id = $user_product_id;

	            $user_product_pictures->picture = Helper::upload_file($file, FILE_PATH_HOST);

	            $user_product_pictures->save();

           }
        
        }

        return true;
    
    }
}