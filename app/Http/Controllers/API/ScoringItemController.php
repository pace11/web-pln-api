<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\ResponseController as ResponseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Scoring;
use App\Models\ScoringItem;
use Validator;
use Carbon\Carbon;

class ScoringItemController extends ResponseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $account = ScoringItem::with(['user', 'unit', 'scoring'])->orderBy('period_date', 'asc')->paginate(10);

        return $this->sendResponsePagination($account, "Fetch data success");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showById($id) {
        $account = ScoringItem::with(['user', 'unit', 'scoring'])->where('id', $id)->first();

        if (!$account) {
            return $this->sendError('Not Found', false, 404);
        }
        
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
            'scoring_id' => 'required' 
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);       
        }

        $parent = Scoring::whereId($request->all()['scoring_id'])->first();

        $sum_realization = 0;
        if (count(json_decode($request->all()['attachment'], true)) > 0) {
            $sum_realization = array_reduce(json_decode($request->all()['attachment'], true), function($carry, $item) {
                return $carry + $item['value'];
            });
        }

        $input = $request->all();
        $input['period_date'] = Carbon::now();
        $input['realization'] = $sum_realization;
        $input['value'] = $parent->target ? round(($sum_realization/$parent->target)*100) : 0;
        $input['unit_id'] = $user->unit_id;
        $input['users_id'] = $user->id;
        $input['created_at'] = Carbon::now();
        $input['updated_at'] = Carbon::now();
        
        $create = ScoringItem::create($input);

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
            'scoring_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);       
        }

        $parent = Scoring::whereId($request->all()['scoring_id'])->first();

        $sum_realization = 0;
        if (count(json_decode($request->all()['attachment'], true)) > 0) {
            $sum_realization = array_reduce(json_decode($request->all()['attachment'], true), function($carry, $item) {
                return $carry + $item['value'];
            });
        }

        $input = $request->all();
        $input['realization'] = $sum_realization;
        $input['value'] = $parent->target ? round(($sum_realization/$parent->target)*100) : 0;
        $input['updated_at'] = Carbon::now();

        ScoringItem::whereId($id)->update($input);
        $update = ScoringItem::whereId($id)->first();

        return $this->sendResponse($input, "Update data success");
    }

    /**
     * Remove the specific resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteById($id) {
        $account = ScoringItem::whereId($id)->delete();

        if (!$account) {
            return $this->sendError('Not Found', false, 404);
        }
        
        return $this->sendResponse(null, 'Delete data success');
    }

}
