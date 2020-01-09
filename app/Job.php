<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $fillable = [
        'fechas','phone','status'
    ];

    public function staffs(){
        return $this->belongsToMany('App\Staff', 'job_staff', 'job_id', 'staff_id');
    }

    public function permits(){
        return $this->belongsToMany('App\Archivo', 'job_permit', 'job_id', 'archivo_id');
    }

    public function tgs(){
        return $this->belongsToMany('App\Archivo', 'job_tgs', 'job_id', 'archivo_id');
    }

}
