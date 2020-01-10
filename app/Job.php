<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $fillable = [
        'job_type','shift_type','number_utes','number_trafic','address','location','setup_required','notes','date','time_start','status','tbc',
    ];

    public function staffs(){
        return $this->belongsToMany('App\Staff', 'job_staff', 'job_id', 'staff_id');
    }

    public function client(){
        return $this->belongsTo('App\Client','id_client');
    }

    public function permits(){
        return $this->belongsToMany('App\Archivo', 'job_permit', 'job_id', 'archivo_id');
    }

    public function tgs(){
        return $this->belongsToMany('App\Archivo', 'job_tgs', 'job_id', 'archivo_id');
    }

}
