<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\bill;
use App\Models\order;
class paymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {   
        $order = order::where('id',$this->pivot_id)->first();
        $bill = bill::where('id',$order->bill_id)->first();


        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'pivot_id' => $order->id,
            'status' => $order->status,
            'bill_name' => $bill->bill_name,
            'transaction_id' => $this->transaction_id,
            'order_id' => $this->order_id,
            'name' => $this->name,
            'email' => $this->email,
            'transaction_status' => $this->transaction_status,
            'transaction_time' => $this->transaction_time,
            'price' => $this->gross_amount,
            'fraud_status' => $this->fraud_status,
            'payment_type' => $this->payment_type,
            'no_va' => $this->no_va,
            'bank' => $this->bank,
            'published' => $this->created_at->format('d-m-y'),
        ];
    }
}
