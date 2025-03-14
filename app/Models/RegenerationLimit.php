<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegenerationLimit extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'resume_id',
        'section',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }
}