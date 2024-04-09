<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class pengumumanResource extends JsonResource
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
            'title' => $this->title,
            'date' => $this->date,
            'body' => $this->body,
            'published' => $this->created_at->format('d-m-y'),
        ];
    }
}
