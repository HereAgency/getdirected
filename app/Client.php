<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Client extends Model
{
    use HasApiTokens,Notifiable,SoftDeletes;
    
    protected $fillable = [
       'name','address','mobile','email','phone','since_date','contact_name','status',
    ];

    public function lastjob(){
        return $this->belongsTo('App\Job','id_lastjob');
    }
}
