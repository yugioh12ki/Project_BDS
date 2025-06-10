<?php

// Test login với MD5 hash
$url = 'http://192.168.102.84:8000/api/v1/login';
$data = [
    'email' => 'everyonebody440@gmail.com',
    'password' => 'e10adc3949ba59abbe56e057f20f883e' // MD5 hash of '123456'
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

echo "Login Response:\n";
echo $response . "\n\n";

// Test với plain text password để xem có hoạt động không
echo "Testing with plain text password '123456':\n";
$data2 = [
    'email' => 'everyonebody440@gmail.com',
    'password' => '123456'
];

$postData2 = json_encode($data2);

$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, $url);
curl_setopt($ch2, CURLOPT_POST, 1);
curl_setopt($ch2, CURLOPT_POSTFIELDS, $postData2);
curl_setopt($ch2, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($postData2)
]);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);

$response2 = curl_exec($ch2);
curl_close($ch2);

echo $response2 . "\n";
