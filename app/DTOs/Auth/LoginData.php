<?php

namespace App\DTOs\Auth;

class LoginData
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $email,
        public readonly string $password
    ){}
}
