<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TalentBenefit;

class TalentBenefitController extends Controller
{
    

public function index()
{
    $benefit = TalentBenefit::firstOrFail();
    return view('frontend.pages.talent-benefits', compact('benefit'));
}
}
