<?php



namespace Database\Seeders;



use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

use App\Models\ServiceShippingPolicy;

use App\Models\RefundReturnPolicy;

use File;



class PolicySeeder extends Seeder

{

    /**

     * Run the database seeds.

     */

    public function run(): void

    {

        // Create service shipping policy directories if they don't exist

        $serviceDir = resource_path('views/backend/service_shipping_policy');

        $refundDir = resource_path('views/backend/refund_return_policy');



        if (!File::isDirectory($serviceDir)) {

            File::makeDirectory($serviceDir, 0755, true, true);

        }



        if (!File::isDirectory($refundDir)) {

            File::makeDirectory($refundDir, 0755, true, true);

        }



        // Seed Service & Shipping Policy

        ServiceShippingPolicy::firstOrCreate(

            ['id' => 1],

            [

                'title' => 'Service & Shipping Policy',

                'meta_title' => 'Service & Shipping Policy - Right Freelancer',

                'meta_description' => 'Learn how digital services are delivered on Right Freelancer platform',

                'heading' => 'Service & Shipping Policy',

                'short_description' => 'Right Freelancer is a digital marketplace dedicated entirely to connecting clients with independent freelancers for virtual, professional, and digital services.',

                'content' => file_exists(base_path('service_shipping_policy_content.md')) 

                    ? file_get_contents(base_path('service_shipping_policy_content.md'))

                    : 'Digital services are delivered through our secure platform workspace.',

                'faqs' => [

                    [

                        'question' => 'How are digital services delivered or "shipped" on Right Freelancer?',

                        'answer' => 'All services are delivered digitally. Freelancers upload their completed deliverables directly inside the contract workspace on our platform.'

                    ],

                    [

                        'question' => 'Are there any shipping or delivery fees?',

                        'answer' => 'No. Because Right Freelancer hosts entirely digital/virtual services, there are absolutely no physical shipping fees, delivery charges, or packaging costs.'

                    ]

                ]

            ]

        );



        // Seed Refund or Return Policy

        RefundReturnPolicy::firstOrCreate(

            ['id' => 1],

            [

                'title' => 'Refund or Return Policy',

                'meta_title' => 'Refund or Return Policy - Right Freelancer',

                'meta_description' => 'Understand Right Freelancer\'s refund and return policy for digital services',

                'heading' => 'Refund or Return Policy',

                'short_description' => 'As a dynamic virtual marketplace specializing in professional, digital, and custom services, physical "returns" are not applicable. Instead, we maintain a secure Escrow-backed Refund Policy.',

                'content' => file_exists(base_path('refund_return_policy_content.md'))

                    ? file_get_contents(base_path('refund_return_policy_content.md'))

                    : 'Our refund policy is designed to protect both clients and freelancers during transactions.',

                'faqs' => [

                    [

                        'question' => 'Can I get a refund after approving the work?',

                        'answer' => 'No. Approving the work releases the funds directly to the freelancer. Once released, the platform cannot recover or refund them.'

                    ],

                    [

                        'question' => 'What happens if the freelancer cancels the order?',

                        'answer' => 'If the freelancer initiates a cancellation, the system will automatically void the contract and refund 100% of the escrowed amount.'

                    ]

                ]

            ]

        );



        $this->command->info('Policy tables seeded successfully!');

    }

}

