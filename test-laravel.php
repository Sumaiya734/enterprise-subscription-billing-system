<?php
// Simple test to check if Laravel routing works

echo "<h1>Laravel Test</h1>";
echo "<p>If you can see this page with proper HTML, Laravel is working!</p>";
echo "<p>Current URL: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>Server: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";

// Test Laravel
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    echo "<p style='color: green;'>✅ Laravel bootstrap successful!</p>";
    
    // Test database
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    $count = App\Models\CustomerMessage::count();
    echo "<p style='color: green;'>✅ Database connection successful! Messages: $count</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>Test Links:</h2>";
echo "<ul>";
echo "<li><a href='/customer-messages-working'>Customer Messages (Working Route)</a></li>";
echo "<li><a href='/admin/customer-messages'>Customer Messages (Admin Route)</a></li>";
echo "<li><a href='/test-layout'>Test Layout</a></li>";
echo "</ul>";
?>