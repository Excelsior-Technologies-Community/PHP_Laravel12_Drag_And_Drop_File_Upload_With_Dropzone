<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DropzoneController;

/*
|--------------------------------------------------------------------------
| Dropzone File Upload Routes
|--------------------------------------------------------------------------
|
| These routes handle the drag & drop file upload feature
| using Dropzone.js in Laravel 12.
|
*/

// Display the Dropzone upload page
Route::get('/dropzone', [DropzoneController::class, 'index']);

// Handle file upload request from Dropzone (AJAX)
Route::post('/dropzone/store', [DropzoneController::class, 'store'])
     ->name('dropzone.store');

// Handle file delete request (soft delete + storage delete)
Route::delete('/dropzone/{id}', [DropzoneController::class, 'destroy'])
     ->name('dropzone.destroy');

/*
|--------------------------------------------------------------------------
| Default Welcome Route
|--------------------------------------------------------------------------
|
| Default Laravel welcome page route.
|
*/
Route::get('/', function () {
    return view('welcome');
});
