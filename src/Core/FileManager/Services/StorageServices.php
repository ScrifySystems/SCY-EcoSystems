<?php 
namespace Scy\Core\FileManager\Services;

class StorageService
{
    private string $file;

    public function __construct()
    {
        $this->file = __DIR__ . '/../Storage/storage.json';
    }

    public function getData(): array
    {
        return json_decode(file_get_contents($this->file), true);
    }

    public function getUsers(): array
    {
        return $this->getData()['users'] ?? [];
    }
}