<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\ResponseController as ResponseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\PublicInformation;
use App\Models\PublicInformationItem;
use Validator;
use Carbon\Carbon;

class PublicInformationController extends ResponseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $year = $request->query('posted') ?? Carbon::now()->year;

        $account = PublicInformation::with(['user'])->whereYear('period_date', $year)->orderBy('period_date', 'asc')->paginate(10);

        return $this->sendResponsePagination($account, "Fetch data success");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showById($id) {
        $account = PublicInformation::with(['user'])->where('id', $id)->first();

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
            'period_date' => 'required',
        ]);

        $found = PublicInformation::
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
            PublicInformation::create([
                'period_date' => Carbon::parse($request->all()['period_date'].'-'.$i.'-1')->format('Y-m-d'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'users_id' => $user->id
            ]);
        }

        return $this->sendResponse(null, "Submit data success", 201);
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

        $child = PublicInformationItem::where('scoring_id', $id)->get();

        $input = $request->all();
        $input['updated_at'] = Carbon::now();

        if (count($child)) {
            foreach($child as $value) {
                PublicInformationItem::whereId($value->id)->update([
                    'value' => $input['target'] ? round(($value->realization/$input['target'])*100) : 0,
                ]);
            }
        }

        PublicInformation::whereId($id)->update($input);
        $update = PublicInformation::where('id', $id)->first();

        return $this->sendResponse($update, "Update data success");
    }

    /**
     * Remove the specific resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteById($id) {
        $account = PublicInformation::whereId($id)->delete();

        if (!$account) {
            return $this->sendError('Not Found', false, 404);
        }
        
        return $this->sendResponse(null, 'Delete data success');
    }

}
