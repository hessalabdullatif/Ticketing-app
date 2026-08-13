<?php
//for frontend
use Inertia\Inertia;

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');
//for frontend
Route::get('/events', function () {
    return Inertia::render('events/index');
});

Route::get('/events/{id}', function ($id) {
    return Inertia::render('events/show', ['id' => $id]);
});