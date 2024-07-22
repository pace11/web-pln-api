<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\ResponseController as ResponseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Posts;
use App\Models\Categories;
use App\Models\User;
use App\Models\Unit;
use App\Models\Media;
use Validator;

class GrafikController extends ResponseController
{
 
    public function index() {
        $user = Auth::guard('api')->user();

        if ($user->type == 'superadmin') {
            $filter = [];
            $filter_user = [
                ['type','!=','superadmin'],
            ];
        } else {
            $filter = [['unit_id', $user->unit_id]];
            $filter_user = [
                ['unit_id','=',$user->unit_id],
                ['type','!=','superadmin'],
            ];
        }

        $response = [
            "post_count" => Posts::where($filter)->count(),
            "media_count" => Media::where($filter)->count(),
            "category_news_count" => Categories::count(),
            "user_count" => User::where($filter_user)->count(),
            "unit_count" => Unit::count()
        ];

        return $this->sendResponse($response, "Fetch grafik count success");
    }

    public function getStatusPostByUnit($status) {
        $user = Auth::guard('api')->user();

        if ($user->type == 'superadmin') $filter = [];
        else $filter = [['id','=',$user->unit_id]];

        $unit = Unit::where($filter)->get();
        $response = [];

        foreach($unit as $item) {
            $response[] = [
                'primary' => $item->title,
                'secondary' => Posts::where([['unit_id','=',$item->id],['status','LIKE','%'.$status.'%']])->count()
            ];
        }

        return $response;
    }

    public function getUserTypeByUnit($type) {
        $user = Auth::guard('api')->user();

        if ($user->type == 'superadmin') $filter = [];
        else $filter = [['id','=',$user->unit_id]];

        $unit = Unit::where($filter)->get();
        $response = [];

        foreach($unit as $item) {
            $response[] = [
                'primary' => $item->title,
                'secondary' => User::where([['unit_id','=',$item->id],['type','LIKE','%'.$type.'%']])->count()
            ];
        }

        return $response;
    }

    public function indexPostStatusByUnit() {
        $user = Auth::guard('api')->user();
        $response = [];

        if ($user->type == 'superadmin') $filter = [];
        else $filter = [['id','=',$user->unit_id]];

        $status = array('created', 'checked', 'approved', 'rejected');
        
        foreach($status as $item) {
            $response[] = [
                'label' => $item,
                'data' => $this->getStatusPostByUnit($item)
            ];
        }

        return $this->sendResponse($response, "Fetch grafik post success");
    }

    public function indexUserTypeByUnit() {
        $response = [];

        $type = array('creator', 'checker', 'approver');
        
        foreach($type as $item) {
            $response[] = [
                'label' => $item,
                'data' => $this->getUserTypeByUnit($item)
            ];
        }

        return $this->sendResponse($response, "Fetch grafik unit success");
    }

}
