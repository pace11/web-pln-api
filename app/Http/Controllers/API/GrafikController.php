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
use App\Models\MediaItem;
use App\Models\NewsItem;
use App\Models\AccountInfluencerItem;
use App\Models\InternalCommunicationItem;
use App\Models\ScoringItem;
use App\Models\PublicInformationItem;
use Validator;
use Carbon\Carbon;

class GrafikController extends ResponseController
{
 
    public function index() {
        $user = Auth::guard('api')->user();

        if ($user->type == 'superadmin' || is_null($user->placement) || $user->placement == 'main_office') {
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
            "media_count" => MediaItem::where($filter)->count(),
            "category_news_count" => Categories::count(),
            "user_count" => User::where($filter_user)->count(),
            "unit_count" => Unit::count()
        ];

        return $this->sendResponse($response, "Fetch grafik count success");
    }

    public function getStatusPostByUnit($status) {
        $user = Auth::guard('api')->user();

        if ($user->type == 'superadmin' || is_null($user->placement) || $user->placement == 'main_office') $filter = [];
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

        if ($user->type == 'superadmin' || is_null($user->placement) || $user->placement == 'main_office') $filter = [];
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

        if ($user->type == 'superadmin' || is_null($user->placement) || $user->placement == 'main_office') $filter = [];
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

    public function getIndicatorsByMonth($indicator, $unit, $year) {
        $ind_1 = NewsItem::with(['user', 'unit', 'news'])
                ->where('unit_id', $unit)
                ->whereHas('news', function($q) use($year) {
                    $q->whereYear('period_date', $year);
                })->sum('value');
        $ind_2 = MediaItem::with(['user', 'unit', 'media'])
                ->where('unit_id', $unit)
                ->whereHas('media', function($q) use($year) {
                    $q->whereYear('period_date', $year);
                })->sum('value');
        $ind_3 = AccountInfluencerItem::with(['user', 'unit', 'account_influencer'])
                ->where('unit_id', $unit)
                ->whereHas('account_influencer', function($q) use($year) {
                    $q->whereYear('period_date', $year);
                })->sum('value');
        $ind_4 = InternalCommunicationItem::with(['user', 'unit', 'internal_communication'])
                ->where('unit_id', $unit)
                ->whereHas('internal_communication', function($q) use($year) {
                    $q->whereYear('period_date', $year);
                })->sum('value');
        $ind_5 = ScoringItem::with(['user', 'unit', 'scoring'])
                ->where('unit_id', $unit)
                ->whereHas('scoring', function($q) use($year) {
                    $q->whereYear('period_date', $year);
                })->sum('value');
        $ind_6 = PublicInformationItem::with(['user', 'unit', 'public_information'])
                ->where('unit_id', $unit)
                ->whereHas('public_information', function($q) use($year) {
                    $q->whereYear('period_date', $year);
                })->sum('value');

        $mapping = [
            '1' => round(($ind_1 ?? 0)/12),
            '2' => round(($ind_2 ?? 0)/12),
            '3' => round(($ind_3 ?? 0)/12),
            '4' => round(($ind_4 ?? 0)/12),
            '5' => round(($ind_5 ?? 0)/12),
            '6' => round(($ind_5 ?? 0)/12),
        ];

        return round($mapping[$indicator]);
    }

    public function unitWithIndicators(Request $request) {
        $user = Auth::guard('api')->user();
        $year = $request->query('year') ?? Carbon::now()->year;

        if ($user->type == 'superadmin' || is_null($user->placement) || $user->placement == 'main_office') $filter = [];
        else $filter = [['id','=',$user->unit_id]];

        $unit = Unit::where($filter)->get();

        foreach($unit as $key => $value) {
           $arr[] = [
            'unit_name' => $value->title,
            'indicator_1' => $this->getIndicatorsByMonth(1, $value->id, $year),
            'indicator_2' => $this->getIndicatorsByMonth(2, $value->id, $year),
            'indicator_3' => $this->getIndicatorsByMonth(3, $value->id, $year),
            'indicator_4' => $this->getIndicatorsByMonth(4, $value->id, $year),
            'indicator_5' => $this->getIndicatorsByMonth(5, $value->id, $year),
            'indicator_6' => $this->getIndicatorsByMonth(6, $value->id, $year),
            'target' => 100,
            'realization' => round((
                $this->getIndicatorsByMonth(1, $value->id, $year)+
                $this->getIndicatorsByMonth(2, $value->id, $year)+
                $this->getIndicatorsByMonth(3, $value->id, $year)+
                $this->getIndicatorsByMonth(4, $value->id, $year)+
                $this->getIndicatorsByMonth(5, $value->id, $year)+
                $this->getIndicatorsByMonth(6, $value->id, $year))/6)
           ];
        }

        return $this->sendResponse($arr, "Fetch data success");
    }

}
