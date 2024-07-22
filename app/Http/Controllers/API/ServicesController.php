<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\ResponseController as ResponseController;
use Illuminate\Support\Facades\Storage;
use Validator;

class ServicesController extends ResponseController
{

    public function uploadImage(Request $request) {
        $validator = Validator::make($request->all(), [
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2000',
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);       
        }

        $image = $request->file;
        $filename = $image->hashName();

        Storage::putFileAs('images', $request->file, $filename);

        $response = [
            'image' => env('APP_URL_IMAGE', '')."/".$filename
        ];
        
        return $this->sendResponse($response, 'Upload image success');
    }
}
