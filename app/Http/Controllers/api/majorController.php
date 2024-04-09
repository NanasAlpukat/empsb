<?php

namespace App\Http\Controllers\api;

use App\Exports\MajorsExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\majorResource;
use App\Models\major;
use App\Models\student;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class majorController extends Controller
{
    public function excelMajor(){
      
        return Excel::download(new MajorsExport, 'majors.xlsx');
         
    }
    public function major(){
        
        $majors = major::all();
        return majorResource::collection($majors);
    }
    public function getMajor(Request $request){
        if($request->has('major')){

            $majors = major::where('major_name','LIKE','%'.$request->major.'%')->latest()->paginate(10)->withQueryString();
            return majorResource::collection($majors);

        }
        $majors = major::latest()->paginate(10);
        return majorResource::collection($majors);
    }

    public function showMajor(Major $major){
        return response()->json([
            'data' => $major
        ]);
    }

    public function createMajor(Request $request){
        $validate = Validator::make($request->all(),[
            'major_name' => ['required'],
        ],[
            'required' => 'Inputan ini wajib di isi'
        ]);

        if($validate->fails()){
            return response()->json($validate->errors(),422); 
        }

        try{
            $major = major::create($request->all());
            return response()->json([
                'message' => 'Data berhasil di buat',
                'data' => $major
            ],201);
        }catch(QueryException $e){
            return response()->json([
                'message'=>$e->errorInfo
            ]);
        }
    }

    public function setMajor(Request $request, Major $major){
        $major = major::findOrFail($major->id);
        $validate = Validator::make($request->all(),[
            'major_name' => ['required'],
        ],[
            'required' => 'Inputan ini wajib di isi'
        ]);

        if($validate->fails()){
            return response()->json($validate->errors(),422); 
        }

        try{
            $major->update($request->all());
            return response()->json([
                'message' => 'Data berhasil di update',
                'data' => $major
            ]);
        }catch(QueryException $e){
            return response()->json([
                'message'=>$e->errorInfo
            ]);
        }
    }

    public function dropMajor(Major $major){
        $major = major::findOrFail($major->id);

        try{

            $student = student::where('major_id',$major->id)->first();
            if($student == []){
                $major->delete();
                return response()->json([
                    'meassage' => 'Data berhasil hapus'
                ],200);
            }

            return response()->json([
                'meassage' => 'Mhon maaf data ini masih digunakan',
                'data' =>$student
            ],202);


            
        }catch(QueryException $e){
            return response()->json([

                'message'=>$e->errorInfo
            ],500);
        }
    }
}
