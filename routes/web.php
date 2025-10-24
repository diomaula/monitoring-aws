<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AwsController;
use App\Http\Controllers\ReportController;

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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('/signUp', [AuthController::class, 'registerStore'])->name('signUp');
Route::post('/signIn', [AuthController::class, 'loginStore'])->name('signIn');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {return view('dashboard');})->name('dashboard');

    Route::get('/api/stations', [AwsController::class, 'stations']);
    Route::get('/api/aws/weekly-average', [AwsController::class, 'getWeeklyAverage']);
    Route::get('/api/aws/weekly-multi', [AwsController::class, 'weeklyMultiParameter']);
    Route::get('/aws/{id}', [AwsController::class, 'index']);
    Route::get('chart-data/{code}', [AwsController::class, 'getChartData']);
    Route::get('/report', [ReportController::class, 'index']);
    Route::get('/report', [ReportController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/pdf', [ReportController::class, 'cetakPdf'])->name('laporan.pdf');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
