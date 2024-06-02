<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class LoginController extends Controller
{
    public function login(Request $request){
        try {
            $storeId = $request->storeId;
            $password = $request->password;


            $checkCreds = DB::table('store')->where([
                'store_meta_id' => $storeId,
                'store_pass_key' => $password 
            ])->first();
            // dd($storeId, $password);
            if ($checkCreds) {
                $request->session()->put('storeId', $storeId);
                return redirect('/store/dashboard');
            }else{
                return redirect('/login-page');
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function logout(Request $request)
    {
        // dd($request->session()->flush());
        $request->session()->flush();

        return redirect('/login-page')->with('status', 'You have been logged out.');
    }
}
