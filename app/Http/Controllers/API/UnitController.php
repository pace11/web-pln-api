<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\ResponseController as ResponseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Unit;
use Validator;

class UnitController extends ResponseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $unit = Unit::orderBy('updated_at', 'desc')->paginate(10);

        return $this->sendResponsePagination($unit, "Fetch unit success");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexOption() {
        $user = Auth::guard('api')->user();

        if ($user->type == 'superadmin') {
            $filter = [];
        }

        if ($user->type == 'admin') {
            $filter = [
                ['id', '=', $user->unit_id],
            ];
        }

        $unit = Unit::where($filter)->orderBy('updated_at', 'desc')->get();

        return $this->sendResponse($unit, "Fetch unit success");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexArchived(Request $request) {
        $unit = Unit::orderBy('deleted_at', 'desc')->onlyTrashed()->paginate(10);

        return $this->sendResponsePagination($unit, "Fetch unit archived success");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showById($id) {
        $unit = Unit::where('id', $id)->first();

        if (!$unit) {
            return $this->sendError('Not Found', false, 404);
        }
        
        return $this->sendResponse($unit, 'Fetch unit success');
    }

    /**
     * Insert new resource.
     *
     * @param  request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);       
        }

        $input = $request->all();
        $input['created_at'] = date('Y-m-d h:i:s');
        $input['updated_at'] = date('Y-m-d h:i:s');
        $unit = Unit::create($input);

        return $this->sendResponse($unit, "Submit unit success", 201);
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
            'title' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);       
        }

        $input = $request->all();
        $input['updated_at'] = date('Y-m-d h:i:s');
        Unit::whereId($id)->update($input);
        $update = Unit::where('id', $id)->first();

        return $this->sendResponse($update, "Update unit success");
    }

    /**
     * Remove the specific resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteById($id) {
        $unit = Unit::whereId($id)->delete();

        if (!$unit) {
            return $this->sendError('Not Found', false, 404);
        }
        
        return $this->sendResponse(null, 'Delete unit success');
    }

    /**
     * Restore the specific deleted resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restoreById($id) {
        $unit = Unit::whereId($id)->withTrashed()->restore();

        if (!$unit) {
            return $this->sendError('Not Found', false, 404);
        }
        
        return $this->sendResponse(null, 'Restore unit success');
    }

}
