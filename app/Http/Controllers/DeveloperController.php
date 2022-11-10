<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Vmnkeyword;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class DeveloperController extends Controller
{
    public function index(){
        return view('developer.index');
    }
    public function recharge_api_doc(){
        return view('developer.rechargeapi');
    }
    public function long_code_doc(){
        $keywords = Vmnkeyword::where('user_id', Auth::id())->get();
        return view('developer.longcode', compact('keywords'));
    }
    public function money_api_doc(){
        return view('developer.dmrapi');
    }
}
