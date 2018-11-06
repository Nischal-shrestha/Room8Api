<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['jwt.auth'], ['except' => ['login', 'register', 'refresh']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        if ($request->has(['email', 'password'])) {
            $credentials = $request->only('email', 'password');
        } else if ($request->has(['username', 'password'])) {
            $credentials = $request->only('username', 'password');
        } else {
            abort(401);
        }

        if (!$token = auth()->attempt($credentials)) {
            abort(401);
        }

        return $this->respondWithToken($token);
    }

    public function register(Request $request)
    {
        $validate = Validator::make($request->all(), User::$storeRules);
        if (!$validate->fails()) {
            $user = new User($request->all());
            $user->password = bcrypt($request->password);
            if ($user->save()) {
                return response()->json(["status" => "CREATED"], 201); // 201 - created
            }
            return response()->json(["status" => "FAILED"], 400);
        } else {
            $errors["status"] = "FAILED";
            $errors["errors"] = $validate->errors();
            return response()->json($errors, 422);// 422 - unprocessable request
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function payload()
    {
        return auth()->payload();
    }
}