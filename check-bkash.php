<?php
// Quick bKash configuration check
echo "<h2>bKash Configuration Status</h2>";

// Check if bKash config exists
$paymentConfig = include __DIR__ . '/config/payment.php';
$bkashConfig = $paymentConfig['bkash'];

echo "<h3>Current bKash Configuration:</h3>";
echo "<pre>";
var_dump($bkashConfig);
echo "</pre>";

// Check environment variables
$envVars = [
    'BKASH_APP_KEY' => getenv('BKASH_APP_KEY'),
    'BKASH_APP_SECRET' => getenv('BKASH_APP_SECRET'),
    'BKASH_USERNAME' => getenv('BKASH_USERNAME'),
    'BKASH_PASSWORD' => getenv('BKASH_PASSWORD'),
    'BKASH_BASE_URL' => getenv('BKASH_BASE_URL'),
];

echo "<h3>Environment Variables:</h3>";
echo "<pre>";
var_dump($envVars);
echo "</pre>";

// Check what's missing
$missing = [];
foreach ($envVars as $key => $value) {
    if (empty($value)) {
        $missing[] = $key;
    }
}

if (!empty($missing)) {
    echo "<h3 style='color: red;'>Missing Environment Variables:</h3>";
    echo "<p>The following bKash environment variables are not set:</p>";
    echo "<ul>";
    foreach ($missing as $var) {
        echo "<li><code>$var</code></li>";
    }
    echo "</ul>";
    echo "<p><strong>This is why bKash is not working!</strong></p>";
} else {
    echo "<h3 style='color: green;'>✅ All bKash environment variables are set!</h3>";
}

?>
