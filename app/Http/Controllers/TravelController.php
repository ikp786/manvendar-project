<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Carsearch;

class TravelController extends Controller
{
    public function index()
    {
    	return view('agent.travel');
    }
    public function hotels()
    {
    	return view('agent.travel');
    }
    public function cars()
    {
    	return view('agent.travel.car');
    }
    public function cruises()
    {
    	return view('agent.travel');
    }
    public function homestay()
    {
    	return view('agent.travel');
    }
    public function honeymoon()
    {
    	return view('agent.travel');
    }
    public function carsearch()
    {
        return view('agent.travel.carsearch');
    }
    public function carbooking()
    {
        return view('agent.travel.carbook');
    }
    public function confirmcarbooking()
    {
        return view('agent.travel.confirmbooking');
    }
    public function carbookingcondition()
    {
        return view('agent.travel.carbookingcondition');
    }
}
