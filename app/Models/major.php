<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class major extends Model
{
    use HasFactory;
    protected $fillable = [
        'major_name',
    ];
    public function student(){
        return $this->hasMany(student::class,'major_id');
     }
    //  public function history(){
    //     return $this->hasMany(history::class,'major_id');
    // }
    // public function transaction(){
    //     return $this->hasMany(transaction::class,'major_id');
    // }
}
