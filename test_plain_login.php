<?php

// Test login with plain text password (như Flutter sẽ gửi)
$url = 'http://192.168.102.84:8000/api/v1/login';
$data = [
    'email' => 'everyonebody440@gmail.com',
    'password' => '123456' // Plain text password
];

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

$response = curl_exec($ch);
curl_close($ch);

echo "Login with plain text password '123456':\n";
echo $response . "\n\n";

// Test register với plain text password
echo "Testing registration with plain text password:\n";
$registerData = [
    'name' => 'Test User Flutter',
    'email' => 'testflutter@example.com',
    'password' => '123456',
    'password_confirmation' => '123456',
    'phone' => '0123456789',
    'role' => 'Customer'
];

$registerPostData = json_encode($registerData);

$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, 'http://192.168.102.84:8000/api/v1/register');
curl_setopt($ch2, CURLOPT_POST, 1);
curl_setopt($ch2, CURLOPT_POSTFIELDS, $registerPostData);
curl_setopt($ch2, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($registerPostData)
]);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);

$registerResponse = curl_exec($ch2);
curl_close($ch2);

echo $registerResponse . "\n";
