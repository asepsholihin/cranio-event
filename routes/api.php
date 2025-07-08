<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\EventController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('public')->group(function () {
    Route::controller(EventController::class)->group(function () {
        Route::get('/event-list', 'eventList');
        Route::get('/event/jamaah', 'eventJamaah');
         Route::post('/event/attendee', 'attendee');
    });
});
