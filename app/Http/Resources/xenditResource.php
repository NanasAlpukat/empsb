<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\bill;
use App\Models\order;
class xenditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $order = order::where('id',$this->order_id)->first();
        $bill = bill::where('id',$order->bill_id)->first();
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'order_id' => $this->order_id,
            'external_id' => $this->external_id,
            'bill_name' => $bill->bill_name,
            'bank_code' => $this->bank_code,
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
            'currency' => $this->currency,
            'account_number' => $this->account_number,
            'expiration_date' => $this->expiration_date,
            'price' => $this->expected_amount,
            'order_time' => $this->order_time,
        ];
    }
}
