<?php

namespace App\Http\Controller;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class LoginController extends Controller {
	public function login(Request $request)
	{
		$credentials = $request->only('email', 'password');

		try {
			if (!$token = JWTAuth::attempt($credentials)) {
				return response()->json(['error' => 'invalid_credentials'], 400);
			}
		} catch (JWTException $e) {
			return response()->json(['error' => 'could_not_create_token'], 500);
		}

		return response()->json(compact('token'));
	}

	public function register(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'name' => 'required|string|max:255',
			'email' => 'required|string|email|max:255|unique:users',
			'password' => 'required|string|min:6|confirmed',
		]);

		if ($validator->fails()) {
			return response()->json($validator->errors()->toJson(), 400);
		}

		$user = User::create([
			'name' => $request->get('name'),
			'email' => $request->get('email'),
			'password' => Hash::make($request->get('password')),
		]);

		$token = JWTAuth::fromUser($user);

		return response()->json(compact('user', 'token'), 201);
	}

	public function getAuthenticatedUser()
	{
		try {

			if (!$user = JWTAuth::parseToken()->authenticate()) {
				return response()->json(['user_not_found'], 404);
			}
		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

			return response()->json(['token_expired'], $e->getStatusCode());
		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

			return response()->json(['token_invalid'], $e->getStatusCode());
		} catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

			return response()->json(['token_absent'], $e->getStatusCode());
		}

		return response()->json(compact('user'));
	}
	public function authenticate(Request $request)
	{
		// check email & password
		$email = $request->post('email');
		$password = $request->post('password');
		$count = User::where(['email'=>$email, 'password'=>Hash::make($password)])->count();

		$isExist = false;
		if($count > 0)
		{
			$isExist = 1;
		}

		// counter attempt
		$key = 'attemp_'.Session::getId();
		$count = Session::get($key, null);

		if($count === null)
		{
			$count = 0;
		} else {
			$count+=1;
		}

		Session::put($key, $count);

		// is email verified
	}
}