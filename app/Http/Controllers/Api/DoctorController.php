<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function create(Request $request){
        try {
            $payload = [
                'name'=>$request->name,
                'mail'=>$request->mail,
                'phone'=>$request->phone,
                'degree'=>$request->degree,
                'status'=>$request->status,
            ];

            $doctor = Doctor::insert($payload);
            if ($doctor) {
                return response()->json([
                    'status'=>200,
                    'data'=>$doctor
                ],200);
            }
            else{
                return response()->json([
                    'status'=>404,
                    'data'=>'No records found'
                ],404);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function list(){
        try {
            $search_param = request()->search ?? null;
            $page = request()->page ;
    
            if ($search_param ||  $page) {
                $category = Doctor::where('name', 'like', '%' . $search_param . '%')->paginate(5, ['*'], 'page', $page ?? 1);
            }else{
                $doctors = Doctor::get();

            }
            if ($doctors->count() > 0) {
                return response()->json([
                    'status'=>200,
                    'data'=>$doctors
                ],200);
            }
            else{
                return response()->json([
                    'status'=>404,
                    'data'=>'No records found'
                ],404);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
