<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\historyResource;
use App\Http\Resources\orderResource;
use App\Http\Resources\studentResource;
use App\Models\history;
use App\Models\order;
use App\Models\student;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class frontendController extends Controller
{
    public function setting(Student $student, Request $request){
        // if($request->oldimg){
        //     $oldfoto = '1713083113.jpg';
        //     Storage::delete('images/1403850651.jpg');
        // }
        // $oldImg = $request->oldimg;
        // $pecah = explode('/',$oldImg);

        // return response()->json([
        //     'messsage' => 'berhasil',
        //     'image' => $pecah[5]
        // ],200);

        // die;
        // $student = student::findOrFail($student->id);
        // $extends = $request->file('image')->getClientOriginalExtension();
        // $extends = strtolower($extends);
        // $imageName = rand().'.'.$extends; 

        // $request->file('image')->storeAs('images',$imageName);
        // $img = student::getImage($imageName);
       


        // $student->update([
        //     'image' => $img
        // ]);

        // // $img = new student();
        // // $img->image = $imageName;
        // // $img->update();

        // return response()->json([
        //     'messsage' => 'berhasil',
        //     'image' => $student
        // ],200);
        // die;
    
        $student = student::findOrFail($student->id);
        $user = user::findOrFail($student->user_id);
    
        if($request->file('image')){
    
        $validate = Validator::make($request->all(),[
    
            'name' => ['required'],
            'email' => ['required','email'],
            'age' => ['required'],
            'date_of_birth' => ['required'],
           
            'gender' => ['required','in:male,female'],
            'no_phone' => ['required'],
            'parents_name' => ['required'],
            'address' => ['required'],
            'image' => ['image','mimes:png,jpg,jpeg','max:1000']
        ],
    
        [
            'required' => 'Inputan ini tidak boleh kosong',
            
            'email' => 'Inputan ini harus bertipe email',
            
            'mimes' => 'Extensi tidak falid',
            'image' => 'Yang anda upload bukan image',
            'max:500' => 'Ukuran hanya boleh 500 kb'
        ]);
    
        
    
        if($validate->fails()){
            return response()->json($validate->errors(),422); 
        }
        }
    
        $validate = Validator::make($request->all(),[
    
           
            'name' => ['required'],
            'email' => ['required','email'],
            'age' => ['required'],
            'date_of_birth' => ['required'],
            'gender' => ['required','in:male,female'],
            'no_phone' => ['required'],
            'parents_name' => ['required'],
            'address' => ['required'],
        ],
    
        [
            'required' => 'Inputan ini tidak boleh kosong',
            'email' => 'Inputan ini harus bertipe email',
            
        ]);
    
        
    
        if($validate->fails()){
            return response()->json($validate->errors(),422); 
        }
    
    
    
    
        try{
            $time = strtotime($request->date_of_birth);
            $tgl_lahir = date('Y-m-d',$time);
    
            
            if($request->file('image')){
                if($request->oldimg){
                    $oldImg = $request->oldimg;
                    $nameImg = explode('/',$oldImg);
                    Storage::delete('images/'.$nameImg[5]);
                }
                $extends = $request->file('image')->getClientOriginalExtension();
                $extends = strtolower($extends);
                $imageName = rand().'.'.$extends; 

                $request->file('image')->storeAs('images',$imageName);
                $img = student::getImage($imageName);
    
    
                $user->update($request->all());
                $student->update([
                    
                        
                        'name' => $request->name,
                        'age' => $request->age,
                        'date_of_birth' => $tgl_lahir,
                        'gender' => $request->gender,
                        'no_phone' => $request->no_phone,
                        'parents_name' => $request->parents_name,
                        'address' => $request->address,
                        'image' => $img
                ]);
                $data = new studentResource($student);
                    $response = [
                        'message' => 'Data berhasil di ubah',
                        'data' => $data
                    ]; 
        
                return response()->json($response,200);
                }
            
                $user->update($request->all());
                $student->update([
                    
                        
                        'name' => $request->name,
                        'age' => $request->age,
                        'date_of_birth' => $tgl_lahir,
                        'gender' => $request->gender,
                        'no_phone' => $request->no_phone,
                        'parents_name' => $request->parents_name,
                        'address' => $request->address,
                        
                ]);
                $data = new studentResource($student);
                    $response = [
                        'message' => 'Data berhasil di ubah',
                        'data' => $data
                    ]; 
        
                return response()->json($response,200);
    
        }catch(QueryException $e){
            return response()->json([
    
                'message'=> $e->errorInfo
            ],500);
        }
    
    }
    

    public function showOrder(Student $student){
        $waktu = Carbon::now();
        $order = order::where('student_id',$student->id)
        ->whereDate('expired_date' , '>=', $waktu)->get();
        
        return orderResource::collection($order);
    }
    public function showHistory(Student $student){
        $history = history::where('student_id',$student->id)->paginate(10)->withQueryString();
        return historyResource::collection($history);
    }

    public function historyAll(Student $student,Request $request){
        if($request->has('date')){
            $history = history::where('student_id',$student->id)->where('expired_date','LIKE','%'.$request->date.'%')->get();
            return historyResource::collection($history);
        }

        $history = history::where('student_id',$student->id)->get();
        return historyResource::collection($history);
        
    }

    public function dropHistory(History $history){
        $history = history::findOrFail($history->id);

        try{
            $history->delete();
            return response()->json([
                'meassage' => 'Data berhasil hapus'
            ],200);
        }catch(QueryException $e){
            return response()->json([

                'message'=>$e->errorInfo
            ],500);
        }
    }  
}
