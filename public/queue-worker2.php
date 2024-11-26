<?php



require __DIR__.'/../account/vendor/autoload.php';

// Boot up Laravel application

$app = require_once __DIR__.'/../account/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Run the queue worker

$lockFilePath = __DIR__ . '/queue_worker.lock';

// $data = file_get_contents("https://account.xchangebox.com.ng/check/lastupate");


$url = 'https://account.xchangebox.com.ng/check/lastupate';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$data = curl_exec($ch);
curl_close($ch);
// Check if the lock file exists
if (file_exists($lockFilePath)) {
    echo "Queue worker is already running.\n";
    exit; // Exit the script if the worker is already running
}
file_put_contents($lockFilePath, '');
$kernel->call('queue:work', ['--stop-when-empty' => true]);

// Delete the lock file when the queue worker stops
    if (file_exists($lockFilePath)) {
        unlink($lockFilePath);
    }
