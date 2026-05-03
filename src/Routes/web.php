<?php 
use Illuminate\Support\Facades\Route;
use SCY\Core\FileManager\Controllers\PanelController;

Route::prefix('btv/scy/file-manager')->group(function () {

    Route::get('/', [PanelController::class, 'index']);

    Route::get('/login', [PanelController::class, 'loginPage']);

    Route::post('/login', [PanelController::class, 'login']);

});