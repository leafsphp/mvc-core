<?php

app()->get('/privacy', function () {
    response()->inertia('legal/privacy', [
        'appName' => _env('APP_NAME', 'We'),
        'contactEmail' => _env('CONTACT_EMAIL', 'support@example.com'),
        'lastUpdated' => date('F j, Y'),
    ]);
});

app()->get('/terms', function () {
    response()->inertia('legal/terms', [
        'appName' => _env('APP_NAME', 'this service'),
        'contactEmail' => _env('CONTACT_EMAIL', 'support@example.com'),
        'lastUpdated' => date('F j, Y'),
    ]);
});
