<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FeesAndCharge;

class FeesAndChargeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $content = <<<HTML
<p style="text-align:left;"><span style="font-weight:normal;"><b>If you are Hiring</b></span></p>
<p style="text-align:left;"><span style="font-weight:normal;">All employers are allowed to join the Right Freelancer platform for free for the first month of trial. This is an extension of gratuity from our side to build your relationship with the site. Every month thereafter will require a subscription royalty to be charged. The Silver Plan allows employers to feature up to 3 jobs. The Gold Plan allows for up to 100 jobs and the Platinum allows for up to 300. RightFreelancer has set the service charges to the lowest possible. Feel free to change the plan as per your needs.
</span><br></p>
<p style="text-align:left;"><span style="font-weight:normal;"><b>If you are working</b></span><br></p>
<p style="text-align:left;"><span style="font-weight:normal;">All candidates looking to work in top freelance jobs may join the Right Flancer platform free for the first month of trial. There onwards, they may upgrade to the packages based on their needs and requirements. The plans start as low as $10 per month to $50 per month. Each plan allows the job applicants to choose from several engaging and well-paying projects offered by a collection of thousands of clients.
</span><br></p>
HTML;

        FeesAndCharge::updateOrCreate(
            ['id' => 1],
            [
                'title' => 'Fees and Charges',
                'meta_title' => 'Fees and Charges - Right Freelancer | Transparent Pricing for Freelancers & Clients',
                'meta_description' => 'Understand the complete breakdown of service fees, commission rates, and payment charges on Right Freelancer. Stay informed with clear, transparent pricing for both freelancers and clients.',
                'heading' => 'Fees and Charges',
                'short_description' => 'Transparent Pricing for Freelancers & Clients',
                'content' => $content,
            ]
        );
    }
}
