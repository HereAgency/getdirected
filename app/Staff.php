<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $fillable = [
        'fechas','phone','status'
    ];

    public function jobs(){
        return $this->belongsToMany('App\Job');
    }
}
