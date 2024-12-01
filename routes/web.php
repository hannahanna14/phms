<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
});

// Route::get('/about', function(){
//     return inertia('About', ['user' => 'Clepher']);
// });

// or
Route::inertia('/about', 'About', ['user' => 'Clepher']);
