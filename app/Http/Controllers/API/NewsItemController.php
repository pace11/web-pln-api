<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\ResponseController as ResponseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\News;
use App\Models\NewsItem;
use Validator;
use Carbon\Carbon;

class NewsItemController extends ResponseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $account = NewsItem::with(['user', 'unit', 'news'])->orderBy('id', 'asc')->paginate(10);

        return $this->sendResponsePagination($account, "Fetch data success");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexByParentId(Request $request, $id) {
        $user = Auth::guard('api')->user();

        $filter = [['news_id','=',$id]];
        
        if ($user->type == 'creator' && $user->placement == 'executor_unit') {
            $filter = [
                ['news_id','=',$id],
                ['unit_id','=',$user->unit_id]
            ];
        }

        $account = NewsItem::with(['user', 'unit', 'news'])->where($filter)->orderBy('id', 'asc')->paginate(10);

        return $this->sendResponsePagination($account, "Fetch data success");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showById($id) {
        $account = NewsItem::with(['user', 'unit', 'news'])->where('id', $id)->first();
        
        return $this->sendResponse($account, 'Fetch data success');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showByParentId($id) {
        $account = NewsItem::with(['user', 'unit', 'news'])->where('news_id', $id)->first();
        
        return $this->sendResponse($account, 'Fetch data success');
    }

    /**
     * Insert new resource.
     *
     * @param  request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        $user = Auth::guard('api')->user();
        $validator = Validator::make($request->all(), [
            'attachment' => 'required',
            'news_id' => 'required' 
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);       
        }

        $parent = News::whereId($request->all()['news_id'])->first();
        $count = count(json_decode($request->all()['attachment'], true)['berita']);

        $input = $request->all();
        $input['realization'] = $count;
        $input['value'] = $parent->target ? round(($count/$parent->target)*100) : 0;
        $input['unit_id'] = $user->unit_id;
        $input['users_id'] = $user->id;
        $input['created_at'] = Carbon::now();
        $input['updated_at'] = Carbon::now();
        
        $create = NewsItem::create($input);

        return $this->sendResponse($create, "Submit data success", 201);
    }

    /**
     * Modified the specific resource.
     *
     * @param  request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'attachment' => 'required',
            'news_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);       
        }

        $parent = News::whereId($request->all()['news_id'])->first();
        $count = count(json_decode($request->all()['attachment'], true)['berita']);

        $input = $request->all();
        $input['realization'] = $count;
        $input['value'] = $parent->target ? round(($count/$parent->target)*100) : 0;
        $input['updated_at'] = Carbon::now();

        NewsItem::whereId($id)->update($input);
        $update = NewsItem::whereId($id)->first();

        return $this->sendResponse($input, "Update data success");
    }

    /**
     * Remove the specific resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteById($id) {
        $account = NewsItem::whereId($id)->delete();

        if (!$account) {
            return $this->sendError('Not Found', false, 404);
        }
        
        return $this->sendResponse(null, 'Delete data success');
    }

}
