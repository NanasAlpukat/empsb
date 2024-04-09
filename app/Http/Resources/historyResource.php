<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class historyResource extends JsonResource
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
            'bill_name' => $this->bill_name,
            'price' => $this->price,
            'major_name' => $this->major_name,
            'status' => $this->status,
            'history_time' => $this->history_time,
            'published' => $this->created_at->format('Y-m-d'),
        ];
    }
}
