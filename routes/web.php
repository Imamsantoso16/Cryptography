<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;
use Yajra\DataTables\Facades\DataTables;
use App\Models\File;

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

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/panduan', function () {
        return view('panduan');
    });

    Route::get('/dekripsi', [FileController::class, 'showDekripsiForm'])->name('dekripsi.form');
    Route::post('/dekripsi', [FileController::class, 'dekripsi'])->name('dekripsi.store');

    Route::get('/enkripsi', function () {
        return view('enkripsi');
    });
    Route::post('/enkripsi', [FileController::class, 'store'])->name('enkripsi.store');

    Route::get('/daftar-file', [FileController::class, 'index']);
    Route::delete('/files/{file}', [FileController::class, 'destroy'])->name('files.destroy');
    Route::get('files/download/{file}/{type}', [FileController::class, 'download'])->name('files.download');
});


Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
