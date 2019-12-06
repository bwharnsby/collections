<?php

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

use App\CollectionExample;

Route::get('/', function () {
    $obj = new CollectionExample();
    $data = [
        'Team 1', 'Team 2', 'Team 3', 'Team 4',
        'Team 5', 'Team 6', 'Team 7', 'Team 8',
    ];
    $schedule = $obj->scheduler($data, 1);
    return view('welcome', compact('schedule'));
});
