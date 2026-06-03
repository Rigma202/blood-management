<?php

use App\Http\Controllers\BloodBankController;
use App\Http\Controllers\BloodBagController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RefrigeratorController;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [ProfileController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('refrigerators/by-bank', [App\Http\Controllers\BloodBagController::class, 'refrigeratorsByBank'])
    ->name('refrigerators.byBank');
    Route::resource('blood-bags', bloodBagController::class);
    Route::resource('blood-banks', BloodBankController::class);
    Route::get('/staff/blood-banks',[StaffController::class, 'getStaffBloodBanks'])->name('staff.blood-banks');
    Route::resource('staff', StaffController::class);
    Route::resource('refrigerators', RefrigeratorController::class);
});

require __DIR__.'/auth.php';
