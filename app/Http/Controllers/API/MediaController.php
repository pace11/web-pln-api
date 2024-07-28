<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\ResponseController as ResponseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Media;
use Validator;
use Carbon\Carbon;

class MediaController extends ResponseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $year = $request->query('posted') ?? Carbon::now()->year;

        $account = Media::with(['user'])->whereYear('period_date', $year)->orderBy('period_date', 'asc')->paginate(10);

        return $this->sendResponsePagination($account, "Fetch data success");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showById($id) {
        $account = Media::with(['user'])->where('id', $id)->first();
        
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
            'period_date' => 'required',
            'target' => '',
        ]);

        $found = Media::
                whereYear('period_date', $request->all()['period_date'])
                ->first();

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);       
        }

        if ($found) {
            return $this->sendError('Error duplicate resource', 'Please try again with different resource', 409);
        }

        // generate 12 month in 1 year
        for ($i = 1; $i <= 12; $i++) {
            Media::create([
                'period_date' => Carbon::parse($request->all()['period_date'].'-'.$i.'-1')->format('Y-m-d'),
                'target' => $request->all()['target'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'users_id' => $user->id
            ]);
        }

        return $this->sendResponse($found, "Submit data success", 201);
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
            'target' => '',
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);       
        }

        $input = $request->all();
        $input['updated_at'] = Carbon::now();

        Media::whereId($id)->update($input);
        $update = Media::where('id', $id)->first();

        return $this->sendResponse($update, "Update data success");
    }

    /**
     * Remove the specific resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteById($id) {
        $account = Media::whereId($id)->delete();

        if (!$account) {
            return $this->sendError('Not Found', false, 404);
        }
        
        return $this->sendResponse(null, 'Delete data success');
    }

}
