<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEntry extends Model
{
    use HasFactory;

    public function schedule()
    {
        return $this->belongsTo('App\Scheduler', 'schedule_id');
    }
    public function job()
    {
        return $this->belongsTo('App\Job', 'job_id');
    }
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

}
