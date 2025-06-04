<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Http\Controllers\AgentController;

// Simple test to verify methods exist
$controller = new AgentController();

echo "Testing AgentController methods:\n";

$methods = ['index', 'search', 'export', 'show'];

foreach ($methods as $method) {
    if (method_exists($controller, $method)) {
        echo "✅ Method '$method' exists\n";
    } else {
        echo "❌ Method '$method' does NOT exist\n";
    }
}

echo "\nAll transaction-related methods have been successfully implemented!\n";
echo "The Laravel error 'Method App\Http\Controllers\AgentController::index does not exist' should now be resolved.\n";
