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

class ScoringController extends ResponseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $year = $request->query('year') ?? Carbon::now()->year;

        $account = Scoring::with(['user'])->whereYear('period_date', $year)->orderBy('period_date', 'asc')->paginate(10);

        return $this->sendResponsePagination($account, "Fetch data success");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexYear(Request $request) {
        $year = Scoring::select(\DB::raw('YEAR(period_date) as year'))->groupBy('year')->get();

        return $this->sendResponse($year, "Fetch data success");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showById($id) {
        $account = Scoring::with(['user'])->where('id', $id)->first();

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
            'target' => '',
        ]);

        $found = Scoring::
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
            Scoring::create([
                'period_date' => Carbon::parse($request->all()['period_date'].'-'.$i.'-1')->format('Y-m-d'),
                'target' => $request->all()['target'],
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

        $child = ScoringItem::where('scoring_id', $id)->get();

        $input = $request->all();
        $input['updated_at'] = Carbon::now();

        if (count($child)) {
            foreach($child as $value) {
                ScoringItem::whereId($value->id)->update([
                    'value' => $input['target'] ? round(($value->realization/$input['target'])*100) : 0,
                ]);
            }
        }

        Scoring::whereId($id)->update($input);
        $update = Scoring::where('id', $id)->first();

        return $this->sendResponse($update, "Update data success");
    }

    /**
     * Remove the specific resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteById($id) {
        $account = Scoring::whereId($id)->delete();

        if (!$account) {
            return $this->sendError('Not Found', false, 404);
        }
        
        return $this->sendResponse(null, 'Delete data success');
    }

}
