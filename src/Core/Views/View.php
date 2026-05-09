<?php

namespace Scy\Core\Views;

class View
{
    /**
     * Render a view file and return the output as a string.
     *
     * @param  string      $view    Dot-notation view name  (e.g. "folder.file" → folder/file.blade.php)
     * @param  array       $data    Variables to expose inside the view
     * @param  string|null $module  Override the auto-detected module name
     * @return string              Rendered HTML
     *
     * @throws \InvalidArgumentException  When $view is empty
     * @throws \RuntimeException          When the view file cannot be found
     */
    public static function make(string $view, array $data = [], ?string $module = null): string
    {
        if (trim($view) === '') {
            throw new \InvalidArgumentException('View name cannot be empty.');
        }

        $module = $module ?? self::guessModule();

        if (empty($module)) {
            throw new \RuntimeException(
                'Module could not be resolved automatically. Pass $module explicitly to View::make().'
            );
        }

        $file = self::resolvePath($module, $view);

        if (!is_file($file)) {
            throw new \RuntimeException("View not found: [{$module}] {$view}  →  {$file}");
        }

        return self::render($file, $data);
    }

    // ─────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────

    /**
     * Build the absolute path to a view file.
     * dirname(__DIR__) points at the directory that contains the Core folder.
     */
    private static function resolvePath(string $module, string $view): string
    {
        // Convert dot-notation → directory separator  (e.g. "admin.users" → "admin/users")
        $relativePath = str_replace('.', DIRECTORY_SEPARATOR, $view);

        return implode(DIRECTORY_SEPARATOR, [
            dirname(__DIR__),       // project root  (one level above /Core)
            $module,
            'Views',
            $relativePath . '.blade.php',
        ]);
    }

    /**
     * Capture the view output in an output buffer so that the rendered HTML
     * is returned as a string rather than written directly to stdout.
     *
     * @param  array<string, mixed> $____data
     */
    private static function render(string $____file, array $____data): string
    {
        // EXTR_SKIP prevents overwriting variables already in scope
        // (including $____file itself).
        if (!empty($____data)) {
            extract($____data, EXTR_SKIP);
        }

        ob_start();

        try {
            include $____file;
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        return ob_get_clean();
    }

    /**
     * Walk the call stack and infer the module name from the calling class's
     * namespace segment that follows "Core".
     *
     * Example:  Scy\Core\FileManager\Controllers\PanelController  →  "FileManager"
     */
    private static function guessModule(): ?string
    {
        foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 15) as $frame) {
            if (empty($frame['class'])) {
                continue;
            }

            $parts = explode('\\', $frame['class']);

            foreach ($parts as $i => $segment) {
                if ($segment === 'Core' && isset($parts[$i + 1]) && $parts[$i + 1] !== 'Views') {
                    return $parts[$i + 1];
                }
            }
        }

        return null;
    }
}