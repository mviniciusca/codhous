<?php

use App\Http\Controllers\BudgetDownloadController;
use App\Http\Controllers\BudgetPdfController;
use App\Http\Controllers\ContentSectionPreviewController;
use App\Http\Controllers\DesignPreviewController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\NewsletterUnsubscribeController;
use App\Http\Controllers\PageBuilderPreviewController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ShowcaseController;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/**
 * Auto login de desenvolvimento: entra como o primeiro usuário ativo.
 * Só existe em APP_ENV=local; em qualquer outro ambiente vale o login do Filament.
 */
if (app()->isLocal()) {
    Route::get('/admin/login', function (): RedirectResponse {
        if (Auth::check()) {
            return redirect('/admin');
        }

        if ($user = User::query()->where('is_active', true)->orderBy('id')->first()) {
            Auth::login($user);

            return redirect('/admin');
        }

        abort(403, 'Usuário não encontrado. Execute o seeder do banco de dados.');
    })->name('filament.admin.auth.login');
}

// Budget public download route with signed URL protection (legacy, kept for backward compatibility)
Route::get('/budget/download/{budget}', [BudgetDownloadController::class, 'download'])
    ->name('budget.download')
    ->middleware('signed');

// New token-based PDF download route
Route::get('/budget/pdf/{token}', [BudgetPdfController::class, 'download'])
    ->name('budget.pdf.download');

Route::get('nossas-obras/{id}', [ShowcaseController::class, 'show'])->name('showcase.show');

// Page Builder live preview (painel administrativo)
Route::get('/admin/page-builder/preview/{token}', PageBuilderPreviewController::class)
    ->middleware('auth')
    ->where('token', '[a-f0-9]{64}')
    ->name('page-builder.preview');

// Live preview da criação/edição de seções (painel administrativo)
Route::get('/admin/section-builder/preview/{token}', ContentSectionPreviewController::class)
    ->middleware('auth')
    ->where('token', '[a-f0-9]{64}')
    ->name('content-sections.preview');

// Pré-visualização do Design e SEO (configurações não salvas sobre o site real)
Route::get('/admin/design-preview/{token}', DesignPreviewController::class)
    ->middleware('auth')
    ->where('token', '[a-f0-9]{64}')
    ->name('design.preview');

// Convite para a equipe (público; o token é validado pelo hash guardado no banco)
Route::middleware('throttle:20,1')
    ->where(['token' => '[A-Za-z0-9]{64}'])
    ->group(function () {
        Route::get('/convite/{token}', [InvitationController::class, 'show'])->name('invitation.show');
        Route::post('/convite/{token}', [InvitationController::class, 'store'])->name('invitation.accept');
    });

// Cancelar inscrição da newsletter pelo link do e-mail (URL assinada; POST sem CSRF, ver bootstrap/app.php)
Route::middleware(['signed', 'throttle:30,1'])
    ->whereNumber('newsletter')
    ->group(function () {
        Route::get('/newsletter/cancelar/{newsletter}', [NewsletterUnsubscribeController::class, 'show'])->name('newsletter.unsubscribe');
        Route::post('/newsletter/cancelar/{newsletter}', [NewsletterUnsubscribeController::class, 'store'])->name('newsletter.unsubscribe.store');
    });

// Dynamic Pages Catch-all (must be at the end)
Route::get('/{slug?}', [PageController::class, 'show'])
    ->name('page.show')
    ->where('slug', '.*');
