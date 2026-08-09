<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
   use App\Models\ClientBenefit;

class ClientBenefitController extends Controller
{
 

public function index()
{
    $benefit = ClientBenefit::firstOrFail();
    return view('frontend.pages.client-benefits', compact('benefit'));
}
}
