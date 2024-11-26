<?php


// die("KDKDJKDK");
// // Include Laravel's autoload file
// require __DIR__.'/../account/vendor/autoload.php';

// // Boot up Laravel application

// $app = require_once __DIR__.'/../account/bootstrap/app.php';
// $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// // Run the queue worker
// $kernel->call('queue:work', ['--stop-when-empty' => true]);


// Include Laravel's autoload file
require __DIR__.'/../account/vendor/autoload.php';

// Boot up Laravel application
$app = require_once __DIR__.'/../account/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Check if the queue worker is already running
$queueProcessName = 'queue:work';
$runningProcesses = shell_exec("ps aux | grep '{$queueProcessName}' | grep -v grep");
dd($runningProcesses);
if (!$runningProcesses) {
    // If the queue worker is not running, start it
    $kernel->call('queue:work', ['--stop-when-empty' => true]);
    echo "Queue worker started running.\n";
} else {
    echo "Queue worker is already running.\n";
}

