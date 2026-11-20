<?php

return [
    'name' => 'AfroISO Institute & Consultants',
    'address' => '2nd Floor, East Wing, Celestial Park, Liberation Legacy Way, Harare, Zimbabwe',
    'phone' => '+263 718 781 215',
    'phone_link' => '+263718781215',
    'email' => 'info@afroisoconsultants.org',

    // UAT (test) copy of the site. Leave UAT_MODE off on the live site.
    // When it is on, the site shows a test banner, is hidden from search engines,
    // and asks for the UAT_USER / UAT_PASSWORD login if a password is set.
    'uat' => (bool) env('UAT_MODE', false),
    'uat_user' => env('UAT_USER', 'afroiso'),
    'uat_password' => env('UAT_PASSWORD'),

    // The policy pages show their full text on the live site only when this is on.
    // Turn it on (POLICIES_PUBLISHED=true in .env) once AfroISO has approved the wording.
    'policies_published' => (bool) env('POLICIES_PUBLISHED', false),

    // Code from Google Search Console (the content value of its HTML tag). Optional.
    'google_site_verification' => env('GOOGLE_SITE_VERIFICATION'),

    // WhatsApp number for the chat box, digits only with the country code. Leave WHATSAPP_NUMBER empty in .env to hide the chat box.
    'whatsapp_number' => env('WHATSAPP_NUMBER', '263718781215'),

    // Enquiries from the website are emailed to this address.
    'office_email' => env('OFFICE_EMAIL', 'info@afroisoconsultants.org'),

    // First admin account, created by "php artisan db:seed".
    'admin_email' => env('ADMIN_EMAIL', 'admin@afroisoconsultants.org'),
    'admin_password' => env('ADMIN_PASSWORD'),

    // Leave a link empty to hide that icon in the footer.
    'social' => [
        'facebook' => env('SOCIAL_FACEBOOK'),
        'linkedin' => env('SOCIAL_LINKEDIN'),
        'instagram' => env('SOCIAL_INSTAGRAM'),
        'whatsapp' => env('SOCIAL_WHATSAPP'),
        'x-twitter' => env('SOCIAL_X'),
        'youtube' => env('SOCIAL_YOUTUBE'),
        'tiktok' => env('SOCIAL_TIKTOK'),
    ],
];
