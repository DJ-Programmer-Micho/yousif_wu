<?php

use App\Http\Middleware\Localization;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\ReceiptDompdfController;
use App\Http\Controllers\BankStatementExportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::post('/set-locale', [Localization::class, 'setLocale'])->name('setLocale');
Route::middleware('guest')->group(function () {
    Route::get('/auth', [AuthController::class, 'login'])->name('auth.login');
    Route::post('/auth', [AuthController::class, 'loginAction'])->name('auth.login.action');
    Route::get('/account-suspended', [AuthController::class, 'suspended'])->name('auth.suspended');


});
Route::middleware('auth','userstatus')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/splash-options', [AppController::class, 'splash'])->name('splash');
    Route::post('/splash-options', [AppController::class, 'splashSave'])->name('splash.save');
    
    Route::get('/admin-2fa', [TwoFactorController::class, 'challenge'])->name('2fa.challenge');
    Route::post('/admin-2fa', [TwoFactorController::class, 'verify'])->name('2fa.verify');

    Route::middleware([Localization::class])->group(function () {
        Route::get('/', [AppController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [AppController::class, 'profile'])->name('profile');
        Route::get('/setting', [AppController::class, 'setting'])->name('setting');
        Route::get('/announcement', [AppController::class, 'announcement'])->name('announcement');

        Route::get('/auth-register', [AppController::class, 'register'])->middleware('twofactor')->name('register');
        Route::get('/sender', [AppController::class, 'sender'])->name('sender');
        Route::middleware(['receiver.enabled'])->group(function () {
          Route::get('/reciever', [AppController::class, 'reciever'])->name('reciever');
        });
        Route::get('/country-limit', [AppController::class, 'countryLimit'])->name('country-limit');
        Route::get('/general-country-limit', [AppController::class, 'generalCountryLimit'])->name('general-country-limit');
        Route::get('/country-tax', [AppController::class, 'countryTax'])->name('country-tax');
        Route::get('/general-country-tax', [AppController::class, 'generalCountryTax'])->name('general-country-tax');
        Route::get('/country-rules', [AppController::class, 'countryRules'])->name('country-rules');
        Route::get('/country-info', [AppController::class, 'countryInfo'])->name('country-info');
        Route::get('/mtcn', [AppController::class, 'mtcn'])->name('mtcn');

        Route::get('/bank-statement', [AppController::class, 'bankStatement'])->name('bank.statement');
        Route::get('/sender-pending-transfers', [AppController::class, 'senderPendingTransfer'])->name('sender.pending.transfer');
        Route::get('/sender-executed-transfers', [AppController::class, 'senderExecutedTransfer'])->name('sender.executed.transfer');
        Route::get('/sender-rejected-transfers', [AppController::class, 'senderRejectedTransfer'])->name('sender.rejected.transfer');
        Route::get('/receiver-pending-transfers', [AppController::class, 'receiverPendingTransfer'])->name('receiver.pending.transfer');
        Route::get('/receiver-executed-transfers', [AppController::class, 'receiverExecutedTransfer'])->name('receiver.executed.transfer');
        Route::get('/receiver-rejected-transfers', [AppController::class, 'receiverRejectedTransfer'])->name('receiver.rejected.transfer');

        Route::get('/register-sender-balance', [AppController::class, 'senderBalance'])->middleware('twofactor')->name('balance.sender');
        Route::get('/register-receiver-balance', [AppController::class, 'receiverBalance'])->middleware('twofactor')->name('balance.reciever');
    });
});
    // Route::middleware('auth','userstatus')->group(function () {
    Route::get('/bank-statement/export', BankStatementExportController::class)
      ->name('bank-statement.export');

    // routes/web.php
    Route::get('/balance-details/export', \App\Http\Controllers\BalanceDetailsExportController::class)
        ->name('balance.details.export')
        ->middleware(['auth']);

    Route::get('/receipts/{sender}/{type}', [ReceiptDompdfController::class, 'show'])
      ->whereIn('type', ['customer','agent','both'])
      ->middleware(['auth','userstatus'])
      ->name('receipts.dompdf.show');

    Route::get('/receipts-executed/{sender}/{type}', [ReceiptDompdfController::class, 'senderShow'])
      ->whereIn('type', ['customer','agent','both'])
      ->middleware(['auth','userstatus'])
      ->name('receipts.dompdf.senderShow');

    Route::get('/receipts-receiver/{receiver}/{type}', [ReceiptDompdfController::class, 'receiverShow'])
      ->whereIn('type', ['customer','agent','both'])
      ->middleware(['auth','userstatus'])
      ->name('receipts.receiver.dompdf.show');

    Route::get('/receipts-receiver-executed/{receiver}/{type}', [ReceiptDompdfController::class, 'executedReceiverShow'])
      ->whereIn('type', ['customer','agent','both'])
      ->middleware(['auth','userstatus'])
      ->name('receipts.receiver.dompdf.receiverShow');
// });