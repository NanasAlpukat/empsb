<?php

namespace App\Http\Controllers\api;

use App\Exports\StudentsExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\studentResource;
use App\Models\student;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class studentController extends Controller
{
    public function exportExel(){
        // return response()->json([
        //     'message' =>'berhasil'
        // ]);
        // die;
        return Excel::download(new StudentsExport, 'students.xlsx');
         
    }

    public function auth($id){
        $student = student::where('user_id',$id)->first();
        return new studentResource($student);
    }

    public function getStudent(Request $request){
        if($request->has('name')){

            $students = student::where('name','LIKE','%'.$request->name.'%')->latest()->paginate(10)->withQueryString();
            return studentResource::collection($students);

        }elseif($request->has('major')){
            
            $students = student::where('major_id','LIKE','%'.$request->major.'%')->latest()->paginate(10)->withQueryString();
            return studentResource::collection($students);

        }elseif($request->has('class')){
            // $class = intval($request->class);
            $students = student::where('class','LIKE','%'.$request->class.'%')->latest()->paginate(10)->withQueryString();
            return studentResource::collection($students);

        }
        
        $students = student::latest()->paginate(10)->withQueryString();
        return studentResource::collection($students);


    }

    public function showStudent(Student $student){
        return new studentResource($student);
    }

    public function createStudent(Request $request){
        if($request->file('image')){
        $validate = Validator::make($request->all(),[

            'major_id' => ['required'],
            'nis' => ['required','unique:students'],
            'name' => ['required'],
            'email' => ['required','email','unique:users'],
            'age' => ['required'],
            'date_of_birth' => ['required'],
            'kelas' => ['required','in:10,11,12'],
            'gender' => ['required','in:male,female'],
            'no_phone' => ['required'],
            'parents_name' => ['required'],
            'address' => ['required'],
            'image' => ['required','mimes:png,jpg,jpeg','max:500']
        ],

        [
            'required' => 'Inputan ini tidak boleh kosong',
            'in' => 'Field harus sesuai',
            'email' => 'Inputan ini harus bertipe email',
            'unique' => 'Inputan ini harus unique',
            'mimes' => 'Extensi tidak falid',
            'max:500' => 'Ukuran hanya boleh 500 kb'
        ]);
    }else{
        $validate = Validator::make($request->all(),[

            'major_id' => ['required'],
            'nis' => ['required','unique:students'],
            'name' => ['required'],
            'email' => ['required','email','unique:users'],
            'age' => ['required'],
            'date_of_birth' => ['required'],
            'kelas' => ['required','in:10,11,12'],
            'gender' => ['required','in:male,female'],
            'no_phone' => ['required'],
            'parents_name' => ['required'],
            'address' => ['required'],
            
        ],

        [
            'required' => 'Inputan ini tidak boleh kosong',
            'in' => 'Field harus sesuai',
            'email' => 'Inputan ini harus bertipe email',
            'unique' => 'Inputan ini harus unique',
            
        ]);
    }


        if($validate->fails()){
            return response()->json($validate->errors(),422); 
        }

        try{
            $user = User::create([
                    'email' => $request->email,
                    'role' => 'student',
                    'password' => Hash::make('rahasia')
            ]);
            

            if($request->file('image')){
                $extends = $request->file('image')->getClientOriginalExtension();
                $extends = strtolower($extends);
                $imageName = rand().'.'.$extends; 

                $request->file('image')->storeAs('images',$imageName);
                $img = student::getImage($imageName);
              }else{
                  $img = '';
              }
            $time = strtotime($request->date_of_birth);
            $tgl_lahir = date('Y-m-d',$time);

            $request->request->add(['user_id' => $user->id]);
            $student = student::create([
                'user_id' => $request->user_id,
                'major_id' => $request->major_id,
                'nis' => $request->nis,
                'name' => $request->name,
                'age' => $request->age,
                'date_of_birth' => $tgl_lahir,
                'class' => $request->kelas,
                'gender' => $request->gender,
                'no_phone' => $request->no_phone,
                'parents_name' => $request->parents_name,
                'address' => $request->address,
                'image' => $img
            ]);

            $data = new studentResource($student);
            $response = [
                'message' => 'Success',
                'data' => $data
            ]; 

            return response()->json($response,201);

        }catch(QueryException $e){
            return response()->json([
                'message'=> $e->errorInfo
            ],500);
        }
}

public function dropStudent(Student $student){
    $user = user::findOrFail($student->user_id);
    $image = $student->image;
    try{
        $nameImg = explode('/',$image);
        Storage::delete('images/'.$nameImg[5]);
        $user->delete();
        return response()->json([
            'meassage' => 'Murid  berhasil di hapus'
        ],200);
    }catch(QueryException $e){
        return response()->json([

            'message'=>$e->errorInfo
        ],500);
    }
}


public function test(Student $student, Request $request){
    $data = $request->name;
    return response()->json([
        'message' => $data
    ],200);
    
}

public function setStudent(Student $student, Request $request){
    // return response()->json([
    //     'messsage' => $student
    // ],200);
    // die;

    $student = student::findOrFail($student->id);
    $user = user::findOrFail($student->user_id);

    



    if($request->file('image')){

    $validate = Validator::make($request->all(),[

        'major_id' => ['required'],
        'nis' => ['required'],
        'name' => ['required'],
        'email' => ['required','email'],
        'age' => ['required'],
        'date_of_birth' => ['required'],
        'kelas' => ['required','in:10,11,12'],
        'gender' => ['required','in:male,female'],
        'no_phone' => ['required'],
        'parents_name' => ['required'],
        'address' => ['required'],
        'image' => ['required','mimes:png,jpg,jpeg','max:500']
    ],

    [
        'required' => 'Inputan ini tidak boleh kosong',
        'in' => 'Field harus sesuai',
        'email' => 'Inputan ini harus bertipe email',
        'unique' => 'Inputan ini harus unique',
        'mimes' => 'Extensi tidak falid',
        'max:500' => 'Ukuran hanya boleh 500 kb'
    ]);

    

    if($validate->fails()){
        return response()->json($validate->errors(),422); 
    }
    }

    $validate = Validator::make($request->all(),[

        'major_id' => ['required'],
        'nis' => ['required'],
        'name' => ['required'],
        'email' => ['required','email'],
        'age' => ['required'],
        'date_of_birth' => ['required'],
        'kelas' => ['required','in:10,11,12'],
        'gender' => ['required','in:male,female'],
        'no_phone' => ['required'],
        'parents_name' => ['required'],
        'address' => ['required'],
    ],

    [
        'required' => 'Inputan ini tidak boleh kosong',
        'in' => 'Field harus sesuai',
        'email' => 'Inputan ini harus bertipe email',
        'unique' => 'Inputan ini harus unique',
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
                
                    'major_id' => $request->major_id,
                    'nis' => $request->nis,
                    'name' => $request->name,
                    'age' => $request->age,
                    'date_of_birth' => $tgl_lahir,
                    'class' => $request->kelas,
                    'gender' => $request->gender,
                    'no_phone' => $request->no_phone,
                    'parents_name' => $request->parents_name,
                    'address' => $request->address,
                    'image' => $img
            ]);
            $data = new studentResource($student);
                $response = [
                    'message' => 'Success',
                    'data' => $data
                ]; 
    
            return response()->json($response,200);
            }
        
            $user->update($request->all());
            $student->update([
                
                    'major_id' => $request->major_id,
                    'nis' => $request->nis,
                    'name' => $request->name,
                    'age' => $request->age,
                    'date_of_birth' => $tgl_lahir,
                    'class' => $request->kelas,
                    'gender' => $request->gender,
                    'no_phone' => $request->no_phone,
                    'parents_name' => $request->parents_name,
                    'address' => $request->address,
                    
            ]);
            $data = new studentResource($student);
                $response = [
                    'message' => 'Success',
                    'data' => $data
                ]; 
    
            return response()->json($response,200);

    }catch(QueryException $e){
        return response()->json([

            'message'=> $e->errorInfo
        ],500);
    }

}
}
