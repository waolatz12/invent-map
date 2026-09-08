<?php

namespace App\Support;
use App\Models\Company;
use RuntimeException;

class CurrentOrganization
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function get(): Company
    {
        $organization = app(
            'currentOrganization'
        );

        if (! $organization instanceof Company) {
            throw new RuntimeException(
                'Current company has not been established.'
            );
        }

        return $organization;
    }

    public function id(): int
    {
        return $this->get()->id;
    }

}
