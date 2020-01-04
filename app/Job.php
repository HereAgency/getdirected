<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $fillable = [
        'fechas','phone','status'
    ];

    public function staffs(){
        return $this->belongsToMany('App\Staff');
    }
}
