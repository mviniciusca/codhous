<?php

use App\Http\Controllers\BudgetDownloadController;
use App\Http\Controllers\BudgetPdfController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/**
 * TODO: Remove this fn after development
 * Auth auto login
 */
Route::get('/admin/login', function (): RedirectResponse {
    if (Auth::check()) {
        return redirect('/admin');
    }

    if ($user = \App\Models\User::first()) {
        Auth::login($user);
        return redirect('/admin');
    }

    abort(403, 'Usuário não encontrado. Execute o seeder do banco de dados.');
})->name('filament.admin.auth.login');

// Budget public download route with signed URL protection (legacy, kept for backward compatibility)
Route::get('/budget/download/{budget}', [BudgetDownloadController::class, 'download'])
    ->name('budget.download')
    ->middleware('signed');

// New token-based PDF download route
Route::get('/budget/pdf/{token}', [BudgetPdfController::class, 'download'])
    ->name('budget.pdf.download');

Route::get('/nossas-obras', function () {
    return view('showcase');
})->name('showcase');
