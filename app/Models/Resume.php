<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{ 
    protected $fillable = [
        'title',
        'general_details',
        'work_experience',
        'education',
        'skills', 
        'professional_qualifications', 
        'referees',
        'job_description',
        'status',
        'user_id',
        'cover_letter',
        'creation_step',
        'draft_data',
        'source_text'
    ];

    protected $casts = [
        'general_details' => 'array',
        'work_experience' => 'array',
        'education' => 'array',
        'skills' => 'array',
        'professional_qualifications' => 'array', 
        'referees' => 'array',  
        'draft_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}