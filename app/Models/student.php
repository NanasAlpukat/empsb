<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'major_id',
        'nis',
        'name',
        'date_of_birth',
        'age',
        'class',
        'gender',
        'no_phone',
        'parents_name',
        'address',
        'image',
    ];

    public function getImage($value){
            return Storage::url("images/".$value);
    }

    // protected $with = ['user','major'];

    public function User(){
        return $this->belongsTo(User::class);
     }
    public function major(){
        return $this->belongsTo(major::class);
     }
     public function history(){
        return $this->hasMany(history::class,'student_id');
    }
    public function payxendit(){
        return $this->hasMany(payxendit::class,'student_id');
    }
    public function mid(){
        return $this->hasMany(mid::class,'student_id');
    }
}
