<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\ResponseController as ResponseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Posts;
use App\Models\Categories;
use App\Models\User;
use Validator;

class GrafikController extends ResponseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $user = Auth::guard('api')->user();

        if ($user->type == 'superadmin') {
            $filter = [];
            $filter_user = [];
        } else {
            $filter = [['unit_id', $user->unit_id]];
            $filter_user = [
                ['unit_id','=',$user->unit_id],
                ['type','!=','superadmin'],
                ['type','!=','admin']
            ];
        }

        $response = [
            "post_count" => [
                "total_posts" => Posts::where($filter)->count(),
                "created_posts" => Posts::where($filter)->where('status', 'pending')->count(),
                "checked_posts" => Posts::where($filter)->where('status', 'checked')->count(),
                "approved_posts" => Posts::where($filter)->where('status', 'approved')->count(),
                "rejected_posts" => Posts::where($filter)->where('status', 'rejected')->count(),
            ],
            "category_count" => Categories::count(),
            "user_count" => User::where($filter_user)->count()
        ];

        return $this->sendResponse($response, "Fetch grafik success");
    }

}
