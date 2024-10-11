<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardJob extends Model
{
    use HasFactory;

    public function jobType() {
        return $this->belongsTo(JobType::class);
    }
}

