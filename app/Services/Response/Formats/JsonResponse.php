<?php

namespace App\Services\Response\Formats;

use App\Services\Response\Contracts\ResponseContract;
use Symfony\Component\HttpFoundation\Response;

class JsonResponse implements ResponseContract
{
    public function toResponse(array $data): Response
    {
        return response()->json($data);
    }
}
