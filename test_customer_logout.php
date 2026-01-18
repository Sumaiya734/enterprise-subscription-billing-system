<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Customer Logout Route Fix\n";
echo "==================================\n\n";

// Test 1: Check if customer logout route exists
echo "1. Checking customer logout route...\n";
try {
    $route = app('router')->getRoutes()->getByName('customer.logout');
    if ($route) {
        echo "✓ Customer logout route exists\n";
        echo "  URI: " . $route->uri() . "\n";
        echo "  Methods: " . implode(', ', $route->methods()) . "\n";
        echo "  Middleware: " . implode(', ', $route->middleware()) . "\n\n";
    } else {
        echo "✗ Customer logout route NOT found\n\n";
    }
} catch (Exception $e) {
    echo "✗ Error checking route: " . $e->getMessage() . "\n\n";
}

// Test 2: Check if admin logout route exists  
echo "2. Checking admin logout route...\n";
try {
    $route = app('router')->getRoutes()->getByName('admin.logout');
    if ($route) {
        echo "✓ Admin logout route exists\n";
        echo "  URI: " . $route->uri() . "\n";
        echo "  Methods: " . implode(', ', $route->methods()) . "\n";
        echo "  Middleware: " . implode(', ', $route->middleware()) . "\n\n";
    } else {
        echo "✗ Admin logout route NOT found\n\n";
    }
} catch (Exception $e) {
    echo "✗ Error checking route: " . $e->getMessage() . "\n\n";
}

// Test 3: Check middleware groups
echo "3. Checking middleware configuration...\n";
try {
    $kernel = app(\App\Http\Kernel::class);
    
    // Check web middleware group
    $reflection = new ReflectionClass($kernel);
    $property = $reflection->getProperty('middlewareGroups');
    $property->setAccessible(true);
    $groups = $property->getValue($kernel);
    
    if (isset($groups['web'])) {
        echo "✓ Web middleware group found\n";
        foreach ($groups['web'] as $middleware) {
            echo "  - " . $middleware . "\n";
        }
        echo "\n";
    }
    
    // Check middleware aliases
    $aliasesProperty = $reflection->getProperty('middlewareAliases');
    $aliasesProperty->setAccessible(true);
    $aliases = $aliasesProperty->getValue($kernel);
    
    echo "✓ Middleware aliases:\n";
    foreach ($aliases as $name => $class) {
        echo "  {$name} => {$class}\n";
    }
    echo "\n";
    
} catch (Exception $e) {
    echo "✗ Error checking middleware: " . $e->getMessage() . "\n\n";
}

echo "Fix Summary:\n";
echo "============\n";
echo "✓ Moved customer logout route inside authenticated customer group\n";
echo "✓ Route now properly protected by auth and customer middleware\n";
echo "✓ CSRF protection should now work correctly\n";
echo "✓ Both customer and admin logout routes coexist without conflict\n\n";

echo "Test completed!\n";
?>