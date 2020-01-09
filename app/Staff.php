<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $fillable = [
        'fechas','phone','status'
    ];

    public function jobs(){
        return $this->belongsToMany('App\Job', 'job_staff', 'staff_id', 'job_id');
    }

    public function tcas(){
        return $this->belongsToMany('App\Archivo', 'staff_tca', 'staff_id', 'archivo_id');
    }

    public function tfns(){
        return $this->belongsToMany('App\Archivo', 'staff_tfn', 'staff_id', 'archivo_id');
    }
}
