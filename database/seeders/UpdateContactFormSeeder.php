<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FormBuilder;
use Modules\CountryManage\Entities\Country;

class UpdateContactFormSeeder extends Seeder
{
    public function run(): void
    {
        $form = FormBuilder::find(1);

        if ($form) {
            $country_list = Country::where('status', 1)->pluck('country')->toArray();

            $fields = [
                "success_message" => "Your Message Successfully Send.",
                "field_type" => ["text", "email", "tel", "select", "select", "textarea"],
                "field_name" => ["your-name", "your-email", "your-phone", "country", "subject", "your-message"],
                "field_placeholder" => ["Your Name", "Your Email", "Your Phone", "Select Country", "Select Subject", "Your Message"],
                "field_required" => (object) [
                    "0" => "on",
                    "1" => "on",
                    "2" => "on",
                    "3" => "on",
                    "4" => "on",
                    "5" => "on",
                ],
                "select_options" => [
                    "country" => $country_list,
                    "subject" => [
                        "Business Inquiry",
                        "General Inquiry",
                        "Partnerships",
                        "IPR Report",
                        "Freelancer Inquiry",
                        "Other"
                    ]
                ]
            ];

            $form->fields = json_encode($fields);
            $form->save();

            echo "Contact Form updated successfully.\n";
        } else {
            echo "Contact Form with ID 1 not found.\n";
        }
    }
}
