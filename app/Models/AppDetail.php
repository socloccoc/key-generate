<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppDetail extends Model
{
    protected $fillable = ['app_id', 'key', 'serial_number', 'expire_time', 'expire_date'];

    public function app(){
        return $this->hasOne('App\Models\App', 'id', 'app_id');
    }
}
