<?php

use App\Events\MessageReceived;
use App\Models\Message;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-event', function () {
    $message = Message::first();

    if (!$message) return;

//    MessageReceived::dispatch($message);
    event(new MessageReceived($message));

    return $message;
});
