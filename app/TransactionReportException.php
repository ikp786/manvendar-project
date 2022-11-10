<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionReportException extends Model
{
    //
	protected $fillable = ['report_id','exception','exception_type','dist_data','md_data','admin_data','other_data'];
}
