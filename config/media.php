<?php

return [
    'uploads_enabled' => (bool) env('MEDIA_UPLOADS_ENABLED', true),
    'max_image_kb' => (int) env('MEDIA_MAX_IMAGE_KB', 5120),
    'max_pdf_kb' => (int) env('MEDIA_MAX_PDF_KB', 20480),
];
