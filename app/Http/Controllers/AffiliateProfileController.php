<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AffiliateProgram;
use Modules\CountryManage\Entities\Country;
use Modules\CountryManage\Entities\State;
use Modules\CountryManage\Entities\City;
class AffiliateProfileController extends Controller
{
    // Show affiliate profile settings page
    public function profile()
    {
        $affiliate = AffiliateProgram::find(session('logged_in_affiliate_id'));

        if (!$affiliate) {
            return redirect()->route('affiliate.login')->with('error', 'Unauthorized access');
        }

        $countries = Country::all();
        $states = State::where('country_id', old('country_id', $affiliate->country_id))->get();
        $cities = City::where('state_id', old('state_id', $affiliate->state_id))->get();
        $step1Complete = $affiliate->first_name && $affiliate->last_name && $affiliate->email;
        return view('frontend.affiliate.profile.profile-settings', compact('affiliate', 'countries', 'states', 'cities', 'step1Complete'));
    }

    // Update affiliate profile
    public function edit_profile(Request $request)
    {
        $affiliate = AffiliateProgram::find(session('logged_in_affiliate_id'));

        if (!$affiliate) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'first_name' => 'required|min:1|max:50',
            'last_name' => 'required|min:1|max:50',
            'username' => 'required|min:1|max:50|unique:affiliates_programs,username,' . $affiliate->id,
            'email' => 'required|email|unique:affiliates_programs,email,' . $affiliate->id,
            'country_id' => 'required',
        ]);

        $affiliate->update([
            'first_name'   => $request->first_name,
            'last_name'    => $request->last_name,
            'username'     => $request->username,
            'email'        => $request->email,
            'country_id'   => $request->country_id,
            'state_id'     => $request->state_id,
            'city_id'      => $request->city_id,
            'company_website' => $request->company_website,
        ]);

        return response()->json(['status' => 'ok']);
    }

    // Update affiliate profile photo
    public function edit_profile_photo(Request $request)
    {
        $affiliate = AffiliateProgram::find(session('logged_in_affiliate_id'));

        if (!$affiliate) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('affiliate-profile', 'public');
            $affiliate->update(['photo' => $path]);
        }

        return response()->json(['status' => 'ok']);
    }
}
