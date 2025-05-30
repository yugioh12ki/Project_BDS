<?php
// Test authentication flows

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test different user roles
$testUsers = [
    ['email' => 'everyonebody440@gmail.com', 'password' => '123456', 'expected_role' => 'Admin'],
    ['email' => 'nguyenvana1203@gmail.com', 'password' => '123456', 'expected_role' => 'Agent'],
    ['email' => 'nguyenphat241203@gmail.com', 'password' => '123456', 'expected_role' => 'Owner'],
];

echo "Testing Authentication Logic...\n\n";

foreach ($testUsers as $testUser) {
    echo "Testing user: {$testUser['email']}\n";

    // Find user in database
    $user = App\Models\User::where('Email', $testUser['email'])->first();

    if (!$user) {
        echo "❌ User not found\n\n";
        continue;
    }

    echo "✅ User found: {$user->Name} (Role: {$user->Role})\n";

    // Test password
    $passwordMatch = $user->PasswordHash === md5($testUser['password']);
    echo ($passwordMatch ? "✅" : "❌") . " Password check: " . ($passwordMatch ? "PASS" : "FAIL") . "\n";

    // Test role match
    $roleMatch = $user->Role === $testUser['expected_role'];
    echo ($roleMatch ? "✅" : "❌") . " Role check: Expected {$testUser['expected_role']}, Got {$user->Role}\n";

    // Test account status
    $isActive = $user->StatusUser === 'active';
    echo ($isActive ? "✅" : "❌") . " Account status: {$user->StatusUser}\n";

    // Simulate login redirect logic
    $expectedRedirect = match ($user->Role) {
        'Admin' => '/admin/dashboard',
        'Owner', 'Agent' => '/',
        'Customer' => '/',
        default => '/login (error)',
    };

    echo "🔄 Expected redirect: {$expectedRedirect}\n";
    echo "\n";
}

echo "Summary:\n";
echo "- Admin users should redirect to /admin/dashboard\n";
echo "- Owner/Agent/Customer users should redirect to / (homepage)\n";
echo "- All roles should be able to access homepage after login\n";
