<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\ResponseController as ResponseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\ManageLink;
use Validator;
use Carbon\Carbon;

class ManageLinkController extends ResponseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $link = ManageLink::orderBy('active', 'desc')->orderBy('updated_at', 'desc')->paginate(10);

        return $this->sendResponsePagination($link, "Fetch link success");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexArchived(Request $request) {
        $unit = ManageLink::orderBy('deleted_at', 'desc')->onlyTrashed()->paginate(10);

        return $this->sendResponsePagination($unit, "Fetch link archived success");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showById($id) {
        $link = ManageLink::where('id', $id)->first();

        if (!$link) {
            return $this->sendError('Not Found', false, 404);
        }
        
        return $this->sendResponse($link, 'Fetch link success');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showByKey($id) {
        $link = ManageLink::where('key', $id)->orderBy('active', 'desc')->orderBy('updated_at', 'desc')->paginate(10);
        
        return $this->sendResponsePagination($link, 'Fetch link success');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showByKeyActive($id) {
        $link = ManageLink::where([['key', $id],['active', true]])->paginate(10);
        
        return $this->sendResponsePagination($link, 'Fetch link success');
    }

    /**
     * Insert new resource.
     *
     * @param  request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'period' => '',
            'url' => 'required'
        ]);

        $update_inactive = ['active' => false];

        $title = [
            'indicator-3' => 'Pengelolaan Akun Influencer',
            'indicator-4' => 'Pengelolaan Komunikasi Internal',
            'indicator-5' => 'Scoring',
            'indicator-6' => 'Pengelolaan Informasi Publik',
        ];

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);       
        }

        ManageLink::where([['key', $request->all()['key']]])->update($update_inactive);
        $input = $request->all();
        $input['title'] = $title[$request->all()['key']];
        $input['created_at'] = Carbon::now();
        $input['updated_at'] = Carbon::now();
        $unit = ManageLink::create($input);

        return $this->sendResponse($unit, "Submit link success", 201);
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
            'key' => 'required',
            'period' => '',
            'url' => 'required',
            'active' => ''
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);       
        }

        $input = $request->all();
        $input['updated_at'] = Carbon::now();
        ManageLink::whereId($id)->update($input);
        $update = ManageLink::where('id', $id)->first();

        return $this->sendResponse($update, "Update link success");
    }

    /**
     * Remove the specific resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteById($id) {
        $link = ManageLink::whereId($id)->delete();

        if (!$link) {
            return $this->sendError('Not Found', false, 404);
        }
        
        return $this->sendResponse(null, 'Delete link success');
    }

    /**
     * Restore the specific deleted resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restoreById($id) {
        $link = ManageLink::whereId($id)->withTrashed()->restore();

        if (!$link) {
            return $this->sendError('Not Found', false, 404);
        }
        
        return $this->sendResponse(null, 'Restore unit success');
    }

}
