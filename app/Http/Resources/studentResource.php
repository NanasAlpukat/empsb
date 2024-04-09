<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class studentResource extends JsonResource
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
                'user_id' => $this->user_id,
                'major_id' => $this->major_id,
                'nis' => $this->nis,
                'name' => $this->name,
                'email' => $this->user->email,
                'major_name' => $this->major->major_name,
                'age' => $this->age,
                'date' => $this->date_of_birth,
                'class' => $this->class,
                'gender' => $this->gender,
                'no_phone' => $this->no_phone,
                'parents_name' => $this->parents_name,
                'address' => $this->address,
                'image' => $this->image,
                'published' => $this->created_at->format('d-m-y'),
        ];
    }
}
