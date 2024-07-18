<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\ResponseController as ResponseController;
use App\Models\Categories;
use Validator;

class CategoriesController extends ResponseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $categories = Categories::paginate(10);

        return $this->sendResponsePagination($categories, 'Fetch categories success');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexArchived(Request $request) {
        $categories = Categories::onlyTrashed()->paginate(10);

        return $this->sendResponsePagination($categories, "Fetch categories archived success");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showById($id) {
        $categories = Categories::where('id', $id)->first();

        if (!$categories) {
            return $this->sendError('Not Found', false, 404);
        }
        
        return $this->sendResponse($categories, 'Fetch category success');
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
        $input['slug'] = strtolower(preg_replace('/\s+/', '-', $input['title']));
        $input['created_at'] = date('Y-m-d h:i:s');
        $input['updated_at'] = date('Y-m-d h:i:s');
        $create = Categories::create($input);

        return $this->sendResponse($create, "Submit category success", 201);
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
        $input['slug'] = Str::slug($input['title']);
        $input['updated_at'] = date('Y-m-d h:i:s');
        Categories::whereId($id)->update($input);
        $update = Categories::where('id', $id)->first();

        return $this->sendResponse($update, "Update category success");
    }

    /**
     * Restore the specific deleted resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restoreById($id) {
        $categories = Categories::whereId($id)->withTrashed()->restore();

        if (!$categories) {
            return $this->sendError('Not Found', false, 404);
        }
        
        return $this->sendResponse(null, 'Restore category success');
    }

    /**
     * Remove the specific resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteById($id) {
        $categories = Categories::whereId($id)->delete();

        if (!$categories) {
            return $this->sendError('Not Found', false, 404);
        }
        
        return $this->sendResponse(null, 'Delete category success');
    }
}
