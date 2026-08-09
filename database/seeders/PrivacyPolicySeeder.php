<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PrivacyPolicy;

class PrivacyPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $content = <<<HTML
<p style="text-align:left;"><span style="font-weight:normal;">Right Freelancer LLC. provides this data to allow you to have a look at our policies and procedures concerning the gathering, use and speech act of knowledge through https://www.rightfreelancer.com/ (the “Site”), and the other websites, features, applications, widgets or online services that are owned or controlled by RightFreelancer which post a link to the current Privacy Policy (together with the location, the “Service”), additionally as any data RightFreelancer collects offline in reference to the Service. It additionally describes the alternatives accessible to you concerning the utilization of, your access to, and the way to update and proper your personal data. Note that we tend to mix the knowledge we collect from you from the location, through the Service usually.</span></p>
<p style="text-align:left;"><span style="font-weight:normal;">Please note that bound options or services documented during this Privacy Policy might not be offered on the Service the least bit times. Please additionally review our Terms of Service, that governs your use of the Service, and that is accessible at the location. We also have provided short summaries during this Privacy Policy to assist you to perceive what data we tend to collect; however, we tend to use it, and what choices or rights you have got. Whereas these summaries facilitate a number of the ideas in a less complicated manner. Therefore, we tend to encourage you to scan the complete Privacy Policy to own a more robust understanding of our best knowledge practices.</span><br></p>
<span><br></span>

<h3>Automatic Data Assortment</h3>
<p style="text-align:left;"><span style="font-weight:normal;">Like alternative online corporations, we tend to receive technical data once you use our Services. We tend to use our latest technologies to investigate however individuals use our Services, to enhance however our website functions, to save lots of your log-in data for future sessions, and to serve you with advertisements which will interest you.</span></p>
<p style="text-align:left;"><span style="font-weight:normal;"><span><br></span></span></p>
<p style="text-align:left;"><span style="font-weight:normal;">We and our third-party service suppliers, together with analytics and third-party content suppliers, might mechanically collect bound data from you whenever you access or act with our Service. This data might embrace, among alternative data, the browser and OS you're exploitation, the URL or publicity that referred you to the Service, the search terms you entered into a probe engine that junction rectifier you to the Service, areas among the Service that you just visited, what links you clicked on, that pages or content you viewed and for the way long, alternative similar data and statistics regarding your interactions, like content response times, transfer errors and length of visits to bound pages and alternative data unremarkably shared once browsers communicate with websites. We tend to might conjoin this mechanically collected log data with alternative information we collect regarding you. we tend to do that to enhance the services we provide you, and to enhance promoting, analytics, and website practicality.</span><br></p>
<span><br></span>

<h3>Personal Data We Assemble</h3>
<p style="text-align:left;"><span style="font-weight:normal;">We collect personal data regarding our users so as to produce our merchandise, services, and client support. Our merchandise, services, and client support are provided through several platforms together with however not restricted to: websites, phone apps, email, and phone. the particular platform and merchandise, service, or support you act with, might have an effect on the non-public knowledge we tend to collect.</span></p>
<p style="text-align:left;"><span style="font-weight:normal;"><span><br></span></span></p>
<p style="text-align:left;"><span style="font-weight:normal;">Not all data requested, collected, and processed by US is "Personal Information" because it doesn't establish you as a particular person. this may embrace the bulk of "User Generated Content" that you just give US with the intention of sharing with alternative users. Being aforementioned "Non-Personal Information" isn't coated by this privacy policy. However, this data is employed in a mixture or be connected with existing personal information; once during this type, it'll be treated as personal information. As such, this privacy policy can list each sort of data for the sake of transparency.</span><br></p>
<span><br></span>
<p style="text-align:left;"><span style="font-weight:normal;">In some things, Users might give us with personal data while not us posing for it, or through means that not supposed for the gathering of specific sorts of data. while we tend to continuously take rational steps to shield this knowledge, the user can have bypassed our systems, processes, and management and so the knowledge provided won't be ruled by this privacy policy.</span><br></p>
<span><br></span>
<p style="text-align:left;"><span style="font-weight:normal;">In some circumstances, users may provide us with personal information over our Right Freelancer platform. That personal information shared through social media or any social channel will be not in our control. Being said that any information collected by our team is governed by the privacy policy dictated here.</span><br></p>
<span><br></span>

<h3>Personal Information</h3>
<p style="text-align:left;"><span style="font-weight:normal;">Notify that by personal information we include your name, email addrees, company address billing address and your phone number.</span></p>
<p style="text-align:left;"><span style="font-weight:normal;"><span><br></span></span></p>

<h3>Non-Identifying Information</h3>
<p style="text-align:left;"><span style="font-weight:normal;">Apart from this, we may also collect other information like zip code demographic data information regarding your use of our service. We may aggregate information collected from Right Freelancer registered and non-registered users Right Freelancer users. Username is made public through the service and is viewable by other Right Freelancer users.</span></p>
<p style="text-align:left;"><span style="font-weight:normal;"><span><br></span></span></p>

<h3>Collection of Third Party Personal Information</h3>
<p style="text-align:left;"><span style="font-weight:normal;">Collection of third-party personal information we collect only the following personal information from you whether it’s your contact or friends, first name, Last name and email address that you have provided to us while chatting in the message room.</span></p>
<p style="text-align:left;"><span style="font-weight:normal;"><span><br></span></span></p>

<h3>Information Received from Third Parties</h3>
<p style="text-align:left;"><span style="font-weight:normal;">In addition to this, third parties may also give us information about you. And if we combine both information assembled by us via our service, we will still consider that combined information as set forth in this Privacy Policy.</span></p>
<p style="text-align:left;"><span style="font-weight:normal;"><span><br></span></span></p>

<h3>Work Diaries</h3>
<p style="text-align:left;"><span style="font-weight:normal;">We collect information about a particular assigned project that RightFreelancer has done for her client. we share the work diaries with the relevant client and agency. we inform RightFreelancer when we capture information for the work diary. Thus, allowing the RightFreelancer to block such data sharing.</span></p>
<p style="text-align:left;"><span style="font-weight:normal;"><span><br></span></span></p>

<h3>Differentiate Your Actual Profiles From Fake Profiles</h3>
<p style="text-align:left;"><span style="font-weight:normal;">Once you register on Right Freelancer, we ask you to create your profile that consists of specific information. We require from you that includes personal information photographs examples of your work or any outsourced service skills taken tests, scores, hourly pay rates and earnings information feedback rating information and other information including your username that is shown on the Right Freelancer profile. The information in your profile may be visible to all other Right Freelancer and employers.</span></p>
<p style="text-align:left;"><span style="font-weight:normal;"><span><br></span></span></p>
<p style="text-align:left;"><span style="font-weight:normal;">However, you can edit the certain profile information any time you want by going into the account settings. You have the option to limit the visibility of your certain content and profile to specific users, clients, countries and agencies of associated individual users. For instance, if in any unfortunate case, you believe that your unauthorized profile has been created by any other unknown person, you can immediately report us. It will be removed in lieu of verification of your identity and the person who is using your unauthorized profile. You can contact us at Right Freelancer support.</span><br></p>
<span><br></span>

<h3>Message Communication</h3>
<p style="text-align:left;"><span style="font-weight:normal;">Freelance agencies and clients who communicate with each other through our chat service are subject to discuss each other requirements client needs and freelancer work proposals through our platform only. Adding more to the info, if you communicate with an agency or client that agency or client will also be a data controller with respect to such communications. we authorize the rights to display the testimonials of satisfied customers of RightFreelancer.com service.</span></p>
<p style="text-align:left;"><span style="font-weight:normal;"><span><br></span></span></p>
<p style="text-align:left;"><span style="font-weight:normal;">Please note that</span><br></p>
<span><br></span>

<h3>Testimonials Sharing</h3>
<p style="text-align:left;"><span style="font-weight:normal;">Entire content of such reviews feedback or words of appreciation will be displayed publicly. To post your name with your testimonial we may ask you to give us consent. if you wish to delete or update your testimonial you can contact us. each and every feedback shared by the client and freelancer on account of the service taken is displayed publicly. In very rare cases we remove feedback pursuant to the relevant provisions in terms of service and use, unless you request that we delete certain information see your choices and rights below.</span></p>
<p style="text-align:left;"><span style="font-weight:normal;"><span><br></span></span></p>

<h3>Client and Right Freelancer</h3>
<p style="text-align:left;"><span style="font-weight:normal;">Each and every feedback shared by the client and freelancer on account of the service taken is displayed publicly. On very rare occasions, we may remove feedback pursuant to the relevant provisions of our Terms of Service, including the Terms of Use.</span></p>
<p style="text-align:left;"><span style="font-weight:normal;"><span><br></span></span></p>

<h3>Data Retention</h3>
<p style="text-align:left;"><span style="font-weight:normal;">We delete the information you submit to verify your identity after 30 days and we retain other information we collect for at least 5 years. your information may persist in copies made for backup and business continuity purposes for additional time. If you choose to provide us with personal information, we encourage you to routinely update the data to ensure that we have accurate and up to date information about we take a number of steps to protect your data. Nevertheless, security is not guaranteed.</span></p>
<p style="text-align:left;"><span style="font-weight:normal;"><span><br></span></span></p>

<h3>Our Security</h3>
<p style="text-align:left;"><span style="font-weight:normal;">Right Freelancer takes commercially reasonable steps to help protect and secure the information it collects and stores about Right Freelancer users. Complete access to the site is encrypted using industry standard transport layer security technology tls. Whenever you enter sensitive information such as tax identification number, we encrypt the transmission of that information with the help of our secure socket layer technology. We also use http strict transport security to add an additional layer of protection for our Right Freelancer users. Remember that no method of transmission over the internet or method of electronic storage is 100% secure. Hence while we strive to protect your personal information Right Freelancer cannot ensure and does not warranty the security of any information you transmit to us. for any other questions regarding the Right Freelancer privacy policy please feel free to contact. We may change this privacy policy at any time. If we make material changes, we will provide notice.</span></p>
<p style="text-align:left;"><span style="font-weight:normal;"><span><br></span></span></p>

<h3>Privacy Policy Changes</h3>
<p style="text-align:left;"><span style="font-weight:normal;">Right Freelancer may update this privacy policy at any time and any changes will be effective upon posting. In the event that there are material changes to the way we display your personal information as a notice through the services prior to the change becoming effective. We may also notify you by email in our discretion. We will use your personal information in a manner consistent with the privacy policy in effect at the time you submitted the information unless you consent to the new or revised policy.</span></p>
<p style="text-align:left;"><span style="font-weight:normal;"><span><br></span></span></p>

<h3>If you are Hiring</h3>
<p style="text-align:left;"><span style="font-weight:normal;">All Clients are allowed to join the Right Freelancer platform for free for the first month of trial. This is an extension of gratuity from our side to build your relationship with the site. Every month thereafter will require a subscription royalty to be charged. The Silver Plan allows employers to feature up to 3 jobs. The Gold Plan allows for up to 100 jobs and the Platinum allows for up to 300. Right Freelancer has set the service charges to the lowest possible. Feel free to change the plan as per your needs.</span></p>
<p style="text-align:left;"><span style="font-weight:normal;"><span><br></span></span></p>

<h3>If you are working</h3>
<p style="text-align:left;"><span style="font-weight:normal;">All candidates looking to work in top freelance jobs may join the Right Flancer platform free for the first month of trial. There onwards, they may upgrade to the packages based on their needs and requirements. The plans start as low as $10 per month to $50 per month. Each plan allows the job applicants to choose from several engaging and well-paying projects offered by a collection of thousands of clients.</span></p>
<p style="text-align:left;"><span style="font-weight:normal;"><span><br></span></span></p>

<h3>Refund Policy and Dispute Management</h3>
<p style="text-align:left;"><span style="font-weight:normal;">The platform has taken all necessary steps to ensure that the freelancers are protected from mishaps and poor experiences. Similar efforts have been made for the employers to prevent any dissatisfaction that may arise from the online job. However, in case any party, whether a client as in employer or the freelancer may fail to live up to the standards set by the Right Freelancer platform, the refund policy will be upheld on both ends and the dispute shall be resolved in favor of the rightful side.</span></p>
<p style="text-align:left;"><span style="font-weight:normal;"><span><br></span></span></p>
<p style="text-align:left;"><span style="font-weight:normal;">In such a case Right Freelancers will not charge any fee on our end. However, the expense of the payment method used in the transaction says PayPal or Stripe will be subtracted from the refund, both for the refund and for the initial payment.</span><br></p>
<span><br></span>

<h3>Arbitration and Dispute Management Fee</h3>
<p style="text-align:left;"><span style="font-weight:normal;">The arbitration fee for a milestone dispute is $3.00 USD or 3%, whichever is greater. Our dispute resolution system is designed to allow both parties to resolve issues regarding milestone payments amongst themselves without arbitration.</span></p>
<p style="text-align:left;"><span style="font-weight:normal;"><span><br></span></span></p>
<p style="text-align:left;"><span style="font-weight:normal;">After 4 days of a dispute being filed (or 7 days if the dispute is filed by the freelancer) either party may elect to move the dispute to paid arbitration. The other party will then have a further 4 days to agree to pay this fee and for both parties to submit any final evidence. If the other party fails to pay within time, they will lose the dispute.</span><br></p>
<span><br></span>
<p style="text-align:left;"><span style="font-weight:normal;">The arbitration fee will then be refunded to the dispute whether the defendant or plaintiff.</span><br></p>
<span><br></span>

<h3>Maintenance Fee and Sanctions</h3>
<p style="text-align:left;"><span style="font-weight:normal;">*Australian users incur $0.30AUD + 0.99% for Credit/Debit card transactions.</span></p>
<p style="text-align:left;"><span style="font-weight:normal;"><span><br></span></span></p>

<h3>Arbitration Fees</h3>
<p style="text-align:left;"><span style="font-weight:normal;">User Accounts that have not logged in for six months will incur a maintenance fee of up to $10.00 USD per month until either the account is terminated or reactivated for storage, bandwidth, support and management costs of providing Hosting of the user's profile, portfolio storage, listing in directories, provision of the hire service, file storage and message storage. These fees will be refunded upon request by users on subsequent reactivation. Breach of the Terms of Service or Conduct may result in a fine, termination of the account or temporary ban depending on the nature of the violation. Furthermore, at all times, all parties are expected to conduct themselves with the utmost professionalism, decency, and ethics. Anything less than that will result in banishment from the job portal regardless of the package in question.</span></p>
<p style="text-align:left;"><span style="font-weight:normal;"><span><br></span></span></p>
HTML;

        // Upsert logic
        PrivacyPolicy::updateOrCreate(
            ['id' => 1],
            [
                'title' => 'Privacy Policy',
                'meta_title' => 'Privacy Policy - Right Freelancer | Your Data, Our Responsibility',
                'meta_description' => 'Review the Privacy Policy of Right Freelancer to understand how we collect, use, and protect your personal information. We are committed to safeguarding your privacy and data security.',
                'heading' => 'Privacy Policy',
                'short_description' => 'Our commitment to your privacy and data security.',
                'content' => $content,
            ]
        );
    }
}
