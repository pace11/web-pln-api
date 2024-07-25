<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\ResponseController as ResponseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\InternalCommunication;
use App\Models\InternalCommunicationItem;
use Validator;
use Carbon\Carbon;

class InternalCommunicationItemController extends ResponseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $account = InternalCommunicationItem::with(['user', 'unit', 'internal_communication'])->orderBy('period_date', 'asc')->paginate(10);

        return $this->sendResponsePagination($account, "Fetch data success");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showById($id) {
        $account = InternalCommunicationItem::with(['user', 'unit', 'internal_communication'])->where('id', $id)->first();

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
            'internal_communication_id' => 'required' 
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);       
        }

        $parent = InternalCommunication::whereId($request->all()['internal_communication_id'])->first();

        $input = $request->all();
        $input['period_date'] = Carbon::now();
        $input['realization'] = count(json_decode($input['attachment'], true));
        $input['value'] = $parent->target ? round(($input['realization']/$parent->target)*100) : 0;
        $input['unit_id'] = $user->unit_id;
        $input['users_id'] = $user->id;
        $input['created_at'] = Carbon::now();
        $input['updated_at'] = Carbon::now();
        
        $create = InternalCommunicationItem::create($input);

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
            'internal_communication_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);       
        }

        $parent = InternalCommunication::whereId($request->all()['internal_communication_id'])->first();

        $input = $request->all();
        $input['realization'] = count(json_decode($input['attachment'], true));
        $input['value'] = $parent->target ? round(($input['realization']/$parent->target)*100) : 0;
        $input['updated_at'] = Carbon::now();

        InternalCommunicationItem::whereId($id)->update($input);
        $update = InternalCommunicationItem::whereId($id)->first();

        return $this->sendResponse($input, "Update data success");
    }

    /**
     * Remove the specific resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteById($id) {
        $account = InternalCommunicationItem::whereId($id)->delete();

        if (!$account) {
            return $this->sendError('Not Found', false, 404);
        }
        
        return $this->sendResponse(null, 'Delete data success');
    }

}
