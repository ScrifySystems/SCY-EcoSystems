<?php

use Scy\Core\Routes\Route;
use Scy\Core\FileManager\Controllers\PanelController;
Route::prefix('btv/scy/file-manager')->group(function () {

    Route::get('/login', [PanelController::class, 'loginPage']);
    Route::post('/login', [PanelController::class, 'login']);

    Route::get('/', [PanelController::class, 'index']);

    Route::post('/upload', [PanelController::class, 'upload']);
    Route::post('/delete', [PanelController::class, 'delete']);
    Route::post('/mkdir', [PanelController::class, 'mkdir']);
    Route::post('/rename', [PanelController::class, 'rename']);
    Route::post('/chmod', [PanelController::class, 'chmod']);

    Route::get('/edit', [PanelController::class, 'edit']);
    Route::post('/save', [PanelController::class, 'save']);

    Route::get('/download', [PanelController::class, 'download']);
});