<?php

return [
    'enabled' => (bool) env('SUBMISSIONS_ENABLED', false),
    'disk' => env('SUBMISSIONS_DISK', 'local'),
    'privacy_approved' => (bool) env('SUBMISSIONS_PRIVACY_APPROVED', false),
    'storage_persistent' => (bool) env('SUBMISSIONS_STORAGE_PERSISTENT', false),
    'manuscript_max_kb' => (int) env('SUBMISSIONS_MANUSCRIPT_MAX_KB', 15360),
    'attachment_max_kb' => (int) env('SUBMISSIONS_ATTACHMENT_MAX_KB', 10240),
    'personal_email_domains' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('SUBMISSIONS_PERSONAL_EMAIL_DOMAINS', 'gmail.com,hotmail.com,outlook.com,yahoo.com,icloud.com,live.com')),
    ))),
];
