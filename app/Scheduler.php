<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scheduler extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo('App\User', 'user_id');
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
