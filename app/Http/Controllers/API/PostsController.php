<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\ResponseController as ResponseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Posts;
use Validator;

class PostsController extends ResponseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $posted = $request->query('posted');
        $banner = $request->query('banner');
        $slug = $request->query('slug');

        if (isset($posted) || isset($banner) || isset($slug)) {
            $posts = Posts::with(['categories', 'user'])->orderBy('updated_at', 'desc')
                    ->whereHas('categories', function($q) use($slug) {
                        $q->where('slug', $slug ? '=' : '!=', $slug ? $slug : '');
                    })
                    ->where([
                        ['posted', '=', $posted === 'true' ? 1 : 0],
                        ['banner', '=', $banner === 'true' ? 1 : 0]
                    ])->paginate(10);
        } else {
            $posts = Posts::with(['categories', 'user'])->orderBy('updated_at', 'desc')->paginate(10);
        }

        return $this->sendResponsePagination($posts, "Fetch posts success");
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
        $posts = Posts::with(['categories', 'user'])->where('slug', $id)->first();

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

        $posts = Posts::with(['categories', 'user'])
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
            'categories_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);       
        }

        $input = $request->all();
        $input['id'] = Str::uuid();
        $input['slug'] = Str::slug($input['title']);
        $input['users_id'] = $user->id;
        $posts = Posts::create($input);

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
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'thumbnail' => '',
            'posted' => '',
            'banner' => '',
            'categories_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);       
        }

        $input = $request->all();
        $input['slug'] = Str::slug($input['title']);

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

}
