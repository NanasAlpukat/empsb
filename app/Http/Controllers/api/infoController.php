<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\infoResource;
use App\Http\Resources\pengumumanResource;
use App\Models\info;
use App\Models\pengumuman;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class infoController extends Controller
{


    public function get(){
            $info = info::latest()->paginate(5)->withQueryString();
            return infoResource::collection($info);
    }

    public function getPengumuman(){
            $pemgumuman = pengumuman::latest()->paginate(5)->withQueryString();
            return infoResource::collection($pemgumuman);
        
    }


    public function createPengumuman(Request $request){
        $validate = Validator::make($request->all(),[

            'title' => ['required'],
            'body' => ['required'],
            'date' => ['required'],
        ],

        [
            'required' => 'Inputan ini tidak boleh kosong',
        ]);

        if($validate->fails()){
            return response()->json($validate->errors(),422); 
        }

        try{
            $time = strtotime($request->date);
            $tanggal = date('Y-m-d',$time);
            $pengumuman = pengumuman::create([
                'title' => $request->title,
                'body' => $request->body,
                'date' => $tanggal,
            ]);

            $data = new pengumumanResource($pengumuman);
            $response = [
                'message' => 'Data berhasil dibuat',
                'data' => $data
            ]; 

            return response()->json($response,201);

        }catch(QueryException $e){
            return response()->json([
                'message'=> $e->errorInfo
            ],500);
        }
    }



    public function createInfo(Request $request){
        $validate = Validator::make($request->all(),[

            'title' => ['required'],
            'body' => ['required'],
            'date' => ['required'],
            'image' => ['image','mimes:png,jpg,jpeg','max:500']
        ],

        [
            'required' => 'Inputan ini tidak boleh kosong',
            'mimes' => 'Extensi tidak falid',
            'max:500' => 'Ukuran hanya boleh 500 kb'
        ]);
    


        if($validate->fails()){
            return response()->json($validate->errors(),422); 
        }

        try{
            $extends = $request->file('image')->getClientOriginalExtension();
            $extends = strtolower($extends);
            $imageName = rand().'.'.$extends; 

            $request->file('image')->storeAs('images',$imageName);
            $img = info::getImage($imageName);
              
            $time = strtotime($request->date);
            $tanggal = date('Y-m-d',$time);

            $info = info::create([
                'title' => $request->title,
                'body' => $request->body,
                'date' => $tanggal,
                'image' => $img
            ]);

            $data = new infoResource($info);
            $response = [
                'message' => 'Data berhasil dibuat',
                'data' => $data
            ]; 

            return response()->json($response,201);

        }catch(QueryException $e){
            return response()->json([
                'message'=> $e->errorInfo
            ],500);
        }
}

public function setInfo(Info $info, Request $request){
    // return response()->json([
    //     'messsage' => $student
    // ],200);
    // die;

    $info = info::findOrFail($info->id);
    
    if($request->file('image')){

        $validate = Validator::make($request->all(),[

            'title' => ['required'],
            'body' => ['required'],
            'date' => ['required'],
            'image' => ['image','mimes:png,jpg,jpeg','max:500']
        ],

        [
            'required' => 'Inputan ini tidak boleh kosong',
            'mimes' => 'Extensi tidak falid',
            'max:500' => 'Ukuran hanya boleh 500 kb'
        ]);

        if($validate->fails()){
            return response()->json($validate->errors(),422); 
        }
    }

    $validate = Validator::make($request->all(),[

        'title' => ['required'],
        'body' => ['required'],
        'date' => ['required'],
        
    ],

    [
        'required' => 'Inputan ini tidak boleh kosong',
        
    ]);
    

    if($validate->fails()){
        return response()->json($validate->errors(),422); 
    }

    try{
        $time = strtotime($request->date);
        $tanggal = date('Y-m-d',$time);

        
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
            $img = info::getImage($imageName);


           
            $info->update([
                
                'title' => $request->title,
                'body' => $request->body,
                'date' => $tanggal,
                'image' => $img
            ]);
            $data = new infoResource($info);
                $response = [
                    'message' => 'Data berhasil di ubah',
                    'data' => $data
                ]; 
    
            return response()->json($response,200);
            }
        
            $info->update([
                
                'title' => $request->title,
                'body' => $request->body,
                'date' => $tanggal,
                    
            ]);
            $data = new infoResource($info);
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



public function setPengumuman($id,Request $request){
    $pengumuman = pengumuman::findOrFail($id);
    $validate = Validator::make($request->all(),[

        'title' => ['required'],
        'body' => ['required'],
        'date' => ['required'],
    ],

    [
        'required' => 'Inputan ini tidak boleh kosong',
    ]);

    if($validate->fails()){
        return response()->json($validate->errors(),422); 
    }

    try{
        $time = strtotime($request->date);
        $tanggal = date('Y-m-d',$time);
        $pengumuman->update([
            'title' => $request->title,
            'body' => $request->body,
            'date' => $tanggal,
        ]);

        $data = new pengumumanResource($pengumuman);
        $response = [
            'message' => 'Data berhasil diubah',
            'data' => $data
        ]; 

        return response()->json($response,200);

    }catch(QueryException $e){
        return response()->json([
            'message'=> $e->errorInfo
        ],500);
    }
}



public function dropInfo(Info $info){
    $info = info::findOrFail($info->id);
    $image = $info->image;
    try{
        $nameImg = explode('/',$image);
        Storage::delete('images/'.$nameImg[5]);
        $info->delete();
        return response()->json([
            'meassage' => 'Data  berhasil di hapus'
        ],200);
    }catch(QueryException $e){
        return response()->json([
            'message'=>$e->errorInfo
        ],500);
    }
}

public function dropPengumuman($id){
    
    $pemgumuman = pengumuman::findOrFail($id);


    try{
        $pemgumuman->delete();
        return response()->json([
            'meassage' => 'Data  berhasil di hapus'
        ],200);
    }catch(QueryException $e){
        return response()->json([
            'message'=>$e->errorInfo
        ],500);
    }
}


}
