<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class orderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'name' => $this->student->name,
            'bill_id' => $this->bill_id,
            'bill_name' => $this->bill->bill_name,
            'major_id' => $this->major_id,
            'major_name' => $this->major->major_name,
            'price' => $this->price,
            'status' => $this->status,
            'expired_date' => $this->expired_date,
        ];
    }
}
