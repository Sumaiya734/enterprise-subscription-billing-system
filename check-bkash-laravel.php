<?php

// Load Laravel environment
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<h2>bKash Configuration Status</h2>";

// Check bKash config through Laravel
$bkashConfig = config('payment.bkash');

echo "<h3>Current bKash Configuration:</h3>";
echo "<pre>";
print_r($bkashConfig);
echo "</pre>";

// Check what's missing
$missing = [];
if (empty($bkashConfig['app_key'])) $missing[] = 'BKASH_APP_KEY';
if (empty($bkashConfig['app_secret'])) $missing[] = 'BKASH_APP_SECRET';
if (empty($bkashConfig['username'])) $missing[] = 'BKASH_USERNAME';
if (empty($bkashConfig['password'])) $missing[] = 'BKASH_PASSWORD';

if (!empty($missing)) {
    echo "<h3 style='color: red;'>❌ Missing bKash Configuration:</h3>";
    echo "<p>The following environment variables need to be set in your .env file:</p>";
    echo "<ul>";
    foreach ($missing as $var) {
        echo "<li><code>$var=your_value_here</code></li>";
    }
    echo "</ul>";
    echo "<p><strong>This is why bKash payment is failing!</strong></p>";
    echo "<p>The bKash gateway was working before because these credentials were properly configured.</p>";
} else {
    echo "<h3 style='color: green;'>✅ All bKash environment variables are set!</h3>";
    echo "<p>bKash should be working. If it's still failing, there might be an API issue.</p>";
}

echo "<h3>Solution:</h3>";
echo "<p>Add these lines to your <code>.env</code> file:</p>";
echo "<pre>";
echo "# bKash Configuration
BKASH_BASE_URL=https://tokenized.sandbox.bka.sh/v1.2.0-beta
BKASH_APP_KEY=your_app_key_here
BKASH_APP_SECRET=your_app_secret_here
BKASH_USERNAME=your_username_here
BKASH_PASSWORD=your_password_here
BKASH_SANDBOX=true";
echo "</pre>";

?>
