<?php

namespace Scy\Core\FileManager\Controllers;

use Scy\Core\Views\View;

class PanelController
{
    /** Absolute, canonicalised root that all file operations are jailed to. */
    private string $basePath;

    public function __construct()
    {
        $resolved = realpath('/var/www/ByteCore/Home');

        if ($resolved === false) {
            throw new \RuntimeException('File-manager root directory does not exist.');
        }

        $this->basePath = $resolved;
    }

    // ─────────────────────────────────────────────────────────────
    // Actions
    // ─────────────────────────────────────────────────────────────

    /** GET  /btv/scy/file-manager */
    public function index(): void
    {
        $path     = $this->sanitiseRequestPath($_GET['path'] ?? '');
        $fullPath = $this->resolvePath($path);

        if (!is_dir($fullPath)) {
            $this->abort(400, 'Requested path is not a directory.');
        }

        $raw   = scandir($fullPath);
        $items = $this->buildItemList($raw, $fullPath, $path);

        echo View::make('dashboard', [
            'items'       => $items,
            'path'        => $path,
            'breadcrumbs' => $this->buildBreadcrumbs($path),
            'diskUsage'   => $this->humanSize(disk_free_space($fullPath)),
        ], 'FileManager');
    }

    /** POST /btv/scy/file-manager/upload */
    public function upload(): void
    {
        $path     = $this->sanitiseRequestPath($_POST['path'] ?? '');
        $fullPath = $this->resolvePath($path);

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $this->abort(400, 'No valid file received.');
        }

        $file     = $_FILES['file'];
        $safeName = $this->sanitiseFilename(basename($file['name']));
        $dest     = $fullPath . DIRECTORY_SEPARATOR . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            $this->abort(500, 'Upload failed.');
        }

        $this->redirect('/btv/scy/file-manager?path=' . rawurlencode($path));
    }

    /** POST /btv/scy/file-manager/delete */
    public function delete(): void
    {
        $file     = $this->sanitiseRequestPath($_POST['file'] ?? '');
        $fullPath = $this->resolvePath($file);

        if (!is_file($fullPath)) {
            $this->abort(400, 'Target is not a file.');
        }

        if (!unlink($fullPath)) {
            $this->abort(500, 'Could not delete file.');
        }

        $this->back();
    }

    /** POST /btv/scy/file-manager/mkdir */
    public function mkdir(): void
    {
        $parent   = $this->sanitiseRequestPath($_POST['path'] ?? '');
        $name     = $this->sanitiseFilename($_POST['name'] ?? '');
        $fullPath = $this->resolvePath($parent) . DIRECTORY_SEPARATOR . $name;

        if (file_exists($fullPath)) {
            $this->abort(409, 'Directory already exists.');
        }

        if (!mkdir($fullPath, 0755, true)) {
            $this->abort(500, 'Could not create directory.');
        }

        $this->back();
    }

    /** POST /btv/scy/file-manager/rename */
    public function rename(): void
    {
        $file     = $this->sanitiseRequestPath($_POST['file'] ?? '');
        $newName  = $this->sanitiseFilename($_POST['name'] ?? '');
        $fullPath = $this->resolvePath($file);
        $newPath  = dirname($fullPath) . DIRECTORY_SEPARATOR . $newName;

        // Ensure new path is still inside the jail
        $this->assertInsideJail($newPath);

        if (!rename($fullPath, $newPath)) {
            $this->abort(500, 'Rename failed.');
        }

        $this->back();
    }

    /** GET  /btv/scy/file-manager/edit */
    public function edit(): void
    {
        $file     = $this->sanitiseRequestPath($_GET['file'] ?? '');
        $fullPath = $this->resolvePath($file);

        if (!is_file($fullPath)) {
            $this->abort(404, 'File not found.');
        }

        echo View::make('editor', [
            'content'  => file_get_contents($fullPath),
            'fileName' => basename($file),
            'path'     => $file,
        ], 'FileManager');
    }

    /** POST /btv/scy/file-manager/save */
    public function save(): void
    {
        $file     = $this->sanitiseRequestPath($_POST['file'] ?? '');
        $content  = $_POST['content'] ?? '';
        $fullPath = $this->resolvePath($file);

        if (!is_file($fullPath)) {
            $this->abort(404, 'File not found.');
        }

        if (file_put_contents($fullPath, $content) === false) {
            $this->abort(500, 'Could not save file.');
        }

        $this->redirect('/btv/scy/file-manager?path=' . rawurlencode(dirname($file)));
    }

    /** POST /btv/scy/file-manager/chmod */
    public function chmod(): void
    {
        $file     = $this->sanitiseRequestPath($_POST['file'] ?? '');
        $mode     = octdec(preg_replace('/[^0-7]/', '', $_POST['mode'] ?? '755'));
        $fullPath = $this->resolvePath($file);

        chmod($fullPath, $mode);
        $this->back();
    }

    /** POST /btv/scy/file-manager/download */
    public function download(): void
    {
        $file     = $this->sanitiseRequestPath($_GET['file'] ?? '');
        $fullPath = $this->resolvePath($file);

        if (!is_file($fullPath)) {
            $this->abort(404, 'File not found.');
        }

        $name = basename($fullPath);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . addslashes($name) . '"');
        header('Content-Length: ' . filesize($fullPath));
        header('Cache-Control: no-cache');
        readfile($fullPath);
        exit;
    }

    // ─────────────────────────────────────────────────────────────
    // Path / security helpers
    // ─────────────────────────────────────────────────────────────

    /**
     * Strip every path-traversal fragment from user input and return a
     * clean relative path string (no leading slash, no null bytes).
     */
    private function sanitiseRequestPath(string $raw): string
    {
        // Remove null bytes (classic PHP bypass)
        $clean = str_replace("\0", '', $raw);

        // Collapse any ".." sequences — before AND after URL-decoding
        $clean = rawurldecode($clean);
        $clean = preg_replace('/\.{2,}/', '', $clean);
        $clean = str_replace(['\\', '//'], ['/', '/'], $clean);
        $clean = ltrim($clean, '/');

        return $clean;
    }

    /**
     * Resolve a (already-sanitised) relative path to its canonicalised
     * absolute path, then assert it lives inside the jail.
     *
     * @throws \RuntimeException on traversal attempts or missing paths
     */
    private function resolvePath(string $relative): string
    {
        $joined = $relative === ''
            ? $this->basePath
            : $this->basePath . DIRECTORY_SEPARATOR . $relative;

        // realpath() resolves symlinks and collapses ".." — final safety net
        $real = realpath($joined);

        if ($real === false) {
            // Path doesn't exist yet (e.g. new file destination).
            // Build it manually and validate the prefix.
            $real = $this->basePath . DIRECTORY_SEPARATOR . $relative;
        }

        $this->assertInsideJail($real);

        return $real;
    }

    /** Throw if $path escapes the jail directory. */
    private function assertInsideJail(string $path): void
    {
        // Use str_starts_with for a clear prefix check
        $normalised = str_replace('\\', '/', $path);
        $jail       = str_replace('\\', '/', $this->basePath);

        if (!str_starts_with($normalised . '/', $jail . '/')) {
            throw new \RuntimeException('Access denied: path escapes the file-manager root.');
        }
    }

    /**
     * Sanitise a bare filename (no directory separators allowed).
     *
     * @throws \InvalidArgumentException
     */
    private function sanitiseFilename(string $name): string
    {
        $name = trim(basename($name));
        $name = preg_replace('/[^\w.\-]/', '_', $name);

        if ($name === '' || $name === '.' || $name === '..') {
            throw new \InvalidArgumentException('Invalid filename.');
        }

        return $name;
    }

    // ─────────────────────────────────────────────────────────────
    // View-data builders
    // ─────────────────────────────────────────────────────────────

    /**
     * Turn a raw scandir() result into a structured array ready for the view.
     *
     * @param  string[] $raw       scandir() output
     * @param  string   $fullPath  Absolute directory path
     * @param  string   $relPath   Relative path (for building hrefs)
     * @return array<int, array>
     */
    private function buildItemList(array $raw, string $fullPath, string $relPath): array
    {
        $dirs  = [];
        $files = [];

        foreach ($raw as $name) {
            if ($name === '.' || $name === '..') {
                continue;
            }

            $abs   = $fullPath . DIRECTORY_SEPARATOR . $name;
            $isDir = is_dir($abs);
            $rel   = ($relPath !== '' ? $relPath . '/' : '') . $name;
            $perms = substr(sprintf('%o', fileperms($abs)), -4);

            $entry = [
                'name'     => $name,
                'rel'      => $rel,
                'isDir'    => $isDir,
                'perms'    => $perms,
                'modified' => filemtime($abs),
                'size'     => $isDir ? null : filesize($abs),
                'sizeHuman'=> $isDir ? '—' : $this->humanSize(filesize($abs)),
                'ext'      => $isDir ? '' : strtolower(pathinfo($name, PATHINFO_EXTENSION)),
            ];

            $isDir ? $dirs[] = $entry : $files[] = $entry;
        }

        // Directories first, each group sorted alphabetically
        usort($dirs,  fn($a, $b) => strcasecmp($a['name'], $b['name']));
        usort($files, fn($a, $b) => strcasecmp($a['name'], $b['name']));

        return array_merge($dirs, $files);
    }

    /** Build [{label, href}, …] breadcrumb data from a relative path string. */
    private function buildBreadcrumbs(string $path): array
    {
        $crumbs = [['label' => 'root', 'href' => '/btv/scy/file-manager?path=']];

        if ($path === '') {
            return $crumbs;
        }

        $parts       = explode('/', $path);
        $accumulated = '';

        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }

            $accumulated .= ($accumulated !== '' ? '/' : '') . $part;
            $crumbs[]     = [
                'label' => $part,
                'href'  => '/btv/scy/file-manager?path=' . rawurlencode($accumulated),
            ];
        }

        return $crumbs;
    }

    /** Format bytes into a human-readable string. */
    private function humanSize(int|float $bytes): string
    {
        foreach (['B', 'KB', 'MB', 'GB', 'TB'] as $unit) {
            if ($bytes < 1024) {
                return round($bytes, 1) . ' ' . $unit;
            }
            $bytes /= 1024;
        }

        return round($bytes, 1) . ' PB';
    }

    // ─────────────────────────────────────────────────────────────
    // HTTP helpers
    // ─────────────────────────────────────────────────────────────

    private function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }

    private function back(): never
    {
        $ref = filter_var($_SERVER['HTTP_REFERER'] ?? '', FILTER_VALIDATE_URL);
        $this->redirect($ref ?: '/btv/scy/file-manager');
    }

    private function abort(int $code, string $message): never
    {
        http_response_code($code);
        exit(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));
    }
}