<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class info extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'kategori',
        'body',
        'date',
        'image',
    ];

    public function getImage($value){
        return Storage::url("images/".$value);
}
}
