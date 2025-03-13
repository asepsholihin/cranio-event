<?php

namespace App\Console\Commands;

use App\Models\WebGeneralSetting as GeneralSetting;
use App\Models\WebIndexSetting as IndexSetting;
use App\Models\User;
use Illuminate\Console\Command;

class SetupWebGeneralSetting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:setup-web-setting';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup web configuration settings';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $general_setting = GeneralSetting::latest()->first();
        $index_setting = IndexSetting::latest()->first();
        $admin = User::whereHas(
            'roles',
            function ($q) {
                $q->where('name', 'Super-Admin');
            }
        )->first();

        if (empty($admin)) {
            $this->info('Admin user has not been created yet. To continue create them first.');

            if ($this->confirm('Do you wish to continue?', true)) {
                $this->call('admin:initial-access');
                $this->call('admin:setup-web-setting');
            }
        }

        if (!empty($general_setting) || !empty($index_setting)) {
            if (!$this->confirm('Web configuration settings already exist. Do you wish to overwrite them?', false)) {
                return 0;
            }
        }

        $general_default_payload = [
            'web_logo' => $general_setting->web_logo ?? 'https://www.jejakimani.com/assets/logo.png',
            'web_favicon' => $general_setting->web_favicon ?? 'https://www.jejakimani.com/assets/favicon.ico',
            'web_title' => $general_setting->web_title ?? 'Travel Umroh Paket Umroh Terbaik di Jakarta Depok Tangerang Bekasi',
            'web_email' => $general_setting->web_email ?? 'cs@jejakimani.com',
            'meta_keywords' => $general_setting->meta_keywords ?? 'umroh, paket umroh, travel, travel umroh, umroh indonesia',
            'meta_description' => $general_setting->meta_description ?? 'Jejak Imani Merupakan Travel Umroh Paket Umroh terbaik dan Berkualitas melayani Info biaya Umroh di Jakarta Bogor Depok Tangerang Bekasi',
            'phone_number' => $general_setting->phone_number ?? '+62 821-821-821',
            'wa_number_1' => $general_setting->wa_number_1 ?? '+62 821-821-821',
            'wa_number_2' => $general_setting->wa_number_2 ?? '+62 821-821-821',
            'copyright_text' => $general_setting->copyright_text ?? 'Copyright © 2019 Jejak Imani. All Rights Reserved.',
            'cta_button_text' => $general_setting->cta_button_text ?? 'Daftar Sekarang',
            'footer_consultation' => $general_setting->footer_consultation ?? 'Hubungi Kami',
            'footer_location' => $general_setting->footer_location ?? 'Jl Siliwangi No.4, Pondok Benda, Kec. Pamulang, Kota Tangerang Selatan, Banten 15417'
        ];

        $index_default_payload = [
            'why_us_title' => $index_setting->why_us_title ?? 'Mengapa Kami',
            'about_image' => $index_setting->about_image ?? 'https://www.jejakimani.com/assets/about.jpg',
            'about_title' => $index_setting->about_title ?? 'Tentang Kami',
            'profile_ustadz_image' => $index_setting->profile_ustadz_image ?? 'https://www.jejakimani.com/assets/profile-ustadz.jpg',
            'profile_ustadz_title' => $index_setting->profile_ustadz_title ?? 'Profil Ustadz',
            'tour_package_title' => $index_setting->tour_package_title ?? 'Paket Umroh',
            'article_title' => $index_setting->article_title ?? 'Artikel',
            'partner_title' => $index_setting->partner_title ?? 'Partner',
            'video_url' => $index_setting->video_url ?? 'https://www.youtube.com/embed/86ihAFXmNEM',
        ];

        $author_payload = [
            'created_by' => $admin->id,
            'updated_by' => $admin->id
        ];

        $general_payload = array_merge([
            'web_logo' => $this->ask('Please input the url of your website logo', $general_default_payload['web_logo']),
            'web_favicon' => $this->ask('Please input the url of your website favicon', $general_default_payload['web_favicon']),
            'web_title' => $this->ask('What is the title of your website?', $general_default_payload['web_title']),
            'web_email' => $this->ask('What is the email of your website?', $general_default_payload['web_email']),
            'meta_keywords' => $this->ask('Please input the meta keywords of your website', $general_default_payload['meta_keywords']),
            'meta_description' => $this->ask('Please input the meta description of your website', $general_default_payload['meta_description']),
            'phone_number' => $this->ask('Please input the phone number of your website', $general_default_payload['phone_number']),
            'wa_number_1' => $this->ask('Please input the first WhatsApp number for your website', $general_default_payload['wa_number_1']),
            'wa_number_2' => $this->ask('Please input the second WhatsApp number for your website', $general_default_payload['wa_number_2']),
            'copyright_text' => $this->ask('Please input the copyright text of your website', $general_default_payload['copyright_text']),
            'cta_button_text' => $this->ask('Please input the cta button text of your website', $general_default_payload['cta_button_text']),
            'footer_consultation' => $this->ask('What is the footer consultation of your website?', $general_default_payload['footer_consultation']),
            'footer_location' => $this->ask('What is the footer location of your website?', $general_default_payload['footer_location']),
        ], $author_payload);

        $index_payload = array_merge([
            'why_us_title' => $this->ask('What is the title of your Why Us page?', $index_default_payload['why_us_title']),
            'about_image' => $this->ask('Please input the url of your About page image', $index_default_payload['about_image']),
            'about_title' => $this->ask('What is the title of your About page?', $index_default_payload['about_title']),
            'profile_ustadz_image' => $this->ask('Please input the url of your Profile Ustadz page image', $index_default_payload['profile_ustadz_image']),
            'profile_ustadz_title' => $this->ask('What is the title of your Profile Ustadz page?', $index_default_payload['profile_ustadz_title']),
            'tour_package_title' => $this->ask('What is the title of your Tour Package page?', $index_default_payload['tour_package_title']),
            'article_title' => $this->ask('What is the title of your Article page?', $index_default_payload['article_title']),
            'partner_title' => $this->ask('What is the title of your Partner page?', $index_default_payload['partner_title']),
            'video_url' => $this->ask('Please input the url of preview video of Index page', $index_default_payload['video_url']),
        ], $author_payload);

        empty($general_setting)
            ? GeneralSetting::create($general_payload)
            : $general_setting->update($general_payload);

        empty($index_setting)
            ? IndexSetting::create($index_payload)
            : $index_setting->update($index_payload);

        return 0;
    }
}
