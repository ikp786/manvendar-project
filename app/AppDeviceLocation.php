<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppDeviceLocation extends Model
{
    //
	 protected $fillable= ['device_name','user_id','email','IMEI','sim_subscriber_id','sim_serial_no','ip','hardware_serial_no','longitude','latitude']; 
}
