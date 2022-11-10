<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    protected $fillable = ['accountNumber','ifsc','benificiaryId','customerNumber','bankname','name'];
}
