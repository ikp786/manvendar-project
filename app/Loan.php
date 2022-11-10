<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = ['first_name','last_name','mobile_primary', 'mobile_alternet','kyc','pincode','DOBording','client_3_months','succeding_3_month','email','address','city','district','state','loan_type','loan_amount','tenure_loan','bank_avaliabilty','b_licence','saving_account','current_account','created_at'];
}