<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use URL;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function saveImage($image, $path = 'public')
    {  
        if(!$image){
            return null;
        }

        $filename = time().'.png';

        //savve image
        \Storage::disk($path)->put($filename, base64_decode($image));

        //return the path
        return URL::to('/').'/storage/'.$path.'/'.$filename;
    }
}