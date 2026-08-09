<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvestorRelationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $content = '<p class="section-para"><p style="text-align:left;"><span style="font-weight:normal;"> All investors are obliged to fulfill legal and social rules in their profession and personal conduct, with employees of Right Freelancer, as well as the public. They are expected to understand that the company holds a zero tolerance policy for any behavioral misconduct on the part of the shareholders within the company. The policy briefly instructs:</span></p>

<p class="section-para"><p style="text-align:left;"><span style="font-weight:normal;">means any services provided by Freelancers.</span></p>
<ul>
 <li>- Abstinence from sexual harassment</li>
  <li>- No involvement in any sort of criminal or fraudulent activity</li>
   <li>- No engagement in bribery, corporate espionage or oligarchical decision making</li>
    <li>- Prohibition of engagement in any pressurizing or threatening relating to the business operations</li>
     <li>- Prohibition of involvement in prejudice, hate crimes or shaming individuals</li>
     <li>- Promise to uphold decency and act without malice for one another within the scope of business</li>
</ul>';

        \App\Models\InvestorRelation::create([
            'title' => 'Investor Relations',
            'meta_title' => 'Investor Relation - Financial Insights and Business Strategy | Right Freelancer',
            'meta_description' => 'Explore investor relations at Right Freelancer. Get financial reports, company strategy, and investment opportunities all in one place.',
            'content' => $content,
        ]);
    }
}
