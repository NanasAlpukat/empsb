<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\paymentResource;
use App\Http\Resources\xenditResource;
use App\Mail\SendEmail;
use App\Models\history;
use App\Models\mid;
use App\Models\order;
use App\Models\payxendit;
use App\Models\student;
use App\Models\transaction;
use App\Models\transaksi;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

use Xendit\Xendit;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

class paymentController extends Controller
{
    private $token = 'xnd_development_NEltYZegJJcqgJJl855xbvjho9d42DCVIAdny6T0PV5iDFynzlCECWAOlsLe';


    public function getXenditPay($id){
            $student = student::findOrFail($id);
            $payment = payxendit::where('student_id',$student->id)->get();
            return xenditResource::collection($payment);
    }

    public function cencelXenditPay($id){
       $payment = payxendit::where('id',$id)->firstOrFail();
        $payment->delete();
        return response()->json(['message' => 'berhasil di batalkan']);
    }

    public function paid(Request $request){
        $payxendit = payxendit::where('external_id',$request->external_id)->firstOrFail();
        if($payxendit){
            if($payxendit->expected_amount == $request->amount &&  $payxendit->bank_code == $request->bank_code ){
                date_default_timezone_set('Asia/Jakarta');
                $time = date('H:i:s');
                $payxendit->update([
                    'status' => "COMPLETED",
                    'order_time' => $time
                ]);
                $order = order::where('id',$payxendit->order_id)->firstOrFail();
                $order->update(['status' => 'lunas']);

                history::create([
                    'major_name' => $order->major->major_name,
                    'student_id' => $order->student_id,
                    'bill_name' => $order->bill->bill_name,
                    'status' => $order->status,
                    'history_time' => $time,
                    'price' => $order->price,
                ]);

                $email = [
                    'title' => 'KONFIRMASI TRANSAKSI',
                    'body' => 'Terimaksih sudah menyelesaikan pembayaran',
                    'name' => $payxendit->name, 
                    'major_name' => $order->major->major_name,
                    'student_id' => $order->student_id,
                    'bill_name' => $order->bill->bill_name,
                    'status' => $order->status,
                    'expired_date' => $order->expired_date,
                    'price' => $order->price,
                ];
                 Mail::to($payxendit->email)->send(new SendEmail($email));
                // $payxendit->delete();
                //  $order->delete();

                return response()->json([
                    'data' => $payxendit,
                    'message' => 'success'
                ],200);
        }
        return response()->json([
            'message' => 'gagal'
        ],500);
    }
        return response()->json([
            'message' => 'mohon maaf data tidak ada'
        ],422);
    }


    public function callback(Request $request){

        // return 'culun';
        // die;
        $payxendit = payxendit::where('external_id',$request->external_id)->firstOrFail();
        if($payxendit){
            if($request->status == 'ACTIVE' && $payxendit->xendit_id == $request->id){
                date_default_timezone_set('Asia/Jakarta');
                $time = date('H:i:s');
                $payxendit->update([
                    'status' => 'Menunggu pembayaran',
                    'order_time' => $time
                ]);

                return response()->json([
                    'data' => $payxendit,
                    'message' => 'active'
                ],200);
             }
            if($request->status == 'INACTIVE' && $payxendit->xendit_id == $request->id){
                date_default_timezone_set('Asia/Jakarta');
                $time = date('H:i:s');
                $payxendit->update([
                    'status' => 'Expired',
                    'order_time' => $time
                ]);

                return response()->json([
                    'data' => $payxendit,
                    'message' => 'expired'
                ],200);
             }
        }
        return response()->json([
            'message' => 'mohon maaf data tidak ada'
        ],500);
    }


    public function expired(Request $request){
        // return response()->json([
        //     'message' => 'berhasil'
        // ],200);
        // die;
        $payxendit = payxendit::where('external_id',$request->external_id)->firstOrFail();
        if ($request->status == 'PAID' && $payxendit->expected_amount == $request->amount && $payxendit->bank_code == $request->bank_code) {
            date_default_timezone_set('Asia/Jakarta');
            $time = date('H:i:s');
            $payxendit->update([
                'status' => $request->status,
                'order_time' => $time
            ]);
            // $payxendit->delete();
            return response()->json([
                // 'data' => $payxendit,
                'message' => 'paid'
            ],200);
         }else if($request->status == 'INACTIVE' && $payxendit->expected_amount == $request->amount && $payxendit->bank_code == $request->bank_code){
            date_default_timezone_set('Asia/Jakarta');
            $time = date('H:i:s');
            $payxendit->update([
                'status' => $request->status,
                'order_time' => $time
            ]);
            // $payxendit->delete();
            return response()->json([
                // 'data' => $payxendit,
                'message' => 'expired'
            ],200);
         }

         return response()->json([
            'message' => 'mohon maaf data tidak ada'
        ],500);
    }

    public function pesanXendit(Request $request){
        // $myTime = Carbon::now();
        // $myTime->toDateTimeString();
        // $myTime->toRfc850String();
        // $time = $myTime->toRfc850String();
        // $time = $myTime->toDateTimeString();
       
        // date_default_timezone_set('Asia/Jakarta');
        // $time = date('H:i:s');
        
        // return response()->json([
        //     'time'=> $time
        // ]);
        // die;

        if($request->bank_code == 'BCA' &&  $request->price < '10000'){
            return response()->json([
                'message' => 'Proses ini tidak bisa dilakukan mohon beralih ke bank lain'
            ],202);
        }


        $waktu =  Carbon::now();
        $t = strtotime($waktu);
        date('Y-m-d',$t);
        date_default_timezone_set('Asia/Jakarta');
        $timenow = date('Y-m-d H:i:s');

        $client = New Client();
        $response = $client->post('https://api.xendit.co/callback_virtual_accounts',
        [
            'headers'=>[
                'Accept' => 'application/json',
                'Authorization' => 'Basic eG5kX2RldmVsb3BtZW50X05FbHRZWmVnSkpjcWdKSmw4NTV4YnZqaG85ZDQyRENWSUFkbnk2VDBQVjVpREZ5bnpsQ0VDV0FPbHNMZTo=',
                'Content-Type' => 'application/json'
            ],
            'body'=> json_encode([
                "external_id" =>  \uniqid(),
                "bank_code" =>   $request->bank_code,
                "name" =>  $request->name,
                "is_single_use" => false,
                "is_closed" => false,
                "expected_amount" => $request->price,
                "expiration_date" => $timenow
                // "expiration_date" => Carbon::now()->addDay(1)->toISOString()
                ])
        ]);
        $data = json_decode($response->getBody());
        // return response()->json([
        //     'data' => $data,
        //     'expirad' => $data->expiration_date
        // ]);
        // die;

        $time = strtotime($data->expiration_date);
        $tanggal = date('Y-m-d',$time);

        date_default_timezone_set('Asia/Jakarta');
        $time = date('H:i:s');
        $order = order::findOrFail($request->id);
        $xenditPay = payxendit::create([
            'student_id' => $order->student_id,
            'order_id' => $order->id,
            'external_id' => $data->external_id,
            'xendit_id' => $data->id,
            'bank_code' => $data->bank_code,
            'name' => $order->student->name,
            'email' => $order->student->user->email,
            'status' => $data->status,
            'currency' => $data->currency,
            'account_number' => $data->account_number,
            'expiration_date' => $tanggal,
            'expected_amount' => $data->expected_amount,
            'order_time' => $time,
        ]);

        return response()->json([
            'data' => $xenditPay,
            'message' => 'success'
        ],201);

    }













//  xendit otomatis
public function createXendit(Request $request){
    Xendit::setApiKey($this->token);
    $params = [ 
        "external_id" => \uniqid(),
        "bank_code" => $request->bank_code,
        "name" => $request->name,
        "is_single_use" => true,
        "is_closed" => true,
        "expected_amount" => $request->price
      ];
    
      $createVA = \Xendit\VirtualAccounts::create($params);

     
      return response()->json([
        'data' => $createVA,
        'message' => 'success'
    ],200);

     
}



public function xenditPay(){
    // return 'halloo';
    // die;
    Xendit::setApiKey($this->token);
    $getVABanks = \Xendit\VirtualAccounts::getVABanks();

    return response()->json([
        'data' => $getVABanks,
        'message' => 'success'
    ],200);
}

//  xendit otomatis








    public function showPayment(Student $student){
        $payment = mid::where('student_id',$student->id)->get();
        return paymentResource::collection($payment);

    }

    public function getPayment(Request $request){
        if($request->has('payment')){

            $payments = transaction::where('name','LIKE','%'.$request->payment.'%')->latest()->paginate(7)->withQueryString();
            return paymentResource::collection($payments);

        }
        $payment= transaction::latest()->paginate(7);
        return paymentResource::collection($payment);
    }

    public function pay(Request $request){
        $student = student::where('id',$request->id)->first();
        // return $student;
        // die;
        \Midtrans\Config::$serverKey = 'SB-Mid-server-tj8jyVPH-S8GBRhf-8lR71Ke';
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

            $params = array(
                'transaction_details' => array(
                    'order_id' => rand(),
                    'gross_amount' => 20000,
                ),
                'item_details' => array(
                    [
                        'id' => $request->id,
                        'price' => $request->price,
                        'quantity' => 1,
                        'name' => 'Tahu bulat'
                    ]
                ),
                'customer_details' => array(
                    // 'nis' => $student->nis,
                    'first_name' => 'dewi',
                    'email' => 'budi@gmail.com',
                    'phone' => '6878678657',
                ),
            );


                    $snapToken = \Midtrans\Snap::getSnapToken($params);
                    return response()->json([
                        'token' => $snapToken
                    ]);
    }

    public function create(Request $request,Order $order){
        $order = order::findOrFail($order->id);
        try{
            $transaction = transaction::create([
                'student_id' => $order->student_id,
                'pivot_id' => $order->id,
                'transaction_id' => $request->transaction_id,
                'order_id' => $request->order_id,
                'name' => $order->student->name,
                'email' => $order->student->user->email,
                'transaction_status' =>$request->transaction_status,
                'status_message' =>$request->status_message,
                'gross_amount' => $request->gross_amount,
                'fraud_status' => $request->fraud_status,
                'payment_type' => $request->payment_type,
                'pdf_url' => $request->pdf_url,
            ]);

            return response()->json([
                'data' => $transaction,
                'order' => $order,
                'message' => 'success'
            ],201);

       }catch(QueryException $e){
            return response()->json([
                'message' => $e->errorInfo
            ],404);
       }
    }

    public function set(Request $request){
        // return 'hello';
        // die;
        // $signatur_key = hash('SHA512',$request->order_id.$request->status_code.$request->gross_amount.'SB-Mid-server-T1jyKQSL6k8t8YzLvVwKzOrF');
        $signatur_key = hash('SHA512',$request->order_id.$request->status_code.$request->gross_amount.'SB-Mid-server-tj8jyVPH-S8GBRhf-8lR71Ke');

        try{
            if($signatur_key !== $request->signature_key){
                return response()->json([
                    'message' => 'token invalid'
                ],401);
            }

            if($request->transaction_status == "settlement"){
                $payment = mid::where('order_id',$request->order_id)->firstOrFail();
                $order = order::where('id',$payment->pivot_id)->first();
                $payment->update([
                    'transaction_status' => $request->transaction_status,
                    // 'status_message' => $request->status_message
                ]);
                $order->update(['status' => 'lunas']);
                date_default_timezone_set('Asia/Jakarta');
                $time = date('H:i:s');
                $history = history::create([
                    'major_name' => $order->major->major_name,
                    'student_id' => $order->student_id,
                    'bill_name' => $order->bill->bill_name,
                    'status' => $order->status,
                    'history_time' => $time,
                    'price' => $order->price,
                ]);
                

                
                $email = [
                    'title' => 'KONFIRMASI TRANSAKSI',
                    'body' => 'Terimaksih sudah menyelesaikan pembayaran',
                    'name' => $payment->name, 
                    'major_name' => $order->major->major_name,
                    'student_id' => $order->student_id,
                    'bill_name' => $order->bill->bill_name,
                    'status' => $order->status,
                    'expired_date' => $order->expired_date,
                    'price' => $order->price,
                ];
                 Mail::to($payment->email)->send(new SendEmail($email));

                // $order->delete();
                $payment->delete();
                return response()->json([
                    'message' => "Pesan berhasil di selesai kan",
                    // 'payment' => $payment,
                    // 'history' => $history,
                ]);

            }else if($request->transaction_status == "cancel"){
                $payment = mid::where('order_id',$request->order_id)->firstOrFail();
                $payment->update([
                    'transaction_status' => $request->transaction_status,
                    // 'status_message' => $request->status_message
                ]);
                $payment->delete();
                return response()->json([
                    'message' => "Pesan sudah di batalkan",
                    
                ]);
            }else if($request->transaction_status =='expire'){

                $payment = mid::where('order_id',$request->order_id)->firstOrFail();
                $payment->update([
                    'transaction_status' => $request->transaction_status,
                    // 'status_message' => $request->status_message
                ]);
                $payment->delete();   
                return response()->json([
                    'message' => "Pesan sudah expire",
                    
                ]);
            }


        }catch(QueryException $e){
            return response()->json([
                'message' => $e->errorInfo
            ],500);
        }
    }

    public function cencel(Request $request){
        $payment = mid::where('order_id',$request->order_id)->firstOrFail();
        $client = New Client();
        $response = $client->post('https://api.sandbox.midtrans.com/v2/'.$payment->order_id.'/cancel',[
            'headers'=>[
                        'Accept' => 'application/json',
                        'Authorization' => 'Basic U0ItTWlkLXNlcnZlci10ajhqeVZQSC1TOEdCUmhmLThsUjcxS2U6',
                        // 'Authorization' => 'Basic U0ItTWlkLXNlcnZlci1UMWp5S1FTTDZrOHQ4WXpMdlZ3S3pPckY6',
                        'Content-Type' => 'application/json'
                    ],
        ]);

        return response()->json([
            'message' => 'pesanan berhasil di cencel',
            'data' => $response,
        ]);
    }
    public function mid(Request $request){
    //    $token = 'U0ItTWlkLXNlcnZlci1UMWp5S1FTTDZrOHQ4WXpMdlZ3S3pPckY6';
    // if($token == $request->token){   
    //     return response()->json([
    //         'message' => 'sama'
    //     ],200);
    // }
    //     die;
        $client = New Client();
        $response = $client->post('https://api.sandbox.midtrans.com/v2/charge',
        [
            'headers'=>[
                'Accept' => 'application/json',
                'Authorization' => 'Basic U0ItTWlkLXNlcnZlci10ajhqeVZQSC1TOEdCUmhmLThsUjcxS2U6',
                // 'Authorization' => 'Basic U0ItTWlkLXNlcnZlci1UMWp5S1FTTDZrOHQ4WXpMdlZ3S3pPckY6',
                'Content-Type' => 'application/json'
            ],
            'body'=> json_encode([
                "payment_type" => "bank_transfer",
                "transaction_details"=> [
                    "order_id"=> rand(),
                    "gross_amount"=> $request->price
                ],
                "bank_transfer"=>[
                    "bank"=> $request->bank
                ],
                ])
        ]);
        $data = json_decode($response->getBody());
        return response()->json([
            'data' => $data
        ],200);
    }
    
    public function pesanMidtrans(Request $request){
        // $bank = $request->va_number[0]->bank;
        // return response()->json([
        //     'message' => $bank
        // ]);
        // die;
        $order = order::findOrFail($request->id);
         $transaksi = mid::create([
                'student_id' => $order->student_id,
                'pivot_id' => $order->id,
                'transaction_id' => $request->transaction_id,
                'order_id' => $request->order_id,
                'name' => $order->student->name,
                'email' => $order->student->user->email,
                'transaction_status' =>$request->transaction_status,
                'transaction_time' =>$request->transaction_time,
                'gross_amount' => $order->price,
                'fraud_status' => $request->fraud_status,
                'payment_type' => $request->payment_type,
                'bank' => $request->bank,
                'no_va' => $request->va,
            ]);
            $data = new paymentResource($transaksi);
            $response = [
                'message' => 'Success',
                'data' => $data,
            ];

            return response()->json($response,201);
    }
}
