<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;
    // protected $with =['user']; 

    protected $fillable = [
        'user_id',
        'file',
        'age',
        'condition',
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
