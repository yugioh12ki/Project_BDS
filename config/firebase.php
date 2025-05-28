<?php

return [
    'credentials_file' => env('FIREBASE_CREDENTIALS', storage_path('firebase_sdk.json')),
    'database_uri' => env('FIREBASE_DATABASE_URL'),
];
