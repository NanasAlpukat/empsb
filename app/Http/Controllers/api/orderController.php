<?php

namespace App\Http\Controllers\api;

use App\Exports\OrdersExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\orderResource;
use App\Models\bill;
use App\Models\order;
use App\Models\student;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class orderController extends Controller
{
    
    public function excelOrder(){
      
        return Excel::download(new OrdersExport, 'orders.xlsx');
         
    }

    public function allOrder(Student $student){
        $order = order::where('student_id',$student->id)->get();
        return orderResource::collection($order);
    }
    public function showOrder(Student $student,Request $request){
        if($request->has('price')){

            $orders = order::where('student_id',$student->id)->where('bill_id','LIKE','%'.$request->price.'%')->latest()->paginate(5)->withQueryString();
            return orderResource::collection($orders);

        }
        else if($request->has('date')){

            $orders = order::where('student_id',$student->id)->where('expired_date','LIKE','%'.$request->date.'%')->latest()->paginate(5)->withQueryString();
            return orderResource::collection($orders);

        }
        $order = order::where('student_id',$student->id)->paginate(5);
        return orderResource::collection($order);
    }

    public function order(Order $order){
        $order = order::where('id',$order->id)->first();
        return new orderResource($order);
    }

    public function create(Student $student, Request $request){
            $student = student::findOrFail($student->id);
            $validate = Validator::make($request->all(),[
                'bill_id' => ['required'],
                'major_id' => ['required'],
                'status' => ['required'],
                'expired_date' => ['required'],
                'price' => ['required'],
            ],[
                'required' => 'Inputan ini wajib di isi'
            ]);
    
            if($validate->fails()){
                return response()->json($validate->errors(),422); 
            }
    
            try{

                $time = strtotime($request->expired_date);
                $expired_date = date('Y-m-d',$time);
                $order = order::create([
                    'major_id' => $request->major_id,
                    'student_id' => $student->id,
                    'bill_id' => $request->bill_id,
                    'status' => $request->status,
                    'expired_date' => $expired_date,
                    'price' => $request->price,
                ]);
            $data = new orderResource($order);
            $response = [
                'message' => 'Success',
                'data' => $data
            ]; 

            return response()->json($response,201);
            }catch(QueryException $e){
                return response()->json([
                    'message'=>$e->errorInfo
                ],500);
            }
        }

        public function setOrder(Request $request, Order $order){
            $order = order::findOrFail($order->id);
            $validate = Validator::make($request->all(),[
                'bill_id' => ['required'],
                'major_id' => ['required'],
                'status' => ['required'],
                'expired_date' => ['required'],
                'price' => ['required'],
            ],[
                'required' => 'Inputan ini wajib di isi'
            ]);
    
            if($validate->fails()){
                return response()->json($validate->errors(),422); 
            }
    
            try{
                $order->update($request->all());
                $data = new orderResource($order);
                $response = [
                    'message' => 'Success',
                    'data' => $data
                ]; 
    
                return response()->json($response,200);
            }catch(QueryException $e){
                return response()->json([
                    'message'=>$e->errorInfo
                ],500);
            }
        }
        public function dropOrder(Order $order){
            $order = order::findOrFail($order->id);
    
            try{
                $order->delete();
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
