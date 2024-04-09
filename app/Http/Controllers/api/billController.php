<?php

namespace App\Http\Controllers\api;

use App\Exports\BillsExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\billResource;
use App\Models\bill;
use App\Models\order;
use App\Models\student;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class billController extends Controller
{

    public function excelBill(){
      
        return Excel::download(new BillsExport, 'bills.xlsx');
         
    }

    public function bill(){
        
        $bills = bill::all();
        return billResource::collection($bills);
    }
    public function getBill(Request $request){
        if($request->has('bill')){

            $bills = bill::where('bill_name','LIKE','%'.$request->bill.'%')->latest()->paginate(10)->withQueryString();
            return billResource::collection($bills);

        }
        $bills = bill::latest()->paginate(10)->withQueryString();
        return billResource::collection($bills);
    }

    public function showBill(Bill $bill){
        return response()->json([
            'data' => $bill
        ]);
    }

    public function createBill(Request $request){
        $validate = Validator::make($request->all(),[
            'bill_name' => ['required'],
        ],[
            'required' => 'Inputan ini wajib di isi'
        ]);

        if($validate->fails()){
            return response()->json($validate->errors(),422); 
        }

        try{
            $bill = bill::create($request->all());
            return response()->json([
                'message' => 'Data berhasil di buat',
                'data' => $bill
            ],201);
        }catch(QueryException $e){
            return response()->json([
                'message'=>$e->errorInfo
            ]);
        }
    }

    public function setBill(Request $request, Bill $bill){
        $bill = bill::findOrFail($bill->id);
        $validate = Validator::make($request->all(),[
            'bill_name' => ['required'],
        ],[
            'required' => 'Inputan ini wajib di isi'
        ]);

        if($validate->fails()){
            return response()->json($validate->errors(),422); 
        }

        try{
            $bill->update($request->all());
            return response()->json([
                'message' => 'Data berhasil di update',
                'data' => $bill
            ]);
        }catch(QueryException $e){
            return response()->json([
                'message'=>$e->errorInfo
            ]);
        }
    }

    public function dropBill(Bill $bill){
        $bill = bill::findOrFail($bill->id);

        try{
            $order = order::where('bill_id',$bill->id)->first();
            if($order == []){
                $bill->delete();
                return response()->json([
                    'meassage' => 'Data berhasil hapus'
                ],200); 
            }

            return response()->json([
                'meassage' => 'Mhon maaf data ini masih digunakan',
                'data' =>$order
            ],202);
        }catch(QueryException $e){
            return response()->json([

                'message'=>$e->errorInfo
            ],500);
        }
    }
}
