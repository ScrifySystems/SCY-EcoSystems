<?php 
namespace Scy\Core\FileManager\Services;

use Illuminate\Support\ServiceProvider;

class FileManagerService extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');

        $this->loadViewsFrom(__DIR__.'/Views', 'btv-filemanager');

        $this->publishes([
            __DIR__.'/Config/filemanager.php' => config_path('filemanager.php'),
        ], 'config');
    }

    public function register()
    {
        //
    }
}