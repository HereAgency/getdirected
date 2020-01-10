<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
       'name','phone','contact_name','since_date','status',
    ];

    public function lastjob(){
        return $this->belongsTo('App\Job','id_lastjob');
    }
}
