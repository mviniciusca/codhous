<?php

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/**
 * TODO: Remove this fn after development
 * Auth auto login
 */
Route::get('/admin/login', function (): RedirectResponse {
    Auth::loginUsingId(1);

    return redirect('/admin');
})->name('filament.admin.auth.login');
