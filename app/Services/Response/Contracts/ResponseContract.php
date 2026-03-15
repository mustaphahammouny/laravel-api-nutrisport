<?php

namespace App\Services\Response\Contracts;

use Symfony\Component\HttpFoundation\Response;

interface ResponseContract
{
    public function toResponse(array $data): Response;
}
