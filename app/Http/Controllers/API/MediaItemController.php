<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\ResponseController as ResponseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Media;
use App\Models\MediaItem;
use Validator;
use Carbon\Carbon;

class MediaItemController extends ResponseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $user = Auth::guard('api')->user();

        if ($user->type == 'superadmin' || is_null($user->placement) || $user->placement == 'main_office') {
            $filter = [];
        } else {
            $filter = [['unit_id', $user->unit_id]];
        }
        
        $posts = MediaItem::with(['user', 'unit'])->where($filter)->orderBy('updated_at', 'desc')->paginate(10);

        return $this->sendResponsePagination($posts, "Fetch media success");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexByParentId(Request $request, $id) {
        $user = Auth::guard('api')->user();

        $filter = [['media_id','=',$id]];
        
        if ($user->type == 'creator' && $user->placement == 'executor_unit') {
            $filter = [
                ['media_id','=',$id],
                ['unit_id','=',$user->unit_id]
            ];
        }

        $account = MediaItem::with(['user', 'unit', 'media'])->where($filter)->orderBy('id', 'asc')->paginate(10);

        return $this->sendResponsePagination($account, "Fetch data success");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexArchived(Request $request) {
        $posts = MediaItem::with(['categories', 'user'])->orderBy('deleted_at', 'desc')->onlyTrashed()->paginate(10);

        return $this->sendResponsePagination($posts, "Fetch posts archived success");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showById($id) {
        $media = MediaItem::with(['user', 'unit'])->where('id', $id)->first();

        if (!$media) {
            return $this->sendError('Not Found', false, 404);
        }
        
        return $this->sendResponse($media, 'Fetch media success');
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
            'title' => 'required',
            'attachment_images' => '',
            'attachment_videos' => '',
            'caption' => '',
            'media_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);       
        }

        $input = $request->all();
        $input['users_id'] = $user->id;
        $input['unit_id'] = $user->unit_id ?? null;
        $input['created_at'] = Carbon::now();
        $input['updated_at'] = Carbon::now();
        $media = MediaItem::create($input);

        return $this->sendResponse($media, "Submit media success", 201);
    }

    /**
     * Modified the specific resource.
     *
     * @param  request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id) {
        $user = Auth::guard('api')->user();
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'url' => 'required',
            'caption' => 'required',
            'target_post' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);       
        }

        $input = $request->all();
        $input['updated_at'] = Carbon::now();
        MediaItem::whereId($id)->update($input);
        $update = MediaItem::where('id', $id)->first();

        return $this->sendResponse($update, "Update media success");
    }

    /**
     * Remove the specific resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteById($id) {
        $media = MediaItem::whereId($id)->delete();

        if (!$media) {
            return $this->sendError('Not Found', false, 404);
        }
        
        return $this->sendResponse(null, 'Delete media success');
    }

    /**
     * Restore the specific deleted resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restoreById($id) {
        $media = MediaItem::whereId($id)->withTrashed()->restore();

        if (!$media) {
            return $this->sendError('Not Found', false, 404);
        }
        
        return $this->sendResponse(null, 'Restore media success');
    }

    /**
     * Modified the specific resource.
     *
     * @param  request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatusById(Request $request, $id) {
        $user = Auth::guard('api')->user();
        $validator = Validator::make($request->all(), [
            'status' => 'required',
            'remarks' => '',
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);       
        }

        $placement = [
            'main_office' => '(Kantor Induk)',
            'executor_unit' => '(Unit Pelaksana)',
            'superadmin' => '(Superadmin)'
        ];

        $payload = [
            'checked' => [
                'checked_by_date' => Carbon::now(),
                'checked_by_email' => $user->email." ".$placement[$user->placement ?? 'superadmin'],
                'checked_by_remarks' => $request->all()['remarks']
            ],
            'final_checked' => [
                'final_checked_by_date' => Carbon::now(),
                'final_checked_by_email' => $user->email." ".$placement[$user->placement ?? 'superadmin'],
                'final_checked_by_remarks' => $request->all()['remarks']
            ],
            'approved' => [
                'approved_by_date' => Carbon::now(),
                'approved_by_email' => $user->email." ".$placement[$user->placement ?? 'superadmin'],
                'approved_by_remarks' => $request->all()['remarks'],
            ],
            'final_approved' => [
                'final_approved_by_date' => Carbon::now(),
                'final_approved_by_email' => $user->email." ".$placement[$user->placement ?? 'superadmin'],
                'final_approved_by_remarks' => $request->all()['remarks'],
            ],
            'rejected' => [
                'rejected_by_date' => Carbon::now(),
                'rejected_by_email' => $user->email." ".$placement[$user->placement ?? 'superadmin'],
                'rejected_by_remarks' => $request->all()['remarks']
            ],
            'final_rejected' => [
                'final_rejected_by_date' => Carbon::now(),
                'final_rejected_by_email' => $user->email." ".$placement[$user->placement ?? 'superadmin'],
                'final_rejected_by_remarks' => $request->all()['remarks']
            ],
        ];

        $input = $payload[$request->all()['status']];
        $input['updated_at'] = Carbon::now();
        $input['status'] = $request->all()['status'];

        MediaItem::whereId($id)->update($input);
        $update = MediaItem::where('id', $id)->first();

        return $this->sendResponse($update, "Update media success");
    }

}
