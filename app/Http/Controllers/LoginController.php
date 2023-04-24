<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    function login(Request $request)
    {
        $phone = $request->phone;
        $password = $request->password;
        $isUser = Store::where([
            'phone' => $phone,
            'password' => $password
        ])->get();

        if ($isUser->count() > 0) {
            return response()->json([
                'data' => $isUser,
                'message' => 'OK'
            ], 200);
        }else{
            return response()->json([
                'message' => 'Login Failed'
            ], 401);
        }
    }

    public function sanity()
    {
        return response('hello', 200);
    }
}
