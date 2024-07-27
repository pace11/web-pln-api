<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\ResponseController as ResponseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Posts;
use App\Models\PostsWeb;
use App\Models\Notifications;
use Validator;
use Carbon\Carbon;

class PostsController extends ResponseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $user = Auth::guard('api')->user();
        $posted = $request->query('posted');
        $banner = $request->query('banner');
        $slug = $request->query('slug');

        if ($user->type == 'superadmin' || is_null($user->placement) || $user->placement == 'main_office') {
            $filter = [];
        } else {
            $filter = [['unit_id', $user->unit_id]];
        }

        if (isset($posted) || isset($banner) || isset($slug)) {
            $posts = Posts::with(['categories', 'user', 'unit'])->orderBy('updated_at', 'desc')
                    ->whereHas('categories', function($q) use($slug) {
                        $q->where('slug', $slug ? '=' : '!=', $slug ? $slug : '');
                    })
                    ->where([
                        ['posted', '=', $posted === 'true' ? 1 : 0],
                        ['banner', '=', $banner === 'true' ? 1 : 0]
                    ])
                    ->where($filter)->paginate(10);
        } else {
            $posts = Posts::with(['categories', 'user', 'unit'])->where($filter)->orderBy('updated_at', 'desc')->paginate(10);
        }

        return $this->sendResponsePagination($posts, "Fetch posts success");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexWeb(Request $request) {
        $posted = $request->query('posted');
        $banner = $request->query('banner');
        $slug = $request->query('slug');

        if (isset($posted) || isset($banner) || isset($slug)) {
            $posts = PostsWeb::with(['categories', 'user', 'unit'])->orderBy('updated_at', 'desc')
                    ->whereHas('categories', function($q) use($slug) {
                        $q->where('slug', $slug ? '=' : '!=', $slug ? $slug : '');
                    })
                    ->where([
                        ['posted', '=', $posted === 'true' ? 1 : 0],
                        ['banner', '=', $banner === 'true' ? 1 : 0]
                    ])->paginate(10);
        } else {
            $posts = PostsWeb::with(['categories', 'user', 'unit'])->orderBy('updated_at', 'desc')->paginate(10);
        }

        return $this->sendResponsePagination($posts, "Fetch posts success");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexDownload(Request $request) {
        $start_date = $request->query('start_date') ?? '';
        $end_date = $request->query('end_date') ?? '';

        $filter = [
            Carbon::parse($start_date)->format('Y-m-d'), 
            Carbon::parse($end_date)->format('Y-m-d')
        ];

        if ($start_date || $end_date) $posts = PostsWeb::with(['categories', 'user', 'unit'])->whereBetween('created_at', $filter)->orderBy('id', 'asc')->get();
        else $posts = PostsWeb::with(['categories', 'user', 'unit'])->orderBy('id', 'asc')->get();

        return $this->sendResponse($posts, "Download posts success");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexRelease() {
        $user = Auth::guard('api')->user();
        $filter = [];

        if ($user->type == 'creator' && $user->placement == 'executor_unit') {
            $filter = [['unit_id', '=', $user->unit_id]];
        }

        $posts = Posts::where($filter)->whereNotNull('number_release')->orderBy('updated_at', 'desc')->get();

        return $this->sendResponse($posts, "Fetch data success");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexArchived(Request $request) {
        $posts = Posts::with(['categories', 'user'])->orderBy('deleted_at', 'desc')->onlyTrashed()->paginate(10);

        return $this->sendResponsePagination($posts, "Fetch posts archived success");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showById($id) {
        $posts = Posts::with(['categories', 'user'])->where('id', $id)->first();

        if (!$posts) {
            return $this->sendError('Not Found', false, 404);
        }
        
        return $this->sendResponse($posts, 'Fetch posts success');
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function showBySlug($id) {
        $posts = PostsWeb::with(['categories', 'user'])->where('slug', $id)->first();

        if (!$posts) {
            return $this->sendError('Not Found', false, 404);
        }
        
        return $this->sendResponse($posts, 'Fetch posts success');
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function showRelates(Request $request) {
        $slug = $request->query('slug') ?? '';
        $tag = $request->query('tag') ?? '';
        $limit = $request->query('limit') ?? 3;

        $posts = PostsWeb::with(['categories', 'user'])
                ->whereHas('categories', function($q) use($tag) {
                    $q->where('slug', '=', $tag);
                })
                ->where('slug', '!=', $slug)
                ->inRandomOrder()
                ->limit($limit)
                ->get();

        if (!$posts) {
            return $this->sendError('Not Found', false, 404);
        }
        
        return $this->sendResponse($posts, 'Fetch posts success');
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
            'description' => 'required',
            'thumbnail' => '',
            'posted' => '',
            'banner' => '',
            'recreated' => '',
            'categories_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);       
        }

        $input = $request->all();
        $input['slug'] = Str::slug($input['title']);
        $input['users_id'] = $user->id;
        $input['unit_id'] = $user->unit_id;
        $input['created_at'] = Carbon::now();
        $input['updated_at'] = Carbon::now();

        if (is_null($user->placement) || $user->placement == 'main_office') $input['status'] = 'final_created';

        $posts = Posts::create($input);
        $detail_posts = Posts::where('id', $posts->id)->first();
        
        if ($detail_posts) {
            Notifications::create([
                'users_id' => $user->id,
                'posts_id' => $detail_posts->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'status' => is_null($user->placement) || $user->placement == 'main_office' ? 'final_created' : 'created'
            ]);
        }

        return $this->sendResponse($posts, "Submit post success", 201);
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
            'description' => 'required',
            'thumbnail' => '',
            'posted' => '',
            'banner' => '',
            'recreated' => '',
            'categories_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);       
        }

        $input = $request->all();
        $input['slug'] = Str::slug($input['title']);
        $input['updated_at'] = Carbon::now();

        Posts::whereId($id)->update($input);
        $update = Posts::where('id', $id)->first();

        return $this->sendResponse($update, "Update posts success");
    }

    /**
     * Remove the specific resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteById($id) {
        $posts = Posts::whereId($id)->delete();

        if (!$posts) {
            return $this->sendError('Not Found', false, 404);
        }
        
        return $this->sendResponse(null, 'Delete posts success');
    }

    /**
     * Restore the specific deleted resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restoreById($id) {
        $post = Posts::whereId($id)->withTrashed()->restore();

        if (!$post) {
            return $this->sendError('Not Found', false, 404);
        }
        
        return $this->sendResponse(null, 'Restore post success');
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
            'posted' => '',
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
            'final_created' => [
                'final_created_by_date' => Carbon::now(),
                'final_created_by_email' => $user->email." ".$placement[$user->placement ?? 'superadmin'],
                'final_created_by_remarks' => $request->all()['remarks']
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
                // 'posted' => $request->all()['posted']
            ],
            'final_approved_2' => [
                'final_approved_2_by_date' => Carbon::now(),
                'final_approved_2_by_email' => $user->email." ".$placement[$user->placement ?? 'superadmin'],
                'final_approved_2_by_remarks' => $request->all()['remarks'],
                // 'posted' => $request->all()['posted']
            ],
            'final_approved_3' => [
                'final_approved_3_by_date' => Carbon::now(),
                'final_approved_3_by_email' => $user->email." ".$placement[$user->placement ?? 'superadmin'],
                'final_approved_3_by_remarks' => $request->all()['remarks'],
                'number_release' => $this->generateNumberRelease(Carbon::now())
                // 'posted' => $request->all()['posted']
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
            'final_rejected_2' => [
                'final_rejected_2_by_date' => Carbon::now(),
                'final_rejected_2_by_email' => $user->email." ".$placement[$user->placement ?? 'superadmin'],
                'final_rejected_2_by_remarks' => $request->all()['remarks']
            ],
            'final_rejected_3' => [
                'final_rejected_3_by_date' => Carbon::now(),
                'final_rejected_3_by_email' => $user->email." ".$placement[$user->placement ?? 'superadmin'],
                'final_rejected_3_by_remarks' => $request->all()['remarks']
            ],
        ];

        $input = $payload[$request->all()['status']];
        $input['status'] = $request->all()['status'];

        Posts::whereId($id)->update($input);
        $update = Posts::where('id', $id)->first();

        Notifications::create([
            'users_id' => $update->users_id,
            'posts_id' => $update->id,
            'status' => $request->all()['status'],
            'updated_at' => Carbon::now()
        ]);

        return $this->sendResponse($update, "Update posts success");
    }

    /**
     * Modified the specific resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateReplicateById($id) {
        $detail = Posts::find($id);
        Posts::whereId($id)->update([
            'recreated' => true, 
            'slug' => $detail->slug."-recreated-".$detail->id, 
            'updated_at' => $detail->updated_at
        ]);
        $detail['status'] = 'created';
        $detail['updated_at'] = Carbon::now();

        $arr = array('checked','approved','rejected');
        foreach($arr as $value) {
            $detail[$value.'_by_date'] = null;
            $detail[$value.'_by_email'] = null;
            $detail[$value.'_by_remarks'] = null;
            $detail['final_'.$value.'_by_date'] = null;
            $detail['final_'.$value.'_by_email'] = null;
            $detail['final_'.$value.'_by_remarks'] = null;
        }

        $new = $detail->replicate()->save();

        return $this->sendResponse($new, "Recreate posts success", 201);
    }

    public function generateNumberRelease($date) {
        $month = Carbon::parse($date)->month;
        $year = Carbon::parse($date)->year;

        $romawi = array('','I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII');

        $get = Posts::whereNotNull('number_release')->whereMonth('created_at', $month)->whereYear('created_at', $year)->get();
        $count = $get->count() + 1;
        $number_release = str_pad($count, 3, '0', STR_PAD_LEFT).'.KOMSTH/STH.00.01/'.$romawi[$month].'/'.$year;

        return $number_release;
    }

}
