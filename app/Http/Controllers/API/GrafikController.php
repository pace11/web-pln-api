<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\ResponseController as ResponseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Posts;
use App\Models\Categories;
use Validator;

class GrafikController extends ResponseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $response = [
            "post_count" => Posts::count(),
            "category_count" => Categories::count()
        ];

        return $this->sendResponse($response, "Fetch grafik success");
    }

}
