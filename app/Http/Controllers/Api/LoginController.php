<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseFormat;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->format = new ApiResponseFormat();
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'password' => 'required|string|max:100',
                'email' => 'required|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json($this->format->formatResponseWithPages("Validation Error !", $this->format->STAT_BAD_REQUEST(), $validator->errors()->all()));
            }

            $attemptLogin = Auth::attempt(['email' => $request->email, 'password' => $request->password]);

            if ($attemptLogin) {
                $user = Auth::user();
                $data['user'] = $request->email;
                $data['token'] = $user->createToken('nApp')->accessToken;
                return response()->json($this->format->formatResponseWithPages("Success", $this->format->STAT_OK(), $data));
            } else {
                return response()->json($this->format->formatResponseWithPages("Authentication Failed !", $this->format->STAT_UNAUTHORIZED()), 401);
            }
        } catch (\QueryException$th) {
            return response()->json($this->format->formatResponseWithPages("Error SQL", $this->format->STAT_INTERNAL_SERVER_ERROR(), $th->getMessage()), 500);
        } catch (\Exception$th) {
            return response()->json($this->format->formatResponseWithPages("Internal Server Error", $this->format->STAT_INTERNAL_SERVER_ERROR(), $th->getMessage()), 500);
        }
    }

    public function register(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:users',
                'username' => 'required|string|unique:users,name',
                'password' => 'required|confirmed|string',
            ]);

            if ($validator->fails()) {
                return response()->json($this->format->formatResponseWithPages("Validation Error !", $this->format->STAT_BAD_REQUEST(), $validator->errors()->all()), $this->format->STAT_BAD_REQUEST());
            }

            $user = User::create([
                'password' => bcrypt($request->password),
                'email' => $request->email,
                'name' => $request->username,
            ]);

            $user = User::findOrFail($user->id);

            $token = $user->createToken('nApp')->accessToken;

            $user['token'] = $token;

            DB::commit();

            return response()->json($this->format->formatResponseWithPages("Success", $this->format->STAT_OK(), $user));

        } catch (\QueryException$th) {
            DB::rollback();
            return response()->json($this->format->formatResponseWithPages("Error SQL", $this->format->STAT_INTERNAL_SERVER_ERROR(), $th->getMessage()), 500);
        } catch (\Exception$th) {
            DB::rollback();
            return response()->json($this->format->formatResponseWithPages("Internal Server Error", $this->format->STAT_INTERNAL_SERVER_ERROR(), $th->getMessage()), 500);
        }
    }
}
