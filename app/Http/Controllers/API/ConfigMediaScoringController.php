<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\ResponseController as ResponseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\ConfigMediaScoring;
use Validator;
use Carbon\Carbon;

class ConfigMediaScoringController extends ResponseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $data = ConfigMediaScoring::orderBy('updated_at', 'desc')->paginate(10);

        return $this->sendResponsePagination($data, "Fetch data success");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showById($id) {
        $data = ConfigMediaScoring::where('id', $id)->first();
        
        return $this->sendResponse($data, 'Fetch data success');
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
            'value' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);       
        }

        $input = $request->all();
        $input['key'] = Str::slug($input['title']);
        $input['created_at'] = Carbon::now();
        $input['updated_at'] = Carbon::now();
        $data = ConfigMediaScoring::create($input);

        return $this->sendResponse($data, "Submit data success", 201);
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
            'value' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);       
        }

        $input = $request->all();
        $input['key'] = Str::slug($input['title']);
        $input['updated_at'] = Carbon::now();
        ConfigMediaScoring::whereId($id)->update($input);
        $update = ConfigMediaScoring::where('id', $id)->first();

        return $this->sendResponse($update, "Update data success");
    }

    /**
     * Remove the specific resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteById($id) {
        $data = ConfigMediaScoring::whereId($id)->delete();

        if (!$data) {
            return $this->sendError('Not Found', false, 404);
        }
        
        return $this->sendResponse(null, 'Delete data success');
    }

}
