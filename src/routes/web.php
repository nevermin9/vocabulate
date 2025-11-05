<?php

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => 'Hello, World!')
    ->middleware(['auth'])
    ->name('home');

Route::get('dashboard', fn () => Inertia::render('Dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
