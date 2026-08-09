<?php

namespace App\Http\Controllers\Api\Freelancer;

use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use App\Models\IdentityVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Modules\CountryManage\Entities\Country;
use Modules\CountryManage\Entities\State;
use Modules\CountryManage\Entities\City;

class IdentityVerificationApiController extends Controller
{
    /**
     * GET /api/v1/freelancer/identity-verification/status
     * Returns the current identity verification record and status for the logged-in freelancer.
     */
    public function get_verification_status()
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        $verification = IdentityVerification::where('user_id', $user->id)
            ->with(['user_country:id,country', 'user_state:id,state', 'user_city:id,city'])
            ->first();

        $status_label = null;
        if ($verification) {
            if ($verification->status === 1) {
                $status_label = 'approved';
            } elseif ($verification->status === 2) {
                $status_label = 'declined';
            } else {
                $status_label = 'pending';
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'is_submitted'   => $verification ? true : false,
                'status'         => $verification ? $verification->status : null,
                'status_label'   => $status_label,  // null | pending | approved | declined
                'verification'   => $verification ? [
                    'id'                 => $verification->id,
                    'verify_by'         => $verification->verify_by,
                    'country_id'        => $verification->country_id,
                    'country_name'      => optional($verification->user_country)->country,
                    'state_id'          => $verification->state_id,
                    'state_name'        => optional($verification->user_state)->state,
                    'city_id'           => $verification->city_id,
                    'city_name'         => optional($verification->user_city)->city,
                    'address'           => $verification->address,
                    'zipcode'           => $verification->zipcode,
                    'national_id_number'=> $verification->national_id_number,
                    'front_image_url'   => $verification->front_image ? asset('assets/uploads/verification/' . $verification->front_image) : null,
                    'back_image_url'    => $verification->back_image ? asset('assets/uploads/verification/' . $verification->back_image) : null,
                    'created_at'        => $verification->created_at,
                    'updated_at'        => $verification->updated_at,
                ] : null,
            ]
        ]);
    }

    /**
     * POST /api/v1/freelancer/identity-verification/submit
     * Submit or re-submit identity verification documents.
     *
     * Required fields (multipart/form-data):
     *   country           - integer (country ID)
     *   state             - integer (state ID)
     *   city              - integer|null (city ID, optional)
     *   address           - string (max 191)
     *   zipcode           - string|null
     *   national_id_number- string (max 255)
     *   verify_by         - string|null  (e.g. "national_id", "passport", "driving_license")
     *   front_image       - image file (jpeg/png/jpg, max 5MB)
     *   back_image        - image file (jpeg/png/jpg, max 5MB)
     */
    public function submit_verification(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'country'            => 'required|integer|exists:countries,id',
            'state'              => 'required|integer|exists:states,id',
            'city'               => 'nullable|integer',
            'address'            => 'required|max:191',
            'zipcode'            => 'nullable|max:191',
            'national_id_number' => 'required|max:255',
            'verify_by'          => 'nullable|string|max:100',
            'front_image'        => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'back_image'         => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $user_id = $user->id;
        $existing = IdentityVerification::where('user_id', $user_id)->first();

        // Handle front image
        $front_image_name = $this->storeVerificationImage($request, 'front_image', $existing ? $existing->front_image : null);

        // Handle back image
        $back_image_name = $this->storeVerificationImage($request, 'back_image', $existing ? $existing->back_image : null);

        $storage_driver = Storage::getDefaultDriver();

        IdentityVerification::updateOrCreate(
            ['user_id' => $user_id],
            [
                'user_id'            => $user_id,
                'verify_by'          => $request->verify_by,
                'country_id'         => $request->country,
                'state_id'           => $request->state,
                'city_id'            => $request->city ?? 0,
                'address'            => $request->address,
                'zipcode'            => $request->zipcode ?? 0,
                'national_id_number' => $request->national_id_number,
                'front_image'        => $front_image_name,
                'back_image'         => $back_image_name,
                'status'             => null, // reset to pending on re-submit
                'load_from'          => in_array($storage_driver, ['CustomUploader']) ? 0 : 1,
            ]
        );

        // Notify admin via email
        try {
            $message = get_static_option('user_identity_verify_message')
                ?? "<p>Hello,</p><p>You have a new request for user identity verification.</p>";
            Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
                'subject' => get_static_option('user_identity_verify_subject') ?? 'User Identity Verify Email',
                'message' => $message,
            ]));
        } catch (\Exception $e) {
            // Silently fail – do not block the user
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Identity verification submitted successfully. Please wait for admin approval.',
        ]);
    }

    /**
     * GET /api/v1/freelancer/identity-verification/countries
     * Returns all countries for the dropdown.
     */
    public function get_countries()
    {
        $countries = Country::select('id', 'country')->orderBy('country')->get();
        return response()->json(['status' => 'success', 'data' => $countries]);
    }

    /**
     * POST /api/v1/freelancer/identity-verification/states
     * Returns states by country. Body: { country_id: integer }
     */
    public function get_states(Request $request)
    {
        $request->validate(['country_id' => 'required|integer|exists:countries,id']);
        $states = State::select('id', 'state')->where('country_id', $request->country_id)->orderBy('state')->get();
        return response()->json(['status' => 'success', 'data' => $states]);
    }

    /**
     * POST /api/v1/freelancer/identity-verification/cities
     * Returns cities by state. Body: { state_id: integer }
     */
    public function get_cities(Request $request)
    {
        $request->validate(['state_id' => 'required|integer|exists:states,id']);
        $cities = City::select('id', 'city')->where('state_id', $request->state_id)->orderBy('city')->get();
        return response()->json(['status' => 'success', 'data' => $cities]);
    }

    // -------------------------------------------------------------------------
    // Private helper
    // -------------------------------------------------------------------------

    private function storeVerificationImage(Request $request, string $field, ?string $oldFileName): string
    {
        if ($image = $request->file($field)) {
            $newName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

            if (cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi'])) {
                if ($oldFileName) {
                    delete_frontend_cloud_image_if_module_exists('verification/' . $oldFileName);
                }
                add_frontend_cloud_image_if_module_exists('verification', $image, $newName, 'public');
            } else {
                $oldPath = 'assets/uploads/verification/' . $oldFileName;
                if ($oldFileName && file_exists($oldPath)) {
                    File::delete($oldPath);
                }
                $resized = Image::make($image)->fit(500, 300);
                $resized->save('assets/uploads/verification/' . $newName);
            }
            return $newName;
        }

        // No new file uploaded – return existing filename (should not happen due to validation, but safe fallback)
        return $oldFileName ?? '';
    }
}
