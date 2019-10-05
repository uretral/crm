<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Form\Field;
//use Illuminate\Http\Request;

class ImageController extends Controller
{

    public function uploadImageContent()
    {

        $this->validate(request(), [
            'file' => 'mimes:jpeg,pdf,jpg,gif,png'
        ]);

        $file = request()->file('file');
        $filename = md5(uniqid()).'.'.$file->getClientOriginalExtension();


        $imagePath = "/storage/docs/";



        $file->move(public_path() . $imagePath, $filename);

        $url = $imagePath . $filename;

        $ff = [
            'uploaded' => 1,
            'fileName' => $filename,
            'url' => $url
        ];

        echo json_encode($ff);
    }


    public function attach()
    {
        $this->uploadImageContent(request()->all());
    }
}
