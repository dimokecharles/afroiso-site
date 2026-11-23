<?php

/*
 * Website text that staff can edit under "Website text" in the admin panel.
 *
 * kind: "line" is one line, "paragraph" is a short paragraph, "page" is long text with headings and lists.
 * default: the built-in wording. Policy pages have none here; their built-in drafts are in
 * resources/views/pages/policies.
 */
return [
    'items' => [
        'home.hero_title' => [
            'label' => 'Home page: main heading',
            'group' => 'Home page',
            'kind' => 'line',
            'default' => 'Training the people who build and audit management systems.',
        ],
        'home.hero_intro' => [
            'label' => 'Home page: introduction under the heading',
            'group' => 'Home page',
            'kind' => 'paragraph',
            'default' => 'AfroISO Institute & Consultants teaches ISO 9001, ISO 14001, ISO 45001 and ISO/IEC 27001, including internal auditor courses, through our e-learning platform. Our consultants help organisations prepare for certification, and we publish and distribute books and magazines.',
        ],
        'about.vision' => [
            'label' => 'About page: our vision',
            'group' => 'About page',
            'kind' => 'paragraph',
            'default' => "To be Africa's most trusted institute for management system standards, so that African organisations can compete on quality anywhere in the world.",
        ],
        'about.mission' => [
            'label' => 'About page: our mission',
            'group' => 'About page',
            'kind' => 'paragraph',
            'default' => 'To make ISO standards practical and within reach for African professionals and organisations, through e-learning, hands-on consulting and clear publications.',
        ],
        'policy.privacy-policy' => ['label' => 'Privacy policy', 'group' => 'Policies', 'kind' => 'page', 'default' => ''],
        'policy.refund-policy' => ['label' => 'Refund policy', 'group' => 'Policies', 'kind' => 'page', 'default' => ''],
        'policy.terms' => ['label' => 'Terms and conditions', 'group' => 'Policies', 'kind' => 'page', 'default' => ''],
    ],
];
