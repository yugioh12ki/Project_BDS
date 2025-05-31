<?php

echo "=== TESTING REAL ESTATE APP AUTHENTICATION ===\n\n";

$baseUrl = 'http://192.168.102.84:8000/api/v1';

// Test 1: Login với user hiện tại
echo "1. Testing LOGIN with existing user:\n";
$loginData = [
    'email' => 'everyonebody440@gmail.com',
    'password' => '123456'
];

$response = sendPostRequest($baseUrl . '/login', $loginData);
echo "Response: " . json_encode(json_decode($response), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// Test 2: Register user mới
echo "2. Testing REGISTER with new user:\n";
$registerData = [
    'name' => 'Flutter Test User',
    'email' => 'fluttertest' . rand(1000, 9999) . '@example.com',
    'password' => '123456',
    'password_confirmation' => '123456',
    'phone' => '098' . rand(1000000, 9999999),
    'role' => 'Customer'
];

$response = sendPostRequest($baseUrl . '/register', $registerData);
$registerResponse = json_decode($response, true);
echo "Response: " . json_encode($registerResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// Test 3: Login với user vừa register
if (isset($registerResponse['success']) && $registerResponse['success']) {
    echo "3. Testing LOGIN with newly registered user:\n";
    $newLoginData = [
        'email' => $registerData['email'],
        'password' => $registerData['password']
    ];
    
    $response = sendPostRequest($baseUrl . '/login', $newLoginData);
    echo "Response: " . json_encode(json_decode($response), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
}

echo "=== AUTHENTICATION TESTS COMPLETED ===\n";

function sendPostRequest($url, $data) {
    $postData = json_encode($data);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($postData)
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    
    if (curl_error($ch)) {
        $response = '{"success": false, "message": "Connection error: ' . curl_error($ch) . '"}';
    }
    
    curl_close($ch);
    return $response;
}
