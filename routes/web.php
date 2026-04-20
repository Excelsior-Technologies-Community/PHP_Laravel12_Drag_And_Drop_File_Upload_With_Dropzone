<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DropzoneController;

Route::get('/', [DropzoneController::class, 'index'])->name('dropzone.index');
Route::post('/dropzone/store', [DropzoneController::class, 'store'])->name('dropzone.store');
Route::delete('/dropzone/delete/{id}', [DropzoneController::class, 'destroy'])->name('dropzone.delete');
Route::get('/dropzone/download/{id}', [DropzoneController::class, 'download'])->name('dropzone.download');