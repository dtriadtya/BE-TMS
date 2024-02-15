<?php

use App\Http\Controllers\Admin\AdminPegawaiController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PenggajianController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\ReimburseController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// User
Route::get('/users', [UserController::class, 'index']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/change-password', [UserController::class, 'changePassword']);


// Presensi
Route::get('/presensi', [PresensiController::class, 'index']);
Route::get('/presensi/user/{userId}', [PresensiController::class, 'byUser']);
Route::get('/presensi-today/user/{userId}', [PresensiController::class, 'getPresensiToday']);
Route::post('/presensi/attend', [PresensiController::class, 'presensi']);

// Perusahaan
Route::get('/perusahaan', [PerusahaanController::class, 'index']);

//pegawai
Route::get('/pegawai/{userId}', [PegawaiController::class, 'getPegawai']);
Route::get('/pegawai/image/{pegawaiId}', [PegawaiController::class, 'getPhotoByPegawaiId']);
Route::post('/pegawai/update-profile-picture', [PegawaiController::class, 'imageStore']);
Route::post('/pegawai/update-profile', [PegawaiController::class, 'updateProfile']);

//penggajian
Route::get('/penggajian/{userId}', [PenggajianController::class, 'getAllPayRollByUser']);

//reimburse
Route::get('/reimburse/{userId}', [ReimburseController::class, 'getAllReimburseByUser']);
Route::get('/reimburse/lampiran/{id_reimburse}', [ReimburseController::class, 'showLampiranById']);
Route::post('/reimburse/lampiran', [ReimburseController::class, 'storeLampiranReimburseById']);
Route::post('/reimburse/create', [ReimburseController::class, 'storeReimburseByUser']);

//Cuti
Route::controller(CutiController::class)->group(function(){
    Route::get('/cutis/{userId}', 'findByUser');
    Route::get('/cuti/{cutiId}', 'getDetailCuti');
    Route::post('/cuti/add', 'addCuti');
    Route::get('/cuti-sisa/{userId}', 'getAddData');
    Route::put('/cuti/approve/{cutiId}','approveCuti');
    Route::put('/cuti/reject/{cutiId}','rejectCuti');

});

Route::prefix('admin')->group(function() {
    Route::prefix('pegawai')->group(function(){
        Route::controller(AdminPegawaiController::class)->group(function(){
            Route::post('', 'getPegawai');
            Route::post('/add', 'addPegawai');
            Route::post('/update', 'updatePegawai');
            Route::get('/image/{id}', 'getPhotoByPegawaiId');
        });
    });
});
