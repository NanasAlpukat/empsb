<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class infoResource extends JsonResource
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
            'kategori' => $this->kategori,
            'date' => $this->date,
            'body' => $this->body,
            'image' => $this->image,
            'published' => $this->created_at->format('d-m-y'),
        ];
    }
}
