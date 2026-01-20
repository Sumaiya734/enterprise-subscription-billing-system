<?php

// Test bKash configuration
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Config;

echo "<h2>bKash Configuration Test</h2>";

// Check bKash config
$bkashConfig = [
    'base_url' => env('BKASH_BASE_URL', 'https://tokenized.sandbox.bka.sh/v1.2.0-beta'),
    'app_key' => env('BKASH_APP_KEY', ''),
    'app_secret' => env('BKASH_APP_SECRET', ''),
    'username' => env('BKASH_USERNAME', ''),
    'password' => env('BKASH_PASSWORD', ''),
];

echo "<pre>";
echo "bKash Configuration:\n";
print_r($bkashConfig);
echo "</pre>";

// Check if configuration is complete
$missing = [];
if (empty($bkashConfig['app_key'])) $missing[] = 'BKASH_APP_KEY';
if (empty($bkashConfig['app_secret'])) $missing[] = 'BKASH_APP_SECRET';
if (empty($bkashConfig['username'])) $missing[] = 'BKASH_USERNAME';
if (empty($bkashConfig['password'])) $missing[] = 'BKASH_PASSWORD';

if (!empty($missing)) {
    echo "<h3 style='color: red;'>Missing Configuration:</h3>";
    echo "<p>Please add these to your .env file:</p>";
    echo "<pre>";
    foreach ($missing as $key) {
        echo "$key=your_value_here\n";
    }
    echo "</pre>";
} else {
    echo "<h3 style='color: green;'>bKash configuration is complete!</h3>";
}

?>
