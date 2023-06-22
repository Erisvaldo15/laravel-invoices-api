<?php

namespace App\Http\Controllers;

use App\Traits\HttpResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller {

    use HttpResponse;

    public function __construct()
    {
        $this->middleware("auth:sanctum")->only("logout");
    }

    public function login(Request $request) {

        if(Auth::attempt($request->only(["email", "password"]))) {
            return $this->success("Authorized", 200, [
                "token" => $request->user()->createToken("invoice")->plainTextToken,
                "data" => $request->user(),
            ]);
        }


        return $this->error("Unauthorized", 401);
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return $this->success("Logout realized with success");
    }

}