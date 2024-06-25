<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Cms\PrivacyPolicyController;
use App\Http\Controllers\Admin\Cms\TermConditionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Common\GuestController;

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

Route::middleware('auth')->group(function () {
    Route::get('/logout', [LoginController::class, 'logout'])->name('auth.logout');
});

Route::get('/privacy-policy', [PrivacyPolicyController::class, 'webView'])->name('auth.privacyPolicy.webView');
Route::get('/terms-and-condition', [TermConditionController::class, 'webView'])->name('auth.termsAndCondition.wevView');

Route::group(['domain' => config('services.url.web'), 'as' => 'guest.'], function () {
    Route::get('/', [GuestController::class, 'index'])->name('home');
    Route::get('/auth/reset-password/{email}', [GuestController::class, 'resetPassword'])->name('auth.resetPassword');
    Route::post('/auth/update-reset-password', [GuestController::class, 'updateResetPassword'])->name('auth.updateResetPassword');
    Route::get('/auth/password-reset-confirmation', [GuestController::class, 'passwordResetConfirmation'])->name('auth.passwordResetConfirmation');

    Route::get('/privacy-policy', [PrivacyPolicyController::class, 'webView'])->name('auth.privacyPolicy.webView');
    Route::get('/terms-and-condition', [TermConditionController::class, 'webView'])->name('auth.termsAndCondition.wevView');
});
