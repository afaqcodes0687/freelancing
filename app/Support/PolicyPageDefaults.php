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
        $serviceDir = resource_path('views/backend/service_shipping_policy');
        $refundDir = resource_path('views/backend/refund_return_policy');

        if (!File::isDirectory($serviceDir)) {
            File::makeDirectory($serviceDir, 0755, true, true);
        }

        if (!File::isDirectory($refundDir)) {
            File::makeDirectory($refundDir, 0755, true, true);
        }

        // ==================== Service & Shipping Policy ====================
        ServiceShippingPolicy::updateOrCreate(
            ['id' => 1],
            [
                'title'             => 'Service & Shipping Policy',
                'meta_title'        => 'Service & Shipping Policy - Right Freelancer',
                'meta_description'  => 'Learn how digital services are delivered on Right Freelancer platform',
                'heading'           => 'Service & Shipping Policy',
                'short_description' => 'Right Freelancer is a digital marketplace dedicated entirely to connecting clients with independent freelancers for virtual, professional, and digital services.',
                'content'           => '
<p>Welcome to <strong>Right Freelancer</strong>. Because our platform is a digital marketplace dedicated entirely to connecting clients with independent freelancers for virtual, professional, and digital services, this policy explains how our services are delivered, how virtual deliverables are "shipped" or handed over, and the timeline, processes, and service expectations associated with transactions on our platform.</p>

<h2>1. Introduction to Digital Service Fulfillment</h2>
<p>Right Freelancer does not deal with, sell, or ship physical goods. All transactions on our platform are for intangible, professional, and digital services (such as website design, software development, graphic design, SEO, copywriting, consulting, etc.). Therefore, there is no physical shipping of products, and no shipping or handling charges apply to any order unless explicitly stated for a unique virtual license delivery.</p>

<h2>2. Digital Shipping and Service Delivery Process</h2>
<p>When you purchase a project gig or hire a freelancer on Right Freelancer, the delivery of the digital service operates as follows:</p>
<ul>
    <li><strong>Submission through Platform Workspace:</strong> Freelancers deliver the completed work (source code, graphics, documentation, or other assets) by uploading them directly to the active contract workspace or unified messaging system on the Right Freelancer platform.</li>
    <li><strong>Instant Delivery Notification:</strong> Once the freelancer uploads and submits the work, the client receives an instant automated notification via email and platform alert that the digital service has been delivered.</li>
    <li><strong>Client Review Window:</strong> The client is granted a review window (typically 3 to 14 days, depending on the contract agreement) to inspect the digital work and request modifications or revisions if needed.</li>
    <li><strong>Final Release and Acceptance:</strong> If the client is satisfied with the virtual shipment, they can officially accept the delivery. This action immediately releases the milestone payment held in our secure escrow vault to the freelancer.</li>
</ul>

<h2>3. Timelines and Execution Schedules</h2>
<p>All delivery dates are custom-negotiated between the Client and the Freelancer when starting a project or buying a predefined gig:</p>
<ul>
    <li><strong>Freelancer Deadlines:</strong> Freelancers are bound to ship/deliver the completed virtual files within the mutually agreed timeline shown in the project detail panel.</li>
    <li><strong>Milestone Deadlines:</strong> For multi-phase projects, each milestone has its own independent digital delivery timeline.</li>
    <li><strong>Delayed Deliveries:</strong> If a freelancer is unable to deliver within the timeline, the client is encouraged to communicate through the workspace chat. If the freelancer fails to respond or deliver within a reasonable grace period, the client may cancel the order or dispute the contract using our resolution mechanisms.</li>
</ul>

<h2>4. Revision Policies</h2>
<p>Digital delivery includes revisions to ensure satisfaction:</p>
<ul>
    <li><strong>Predefined Revisions:</strong> The number of revisions included in the service is determined by the freelancer\'s offer or custom proposal accepted by the client.</li>
    <li><strong>Requesting Revisions:</strong> If the delivered digital assets require changes, the client can select the "Request Revision" button within the project workspace before the review window expires. The freelancer is then required to update the assets and resubmit.</li>
</ul>

<h2>5. Service Fees, Escrow, and Billing</h2>
<p>Right Freelancer ensures safe financial shipping of funds:</p>
<ul>
    <li><strong>Secure Escrow:</strong> When an order is created, the client pays the order amount. These funds are held securely in our secure escrow wallet and are only released upon final project delivery and client acceptance.</li>
    <li><strong>No Hidden Costs:</strong> There are absolutely no shipping fees, delivery tax, or physical handling surcharges. All applicable service fees, commission structures, and taxes are clearly outlined on our Fees and Charges page.</li>
</ul>

<h2>6. Dispute Resolution and Support</h2>
<p>In case of non-delivery or disagreement regarding the digital shipping of assets:</p>
<ul>
    <li><strong>Resolution Center:</strong> Both freelancers and clients have access to our platform\'s dispute resolution tools.</li>
    <li><strong>Customer Support:</strong> If you face any issues regarding service delivery, digital file access, or payment release, you can contact our round-the-clock support team via the Support Desk or by emailing us at <a href="mailto:support@rightfreelancer.com" style="color: #309400; font-weight: 600;">support@rightfreelancer.com</a>.</li>
</ul>
',
                'faqs' => [
                    [
                        'question' => 'How are digital services delivered or "shipped" on Right Freelancer?',
                        'answer'   => 'All services are delivered digitally. Freelancers upload their completed deliverables (such as source code, images, files, or reports) directly inside the contract workspace on our platform. The client can download and review them instantly.',
                    ],
                    [
                        'question' => 'Are there any shipping or delivery fees?',
                        'answer'   => 'No. Because Right Freelancer hosts entirely digital/virtual services, there are absolutely no physical shipping fees, delivery charges, packaging costs, or import/export duties.',
                    ],
                    [
                        'question' => 'What happens if a freelancer misses the delivery deadline?',
                        'answer'   => 'If the deadline is missed and the freelancer is unresponsive, the client can initiate a cancellation or dispute in the resolution center. The funds held in our escrow will remain protected and may be refunded to the client depending on the case evaluation.',
                    ],
                    [
                        'question' => 'How does the secure escrow system protect my payments?',
                        'answer'   => 'When a client starts a project, the payment is securely deposited into our platform escrow account. Funds are only transferred/released to the freelancer after the client reviews the delivered work and officially clicks the "Approve/Accept" button.',
                    ],
                ],
            ]
        );

        // ==================== Refund or Return Policy ====================
        RefundReturnPolicy::updateOrCreate(
            ['id' => 1],
            [
                'title'             => 'Refund or Return Policy',
                'meta_title'        => 'Refund or Return Policy - Right Freelancer',
                'meta_description'  => 'Understand Right Freelancer\'s refund and return policy for digital services',
                'heading'           => 'Refund or Return Policy',
                'short_description' => 'As a dynamic virtual marketplace specializing in professional, digital, and custom services, physical "returns" are not applicable. Instead, we maintain a secure Escrow-backed Refund Policy.',
                'content'           => '
<p>Welcome to <strong>Right Freelancer</strong>. As a dynamic virtual marketplace specializing in professional, digital, and custom services (such as development, design, marketing, and writing), physical "returns" are not applicable. Instead, we maintain a secure Escrow-backed Refund Policy designed to protect both clients and freelancers during transactions.</p>

<h2>1. Escrow Protection and Funds Security</h2>
<p>To guarantee complete financial security, Right Freelancer uses a secure Escrow System:</p>
<ul>
    <li><strong>Funds Held in Escrow:</strong> When a client hires a freelancer or orders a project gig, the total payment is deposited into our secure escrow account before the work starts.</li>
    <li><strong>Mutual Safety:</strong> These funds are held securely and are only transferred to the freelancer\'s wallet after the client reviews the delivered files and clicks the "Accept/Approve" button.</li>
    <li><strong>Before Acceptance:</strong> As long as the project funds remain in the escrow vault, they are fully eligible for a dispute and subsequent refund if the terms of service are breached.</li>
</ul>

<h2>2. Refund Eligibility Criteria</h2>
<p>A client is eligible to request a refund for a project/gig payment under the following circumstances:</p>
<ul>
    <li><strong>Non-Delivery of Work:</strong> The freelancer fails to deliver any files or updates within the mutually agreed timeline or fails to respond within a reasonable grace period.</li>
    <li><strong>Incomplete or Incorrect Work:</strong> The delivered deliverables do not match the instructions, requirements, or scope documented in the active contract, and the freelancer refuses to perform the included revisions.</li>
    <li><strong>Order Cancellation by Freelancer:</strong> If the freelancer cancels the active order due to personal or professional constraints, the funds are automatically refunded to the client.</li>
</ul>

<h2>3. Ineligibility for Refunds</h2>
<p>Refunds cannot be issued under the following conditions:</p>
<ul>
    <li><strong>Approved Deliverables:</strong> Once the client reviews the work and clicks the "Approve/Accept" button, the escrowed funds are immediately released to the freelancer. Released funds are considered final and cannot be refunded by the platform.</li>
    <li><strong>Subjective Quality Preferences:</strong> Refunds are not granted based on subjective changes of mind, personal taste, or if the client decides they no longer need the work after it has been properly completed.</li>
    <li><strong>Breach of Platform Terms:</strong> If the client attempts to communicate or pay outside the Right Freelancer platform, they forfeit all escrow protections and refund privileges.</li>
</ul>

<h2>4. Dispute Resolution Process</h2>
<p>If a client is unsatisfied with the delivered work and the freelancer disagrees with the feedback, both parties can use our mediation process:</p>
<ul>
    <li><strong>Initiate a Dispute:</strong> Go to the active contract screen and click on "Open Dispute" or contact support before accepting the work.</li>
    <li><strong>Evidence Submission:</strong> Both parties must submit proof (original scope, chat records, draft files, revisions requested) to our resolution specialist.</li>
    <li><strong>Mediation Verdict:</strong> The Right Freelancer safety team will review the submitted evidence impartially and issue a binding decision. This may result in a full refund to the client, full payout to the freelancer, or a partial split based on work completed.</li>
</ul>

<h2>5. How Refunds are Processed</h2>
<p>Once a refund is approved by our system or via a dispute resolution decision:</p>
<ul>
    <li><strong>Refund to Wallet:</strong> By default, refunded amount is instantly credited to the Client\'s platform wallet balance, which can be used to purchase other gigs.</li>
    <li><strong>Refund to Original Payment Method:</strong> Clients can request to withdraw their wallet refund back to their original payment method (Stripe, PayPal, PayPro, or Bank Transfer). This process may take 5 to 10 business days depending on the financial institution.</li>
</ul>

<h2>6. Need Assistance?</h2>
<p>If you have any questions, concerns, or need help regarding an active refund request, visit our Support Desk to submit a ticket or email us directly at <a href="mailto:info@rightfreelancer.com" style="color: #309400; font-weight: 600;">info@rightfreelancer.com</a> with your contract or order ID.</p>
',
                'faqs' => [
                    [
                        'question' => 'Can I get a refund after approving the work?',
                        'answer'   => 'No. Approving the work releases the funds directly to the freelancer. Once the funds are released, they leave our secure escrow vault, and the platform cannot recover or refund them. Please review all files thoroughly before approving.',
                    ],
                    [
                        'question' => 'What happens if the freelancer cancels the order?',
                        'answer'   => 'If the freelancer initiates a cancellation, the system will automatically void the contract and refund 100% of the escrowed amount back to your client wallet instantly.',
                    ],
                    [
                        'question' => 'How long does it take for a refund to reach my bank account?',
                        'answer'   => 'While wallet refunds are instant, transferring the funds back to your credit/debit card or bank account via our payment gateways (Stripe, PayPro, PayPal) usually takes between 5 to 10 business days depending on processing banks.',
                    ],
                    [
                        'question' => 'How does Right Freelancer resolve disputes?',
                        'answer'   => 'Our dispute specialists review the contract requirements, submission records, draft designs/code, and workspace chats. We then issue an objective decision to either release, refund, or split the escrowed funds.',
                    ],
                ],
            ]
        );

        $this->command->info('Policy tables seeded successfully!');
    }
}