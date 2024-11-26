<?php

use Illuminate\Support\Facades\Http;

// Include Laravel's autoload file
require __DIR__.'/../account/vendor/autoload.php';

// Call the URL
$response = Http::get('https://account.xchangebox.com.ng/check/lastupdate');
dd("kdkdkd");
// Check if the request was successful (status code 200)
if ($response->successful()) {
    echo "Request to the URL was successful.\n";
    // You can handle the response here
    echo $response->body();
} else {
    echo "Request to the URL failed with status code: " . $response->status() . "\n";
    // You can handle the failure here
}
