<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Screening extends Model
{
    use HasFactory;

    public $fillable = [
        'firstname',
        'dob',
        'migraine_frequency',
        'daily_frequency',
        'cohort'
    ];


}
