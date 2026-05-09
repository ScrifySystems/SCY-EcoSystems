<?php

namespace Scy\Core\Composer;

class Installer
{
    public static function postInstall(): void
    {
        self::updateIndex();
    }

    public static function postUpdate(): void
    {
        self::updateIndex();
    }

    private static function updateIndex(): void
    {
        $indexPath = getcwd() . '/public/index.php';

        if (!file_exists($indexPath)) {
            echo "public/index.php not found.\n";
            return;
        }

        $content = file_get_contents($indexPath);

        if (str_contains($content, 'Route::load();')) {
            echo "index.php already patched.\n";
            return;
        }

        $replace = <<<'PHP'
Route::load();

$handled = Route::dispatch(
    $_SERVER['REQUEST_URI'],
    $_SERVER['REQUEST_METHOD']
);

if (!$handled) {
    $app->handleRequest(Request::capture());
}
PHP;

        $content = str_replace(
            '$app->handleRequest(Request::capture());',
            $replace,
            $content
        );

        file_put_contents($indexPath, $content);

        echo "index.php updated successfully.\n";
    }
}
