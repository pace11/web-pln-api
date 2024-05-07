<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\ResponseController as ResponseController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;

class UserController extends ResponseController
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $input = $request->all();

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);       
        }

        $found_user = User::where('email', $input['email'])->first();
        
        if ($found_user) {
            return $this->sendError('The email address you specified is already in use', false, 409);
        }

        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        return $this->sendResponse($found_user, "Register user success");
    }

    public function login(Request $request) {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $user_token = $user->createToken('MyApp');
            $success['token'] = $user_token->accessToken; 
            $success['expires_at'] = $user_token->token->expires_at;

            return $this->sendResponse($success, 'Login success');
        } 

        return $this->sendError('Unauthorized', false, 401);
    }

    public function logout() {
        if(Auth::guard('api')->check()){
            $accessToken = Auth::guard('api')->user()->token();

                \DB::table('oauth_refresh_tokens')
                    ->where('access_token_id', $accessToken->id)
                    ->update(['revoked' => true]);
            $accessToken->revoke();

            return $this->sendResponse(null, 'Logout success');
        }

        return $this->sendError('Unauthorized', false, 401);
    }

    public function forgotPassword(Request $request) {
        $user = User::where('email', $request->email)->first();
        
        if ($user) {
            $user_token = $user->createToken('MyApp');
            $success['token'] = $user_token->accessToken; 
            $success['expires_at'] = $user_token->token->expires_at;

            return $this->sendResponse($success, 'Token forgot password created');
        }
        
        return $this->sendError('Email not found', false, 404);
    }

    public function updatePassword(Request $request) {
        $user = Auth::guard('api')->user();
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $update = User::whereId($user->id)->update($input);
        if($update && Auth::guard('api')->check()){
            $accessToken = Auth::guard('api')->user()->token();

                \DB::table('oauth_refresh_tokens')
                    ->where('access_token_id', $accessToken->id)
                    ->update(['revoked' => true]);
            $accessToken->revoke();

            return $this->sendResponse(null, 'Change password success');
        }
    }

    public function me() {
        $user = Auth::guard('api')->user();
        return $this->sendResponse($user, 'Get user success');
    }

    public function showById($id) {
        $user = User::whereId($id)->first();
        return $this->sendResponse($user, 'Get user profile success');
    }
}
