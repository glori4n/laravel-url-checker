<?php

use App\Http\Controllers\UrlController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [UrlController::class, 'read'], function () {
    return view('dashboard');
})->middleware(['auth'])->name('url-read');

Route::get('/getAjaxData', [UrlController::class, 'ajax'])->middleware(['auth'])->name('url-ajax');

Route::group(['namespace' => 'urls', 'middleware' => ['auth'], 'prefix' => 'urls'], function () {
    Route::post('/create', [UrlController::class, 'create'])->name('url-create');
    Route::delete('{id}/delete', [UrlController::class, 'delete'])->name('url-delete');
});

Route::get('/download/{response}', function($response){
    return Storage::download($response);
})->name('download');

require __DIR__.'/auth.php';
