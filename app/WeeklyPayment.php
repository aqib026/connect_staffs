<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyPayment extends Model
{
    use HasFactory;

    public function worker(){
            return $this->belongsTo('App\User', 'worker_id');
    }
}
