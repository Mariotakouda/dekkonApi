<?php

return [
    'currency' => 'XOF',
    'default_delivery_fee' => (float) env('DEKKON_DEFAULT_DELIVERY_FEE', 1000),
    'stock_reservation_minutes' => (int) env('DEKKON_STOCK_RESERVATION_MINUTES', 30),

    'fedapay' => [
        'public_key' => env('FEDAPAY_PUBLIC_KEY'),
        'secret_key' => env('FEDAPAY_SECRET_KEY'),
        'environment' => env('FEDAPAY_ENVIRONMENT', 'sandbox'),
        // Secret de l'endpoint webhook (Workbench → Webhooks → l'endpoint → "Click to reveal").
        // Différent entre sandbox et live, et différent du secret_key ci-dessus.
        'webhook_secret' => env('FEDAPAY_WEBHOOK_SECRET'),
    ],
];
