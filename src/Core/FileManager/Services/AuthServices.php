<?php

namespace Scy\Core\FileManager\Services;
use Scy\Core\FileManager\Services\StorageService;

class AuthService
{
    protected StorageService $storage;

    public function __construct()
    {
        $this->storage = new StorageService();
    }

    /**
     * Login check
     */
    public function login(string $username, string $password): bool
    {
        $users = $this->storage->getUsers();

        if (!isset($users[$username])) {
            return false;
        }

        $user = $users[$username];

        // simple password (DEV mode)
        return $user['password'] === $password;
    }

    /**
     * Get user role
     */
    public function getRole(string $username): ?string
    {
        $users = $this->storage->getUsers();

        return $users[$username]['role'] ?? null;
    }

    /**
     * Get full user data
     */
    public function getUser(string $username): ?array
    {
        $users = $this->storage->getUsers();

        return $users[$username] ?? null;
    }

    /**
     * Check if user exists
     */
    public function exists(string $username): bool
    {
        $users = $this->storage->getUsers();

        return isset($users[$username]);
    }

    /**
     * (Optional) login + session helper
     */
    public function attempt(string $username, string $password): bool
    {
        if (!$this->login($username, $password)) {
            return false;
        }

        session([
            'btv_user' => $username,
            'btv_role' => $this->getRole($username)
        ]);

        return true;
    }
}