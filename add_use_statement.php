<?php
$content = file_get_contents('routes/web.php');
$lines = explode("\n", $content);
$newLines = [];

for($i = 0; $i < count($lines); $i++) {
    $newLines[] = $lines[$i];
    if(strpos($lines[$i], 'use App\Http\Controllers\Customer\CustomerNotificationController;') !== false) {
        $newLines[] = 'use App\Http\Controllers\PublicContactController;';
    }
}

file_put_contents('routes/web.php', implode("\n", $newLines));
echo "Added the use statement successfully!\n";
?>