<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<h2>✅ bKash Configuration Status</h2>";

$bkashConfig = config('payment.bkash');

echo "<h3>bKash Configuration Loaded:</h3>";
echo "<pre>";
print_r($bkashConfig);
echo "</pre>";

// Check if all required fields are present
$required = ['app_key', 'app_secret', 'username', 'password'];
$missing = [];

foreach ($required as $field) {
    if (empty($bkashConfig[$field])) {
        $missing[] = $field;
    }
}

if (empty($missing)) {
    echo "<h3 style='color: green;'>🎉 SUCCESS: All bKash credentials are configured!</h3>";
    echo "<p>bKash gateway should now work properly.</p>";
    echo "<p><strong>Next step:</strong> Test the bKash payment on the purchase page.</p>";
} else {
    echo "<h3 style='color: red;'>❌ Missing Configuration:</h3>";
    echo "<p>Missing fields: " . implode(', ', $missing) . "</p>";
}

?>
