<?php

namespace App\DTOs\Auth;

class RegisterUserData
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly string $organizationSlug,
        public readonly string $organizationName,
        public readonly string $name
    ){}


}
