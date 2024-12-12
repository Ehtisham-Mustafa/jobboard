<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BoardJob;

class JobApplication extends Model
{
    use HasFactory;
    public function boardJob(){
        return $this->belongsTo(BoardJob::class);
    }
}
