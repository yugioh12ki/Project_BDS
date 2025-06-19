<?php

return [
    /*
    |--------------------------------------------------------------------------
    | VNPay Configuration
    |--------------------------------------------------------------------------
    */

    'url' => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
    'tmn_code' => env('VNPAY_TMN_CODE', 'F0DR9Y2U'),
    'hash_secret' => env('VNPAY_HASH_SECRET', 'BC0OVP01E49X7O56QN2A0METEOO8GRES'),

    // Return URLs
    'return_url' => env('APP_URL', 'http://localhost:8000') . '/payment/vnpay/return',
    'ipn_url' => env('APP_URL', 'http://localhost:8000') . '/payment/vnpay/ipn',
];
